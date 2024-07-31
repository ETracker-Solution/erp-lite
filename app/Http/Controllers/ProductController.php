<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Product;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Models\Unit;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (\request()->ajax()) {
            $products = Product::with('category', 'brand', 'unit')->latest();
            return DataTables::of($products)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    return view('product.action', compact('row'));
                })
                ->addColumn('created_at', function ($row) {
//                    return view('common.created_at', compact('row'));
                    return '';
                })
                ->editColumn('status', function ($row) {
                    return showStatus($row->status);
                })
                ->rawColumns(['action', 'created_at', 'status'])
                ->make(true);
        }
        return view('product.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $data = [
            'brands' => Brand::all(),
            'units' => Unit::where(['status' => 'active'])->get(),
            'categories' => DB::table('categories')->get(),
        ];
        return view('product.create', $data);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreProductRequest $request)
    {
        $validated = $request->validated();
        DB::beginTransaction();
        try {
            Product::create($validated);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            Toastr::info('Something went wrong!.', '', ["progressBar" => true]);
            return back();
        }
        Toastr::success('Product Created Successfully!.', '', ["progressBar" => true]);
        return redirect()->route('products.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(Product $product)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Product $product)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateProductRequest $request, Product $product)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {
        //
    }

    public function fetch_products_by_cat_id($id)
    {
        $data = array();
        $products = Product::where(['status' => 'active', 'category_id' => $id])->get();
        // dd($products);


        foreach ($products as $product) {

            $product['stock'] = \App\Classes\AvailableProductCalculation::product_id($product->id);
            $product['quantity'] = 0;
            $product['price'] = 0;
            $product['selling_price'] = 0;
        }
        //dd($products);
        $data = [

            'products' => $products,
        ];

        return $data;
    }

    public function fetch_product($id)
    {

        return Product::findOrFail($id);
    }

    public function fetch_product_sale($id)
    {

        $product = Product::with('unit')->findOrFail($id);
        $data = [
            'product_name' => $product->name,
            'unit' => $product->unit_of_measurement->name,
            'sale_price' => $product->selling_price,
            'product_id' => $id,
            'stock' => \App\Classes\AvailableProductCalculation::product_id($id),
        ];
        return $data;
    }
}
