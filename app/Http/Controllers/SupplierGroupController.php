<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSupplierGroupRequest;
use App\Http\Requests\UpdateSupplierGroupRequest;
use App\Models\SupplierGroup;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\DB;
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
                    return view('supplier_group.action', compact('row'));
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
        $validated = $request->validated();

        DB::beginTransaction();
        try {
            SupplierGroup::create($validated);
            DB::commit();
        } catch (\Exception $error) {
            DB::rollBack();
            Toastr::info('Something went wrong!.', '', ["progressBar" => true]);
            return back();
        }
        Toastr::success('Supplier Group Created Successfully!.', '', ["progressBar" => true]);
        return redirect()->route('supplier-groups.index');
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
    public function edit($id)
    {
        $supplierGroup = SupplierGroup::findOrFail(decrypt($id));
        return view('supplier_group.create',compact('supplierGroup'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateSupplierGroupRequest $request, $id)
    {
        $validated = $request->validated();
        DB::beginTransaction();
        try {
            SupplierGroup::findOrFail($id)->update($validated);
            DB::commit();
        } catch (\Exception $error) {
            DB::rollBack();
            Toastr::info('Something went wrong!.', '', ["progressBar" => true]);
            return back();
        }
        Toastr::success('Supplier Group Updated Successfully!.', '', ["progressBar" => true]);
        return redirect()->route('supplier-groups.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            SupplierGroup::findOrFail(decrypt($id))->delete();
            DB::commit();
        } catch (\Exception $error) {
            DB::rollBack();
            Toastr::info('Something went wrong!.', '', ["progressBar" => true]);
            return back();
        }
        Toastr::success('Supplier Group Deleted Successfully!.', '', ["progressBar" => true]);
        return redirect()->route('supplier-groups.index');
    }
}
