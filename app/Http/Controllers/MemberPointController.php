<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreMemberPointRequest;
use App\Http\Requests\UpdateMemberPointRequest;
use App\Models\MemberPoint;
use App\Models\MemberType;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class MemberPointController extends Controller
{
    public function index()
    {
        if (request()->ajax()) {
            $query = MemberPoint::query()
                ->select([
                    'member_points.id',
                    'member_points.from_amount',
                    'member_points.to_amount',
                    'member_points.per_amount',
                    'member_points.point',
                    'member_points.member_type_id',
                    'member_points.created_at',
                ])
                ->with(['memberType:id,name'])
                ->latest('id');

            return DataTables::eloquent($query)
                ->addIndexColumn()
                ->editColumn('per_amount', fn ($row) => number_format((float) $row->per_amount, 2))
                ->editColumn('point', fn ($row) => number_format((float) $row->point, 2))
                ->addColumn('action', function ($row) {
                    return view('member-point.action-button', compact('row'));
                })
                ->addColumn('created_at', function ($row) {
                    return view('common.created_at', compact('row'));
                })
                ->rawColumns(['action', 'created_at'])
                ->make(true);
        }

        return view('member-point.index');
    }

    public function create()
    {
        $memberTypes = MemberType::query()->select('id', 'name')->orderBy('name')->get();

        return view('member-point.create', compact('memberTypes'));
    }

    public function store(StoreMemberPointRequest $request)
    {
        $validated = $request->validated();

        DB::beginTransaction();
        try {
            MemberPoint::create($validated);
            DB::commit();
        } catch (\Exception $error) {
            DB::rollBack();
            Toastr::info('Something went wrong!.', '', ['progressBar' => true]);

            return back();
        }
        Toastr::success('Member Point Created Successfully!.', '', ['progressBar' => true]);

        return redirect()->route('member-points.index');
    }

    public function show($id)
    {
        $memberPoint = MemberPoint::query()
            ->with(['memberType:id,name'])
            ->findOrFail(decrypt($id));

        return view('member-point.show', compact('memberPoint'));
    }

    public function edit($id)
    {
        $memberTypes = MemberType::query()->select('id', 'name')->orderBy('name')->get();
        $memberPoint = MemberPoint::findOrFail(decrypt($id));

        return view('member-point.edit', compact('memberPoint', 'memberTypes'));
    }

    public function update(UpdateMemberPointRequest $request, $id)
    {
        $validated = $request->validated();
        DB::beginTransaction();
        try {
            MemberPoint::findOrFail(decrypt($id))->update($validated);
            DB::commit();
        } catch (\Exception $error) {
            DB::rollBack();
            Toastr::info('Something went wrong!.', '', ['progressBar' => true]);

            return back();
        }
        Toastr::success('Member Point Updated Successfully!.', '', ['progressBar' => true]);

        return redirect()->route('member-points.index');
    }

    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            MemberPoint::findOrFail(decrypt($id))->delete();
            DB::commit();
        } catch (\Exception $error) {
            DB::rollBack();
            Toastr::info('Something went wrong!.', '', ['progressBar' => true]);

            return back();
        }
        Toastr::success('Member Point Deleted Successfully!.', '', ['progressBar' => true]);

        return redirect()->route('member-points.index');
    }
}
