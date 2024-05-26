<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreFGInventoryAdjustmentRequest;
use App\Http\Requests\UpdateFGInventoryAdjustmentRequest;
use App\Models\ChartOfInventory;
use App\Models\InventoryAdjustment;
use App\Models\InventoryAdjustmentItem;
use App\Models\Store;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class FGInventoryAdjustmentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $fGInventoryAdjustments = InventoryAdjustment::with('store')->latest();
        if (\request()->ajax()) {
            return DataTables::of($fGInventoryAdjustments)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    return view('fg_inventory_adjustment.action', compact('row'));
                })
                ->addColumn('created_at', function ($row) {
                    return view('common.created_at', compact('row'));
                })
                ->rawColumns(['action', 'amount_info'])
                ->make(true);
        }
        return view('fg_inventory_adjustment.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $serial_count = InventoryAdjustment::latest()->first() ? InventoryAdjustment::latest()->first()->id : 0;
        $serial_no = $serial_count + 1;
        $data = [
            'groups' => ChartOfInventory::where(['type' => 'group', 'rootAccountType' => 'FG'])->get(),
            'stores' => Store::where(['type' => 'FG'])->get(),
            'serial_no' => $serial_no,
        ];
        return view('fg_inventory_adjustment.create', $data);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreFGInventoryAdjustmentRequest $request)
    {
        $data = $request->validated();
        DB::beginTransaction();
        try {
            $fGInventoryAdjustment = InventoryAdjustment::create($data);
            foreach ($data['products'] as $product) {
                // FG Inventory Transfer Effect
                InventoryAdjustmentItem::query()->create([
                    'inventory_adjustment_id' => $fGInventoryAdjustment->id,
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
        Toastr::success('FG Inventory Adjustment Successful!.', '', ["progressBar" => true]);
        return redirect()->route('fg-inventory-adjustments.index');
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {

        $fGInventoryAdjustment = InventoryAdjustment::findOrFail(decrypt($id));
        $items = InventoryAdjustmentItem::where('inventory_adjustment_id', decrypt($id))->get();
        return view('fg_inventory_adjustment.show', compact('fGInventoryAdjustment', 'items'));
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
