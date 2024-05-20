<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreEmployeeRequest;
use App\Http\Requests\UpdateEmployeeRequest;
use App\Models\Department;
use App\Models\Designation;
use App\Models\Employee;
use App\Models\Outlet;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class EmployeeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $employees = Employee::all();
        if (\request()->ajax()) {
            return DataTables::of($employees)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    return view('employee.action', compact('row'));
                })
                ->addColumn('created_at', function ($row) {
                    return view('common.created_at',compact('row'));
                })
                ->rawColumns(['action','created_at'])
                ->make(true);
        }
        return view('employee.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $designations = Designation::all();
        $departments = Department::all();
        $outlets = Outlet::all();
        return view('employee.create',compact('designations','departments','outlets'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreEmployeeRequest $request)
    {
        $validated = $request->validated();

        DB::beginTransaction();
        try {
            Employee::create($validated);
            DB::commit();
        } catch (\Exception $error) {
            DB::rollBack();
            Toastr::info('Something went wrong!.', '', ["progressBar" => true]);
            return back();
        }
        Toastr::success('Employee Created Successfully!.', '', ["progressBar" => true]);
        return redirect()->route('employees.index');
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $employee = Employee::where('employee_id',$id)->first();
        if(!$employee){
            return false;
        }
        $employee->update_url = route('employees.update',$employee->id);
        return $employee;
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $designations = Designation::all();
        $departments = Department::all();
        $outlets = Outlet::all();
        $employee = Employee::findOrFail($id);
        return view('employee.edit',compact('employee','designations','departments','outlets'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateEmployeeRequest $request, $id)
    {
        $validated = $request->validated();
        DB::beginTransaction();
        try {
            Employee::findOrFail($id)->update($validated);
            DB::commit();
        } catch (\Exception $error) {
            DB::rollBack();
            Toastr::info('Something went wrong!.', '', ["progressBar" => true]);
            return back();
        }
        Toastr::success('Employee Updated Successfully!.', '', ["progressBar" => true]);
        return redirect()->route('employees.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            Employee::findOrFail(decrypt($id))->delete();
            DB::commit();
        } catch (\Exception $error) {
            DB::rollBack();
            Toastr::info('Something went wrong!.', '', ["progressBar" => true]);
            return back();
        }
        Toastr::success('Employee Deleted Successfully!.', '', ["progressBar" => true]);
        return redirect()->route('employees.index');
    }
}
