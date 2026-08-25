<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreRMInventoryAdjustmentRequest;
use App\Http\Requests\UpdateFGInventoryAdjustmentRequest;
use App\Models\AccountTransaction;
use App\Models\ChartOfInventory;
use App\Models\InventoryAdjustment;
use App\Models\InventoryTransaction;
use App\Models\Store;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class RMInventoryAdjustmentController extends Controller
{
    public function index()
    {
        if (request()->ajax()) {
            $query = InventoryAdjustment::query()
                ->select([
                    'inventory_adjustments.id',
                    'inventory_adjustments.uid',
                    'inventory_adjustments.date',
                    'inventory_adjustments.status',
                    'inventory_adjustments.transaction_type',
                    'inventory_adjustments.store_id',
                    'inventory_adjustments.subtotal',
                    'inventory_adjustments.created_at',
                ])
                ->with('store:id,name')
                ->where('type', 'RM')
                ->latest('id');

            return DataTables::eloquent($query)
                ->addIndexColumn()
                ->editColumn('status', fn ($row) => showStatus($row->status))
                ->editColumn('transaction_type', fn ($row) => showStatus($row->transaction_type))
                ->editColumn('subtotal', fn ($row) => number_format((float) $row->subtotal, 2))
                ->addColumn('action', function ($row) {
                    return view('rm_inventory_adjustment.action', compact('row'));
                })
                ->addColumn('created_at', function ($row) {
                    return view('common.created_at', compact('row'));
                })
                ->rawColumns(['action', 'status', 'transaction_type', 'created_at'])
                ->make(true);
        }

        return view('rm_inventory_adjustment.index');
    }

    public function create()
    {
        $factoryId = auth()->user()?->employee?->factory_id;

        $data = [
            'groups' => ChartOfInventory::query()
                ->where(['type' => 'group', 'rootAccountType' => 'RM'])
                ->orderBy('name')
                ->get(['id', 'name']),
            'stores' => Store::query()
                ->whereType('RM')
                ->when($factoryId, fn ($q) => $q->where(['doc_type' => 'factory', 'doc_id' => $factoryId]))
                ->orderBy('name')
                ->get(['id', 'name']),
            'serial_no' => (int) InventoryAdjustment::query()->max('id') + 1,
        ];

        return view('rm_inventory_adjustment.create', $data);
    }

    public function store(StoreRMInventoryAdjustmentRequest $request)
    {
        $data = $request->validated();

        DB::beginTransaction();
        try {
            $isHeadOffice = optional(Store::find($data['store_id']))->doc_type === 'ho';
            $data['uid'] = generateUniqueUUID($data['store_id'], InventoryAdjustment::class, 'uid', false, $isHeadOffice);
            $adjustment = InventoryAdjustment::create($data);

            $totalAmount = 0;
            $stockType = $data['transaction_type'] === 'increase' ? 1 : -1;

            foreach ($data['products'] as $product) {
                $lineAmount = (float) $product['quantity'] * (float) $product['rate'];
                $totalAmount += $lineAmount;
                $adjustment->items()->create($product);

                InventoryTransaction::query()->create([
                    'store_id' => $adjustment->store_id,
                    'doc_type' => 'RMIA',
                    'doc_id' => $adjustment->id,
                    'quantity' => $product['quantity'],
                    'rate' => $product['rate'],
                    'amount' => $lineAmount,
                    'date' => $adjustment->date,
                    'type' => $stockType,
                    'coi_id' => $product['coi_id'],
                ]);
            }

            $adjustment->subtotal = $totalAmount;
            $adjustment->save();

            // GL helper expects $doc->amount in memory (column is subtotal).
            $adjustment->amount = $totalAmount;
            if ($data['transaction_type'] === 'increase') {
                addAccountsTransaction('RMIA', $adjustment, getRMInventoryGLId(), getInventoryAdjustmentGLId());
            } else {
                addAccountsTransaction('RMIA', $adjustment, getInventoryAdjustmentGLId(), getRMInventoryGLId());
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

    public function show($id)
    {
        $RMInventoryAdjustment = InventoryAdjustment::query()
            ->with([
                'store:id,name',
                'items.coi:id,name,parent_id,unit_id',
                'items.coi.parent:id,name',
                'items.coi.unit:id,name',
            ])
            ->where('type', 'RM')
            ->findOrFail(decrypt($id));

        $items = $RMInventoryAdjustment->items;

        return view('rm_inventory_adjustment.show', compact('RMInventoryAdjustment', 'items'));
    }

    public function edit(InventoryAdjustment $fGInventoryAdjustment)
    {
        //
    }

    public function update(UpdateFGInventoryAdjustmentRequest $request, InventoryAdjustment $fGInventoryAdjustment)
    {
        //
    }

    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $adjustment = InventoryAdjustment::query()->where('type', 'RM')->findOrFail($id);
            AccountTransaction::where('doc_type', 'RMIA')->where('doc_id', $adjustment->id)->delete();
            InventoryTransaction::where('doc_type', 'RMIA')->where('doc_id', $adjustment->id)->delete();
            $adjustment->items()->delete();
            $adjustment->delete();
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
