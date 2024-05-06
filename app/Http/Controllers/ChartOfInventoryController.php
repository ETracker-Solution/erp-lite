<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\ChartOfInventory;
use App\Models\Customer;
use App\Models\Product;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;

class ChartOfInventoryController extends Controller
{
    public function index()
    {
        $allChartOfInventories = ChartOfInventory::whereNull('parent_id')->get();
        $groups = ChartOfInventory::where('type','group')->get();
        return view('chart_of_inventory.index', compact('allChartOfInventories','groups'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'parent_id' => 'required',


        ]);
        $chartOfAccount = ChartOfInventory::find($request->input('parent_id'));
        ChartOfInventory::create([
            'name' => $request->input('name'),
            'parent_id' => $request->input('parent_id'),
            'type' => $request->input('type'),
        ]);
        Toastr::success('Chart of Account Created Successfully!.', '', ["progressBar" => true]);
        return redirect()->route('chart-of-inventories.index');
    }

}
