<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSalesReturnRequest;
use App\Http\Requests\UpdateSalesReturnRequest;
use App\Models\Sale;
use App\Models\SalesReturn;
use App\Models\SalesReturnItem;
use App\Models\Store;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class SalesReturnController extends Controller
{
    public function autocompleteSearch(Request $request)
    {
        $search = $request->searchquery;
        $query = Sale::query()
            ->select(['id', 'invoice_number', 'date', 'outlet_id', 'grand_total'])
            ->where('invoice_number', 'like', '%' . $search . '%');

        if (auth()->user()?->employee?->outlet_id && !auth()->user()->is_super) {
            $query->where('outlet_id', auth()->user()->employee->outlet_id);
        }

        return $query->latest('id')->limit(20)->get();
    }

    public function fetchSaleInfo($id)
    {
        $sale = Sale::with([
            'customer:id,name,mobile',
            'items.coi:id,name,parent_id,unit_id',
            'items.coi.parent:id,name',
            'items.coi.unit:id,name',
        ])->findOrFail($id);

        $returnedBefore = $sale->salesReturns()->pluck('id')->toArray();
        $items = [];

        foreach ($sale->items as $row) {
            $returnedQty = SalesReturnItem::whereIn('sales_return_id', $returnedBefore)
                ->where('coi_id', $row->coi->id)
                ->sum('quantity');
            $last_qty = (float) $row->quantity - (float) $returnedQty;
            if ($last_qty > 0) {
                $items[] = [
                    'sale_id' => $id,
                    'coi_id' => $row->coi->id,
                    'unit' => $row->coi->unit->name ?? '',
                    'name' => $row->coi->name ?? '',
                    'group' => $row->coi->parent->name ?? '',
                    'sale_quantity' => $last_qty,
                    'rate' => $row->unit_price,
                    'quantity' => $last_qty,
                    'return_quantity' => $last_qty,
                    'discount_type' => $row->discount_type,
                    'discount_value' => $row->discount_value,
                    'discount' => $row->discount,
                    'discount_amount' => 0,
                ];
            }
        }

        return response()->json([
            'items' => $items,
            'date' => $sale->date,
            'customer_id' => $sale->customer_id,
            'outlet_id' => $sale->outlet_id,
            'reference_no' => $sale->reference_no,
            'remark' => $sale->remark,
            'sale' => $sale,
        ]);
    }

    public function index()
    {
        if (request()->ajax()) {
            $query = SalesReturn::query()
                ->select([
                    'sales_returns.id',
                    'sales_returns.uid',
                    'sales_returns.date',
                    'sales_returns.status',
                    'sales_returns.subtotal',
                    'sales_returns.discount',
                    'sales_returns.grand_total',
                    'sales_returns.sale_id',
                    'sales_returns.created_at',
                ])
                ->with(['sale:id,invoice_number'])
                ->latest('id');

            return DataTables::eloquent($query)
                ->addIndexColumn()
                ->editColumn('status', fn ($row) => showStatus($row->status ?: 'final'))
                ->editColumn('subtotal', fn ($row) => number_format((float) $row->subtotal, 2))
                ->editColumn('discount', fn ($row) => number_format((float) $row->discount, 2))
                ->editColumn('grand_total', fn ($row) => number_format((float) $row->grand_total, 2))
                ->addColumn('action', function ($row) {
                    return view('sales_return.action', compact('row'));
                })
                ->addColumn('created_at', function ($row) {
                    return view('common.created_at', compact('row'));
                })
                ->rawColumns(['action', 'created_at', 'status'])
                ->make(true);
        }

        return view('sales_return.index');
    }

    public function create()
    {
        $storesQuery = Store::query()
            ->where(['doc_type' => 'outlet', 'type' => 'FG', 'status' => 'active'])
            ->orderBy('name');

        if (!auth()->user()->is_super && auth()->user()?->employee?->user_of == 'outlet') {
            $storesQuery->where('doc_id', auth()->user()->employee->outlet_id);
        }

        return view('sales_return.create', [
            'stores' => $storesQuery->get(['id', 'name']),
        ]);
    }

    public function store(StoreSalesReturnRequest $request)
    {
        $validated = $request->validated();
        $products = collect($validated['products'] ?? [])
            ->filter(fn ($p) => (float) ($p['quantity'] ?? 0) > 0)
            ->values()
            ->all();

        if (empty($products)) {
            Toastr::info('At Least One Product Required.', '', ["progressBar" => true]);
            return back();
        }

        DB::beginTransaction();
        try {
            $sale = Sale::findOrFail($validated['sale_id']);
            $validated['uid'] = generateUniqueUUID($sale->outlet_id, SalesReturn::class, 'uid', false, false);
            $validated['products'] = $products;
            $return = SalesReturn::create($validated);

            $returnAmount = 0;
            $cogsAmount = 0;
            foreach ($products as $product) {
                $obj = new \stdClass();
                $obj->date = $return->date;
                $obj->quantity = $product['quantity'];
                $obj->rate = $product['rate'];
                $obj->amount = $product['quantity'] * $product['rate'];
                $obj->store_id = $validated['store_id'];
                $obj->coi_id = $product['coi_id'];
                $obj->id = $return->id;
                addInventoryTransaction(1, 'SR', $obj);
                $return->items()->create($product);
                $lineDiscount = $product['discount'] ?? 0;
                $returnAmount += ($product['quantity'] * $product['rate']) - $lineDiscount;
                $cogsAmount += averageFGRate($product['coi_id']) * $product['quantity'];
            }

            $return->amount = $returnAmount;
            addAccountsTransaction('SR', $return, getIncomeFromSalesGLId(), getAccountsReceiveableGLId());
            $return->amount = $cogsAmount;
            addAccountsTransaction('SR', $return, getFGInventoryGLId(), getCOGSGLId());
            DB::commit();
        } catch (\Exception $error) {
            DB::rollBack();
            Toastr::info('Something went wrong!.', '', ["progressBar" => true]);
            return back();
        }

        Toastr::success('Sales Return Created Successfully!.', '', ["progressBar" => true]);
        return redirect()->route('sales-returns.index');
    }

    public function show($id)
    {
        $salesReturn = SalesReturn::query()
            ->with([
                'sale:id,invoice_number,date',
                'items.coi:id,name,parent_id,unit_id',
                'items.coi.parent:id,name',
                'items.coi.unit:id,name',
            ])
            ->findOrFail(decrypt($id));

        return view('sales_return.show', compact('salesReturn'));
    }

    public function edit(SalesReturn $salesReturn)
    {
        //
    }

    public function update(UpdateSalesReturnRequest $request, SalesReturn $salesReturn)
    {
        //
    }

    public function destroy(SalesReturn $salesReturn)
    {
        //
    }
}
