<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreMembershipRequest;
use App\Http\Requests\UpdateMembershipRequest;
use App\Models\Customer;
use App\Models\MemberPoint;
use App\Models\Membership;
use App\Models\MemberType;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class MembershipController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $memberships = Membership::with('memberType', 'customer')->latest();
        if (\request()->ajax()) {
            return DataTables::of($memberships)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    return view('membership.action', compact('row'));
                })
                ->addColumn('created_at', function ($row) {
                    return view('common.created_at', compact('row'));
                })
                ->rawColumns(['action', 'amount_info'])
                ->make(true);
        }
        return view('membership.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $memberTypes = MemberType::all();
        $customers = Customer::where('type', '!=', 'default')->get();
        return view('membership.create', compact('memberTypes', 'customers'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \App\Http\Requests\StoreMembershipRequest $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreMembershipRequest $request)
    {
        $validated = $request->validated();

        DB::beginTransaction();
        try {
            Membership::create($validated);
            DB::commit();
        } catch (\Exception $error) {
            DB::rollBack();
            Toastr::info('Something went wrong!.', '', ["progressBar" => true]);
            return back();
        }
        Toastr::success('Membership Created Successfully!.', '', ["progressBar" => true]);
        return redirect()->route('memberships.index');
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Models\Membership $membership
     * @return \Illuminate\Http\Response
     */
    public function show(Membership $membership)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Models\Membership $membership
     * @return \Illuminate\Http\Response
     */
    public function edit(Membership $membership)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \App\Http\Requests\UpdateMembershipRequest $request
     * @param \App\Models\Membership $membership
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateMembershipRequest $request, Membership $membership)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Models\Membership $membership
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            Membership::findOrFail(decrypt($id))->delete();
            DB::commit();
        } catch (\Exception $error) {
            DB::rollBack();
            Toastr::info('Something went wrong!.', '', ["progressBar" => true]);
            return back();
        }
        Toastr::success('Membership Deleted Successfully!.', '', ["progressBar" => true]);
        return redirect()->route('memberships.index');
    }
}
