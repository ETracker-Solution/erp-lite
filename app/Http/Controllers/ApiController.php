<?php

namespace App\Http\Controllers;

use App\Models\ChartOfInventory;
use App\Models\Product;
use Illuminate\Http\Request;

class ApiController extends Controller
{

    public function fetchItemById($id)
    {
        return ChartOfInventory::with('unit')->findOrFail($id);
    }
    public function fetch_product_sale($id)
    {

        $product = Product::findOrFail($id);
        $data = [
            'product_name' => $product->name,
            'sale_price' => $product->selling_price,
            'buy_price' => $product->buying_price,
            'product_id' => $id,
            'stock' => \App\Classes\AvailableProductCalculation::product_id($id),
        ];
        return $data;
    }
    public function fetch_products_by_cat_id($id)
    {
        $data = array();
        $products = ChartOfInventory::where(['status' => 'active', 'parent_id' => $id])->get();
        // dd($products);


        foreach ($products as $product) {

            $product['stock'] = 0;
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
}
