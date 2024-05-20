<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreFGInventoryTransferRequest;
use App\Http\Requests\UpdateFGInventoryTransferRequest;
use App\Models\ChartOfInventory;
use App\Models\Customer;
use App\Models\FGInventoryTransfer;
use App\Models\Requisition;
use App\Models\Store;

class FGInventoryTransferController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
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
        dd($data);
    }

    /**
     * Display the specified resource.
     */
    public function show(FGInventoryTransfer $fGInventoryTransfer)
    {
        //
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
