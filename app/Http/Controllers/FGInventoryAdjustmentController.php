<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreFGInventoryAdjustmentRequest;
use App\Http\Requests\UpdateFGInventoryAdjustmentRequest;
use App\Models\ChartOfInventory;
use App\Models\InventoryAdjustment;
use App\Models\InventoryTransaction;
use App\Models\Store;
use Brian2694\Toastr\Facades\Toastr;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class FGInventoryAdjustmentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (request()->ajax()) {
            $query = $this->buildIndexQuery();

            return DataTables::eloquent($query)
                ->addIndexColumn()
                ->editColumn('status', fn ($row) => showStatus($row->status))
                ->editColumn('transaction_type', fn ($row) => showStatus($row->transaction_type))
                ->addColumn('type', fn ($row) => showStatus($row->transaction_type))
                ->addColumn('action', function ($row) {
                    return view('fg_inventory_adjustment.action', compact('row'));
                })
                ->addColumn('created_at', function ($row) {
                    return view('common.created_at', compact('row'));
                })
                ->rawColumns(['action', 'status', 'type', 'created_at'])
                ->make(true);
        }

        return view('fg_inventory_adjustment.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $outletId = auth()->user()?->employee?->outlet_id;
        $factoryId = auth()->user()?->employee?->factory_id;

        return view('fg_inventory_adjustment.create', [
            'groups' => ChartOfInventory::query()
                ->where(['type' => 'group', 'rootAccountType' => 'FG'])
                ->orderBy('id')
                ->get(['id', 'name']),
            'stores' => Store::query()
                ->whereType('FG')
                ->where('status', 'active')
                ->when($outletId, fn ($q) => $q->where(['doc_type' => 'outlet', 'doc_id' => $outletId]))
                ->when($factoryId && !$outletId, fn ($q) => $q->where(['doc_type' => 'factory', 'doc_id' => $factoryId]))
                ->orderBy('name')
                ->get(['id', 'name']),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreFGInventoryAdjustmentRequest $request)
    {
        $data = $request->validated();
        $products = $data['products'];
        unset($data['products']);

        DB::beginTransaction();
        try {
            $store = Store::findOrFail($data['store_id']);
            $isFactory = $store->doc_type === 'factory';
            $isHeadOffice = $store->doc_type === 'ho';
            $data['uid'] = generateUniqueUUID(
                $store->doc_id,
                InventoryAdjustment::class,
                'uid',
                $isFactory,
                $isHeadOffice
            );
            $adjustment = InventoryAdjustment::create($data);

            $totalAmount = 0;
            $stockType = $data['transaction_type'] === 'increase' ? 1 : -1;

            // Create each line individually with explicit scalars so multi-item
            // adjustments never reuse another line's coi_id / quantity / rate.
            foreach ($products as $product) {
                $coiId = (int) $product['coi_id'];
                $quantity = (float) $product['quantity'];
                $rate = (float) $product['rate'];
                $lineAmount = $quantity * $rate;
                $totalAmount += $lineAmount;

                $adjustment->items()->create([
                    'coi_id' => $coiId,
                    'quantity' => $quantity,
                    'rate' => $rate,
                ]);

                InventoryTransaction::query()->create([
                    'store_id' => $adjustment->store_id,
                    'doc_type' => 'FGIA',
                    'doc_id' => $adjustment->id,
                    'quantity' => $quantity,
                    'rate' => $rate,
                    'amount' => $lineAmount,
                    'date' => $adjustment->date,
                    'type' => $stockType,
                    'coi_id' => $coiId,
                ]);
            }

            $adjustment->subtotal = $totalAmount;
            $adjustment->save();

            $adjustment->amount = $totalAmount;
            if ($data['transaction_type'] === 'increase') {
                addAccountsTransaction('FGIA', $adjustment, getFGInventoryGLId(), getInventoryAdjustmentGLId());
            } else {
                addAccountsTransaction('FGIA', $adjustment, getInventoryAdjustmentGLId(), getFGInventoryGLId());
            }

            forgetPosTransactionAbleStockMap((int) $adjustment->store_id);

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
        $fGInventoryAdjustment = InventoryAdjustment::query()
            ->with([
                'store:id,name',
                'items.coi:id,name,parent_id,unit_id',
                'items.coi.parent:id,name',
                'items.coi.unit:id,name',
            ])
            ->findOrFail(decrypt($id));

        $items = $fGInventoryAdjustment->items;

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
    public function update($fGInventoryAdjustment)
    {
        try {
            $fGInventoryAdjustment = InventoryAdjustment::find($fGInventoryAdjustment);
            if (!$fGInventoryAdjustment) {
                Toastr::info('No Data FOund', '', ["progressBar" => true]);
                return back();

            }
            InventoryTransaction::where([
                'doc_type' => 'FGIA',
                'doc_id' => $fGInventoryAdjustment->id
            ])->delete();
            $fGInventoryAdjustment->status = 'cancelled';
            $fGInventoryAdjustment->save();
        } catch (\Exception $exception) {
            Toastr::info($exception->getMessage(), '', ["progressBar" => true]);
            return back();
        }
        Toastr::success('FG Inventory Adjustment Cancelled!.', '', ["progressBar" => true]);
        return redirect()->route('fg-inventory-adjustments.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            InventoryAdjustment::findOrFail($id)->delete();
            DB::commit();
        } catch (\Exception $error) {
            DB::rollBack();
            Toastr::info('Something went wrong!.', '', ["progressBar" => true]);
            return back();
        }
        Toastr::success('FG Inventory Transfer Deleted Successfully!.', '', ["progressBar" => true]);
        return redirect()->route('fg-inventory-adjustments.index');
    }

    private function buildIndexQuery()
    {
        $query = InventoryAdjustment::query()
            ->select([
                'inventory_adjustments.id',
                'inventory_adjustments.uid',
                'inventory_adjustments.date',
                'inventory_adjustments.transaction_type',
                'inventory_adjustments.status',
                'inventory_adjustments.store_id',
                'inventory_adjustments.created_at',
            ])
            ->with(['store:id,name'])
            ->where('inventory_adjustments.type', 'FG');

        if (auth()->user()->employee && auth()->user()->employee->user_of != 'ho') {
            $storeIds = collect();
            if (auth()->user()->employee->factory_id) {
                $storeIds = auth()->user()->employee->factory->stores()->pluck('id');
            } elseif (auth()->user()->employee->outlet_id) {
                $storeIds = auth()->user()->employee->outlet->stores()->pluck('id');
            }
            if ($storeIds->isNotEmpty()) {
                $query->whereIn('store_id', $storeIds);
            }
        }

        if (request()->filled('transaction_type')) {
            $query->where('transaction_type', request()->transaction_type);
        }
        if (request()->filled('from_date') && request()->filled('to_date')) {
            $fromDate = Carbon::parse(request()->from_date)->format('Y-m-d');
            $toDate = Carbon::parse(request()->to_date)->format('Y-m-d');
            $query->whereDate('date', '>=', $fromDate)->whereDate('date', '<=', $toDate);
        }

        return $query->latest('inventory_adjustments.id');
    }
}
