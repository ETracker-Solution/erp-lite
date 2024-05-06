<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Customer;
use App\Models\Product;
use Illuminate\Http\Request;

class ChartOfInventoryController extends Controller
{
    public function index()
    {
        $categories = Category::where('status', 'active')->get();
        $products = Product::where('status', 'active')->get();
        $customers = Customer::where('status', 'active')->get();
        return view('chart_of_inventory.index', compact('products', 'customers', 'categories'));
    }
}
