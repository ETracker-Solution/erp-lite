<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use App\Models\SupplierGroup;
use Yajra\DataTables\Facades\DataTables;
use App\Http\Requests\StoreSupplierRequest;
use App\Http\Requests\UpdateSupplierRequest;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\DB;

class SupplierController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $suppliers = Supplier::with('group')->latest();
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
        return view('supplier.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $supplier_groups = SupplierGroup::where('status','active')->get();
        $serial_count = Supplier::latest()->first() ? Supplier::latest()->first()->id : 0;
        $serial_no = $serial_count + 1;
        return view('supplier.create', compact('serial_no','supplier_groups'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreSupplierRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreSupplierRequest $request)
    {
        $validated = $request->validated();

        DB::beginTransaction();
        try {
            Supplier::create($validated);
            DB::commit();
        } catch (\Exception $error) {
            DB::rollBack();
            Toastr::info('Something went wrong!.', '', ["progressBar" => true]);
            return back();
        }
        Toastr::success('Supplier Created Successfully!.', '', ["progressBar" => true]);
        return redirect()->route('suppliers.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Supplier  $supplier
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $supplier = Supplier::findOrFail(decrypt($id));
        return view('supplier.show',compact('supplier'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Supplier  $supplier
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $supplier_groups = SupplierGroup::all();
        $supplier = Supplier::findOrFail(decrypt($id));
        return view('supplier.edit',compact('supplier','supplier_groups'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateSupplierRequest  $request
     * @param  \App\Models\Supplier  $supplier
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateSupplierRequest $request, $id)
    {
        $validated = $request->validated();
        DB::beginTransaction();
        try {
            Supplier::findOrFail($id)->update($validated);
            DB::commit();
        } catch (\Exception $error) {
            DB::rollBack();
            Toastr::info('Something went wrong!.', '', ["progressBar" => true]);
            return back();
        }
        Toastr::success('Supplier Updated Successfully!.', '', ["progressBar" => true]);
        return redirect()->route('suppliers.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Supplier  $supplier
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            Supplier::findOrFail(decrypt($id))->delete();
            DB::commit();
        } catch (\Exception $error) {
            DB::rollBack();
            Toastr::info('Something went wrong!.', '', ["progressBar" => true]);
            return back();
        }
        Toastr::success('Supplier Deleted Successfully!.', '', ["progressBar" => true]);
        return redirect()->route('suppliers.index');
    }
    public function trashList(){

        if (\request()->ajax()) {
            $suppliers = Supplier::onlyTrashed()->latest();
            return DataTables::of($suppliers)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    return view('user.supplier.trash-action-button',compact('row'));
                })
                // ->editColumn('status', function ($row) {
                //     return showStatus($row->status);
                // })
                ->addColumn('created_at', function ($row) {
                    return view('user.common.created_at',compact('row'));
                })
                ->rawColumns(['action','created_at'])
                ->make(true);
        }
        return view('user.supplier.trash-list');
    }
    public function restore($id){
        $supplier = Supplier::withTrashed()->where('id',decrypt($id))->first();
        $supplier->restore();
        Toastr::success('Supplier has been Restored Successfully!.', '', ["progressBar" => true]);
        return redirect()->route('user.suppliers.index');
    }
    public function permanentDelete($id){
        $supplier = Supplier::onlyTrashed()->where('id',decrypt($id))->first();
        $supplier->forceDelete();
        Toastr::success('Supplier has been Permanent Deleted Successfully!.', '', ["progressBar" => true]);
        return redirect()->route('user.suppliers.index');
    }
}
