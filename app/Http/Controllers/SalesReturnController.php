<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSalesReturnRequest;
use App\Http\Requests\UpdateSalesReturnRequest;
use App\Models\Batch;
use App\Models\ChartOfInventory;
use App\Models\Factory;
use App\Models\Product;
use App\Models\Production;
use App\Models\Sale;
use App\Models\SalesReturn;
use App\Models\Store;
use Illuminate\Http\Request;

class SalesReturnController extends Controller
{

    public function autocompleteSearch(Request $request)
    {

        $search_query = '%' . $request->searchquery . '%';
        return Sale::where('invoice_number', 'like', $search_query)
            ->get();

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
        //
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
