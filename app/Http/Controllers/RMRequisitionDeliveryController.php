<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreRMRequisitionDeliveryRequest;
use App\Models\ChartOfInventory;
use App\Models\InventoryTransaction;
use App\Models\Requisition;
use App\Models\RequisitionDelivery;
use App\Models\Store;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\Facades\DataTables;
use niklasravnsborg\LaravelPdf\Facades\Pdf;

class RMRequisitionDeliveryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {

        if (\request()->ajax()) {
            $data = RequisitionDelivery::with('fromStore','toStore','requisition')->where('type', 'RM')->latest();
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    return view('rm_requisition_delivery.action', compact('row'));
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
        return view('rm_requisition_delivery.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $data = [
            'groups' => ChartOfInventory::where(['type' => 'group', 'rootAccountType' => 'RM'])->get(),
            'from_stores' => Store::where(['type' => 'RM', 'doc_type' => 'ho'])->get(),
            'to_stores' => Store::where(['type' => 'RM', 'doc_type' => 'factory'])->get(),
            'requisitions' => Requisition::where(['type' => 'RM', 'status' => 'pending'])->get()
        ];
        return view('rm_requisition_delivery.create', $data);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreRMRequisitionDeliveryRequest $request)
    {
        try {
            DB::beginTransaction();
            $data = $request->validated();
            $requisition_delivery = RequisitionDelivery::query()->create($data);
            $products = $request->get('products');
            foreach ($products as $product) {
                $requisition_delivery->items()->create($product);
                // Inventory Transaction Effect
                InventoryTransaction::query()->create([
                    'store_id' => $requisition_delivery->from_store_id,
                    'doc_type' => 'RMRD',
                    'doc_id' => $requisition_delivery->id,
                    'quantity' => $product['quantity'],
                    'rate' => $product['rate'] ?? 0,
                    'amount' => $product['quantity'] * $product['rate'],
                    'date' => $requisition_delivery->date,
                    'type' => -1,
                    'coi_id' => $product['coi_id'],
                ]);
                InventoryTransaction::query()->create([
                    'store_id' => $requisition_delivery->to_store_id,
                    'doc_type' => 'RMRD',
                    'doc_id' => $requisition_delivery->id,
                    'quantity' => $product['quantity'],
                    'rate' => $product['rate'] ?? 0,
                    'amount' => $product['quantity'] * $product['rate'],
                    'date' => $requisition_delivery->date,
                    'type' => 1,
                    'coi_id' => $product['coi_id'],
                ]);

            }
            Requisition::where('id', $data['requisition_id'])->update(['status' => 'completed']);
            DB::commit();
            Toastr::success('RM Requisition Delivery Entry Successful!.', '', ["progressBar" => true]);
            return redirect()->route('rm-requisition-deliveries.index');
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
    public function show(string $id)
    {
        $requisitionDelivery = RequisitionDelivery::with('toStore', 'fromStore')->find(decrypt($id));
        return view('rm_requisition_delivery.show', compact('requisitionDelivery'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    public function pdfDownload($id)
    {
        $data = [
            'requisitionDelivery' => RequisitionDelivery::with('toStore', 'fromStore')->find(decrypt($id)),
        ];

        $pdf = PDF::loadView(
            'rm_requisition_delivery.pdf',
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
}
