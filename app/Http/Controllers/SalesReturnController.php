<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSalesReturnRequest;
use App\Http\Requests\UpdateSalesReturnRequest;
use App\Models\Batch;
use App\Models\ChartOfInventory;
use App\Models\Factory;
use App\Models\InventoryTransaction;
use App\Models\Product;
use App\Models\Production;
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

        $search_query = '%' . $request->searchquery . '%';
        return Sale::where('invoice_number', 'like', $search_query)
            ->get();

    }

    public function fetchSaleInfo($id)
    {
        $sale = Sale::with('customer', 'items')->where('id', $id)->first();
        $returnedBefore = $sale->salesReturns()->pluck('id')->toArray();
        $r_items = $sale->items;
        $items = [];

        foreach ($r_items as $row) {
            $returnedQty = SalesReturnItem::whereIn('sales_return_id', $returnedBefore)->where('coi_id', $row->coi->id)->sum('quantity');
            $last_qty = $row->quantity - $returnedQty;
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
                    'discount_type' => $row->discount_type,
                    'discount_value' => $row->discount_value,
                    'discount' => $row->discount,
                    'discount_amount' => 0
                ];
            }
        }
        $data = [
            'items' => $items,
            'date' => $sale->date,
            'customer_id' => $sale->customer_id,
            'outlet_id' => $sale->outlet_id,
            'reference_no' => $sale->reference_no,
            'remark' => $sale->remark,
            'sale' => $sale
        ];
        return response()->json($data);
    }

    public function index()
    {
        if (\request()->ajax()) {
            $sales_return = SalesReturn::latest();
            return DataTables::of($sales_return)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
//                    return view('sales_return.action', compact('row'));
                })
                ->addColumn('created_at', function ($row) {
                    return view('common.created_at', compact('row'));
                })
                ->editColumn('status', function ($row) {
                    return showStatus($row->status);
                })
                ->rawColumns(['action', 'created_at', 'status'])
                ->make(true);
        }

        return view('sales_return.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $stores = [];
        if (auth()->user()->is_super) {
            $stores = Store::where(['doc_type' => 'outlet', 'type' => 'FG'])->get();
        }
        if (!auth()->user()->is_super && \auth()->user()->employee->user_of == 'outlet') {
            $stores = Store::where(['doc_type' => 'outlet', 'doc_id' => \auth()->user()->employee->outlet_id, 'type' => 'FG'])->get();
        }
        $data = [
            'groups' => ChartOfInventory::where(['type' => 'group', 'rootAccountType' => 'FG'])->get(),
            'batches' => Batch::where(['is_production' => false])->get(),
            'factories' => Factory::query()->get(),
            'stores' => $stores,

        ];
        return view('sales_return.create', $data);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreSalesReturnRequest $request)
    {
//        return $request->all();
        $validated = $request->validated();
        DB::beginTransaction();
        try {
            $sale = Sale::find($validated['sale_id']);
            $validated['uid'] = generateUniqueUUID($sale->outlet_id, SalesReturn::class, 'uid', false, false);
            $return = SalesReturn::create($validated);
            foreach ($validated['products'] as $product) {
                $obj = new \stdClass();
                $obj->date =  $return->date;
                $obj->quantity =  $product['quantity'];
                $obj->rate =  $product['rate'];
                $obj->amount =  $product['quantity'] * $product['rate'];
                $obj->store_id =  $validated['store_id'];
                $obj->coi_id = $product['coi_id'];
                $obj->id =  $return->id;
                addInventoryTransaction(1, 'SR', $obj);
                $return->items()->create($product);
            }
            DB::commit();
        } catch (\Exception $error) {
            DB::rollBack();
            return $error;
            Toastr::info('Something went wrong!.', '', ["progressBar" => true]);
            return back();
        }
        Toastr::success('Sales Return Created Successfully!.', '', ["progressBar" => true]);
        return redirect()->route('sales-returns.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(SalesReturn $salesReturn)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(SalesReturn $salesReturn)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateSalesReturnRequest $request, SalesReturn $salesReturn)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(SalesReturn $salesReturn)
    {
        //
    }
}
