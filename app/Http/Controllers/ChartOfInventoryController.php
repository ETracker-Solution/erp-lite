<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\ChartOfInventory;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Unit;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;

class ChartOfInventoryController extends Controller
{
    public function index()
    {
        $units = Unit::all();
        $allChartOfInventories = ChartOfInventory::whereNull('parent_id')->get();
        $groups = ChartOfInventory::whereIn('type',['group','fixed'])->get();
        return view('chart_of_inventory.index', compact('allChartOfInventories','groups','units'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'parent_id' => 'required',
            'unit_id' => 'required',



        ]);
        $chartOfAccount = ChartOfInventory::find($request->input('parent_id'));
        ChartOfInventory::create([
            'name' => $request->input('name'),
            'parent_id' => $request->input('parent_id'),
            'type' => $request->input('type'),
            'unit_id' => $request->input('unit_id'),
            'price' => $request->input('price'),
            'rootAccountType' => $chartOfAccount->rootAccountType,
            'created_by' => auth()->user()->id,
        ]);
        Toastr::success('Chart of Account Created Successfully!.', '', ["progressBar" => true]);
        return redirect()->route('chart-of-inventories.index');
    }

}
