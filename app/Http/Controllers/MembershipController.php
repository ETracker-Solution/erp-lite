<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreMembershipRequest;
use App\Models\Customer;
use App\Models\Membership;
use App\Models\MemberType;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class MembershipController extends Controller
{
    public function index()
    {
        if (request()->ajax()) {
            $query = Membership::query()
                ->select([
                    'memberships.id',
                    'memberships.membership_number',
                    'memberships.member_type_id',
                    'memberships.customer_id',
                    'memberships.point',
                    'memberships.created_at',
                ])
                ->with([
                    'memberType:id,name',
                    'customer:id,name,mobile',
                ])
                ->latest('id');

            return DataTables::eloquent($query)
                ->addIndexColumn()
                ->editColumn('point', fn ($row) => number_format((float) $row->point, 2))
                ->addColumn('action', function ($row) {
                    return view('membership.action', compact('row'));
                })
                ->addColumn('created_at', function ($row) {
                    return view('common.created_at', compact('row'));
                })
                ->rawColumns(['action', 'created_at'])
                ->make(true);
        }

        return view('membership.index');
    }

    public function create()
    {
        $memberTypes = MemberType::query()->select('id', 'name')->orderBy('name')->get();
        $customers = Customer::query()
            ->select('id', 'name')
            ->where('type', '!=', 'default')
            ->orderBy('name')
            ->get();

        return view('membership.create', compact('memberTypes', 'customers'));
    }

    public function store(StoreMembershipRequest $request)
    {
        $validated = $request->validated();

        DB::beginTransaction();
        try {
            $membership = Membership::create($validated);
            if (empty($membership->membership_number)) {
                $membership->update([
                    'membership_number' => 'MEM-' . str_pad((string) $membership->id, 6, '0', STR_PAD_LEFT),
                ]);
            }
            DB::commit();
        } catch (\Exception $error) {
            DB::rollBack();
            Toastr::info('Something went wrong!.', '', ['progressBar' => true]);

            return back();
        }
        Toastr::success('Membership Created Successfully!.', '', ['progressBar' => true]);

        return redirect()->route('memberships.index');
    }

    public function show($id)
    {
        $membership = Membership::query()
            ->with([
                'memberType:id,name,discount',
                'customer:id,name,mobile,email',
            ])
            ->findOrFail(decrypt($id));

        return view('membership.show', compact('membership'));
    }

    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            Membership::findOrFail(decrypt($id))->delete();
            DB::commit();
        } catch (\Exception $error) {
            DB::rollBack();
            Toastr::info('Something went wrong!.', '', ['progressBar' => true]);

            return back();
        }
        Toastr::success('Membership Deleted Successfully!.', '', ['progressBar' => true]);

        return redirect()->route('memberships.index');
    }
}
