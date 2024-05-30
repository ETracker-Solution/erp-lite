<?php

namespace App\Http\Controllers;

use App\Classes\RequisitionNumber;
use App\Http\Requests\StoreRequisitionRequest;
use App\Http\Requests\UpdateRequisitionRequest;
use App\Libraries\SaleUtil;
use App\Models\ChartOfInventory;
use App\Models\Customer;
use App\Models\Outlet;
use App\Models\Product;
use App\Models\Requisition;
use App\Models\RequisitionItem;
use App\Models\Store;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\Facades\DataTables;
use niklasravnsborg\LaravelPdf\Facades\Pdf;

class RequisitionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data = Requisition::with('fromStore','toStore')->where('type', 'FG')->latest();
        if (\request()->ajax()) {
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    return view('requisition.action', compact('row'));
                })
                ->addColumn('created_at', function ($row) {
                    return view('common.created_at', compact('row'));
                })
                ->editColumn('status', function ($row) {
                    return showStatus($row->status);
                })
                ->rawColumns(['action', 'created_at', 'status'])
                ->make(true);
        }
        return view('requisition.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $data = [
            'groups' => ChartOfInventory::where(['type' => 'group', 'rootAccountType' => 'FG'])->get(),
            'from_stores' => Store::where(['type' => 'FG', 'doc_type' => 'outlet'])->get(),
            'to_stores' => Store::where(['type' => 'FG', 'doc_type' => 'factory'])->get(),
            'serial_no' =>RequisitionNumber::serial_number()
        ];
        return view('requisition.create', $data);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreRequisitionRequest $request)
    {
        try {
            DB::beginTransaction();
            $data = $request->validated();
            $requisition = Requisition::query()->create($data);
            $products = $request->get('products');
            foreach ($products as $row) {
                $requisition->items()->create($row);
            }
            DB::commit();
            Toastr::success('Requisition Entry Successful!.', '', ["progressBar" => true]);
            return redirect()->route('requisitions.index');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());
            Toastr::info('Something went wrong!.', '', ["progressbar" => true]);
            return back();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $requisition = Requisition::findOrFail(decrypt($id));
        return view('requisition.show', compact('requisition'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $data = [
            'groups' => ChartOfInventory::where(['type' => 'group', 'rootAccountType' => 'FG'])->get(),
            'stores' => Store::where(['type' => 'FG'])->get(),
            'requisition' => Requisition::find(decrypt($id))
        ];
        return view('requisition.edit', $data);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateRequisitionRequest $request, $id)
    {
        DB::beginTransaction();
        try {
            $validated = $request->validated();
            $requisition= Requisition::find($id);
            $requisition->update($validated);
            RequisitionItem::where('requisition_id', $requisition->id)->delete();
            $products = $request->get('products');
            foreach ($products as $row) {
                $requisition->items()->create($row);
            }


            DB::commit();
        } catch (\Exception $exception) {
            DB::rollBack();
            return $exception;
            Toastr::info('Something went wrong!.', '', ["progressBar" => true]);
            return back();
        }

        Toastr::success('FG Requisition Updated Successful!.', '', ["progressbar" => true]);
        return redirect()->route('requisitions.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            Requisition::findOrFail($id)->delete();
            DB::commit();
        } catch (\Exception $error) {
            DB::rollBack();
            Toastr::info('Something went wrong!.', '', ["progressBar" => true]);
            return back();
        }
        Toastr::success('Requisition Deleted Successfully!.', '', ["progressBar" => true]);
        return redirect()->route('requisitions.index');
    }

    public function pdfDownload($id)
    {
        $data = [
            'requisition' => Requisition::findOrFail(decrypt($id)),
        ];

        $pdf = PDF::loadView(
            'requisition.pdf',
            $data,
            [],
            [
                'format' => 'A4-P',
                'orientation' => 'P',
                'margin-left' => 1,

                '', // mode - default ''
                '', // format - A4, for example, default ''
                0, // font size - default 0
                '', // default font family
                1, // margin_left
                1, // margin right
                1, // margin top
                1, // margin bottom
                1, // margin header
                1, // margin footer
                'L', // L - landscape, P - portrait

            ]
        );
        $name = \Carbon\Carbon::now()->format('d-m-Y');

        return $pdf->stream($name . '.pdf');
    }

    public function todayRequisition()
    {
        $data = $this->getRequisitionData();
        return view('requisition.today_requisition', $data);
    }
    public function getRequisitionData()
    {
        $headers = [
            'Product',
        ];
        $outlets = Outlet::select('id','name')->get();
        foreach ($outlets as $outlet) {
            $headers[] = $outlet->name;
        }

        $headers[]='Total';

        $values = [];
        $products = ChartOfInventory::where(['type'=>'item','rootAccountType'=>'FG'])->get();

        foreach ($products as $key=>$product){
            $totalQty = 0;
            $values[$key]['product_name']=$product->name;
            foreach ($outlets as $outlet) {
                $qty = getRequisitionQtyByProduct($product->id, $outlet->id);
                $values[$key]['product_quantity'][]=$qty;
                $totalQty +=$qty;
            }
            $values[$key]['total']=$totalQty;
        }

        return [
            'products' => Product::where('type', 'finish')->get(),
            'outlets' => Outlet::all(),
            'headers'=>$headers,
            'values'=>$values
        ];
    }
}
