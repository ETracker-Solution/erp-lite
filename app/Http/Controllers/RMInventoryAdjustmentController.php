<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreFGInventoryAdjustmentRequest;
use App\Http\Requests\StoreRMInventoryAdjustmentRequest;
use App\Http\Requests\UpdateFGInventoryAdjustmentRequest;
use App\Models\ChartOfInventory;
use App\Models\InventoryAdjustment;
use App\Models\InventoryAdjustmentItem;
use App\Models\InventoryTransaction;
use App\Models\Store;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class RMInventoryAdjustmentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $fGInventoryAdjustments = InventoryAdjustment::with('store')->where('type','RM')->latest();
        if (\request()->ajax()) {
            return DataTables::of($fGInventoryAdjustments)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    return view('rm_inventory_adjustment.action', compact('row'));
                })
                ->addColumn('created_at', function ($row) {
                    return view('common.created_at', compact('row'));
                })
                ->rawColumns(['action', 'amount_info'])
                ->make(true);
        }
        return view('rm_inventory_adjustment.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $serial_count = InventoryAdjustment::latest()->first() ? InventoryAdjustment::latest()->first()->id : 0;
        $serial_no = $serial_count + 1;
        $data = [
            'groups' => ChartOfInventory::where(['type' => 'group', 'rootAccountType' => 'RM'])->get(),
            'stores' => Store::where(['type' => 'RM'])->get(),
            'serial_no' => $serial_no,
        ];
        return view('rm_inventory_adjustment.create', $data);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreRMInventoryAdjustmentRequest $request)
    {
        $data = $request->validated();
        DB::beginTransaction();
        try {
            $adjustment = InventoryAdjustment::create($data);
            $adjustment->amount = $adjustment->subtotal;
            foreach ($data['products'] as $product) {
                $adjustment->items()->create($product);

                // Inventory Transaction Effect

                $type = $data['transaction_type'] === 'increase' ? 1 : -1;
                InventoryTransaction::query()->create([
                    'store_id' => $adjustment->store_id,
                    'doc_type' => 'FGIA',
                    'doc_id' => $adjustment->id,
                    'quantity' => $product['quantity'],
                    'rate' => $product['rate'],
                    'amount' => $product['quantity'] * $product['rate'],
                    'date' => $adjustment->date,
                    'type' => $type,
                    'coi_id' => $product['coi_id'],
                ]);
                // Accounts Transaction Effect
                if ($data['transaction_type'] === 'increase') {
                    addAccountsTransaction('RMIA', $adjustment, 16, 52);
                } else {
                    addAccountsTransaction('RMIA', $adjustment, 52, 16);
                }


            }
            DB::commit();
        } catch (\Exception $error) {
            DB::rollBack();
            Toastr::info('Something went wrong!.', '', ["progressBar" => true]);
            return back();
        }
        Toastr::success('RM Inventory Adjustment Successful!.', '', ["progressBar" => true]);
        return redirect()->route('rm-inventory-adjustments.index');
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {

        $fGInventoryAdjustment = InventoryAdjustment::findOrFail(decrypt($id));
        $items = InventoryAdjustmentItem::where('inventory_adjustment_id', decrypt($id))->get();
        return view('rm_inventory_adjustment.show', compact('fGInventoryAdjustment', 'items'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(InventoryAdjustment $fGInventoryAdjustment)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateFGInventoryAdjustmentRequest $request, InventoryAdjustment $fGInventoryAdjustment)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(InventoryAdjustment $fGInventoryAdjustment)
    {
        //
    }
}
