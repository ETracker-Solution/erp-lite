<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreFGDeliveryReceiveRequest;
use App\Models\ChartOfInventory;
use App\Models\DeliveryReceive;
use App\Models\InventoryTransaction;
use App\Models\Requisition;
use App\Models\RequisitionDelivery;
use App\Models\Store;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\Facades\DataTables;

class FGDeliveryReceiveController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (\request()->ajax()) {
            $data = DeliveryReceive::with('fromStore','toStore','requisitionDelivery')->where('type', 'FG')->latest();
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    return view('fg_requisition_delivery_receive.action', compact('row'));
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
        return view('fg_requisition_delivery_receive.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $data = [
            'from_stores' => Store::where(['type' => 'FG', 'doc_type' => 'factory'])->get(),
            'to_stores' => Store::where(['type' => 'FG', 'doc_type' => 'outlet'])->get(),
            'requisition_deliveries' => RequisitionDelivery::where(['type' => 'FG', 'status' => 'completed'])->get()
        ];
        return view('fg_requisition_delivery_receive.create', $data);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreFGDeliveryReceiveRequest $request)
    {
//        try {
//            DB::beginTransaction();
            $data = $request->validated();
            $requisition_delivery = DeliveryReceive::query()->create($data);
            $products = $request->get('products');
            foreach ($products as $product) {
                $requisition_delivery->items()->create($product);
                // Inventory Transaction Effect
                InventoryTransaction::query()->create([
                    'store_id' => $requisition_delivery->from_store_id,
                    'doc_type' => 'FGRD',
                    'doc_id' => $requisition_delivery->id,
                    'quantity' => $product['quantity'],
                    'rate' => $product['rate'],
                    'amount' => $product['quantity'] * $product['rate'],
                    'date' => $requisition_delivery->date,
                    'type' => -1,
                    'coi_id' => $product['coi_id'],
                ]);
                InventoryTransaction::query()->create([
                    'store_id' => $requisition_delivery->to_store_id,
                    'doc_type' => 'FGRD',
                    'doc_id' => $requisition_delivery->id,
                    'quantity' => $product['quantity'],
                    'rate' => $product['rate'],
                    'amount' => $product['quantity'] * $product['rate'],
                    'date' => $requisition_delivery->date,
                    'type' => 1,
                    'coi_id' => $product['coi_id'],
                ]);
            }
            RequisitionDelivery::where('id', $data['requisition_delivery_id'])->update(['status' => 'received']);
         //   DB::commit();
            Toastr::success('FG Delivery Requisition Entry Successful!.', '', ["progressBar" => true]);
            return redirect()->route('fg-delivery-receives.index');
//        } catch (\Exception $e) {
//            DB::rollBack();
//            Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());
//            Toastr::info('Something went wrong!.', '', ["progressbar" => true]);
//            return back();
//        }

    }

    /**
     * Display the specified resource.
     */
    public function show(DeliveryReceive $deliveryReceive)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(DeliveryReceive $deliveryReceive)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, DeliveryReceive $deliveryReceive)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(DeliveryReceive $deliveryReceive)
    {
        //
    }
}
