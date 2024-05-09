<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\User;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Yajra\DataTables\Facades\DataTables;

class UserController extends Controller
{
    public function index()
    {
        $users = User::latest();
        if (\request()->ajax()) {
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

    public function create()
    {
        return view('user.create');
    }
    
    public function store(StoreUserRequest $request)
    {
        $validated = $request->validated();

        DB::beginTransaction();
        try {

            $validated['password'] = bcrypt($validated['password']);

            User::create($validated);
            DB::commit();
        } catch (\Exception $error) {
            DB::rollBack();
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
        return view('user.edit', compact('user'));
    }
    public function update(UpdateUserRequest $request, $id)
    {
        $validated = $request->validated();
        DB::beginTransaction();
        try {
            if (isset($validated['password'])) {
                $validated['password'] = bcrypt($validated['password']);
            }
            User::findOrFail($id)->update($validated);
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
