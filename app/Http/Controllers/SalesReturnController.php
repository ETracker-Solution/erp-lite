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
use App\Models\Store;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
        $sale = Sale::with('customer', 'items')->Where('id', $id)->first();
        $r_items = $sale->items;
        $items = [];

        foreach ($r_items as $row) {
            if ($row->quantity > 0) {
                $items[] = [
                    'sale_id' => $id,
                    'coi_id' => $row->coi->id,
                    'unit' => $row->coi->unit->name ?? '',
                    'name' => $row->coi->name ?? '',
                    'group' => $row->coi->parent->name ?? '',
                    'sale_quantity' => $row->quantity,
                    'rate' => $row->unit_price,
                    'quantity' => $row->quantity,
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
        ];
        return response()->json($data);
    }

    public function index()
    {
        $data = [
            'groups' => ChartOfInventory::where(['type' => 'group', 'rootAccountType' => 'FG'])->get(),
            'batches' => Batch::where(['is_production' => false])->get(),
            'factories' => Factory::query()->get(),
            'stores' => Store::where(['type' => 'FG', 'doc_type' => 'factory'])->get(),

        ];
        return view('sales_return.create', $data);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreSalesReturnRequest $request)
    {
        $validated = $request->validated();
//        DB::beginTransaction();
//        try {

            $sale = SalesReturn::create($validated);
            foreach ($validated['products'] as $product) {
                $sale->items()->create($product);
            }
//            DB::commit();
//        } catch (\Exception $error) {
//            DB::rollBack();
//            Toastr::info('Something went wrong!.', '', ["progressBar" => true]);
//            return back();
//        }
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
