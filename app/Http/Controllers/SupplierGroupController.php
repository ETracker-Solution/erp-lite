<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSupplierGroupRequest;
use App\Http\Requests\UpdateSupplierGroupRequest;
use App\Models\SupplierGroup;
use Yajra\DataTables\Facades\DataTables;

class SupplierGroupController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $suppliers = SupplierGroup::all();
        if (\request()->ajax()) {
            return DataTables::of($suppliers)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    return view('supplier.action', compact('row'));
                })
                ->addColumn('created_at', function ($row) {
                    return view('common.created_at', compact('row'));
                })
                ->rawColumns(['action'])
                ->make(true);
        }
        return view('supplier_group.index');

    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $serial_count = SupplierGroup::latest()->first() ? SupplierGroup::latest()->first()->id : 0;
        $serial_no = $serial_count + 1;
        return view('supplier_group.create', compact('serial_no'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreSupplierGroupRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(SupplierGroup $supplierGroup)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(SupplierGroup $supplierGroup)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateSupplierGroupRequest $request, SupplierGroup $supplierGroup)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(SupplierGroup $supplierGroup)
    {
        //
    }
}
