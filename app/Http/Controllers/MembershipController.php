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
        if (\request()->ajax()) {
            $memberships = Membership::with('memberType', 'customer');
            $memberships = $this->filter($memberships);

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

        $memberTypes = MemberType::all();
        return view('membership.index', compact('memberTypes'));
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

        if (request()->filled('customer_name')) {
            $data->whereHas('customer', function ($query) {
                $query->where('name', 'like', '%' . request('customer_name') . '%');
            });
        }

        if (request()->filled('phone')) {
            $data->whereHas('customer', function ($query) {
                $query->where('mobile', 'like', '%' . request('phone') . '%');
            });
        }

        if (request()->filled('member_type')) {
            $data->where('member_type_id', request('member_type'));
        }

        return $data->latest();
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
