<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreRMInventoryAdjustmentRequest;
use App\Http\Requests\UpdateRMInventoryAdjustmentRequest;
use App\Models\ChartOfInventory;
use App\Models\InventoryAdjustment;
use App\Models\InventoryAdjustmentItem;
use App\Models\InventoryTransaction;
use App\Models\AccountTransaction;
use App\Models\Store;
use App\Models\AdjustmentEditHistory;
use Brian2694\Toastr\Facades\Toastr;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class RMInventoryAdjustmentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (request()->ajax()) {
            $query = InventoryAdjustment::query()
                ->with(['store:id,name'])
                ->where('type', 'RM');

            $query = $this->filter($query, request());

            return DataTables::of($query->latest())
                ->addIndexColumn()
                ->addColumn('action', fn($row) => view('rm_inventory_adjustment.action', compact('row')))
                ->editColumn('status', fn($row) => showStatus($row->status))
                ->editColumn('type', fn($row) => showStatus($row->transaction_type))
                ->addColumn('created_at', fn($row) => view('common.created_at', compact('row')))
                ->rawColumns(['action', 'status', 'type', 'created_at'])
                ->make(true);
        }
        return view('rm_inventory_adjustment.index');
    }

    private function filter($query, $request)
    {
        return $query
            ->when($request->date_range, function ($q) use ($request) {
                searchColumnByDateRange($q, 'date', $request->date_range);
            })
            ->when($request->uid, fn($q) => $q->where('uid', 'like', "%{$request->uid}%"))
            ->when($request->store, function ($q) use ($request) {
                $q->whereHas('store', function ($sq) use ($request) {
                    $sq->where('name', 'like', "%{$request->store}%");
                });
            });
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
            'stores' =>\auth()->user() && \auth()->user()->employee && \auth()->user()->employee->factory_id ? Store::query()->whereType('RM')->where(['doc_type'=>'factory', 'doc_id'=>\auth()->user()->employee->factory_id])->get() : Store::query()->whereType('RM')->get(),
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
            $store = Store::find($data['store_id']);
            $is_factory = false;
            if ($store->doc_type == 'factory') {
                $is_factory = true;
            }
            $outlet_or_factory_id = $store->doc_id;
            $is_headOffice = $store->doc_type == 'ho';
            $data['uid'] = generateUniqueUUID($outlet_or_factory_id, InventoryAdjustment::class, 'uid', $is_factory, $is_headOffice);
            $adjustment = InventoryAdjustment::create($data);
            $adjustment->amount = $adjustment->subtotal;
            foreach ($data['products'] as $product) {
                $adjustment->items()->create($product);

                // Inventory Transaction Effect
                $type = $data['transaction_type'] === 'increase' ? 1 : -1;
                InventoryTransaction::query()->create([
                    'store_id' => $adjustment->store_id,
                    'doc_type' => 'RMIA',
                    'doc_id' => $adjustment->id,
                    'quantity' => $product['quantity'],
                    'rate' => $product['rate'],
                    'amount' => $product['quantity'] * $product['rate'],
                    'date' => $adjustment->date,
                    'type' => $type,
                    'coi_id' => $product['coi_id'],
                ]);
            }
            // Accounts Transaction Effect
            if ($data['transaction_type'] === 'increase') {
                addAccountsTransaction('RMIA', $adjustment, 16, 52);
            } else {
                addAccountsTransaction('RMIA', $adjustment, 52, 16);
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
        $RMInventoryAdjustment = InventoryAdjustment::findOrFail(decrypt($id));
        $items = InventoryAdjustmentItem::where('inventory_adjustment_id', decrypt($id))->get();
        return view('rm_inventory_adjustment.show', compact('RMInventoryAdjustment', 'items'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $RMInventoryAdjustment = InventoryAdjustment::with('items.coi')->findOrFail(decrypt($id));
        
        $data = [
            'groups' => ChartOfInventory::where(['type' => 'group', 'rootAccountType' => 'RM'])->get(),
            'stores' =>\auth()->user() && \auth()->user()->employee && \auth()->user()->employee->factory_id ? Store::query()->whereType('RM')->where(['doc_type'=>'factory', 'doc_id'=>\auth()->user()->employee->factory_id])->get() : Store::query()->whereType('RM')->get(),
            'RMInventoryAdjustment' => $RMInventoryAdjustment,
        ];
        return view('rm_inventory_adjustment.edit', $data);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateRMInventoryAdjustmentRequest $request, $id)
    {
        $adjustment = InventoryAdjustment::findOrFail($id);
        $data = $request->validated();
        
        DB::beginTransaction();
        try {
            // Store History
            $old_data = $adjustment->load('items.coi')->toArray();
            
            // Reverse previous effects
            InventoryTransaction::where(['doc_type' => 'RMIA', 'doc_id' => $adjustment->id])->delete();
            AccountTransaction::where(['doc_type' => 'RMIA', 'doc_id' => $adjustment->id])->delete();
            
            // Update Adjustment
            $adjustment->update($data);
            $adjustment->items()->delete();
            
            foreach ($data['products'] as $product) {
                $adjustment->items()->create($product);

                // Inventory Transaction Effect
                $type = $data['transaction_type'] === 'increase' ? 1 : -1;
                InventoryTransaction::query()->create([
                    'store_id' => $adjustment->store_id,
                    'doc_type' => 'RMIA',
                    'doc_id' => $adjustment->id,
                    'quantity' => $product['quantity'],
                    'rate' => $product['rate'],
                    'amount' => $product['quantity'] * $product['rate'],
                    'date' => $adjustment->date,
                    'type' => $type,
                    'coi_id' => $product['coi_id'],
                ]);
            }
            
            // Accounts Transaction Effect
            if ($data['transaction_type'] === 'increase') {
                addAccountsTransaction('RMIA', $adjustment, 16, 52);
            } else {
                addAccountsTransaction('RMIA', $adjustment, 52, 16);
            }
            
            // Log history
            AdjustmentEditHistory::create([
                'inventory_adjustment_id' => $adjustment->id,
                'old_data' => json_encode($old_data),
                'new_data' => json_encode($adjustment->load('items.coi')->toArray()),
                'remarks' => $request->edit_remark,
                'edited_by' => auth()->id(),
            ]);

            DB::commit();
        } catch (\Exception $error) {
            DB::rollBack();
            Toastr::info('Something went wrong! ' . $error->getMessage(), '', ["progressBar" => true]);
            return back();
        }
        
        Toastr::success('RM Inventory Adjustment Updated Successfully!', '', ["progressBar" => true]);
        return redirect()->route('rm-inventory-adjustments.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            AccountTransaction::where('doc_type', 'RMIA')->where('doc_id', $id)->delete();
            InventoryTransaction::where('doc_type', 'RMIA')->where('doc_id', $id)->delete();
            InventoryAdjustment::findOrFail($id)->delete();
            DB::commit();
        } catch (\Exception $error) {
            DB::rollBack();
            Toastr::info('Something went wrong!.', '', ["progressBar" => true]);
            return back();
        }
        Toastr::success('Inventory Adjustment Deleted Successfully!.', '', ["progressBar" => true]);
        return redirect()->route('rm-inventory-adjustments.index');
    }
}
