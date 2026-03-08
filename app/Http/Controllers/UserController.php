<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\Employee;
use App\Models\Outlet;
use App\Models\User;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Yajra\DataTables\Facades\DataTables;

use function Laravel\Prompts\error;

class UserController extends Controller
{
    public function index()
    {
        if (\request()->ajax()) {
            $users = User::query();
            $users = $this->filter($users);

            return DataTables::of($users)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    return view('user.action', compact('row'));
                })
                ->addColumn('created_at', function ($row) {
                    return view('common.created_at', compact('row'));
                })
                ->rawColumns(['action'])
                ->make(true);
        }
        return view('user.index');
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

        return $data->latest();
    }

    public function create()
    {
        $data = Permission::all()->groupBy('module_name');
        $employees = Employee::all();
        $outlets = Outlet::all();
        return view('user.create',compact('data','employees','outlets'));
    }

    public function store(StoreUserRequest $request)
    { 
        $validated = $request->validated();
        DB::beginTransaction();
        try {
            $validated['password'] = bcrypt($validated['password']);

            $employee = Employee::find($validated['employee_id']);
            $validated['name']=$employee->name;
            $validated['email']=$employee->email;

            $user = User::create($validated);
            $user->syncPermissions($request->permissions);
            DB::commit();
        } catch (\Exception $error) {
            DB::rollBack();
            return $error;
            Toastr::info('Something went wrong!.', '', ["progressBar" => true]);
            return back();
        }
        Toastr::success('User Created Successfully!.', '', ["progressBar" => true]);
        return redirect()->route('users.index');
    }
    public function show($id)
    {
        //
    }
    public function edit($id)
    {
        $user = User::findOrFail(decrypt($id));
        $data = Permission::all()->groupBy('module_name');
        $userPermissions = $user->permissions->pluck('name')->toArray();
        $employees = Employee::all();
        $outlets = Outlet::all();
        return view('user.edit', compact('user','employees','outlets','data','userPermissions'));
    }
    public function update(UpdateUserRequest $request, $id)
    {
        $validated = $request->validated();
        DB::beginTransaction();
        try {
            if (isset($validated['password'])) {
                $validated['password'] = bcrypt($validated['password']);
            }
            $user = User::findOrFail($id);
            $user->syncPermissions($request->permissions);
            $user->update($validated);
            DB::commit();
        } catch (\Exception $error) {
            DB::rollBack();
            Toastr::info('Something went wrong!.', '', ["progressBar" => true]);
            return back();
        }
        Toastr::success('User Updated Successfully!.', '', ["progressBar" => true]);
        return redirect()->route('users.index');
    }
    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            User::findOrFail($id)->delete();
            DB::commit();
        } catch (\Exception $error) {
            DB::rollBack();
            Toastr::info('Something went wrong!.', '', ["progressBar" => true]);
            return back();
        }
        Toastr::success('User Deleted Successfully!.', '', ["progressBar" => true]);
        return redirect()->route('users.index');
    }
}
