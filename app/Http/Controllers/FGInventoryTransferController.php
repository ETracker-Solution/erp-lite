<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreFGInventoryTransferRequest;
use App\Http\Requests\UpdateFGInventoryTransferRequest;
use App\Models\ChartOfInventory;
use App\Models\Customer;
use App\Models\FGInventoryTransfer;
use App\Models\FGInventoryTransferItem;
use App\Models\Requisition;
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
        $fGInventoryTransfers = FGInventoryTransfer::with('toStore','fromStore')->latest();
        if (\request()->ajax()) {
            return DataTables::of($fGInventoryTransfers)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    return view('finish_goods_inventory_transfer.action', compact('row'));
                })
                ->addColumn('created_at', function ($row) {
                    return view('common.created_at', compact('row'));
                })
                ->rawColumns(['action', 'amount_info'])
                ->make(true);
        }
        return view('finish_goods_inventory_transfer.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $serial_count = FGInventoryTransfer::latest()->first() ? FGInventoryTransfer::latest()->first()->id : 0;
        $serial_no = $serial_count + 1;
        $data = [
            'groups' => ChartOfInventory::where(['type' => 'group', 'rootAccountType' => 'FG'])->get(),
            'stores' => Store::where(['type' => 'FG'])->get(),
            'serial_no' => $serial_no,
        ];
        return view('finish_goods_inventory_transfer.create', $data);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreFGInventoryTransferRequest $request)
    {
        $data = $request->validated();
        DB::beginTransaction();
        try {
            $fGInventoryTransfer = FGInventoryTransfer::create($data);
            foreach ($data['products'] as $product) {
                // FG Inventory Transfer Effect
                FGInventoryTransferItem::query()->create([
                    'f_g_inventory_transfer_id' => $fGInventoryTransfer->id,
                    'quantity' => $product['quantity'],
                    'rate' => $product['rate'],
                    'coi_id' => $product['coi_id'],
                ]);
            }
            DB::commit();
        } catch (\Exception $error) {
            DB::rollBack();
            Toastr::info('Something went wrong!.', '', ["progressBar" => true]);
            return back();
        }
        Toastr::success('Member Point Created Successfully!.', '', ["progressBar" => true]);
        return redirect()->route('finish-goods-inventory-transfers.index');
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $items = FGInventoryTransferItem::where('f_g_inventory_transfer_id', $id)->get();
        $fGInventoryTransfer = FGInventoryTransfer::with('toStore','fromStore')->find($id);
        return view('finish_goods_inventory_transfer.show', compact('fGInventoryTransfer','items'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(FGInventoryTransfer $fGInventoryTransfer)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateFGInventoryTransferRequest $request, FGInventoryTransfer $fGInventoryTransfer)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(FGInventoryTransfer $fGInventoryTransfer)
    {
        //
    }
}
