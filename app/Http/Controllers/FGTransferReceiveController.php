<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreFGDeliveryReceiveRequest;
use App\Http\Requests\StoreFGTransferReceiveRequest;
use App\Models\ChartOfInventory;
use App\Models\DeliveryReceive;
use App\Models\InventoryTransaction;
use App\Models\InventoryTransfer;
use App\Models\Outlet;
use App\Models\Requisition;
use App\Models\RequisitionDelivery;
use App\Models\Store;
use App\Models\TransferReceive;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\Facades\DataTables;
use niklasravnsborg\LaravelPdf\Facades\Pdf;

class FGTransferReceiveController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (\request()->ajax()) {
            $data = TransferReceive::with('fromStore', 'toStore', 'inventoryTransfer')->where('type', 'FG')->latest();
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    return view('fg_inventory_transfer_receive.action', compact('row'));
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
        return view('fg_inventory_transfer_receive.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        if (\auth()->user() && \auth()->user()->employee && \auth()->user()->employee->outlet_id) {
            $inventory_transfers = InventoryTransfer::whereHas('toStore', function ($query) {
                $query->where(['doc_type' => 'outlet', 'doc_id' => \auth()->user()->employee->outlet_id]);
            })->where(['type' => 'FG', 'status' => 'pending'])->get();
        } else {
            $inventory_transfers = InventoryTransfer::where(['type' => 'FG', 'status' => 'pending'])->get();
        }
        $data = [
            'from_stores' => Store::where(['type' => 'FG'])->get(),
            'to_stores' => Store::where(['type' => 'FG'])->get(),
            'inventory_transfers' => $inventory_transfers
        ];
        return view('fg_inventory_transfer_receive.create', $data);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreFGTransferReceiveRequest $request)
    {
        //        try {
        //            DB::beginTransaction();
        $data = $request->validated();
        $store = Store::find($data['to_store_id']);
        $data['uid'] = generateUniqueUUID($store->outlet->id, TransferReceive::class, 'uid');
        $fGInventoryTransfer = TransferReceive::query()->create($data);
        $products = $request->get('products');
        foreach ($products as $product) {
            $fGInventoryTransfer->items()->create($product);
            // Inventory Transaction Effect
            InventoryTransaction::query()->create([
                'store_id' => $fGInventoryTransfer->from_store_id,
                'doc_type' => 'FGIT',
                'doc_id' => $fGInventoryTransfer->id,
                'quantity' => $product['quantity'],
                'rate' => $product['rate'],
                'amount' => $product['quantity'] * $product['rate'],
                'date' => $fGInventoryTransfer->date,
                'type' => -1,
                'coi_id' => $product['coi_id'],
            ]);
            InventoryTransaction::query()->create([
                'store_id' => $fGInventoryTransfer->to_store_id,
                'doc_type' => 'FGIT',
                'doc_id' => $fGInventoryTransfer->id,
                'quantity' => $product['quantity'],
                'rate' => $product['rate'],
                'amount' => $product['quantity'] * $product['rate'],
                'date' => $fGInventoryTransfer->date,
                'type' => 1,
                'coi_id' => $product['coi_id'],
            ]);
        }
        InventoryTransfer::where('id', $data['inventory_transfer_id'])->update(['status' => 'received']);
        //   DB::commit();
        Toastr::success('FG Transfer Receive Entry Successful!.', '', ["progressBar" => true]);
        return redirect()->route('fg-transfer-receives.index');
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
    public function show($id)
    {
        $fgTransferReceive = TransferReceive::with('toStore', 'fromStore')->find(decrypt($id));
        return view('fg_inventory_transfer_receive.show', compact('fgTransferReceive'));
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
    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            DeliveryReceive::findOrFail($id)->delete();
            DB::commit();
        } catch (\Exception $error) {
            DB::rollBack();
            Toastr::info('Something went wrong!.', '', ["progressBar" => true]);
            return back();
        }
        Toastr::success('FG Delivery Receive Deleted Successfully!.', '', ["progressBar" => true]);
        return redirect()->route('fg-delivery-receives.index');
    }

    public function pdf($id)
    {
        $data = [
            'fgTransferReceive' => TransferReceive::with('toStore', 'fromStore')->find(decrypt($id)),
        ];

        $pdf = PDF::loadView(
            'fg_inventory_transfer_receive.pdf',
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
