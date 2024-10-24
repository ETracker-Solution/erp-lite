<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreDepartmentRequest;
use App\Http\Requests\UpdateDepartmentRequest;
use App\Models\Department;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class DepartmentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $serial_count = Department::latest()->first() ? Department::latest()->first()->id : 0;
        $uid = $serial_count + 1;
        $departments = Department::all();
        if (\request()->ajax()) {
            return DataTables::of($departments)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    return view('department.action', compact('row'));
                })
                ->editColumn('type', function ($row) {
                    return strtoupper($row->type);
                })
                ->addColumn('created_at', function ($row) {
                    return view('common.created_at', compact('row'));
                })
                ->addColumn('status', function ($row) {
                    return showStatus($row->status);
                })
                ->rawColumns(['action','status'])
                ->make(true);
        }
        return view('department.index',compact('uid'));
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
    public function store(StoreDepartmentRequest $request)
    {
        $validated = $request->validated();

        DB::beginTransaction();
        try {
            Department::create($validated);
            DB::commit();
        } catch (\Exception $error) {
            DB::rollBack();
            Toastr::info('Something went wrong!.', '', ["progressBar" => true]);
            return back();
        }
        Toastr::success('Department Created Successfully!.', '', ["progressBar" => true]);
        return redirect()->route('departments.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(Department $department)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $department = Department::findOrFail(decrypt($id));
        return view('department.index', compact('department'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateDepartmentRequest $request, $id)
    {
        $validated = $request->validated();
        DB::beginTransaction();
        try {
            Department::findOrFail($id)->update($validated);
            DB::commit();
        } catch (\Exception $error) {
            DB::rollBack();
            Toastr::info('Something went wrong!.', '', ["progressBar" => true]);
            return back();
        }
        Toastr::success('Department Updated Successfully!.', '', ["progressBar" => true]);
        return redirect()->route('departments.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            Department::findOrFail(decrypt($id))->delete();
            DB::commit();
        } catch (\Exception $error) {
            DB::rollBack();
            Toastr::info('Something went wrong!.', '', ["progressBar" => true]);
            return back();
        }
        Toastr::success('Department Deleted Successfully!.', '', ["progressBar" => true]);
        return redirect()->route('departments.index');
    }
}
