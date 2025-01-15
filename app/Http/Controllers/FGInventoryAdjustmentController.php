<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreFGInventoryAdjustmentRequest;
use App\Http\Requests\UpdateFGInventoryAdjustmentRequest;
use App\Models\ChartOfInventory;
use App\Models\InventoryAdjustment;
use App\Models\InventoryAdjustmentItem;
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
        if (\request()->ajax()) {
            $fGInventoryAdjustments = $this->getFilteredData();
            return DataTables::of($fGInventoryAdjustments)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    return view('fg_inventory_adjustment.action', compact('row'));
                })
                ->editColumn('status', function ($row) {
                    return showStatus($row->status);
                })
                ->editColumn('type', function ($row) {
                    return showStatus($row->transaction_type);
                })
                ->addColumn('created_at', function ($row) {
                    return view('common.created_at', compact('row'));
                })
                ->rawColumns(['action', 'status', 'amount_info', 'type'])
                ->make(true);
        }
        return view('fg_inventory_adjustment.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        if (\auth()->user() && \auth()->user()->employee && \auth()->user()->employee->outlet_id) {
            $stores = Store::query()->whereType('FG')->where(['doc_type' => 'outlet', 'status' => 'active', 'doc_id' => \auth()->user()->employee->outlet_id])->get();
        } elseif (\auth()->user() && \auth()->user()->employee && \auth()->user()->employee->factory_id) {
            $stores = Store::query()->whereType('FG')->where(['doc_type' => 'factory', 'status' => 'active', 'doc_id' => \auth()->user()->employee->factory_id])->get();
        } else {
            $stores = Store::query()->whereType('FG')->where('status', 'active')->get();
        }

        $serial_count = InventoryAdjustment::latest()->first() ? InventoryAdjustment::latest()->first()->id : 0;
        $serial_no = $serial_count + 1;
        $data = [
            'groups' => ChartOfInventory::where(['type' => 'group', 'rootAccountType' => 'FG'])->get(),
            'stores' => $stores,
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
            $is_factory = false;
            $store = Store::find($data['store_id']);
            if ($store->doc_type == 'factory') {
                $is_factory = true;
            }
            $outlet_or_factory_id = $store->doc_id;
            $data['uid'] = generateUniqueUUID($outlet_or_factory_id, InventoryAdjustment::class, 'uid', $is_factory);
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
                    addAccountsTransaction('FGIA', $adjustment, 16, 52);
                } else {
                    addAccountsTransaction('FGIA', $adjustment, 52, 16);
                }


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

    private function getFilteredData()
    {
        if (auth()->user()->employee && auth()->user()->employee->user_of != 'ho') {
            if (auth()->user()->employee->factory_id) {
                $store_id = auth()->user()->employee->factory->stores()->pluck('id')->toArray();
            }
            if (auth()->user()->employee->outlet_id) {
                $store_id = auth()->user()->employee->outlet->stores()->pluck('id')->toArray();
            }
            $data = InventoryAdjustment::with('store')
                ->where(['type' => 'FG'])->whereIn('store_id', $store_id)->latest();
        } else {
            $data = InventoryAdjustment::with('store')->where(['type' => 'FG'])->latest();
        }

        if (\request()->filled(key: 'transaction_type')) {
            // dd(\request()->transaction_type);
            $data = $data->where('transaction_type', \request()->transaction_type);
        }
        if (\request()->filled('from_date') && \request()->filled('to_date')) {
            $from_date = Carbon::parse(request()->from_date)->format('Y-m-d');
            $to_date = Carbon::parse(request()->to_date)->format('Y-m-d');
            // dd($from_date, $to_date);
            $data = $data->whereDate('date', '>=', $from_date)->whereDate('date', '<=', $to_date);
            // dd($data);
        }
        return $data->latest();
    }
}
