<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreFGRequisitionDeliveryRequest;
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

class FGRequisitionDeliveryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {

        if (\request()->ajax()) {
            $data = RequisitionDelivery::with('fromStore', 'toStore', 'requisition')->where('type', 'FG')->latest();
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    return view('fg_requisition_delivery.action', compact('row'));
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
        return view('fg_requisition_delivery.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $data = [
            'groups' => ChartOfInventory::where(['type' => 'group', 'rootAccountType' => 'FG'])->get(),
            'from_stores' => Store::where(['type' => 'FG', 'doc_type' => 'factory', 'status' => 'active'])->get(),
            'to_stores' => Store::where(['type' => 'FG', 'doc_type' => 'outlet', 'status' => 'active'])->get(),
        ];
        return view('fg_requisition_delivery.create', $data);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreFGRequisitionDeliveryRequest $request)
    {
        try {
            DB::beginTransaction();
            $data = $request->validated();
            $outlet  = Store::find($data['from_store_id']);
            $data['uid'] = generateUniqueUUID($outlet->doc_id, RequisitionDelivery::class, 'uid',true);
            $requisition_delivery = RequisitionDelivery::query()->create($data);
            $products = $request->get('products');
            foreach ($products as $product) {
                $product['requisition_id'] = $data['requisition_id'];
                $requisition_delivery->items()->create($product);
            }
            Requisition::where('id', $data['requisition_id'])->update([
                'delivery_status' => $request->delivery_status
            ]);
            DB::commit();
            Toastr::success('FG Requisition Delivery Entry Successful!.', '', ["progressBar" => true]);
            return redirect()->route('fg-requisition-deliveries.index');
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
        $fgRequisitionDelivery = RequisitionDelivery::with('toStore', 'fromStore', 'requisition','createdBy')->find(decrypt($id));
        return view('fg_requisition_delivery.show', compact('fgRequisitionDelivery'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $fgRequisitionDelivery = RequisitionDelivery::with('toStore', 'fromStore', 'requisition')->find(decrypt($id));
        return view('fg_requisition_delivery.edit', compact('fgRequisitionDelivery'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {

        try {
            DB::beginTransaction();
            $fgRequisitionDelivery = RequisitionDelivery::find(decrypt($id));
            $items = $request->items;
            $totalQty = 0;
            foreach ($items as $key => $item) {
                $prev = $fgRequisitionDelivery->items()->where('id', $key)->first();

                $currentStock = InventoryTransaction::where('coi_id', $prev->coi->id)
                    ->where('store_id', $fgRequisitionDelivery->from_store_id)
                    ->select('coi_id', DB::raw('SUM(quantity * type) AS total_sum'))
                    ->groupBy('coi_id')
                    ->pluck('total_sum')
                    ->toArray();

                $deliveredQty = $prev->coi->requisitionDeliveryItems()->where('requisition_delivery_id','!=', $fgRequisitionDelivery->id)->whereHas('requisitionDelivery', function ($q){
                    return $q->where('status','completed');
                })->sum('quantity');

                $preOrderDeliveredQty = $prev->coi->preOrderItems()->whereHas('preOrder', function ($q){
                    return $q->where('status','delivered');
                })->sum('quantity');

                $currentStock = $currentStock[0] - $deliveredQty - $preOrderDeliveredQty;
                if (max($currentStock,0) < $item){
                    Toastr::warning('No Available Stock for '.$prev->coi->name, '', ["progressBar" => true]);
                    return back();
                }
                $price = $prev->rate / $prev->quantity;
                $prev->update([
                    'quantity' => $item,
                    'rate' => $price * $item
                ]);
                $totalQty += $item;
            }
            $fgRequisitionDelivery->update(['total_quantity', $totalQty]);
            DB::commit();
            Toastr::success('FG Requisition Delivery Updated Successful!.', '', ["progressBar" => true]);
            return redirect()->route('fg-requisition-deliveries.index');
        } catch (\Exception $e) {
            DB::rollBack();
            return $e;
            Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());
            Toastr::info('Something went wrong!.', '', ["progressbar" => true]);
            return back();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        DB::beginTransaction();
        try {
            RequisitionDelivery::findOrFail($id)->delete();
            DB::commit();
        } catch (\Exception $error) {
            DB::rollBack();
            Toastr::info('Something went wrong!.', '', ["progressBar" => true]);
            return back();
        }
        Toastr::success('FG Requisition Delivery Deleted Successfully!.', '', ["progressBar" => true]);
        return redirect()->route('fg-requisition-deliveries.index');
    }

    public function pdfDownload($id)
    {
        $data = [
            'fgRequisitionDelivery' => RequisitionDelivery::with('toStore', 'fromStore', 'requisition','createdBy')->find(decrypt($id)),
        ];

        $pdf = PDF::loadView(
            'fg_requisition_delivery.pdf',
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
