<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\ChartOfInventory;
use App\Models\Customer;
use App\Models\Product;
use Illuminate\Http\Request;

class ChartOfInventoryController extends Controller
{
    public function index()
    {
        $allChartOfInventories = ChartOfInventory::whereNull('parent_id')->get();
        $groups = ChartOfInventory::where('type','group')->get();
        return view('chart_of_inventory.index', compact('allChartOfInventories','groups'));
    }

}
