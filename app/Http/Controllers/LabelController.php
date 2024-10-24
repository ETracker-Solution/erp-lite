<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class LabelController extends Controller
{
    public function index()
    {
        $data = [
            'products' => Product::all(),
        ];

        return view('print_labels.create', $data);

    }

    public function autocompleteSearch(Request $request)
    {

        $searchquery = '%' . $request->searchquery . '%';
        $productstocks = Product::where('name', 'like', $searchquery)
            ->orWhere('sku', 'like', $searchquery)
            ->get();


        // $productstocks = DB::select("SELECT
        //             products.id,
        //             products.name,
        //             product_stocks.serial_number
        //         FROM `product_stocks`
        //         LEFT JOIN products ON products.id = product_stocks.product_id
        //         WHERE product_stocks.stock_status = 'in'
        //         AND ( products.name LIKE '$searchquery' OR product_stocks.serial_number LIKE '$searchquery')");

        // dd($productstocks);
        $response = array();
        foreach ($productstocks as $value) {
            $response[] = array("product_id" => $value->id, "product_name" => $value->name, "product_code" => $value->code, "product_category" => $value->category->name);
        }

        return $response;

    }

    public function fetch_product_info($id)
    {
        return Product::with('category')->Where('id', $id)->first();
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
    public function store(Request $request)
    {
        $request->validate([
            'products' => 'required|array',
        ]);
//dd($request->all());
        $data = [
            'product_name' => $request->product_name,
            'product_price' => $request->product_price,
            'page_size' => $request->page_size,
            'products' => $request->products,

        ];

        return view('print_labels.pdf', $data);

    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
