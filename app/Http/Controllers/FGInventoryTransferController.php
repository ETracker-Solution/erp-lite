<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreFGInventoryTransferRequest;
use App\Http\Requests\UpdateFGInventoryTransferRequest;
use App\Models\ChartOfInventory;
use App\Models\InventoryTransaction;
use App\Models\InventoryTransfer;
use App\Models\InventoryTransferItem;
use App\Models\Store;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class FGInventoryTransferController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $fGInventoryTransfers = InventoryTransfer::with('toStore', 'fromStore')->where(['type' => 'FG'])->latest();
        if (\request()->ajax()) {
            return DataTables::of($fGInventoryTransfers)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    return view('fg_inventory_transfer.action', compact('row'));
                })
                ->addColumn('created_at', function ($row) {
                    return view('common.created_at', compact('row'));
                })
                ->rawColumns(['action', 'amount_info'])
                ->make(true);
        }
        return view('fg_inventory_transfer.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $serial_count = InventoryTransfer::latest()->first() ? InventoryTransfer::latest()->first()->id : 0;
        $serial_no = $serial_count + 1;
        $data = [
            'groups' => ChartOfInventory::where(['type' => 'group', 'rootAccountType' => 'FG'])->get(),
            'stores' => Store::where(['type' => 'FG'])->get(),
            'serial_no' => $serial_no,
        ];
        return view('fg_inventory_transfer.create', $data);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreFGInventoryTransferRequest $request)
    {
        $data = $request->validated();
//        DB::beginTransaction();
//        try {
        $fGInventoryTransfer = InventoryTransfer::create($data);
        foreach ($data['products'] as $product) {
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
//            DB::commit();
//        } catch (\Exception $error) {
//            DB::rollBack();
//            Toastr::info('Something went wrong!.', '', ["progressBar" => true]);
//            return back();
//        }
        Toastr::success('FG Inventory Transfer Successful!.', '', ["progressBar" => true]);
        return redirect()->route('fg-inventory-transfers.index');
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $items = InventoryTransferItem::where('inventory_transfer_id', $id)->get();
        $fGInventoryTransfer = InventoryTransfer::with('toStore', 'fromStore')->find($id);
        return view('fg_inventory_transfer.show', compact('fGInventoryTransfer', 'items'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(InventoryTransfer $inventoryTransfer)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateFGInventoryTransferRequest $request, InventoryTransfer $inventoryTransfer)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(FGInventoryTransfer $inventoryTransfer)
    {
        //
    }
}
