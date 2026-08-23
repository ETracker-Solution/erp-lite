<?php

namespace App\Http\Controllers;

use App\Models\ChartOfInventory;
use App\Models\Unit;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;

class ChartOfInventoryController extends Controller
{
    /**
     * Tree data is loaded via AJAX (/inventory-items) — only units needed for the form.
     */
    public function index()
    {
        $units = Unit::query()
            ->select(['id', 'name'])
            ->where('status', 'active')
            ->orderBy('name')
            ->get();

        return view('chart_of_inventory.index', compact('units'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'parent_id' => 'required',
            'unit_id' => 'required',
        ]);

        $parent = ChartOfInventory::query()
            ->select(['id', 'rootAccountType'])
            ->findOrFail($request->input('parent_id'));

        ChartOfInventory::create([
            'name' => $request->input('name'),
            'parent_id' => $request->input('parent_id'),
            'type' => $request->input('type'),
            'unit_id' => $request->input('unit_id'),
            'price' => $request->input('price'),
            'rootAccountType' => $parent->rootAccountType,
            'created_by' => auth()->id(),
        ]);

        Toastr::success('Chart of Inventory Created Successfully!.', '', ["progressBar" => true]);
        return redirect()->route('chart-of-inventories.index');
    }
}
