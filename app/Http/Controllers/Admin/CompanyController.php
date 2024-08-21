<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\CompanyApprovalMail;
use App\Models\Company;
use App\Models\User;
use Brian2694\Toastr\Facades\Toastr;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Str;

use function Laravel\Prompts\alert;

class CompanyController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (\request()->ajax()) {
            $companies = Company::latest();
            return DataTables::of($companies)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    return view('admin.company.action', compact('row'));
                })
                ->addColumn('created_at', function ($row) {
                    return view('common.created_at',compact('row'));
                })
                ->addColumn('status', function ($row) {
                    return showStatus($row->status);
                })
                ->rawColumns(['created_at','action','status'])
                ->make(true);
        }
        return view('admin.company.index');
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
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $company = Company::find(decrypt($id));
        return view('admin.company.show', compact('company'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    public function changeStatus($id)
    {
       

        $company = Company::find($id);
        $password = Str::random(8);

        if($company->status == 'pending'){
            \App\Models\User::create([
                'email' => $company->email,
                'password' => Hash::make($password),
            ]);
        }
        $credentials = [
            'email' => $company->email,
            'password' => $password,
        ];

        if ($company->status == 'pending') {
            $status = 'active';
        } elseif ($company->status == 'active') {
            $status = 'inactive';
        } elseif ($company->status == 'inactive') {
            $status = 'active';
        }

        $company->update(['status' => $status]);
        if($company->status == 'active' ){
            Mail::to($company->email)->send(new CompanyApprovalMail($company, $credentials));
        }
        Toastr::success('Status Changed Successfully!.', '', ["progressBar" => true]);
        return redirect()->back();

    }
}
