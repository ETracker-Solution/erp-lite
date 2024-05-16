<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreDesignationRequest;
use App\Http\Requests\UpdateDesignationRequest;
use App\Models\Designation;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class DesignationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $serial_count = Designation::latest()->first() ? Designation::latest()->first()->id : 0;
        $uid = $serial_count + 1;
        $designations = Designation::all();
        if (\request()->ajax()) {
            return DataTables::of($designations)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    return view('designation.action', compact('row'));
                })
                ->editColumn('type', function ($row) {
                    return strtoupper($row->type);
                })
                ->addColumn('created_at', function ($row) {
                    return view('common.created_at', compact('row'));
                })
                ->rawColumns(['action'])
                ->make(true);
        }
        return view('designation.index',compact('uid'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreDesignationRequest $request)
    {
        $validated = $request->validated();

        DB::beginTransaction();
        try {
            Designation::create($validated);
            DB::commit();
        } catch (\Exception $error) {
            DB::rollBack();
            Toastr::info('Something went wrong!.', '', ["progressBar" => true]);
            return back();
        }
        Toastr::success('Designation Created Successfully!.', '', ["progressBar" => true]);
        return redirect()->route('designations.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(Designation $designation)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $designation = Designation::findOrFail(decrypt($id));
        return view('designation.index', compact('designation'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateDesignationRequest $request, $id)
    {
        $validated = $request->validated();
        DB::beginTransaction();
        try {
            Designation::findOrFail($id)->update($validated);
            DB::commit();
        } catch (\Exception $error) {
            DB::rollBack();
            Toastr::info('Something went wrong!.', '', ["progressBar" => true]);
            return back();
        }
        Toastr::success('Department Updated Successfully!.', '', ["progressBar" => true]);
        return redirect()->route('designations.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            Designation::findOrFail(decrypt($id))->delete();
            DB::commit();
        } catch (\Exception $error) {
            DB::rollBack();
            Toastr::info('Something went wrong!.', '', ["progressBar" => true]);
            return back();
        }
        Toastr::success('Designation Deleted Successfully!.', '', ["progressBar" => true]);
        return redirect()->route('designations.index');
    }
}
