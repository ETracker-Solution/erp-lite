<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreEmployeeRequest;
use App\Http\Requests\UpdateEmployeeRequest;
use App\Models\Department;
use App\Models\Designation;
use App\Models\Employee;
use App\Models\Factory;
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
        if (\request()->ajax()) {
            $employees = Employee::query();
            $employees = $this->filter($employees);

            return DataTables::of($employees)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    return view('employee.action', compact('row'));
                })
                ->addColumn('created_at', function ($row) {
                    return view('common.created_at', compact('row'));
                })
                ->rawColumns(['action', 'created_at'])
                ->make(true);
        }
        return view('employee.index');
    }

    protected function filter($data)
    {
        if (request('date_range')) {
            $dateRange = [];
            if (str_contains(request('date_range'), ' to ')) {
                $dateRange = explode(' to ', request('date_range'));
            } elseif (str_contains(request('date_range'), ' - ')) {
                $dateRange = explode(' - ', request('date_range'));
            } else {
                $dateRange = [request('date_range'), request('date_range')];
            }

            if (isset($dateRange[0]) && isset($dateRange[1])) {
                $data->whereBetween('created_at', [$dateRange[0] . ' 00:00:00', $dateRange[1] . ' 23:59:59']);
            } elseif (isset($dateRange[0])) {
                $data->whereDate('created_at', $dateRange[0]);
            }
        }

        if (request()->filled('name')) {
            $data->where('name', 'like', '%' . request('name') . '%');
        }

        if (request()->filled('email')) {
            $data->where('email', 'like', '%' . request('email') . '%');
        }

        if (request()->filled('phone')) {
            $data->where('phone', 'like', '%' . request('phone') . '%');
        }

        return $data->latest();
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $designations = Designation::where('status','active')->get();
        $departments = Department::where('status','active')->get();
        $outlets = Outlet::where('status','active')->get();
        $factories = Factory::where('status','active')->get();
        return view('employee.create',compact('designations','departments','outlets','factories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreEmployeeRequest $request)
    {
        // dd($request->all());
        $validated = $request->validated();

        DB::beginTransaction();
        try {
            $filename = '';
            if ($request->hasfile('image')) {
                $file = $request->file('image');
                $filename = date('Ymdmhs') . '.' . $file->getClientOriginalExtension();
                $file->move(public_path('/upload'), $filename);
            }
            $validated['image'] = $filename ?? null;
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
        $factories = Factory::all();
        $employee = Employee::findOrFail(decrypt($id));
        return view('employee.edit',compact('employee','designations','departments','factories','outlets'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateEmployeeRequest $request, $id)
    {
        // dd($request->all());
        $validated = $request->validated();
        DB::beginTransaction();
        try {
            $filename = '';
            if ($request->hasfile('image')) {
                $file = $request->file('image');
                $filename = date('Ymdmhs') . '.' . $file->getClientOriginalExtension();
                $file->move(public_path('/upload'), $filename);
            }
            if (isset($validated['image'])) {
                $validated['image'] = $filename ?? null;
            }
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
