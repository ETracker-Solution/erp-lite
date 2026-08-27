<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreMemberTypeRequest;
use App\Http\Requests\UpdateMemberTypeRequest;
use App\Models\MemberType;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class MemberTypeController extends Controller
{
    public function index()
    {
        if (request()->ajax()) {
            $query = MemberType::query()
                ->select([
                    'member_types.id',
                    'member_types.name',
                    'member_types.from_point',
                    'member_types.to_point',
                    'member_types.minimum_purchase',
                    'member_types.discount',
                    'member_types.created_at',
                ])
                ->latest('id');

            return DataTables::eloquent($query)
                ->addIndexColumn()
                ->editColumn('minimum_purchase', fn ($row) => number_format((float) $row->minimum_purchase, 2))
                ->editColumn('discount', fn ($row) => number_format((float) $row->discount, 2))
                ->addColumn('action', function ($row) {
                    return view('member-type.action-button', compact('row'));
                })
                ->addColumn('created_at', function ($row) {
                    return view('common.created_at', compact('row'));
                })
                ->rawColumns(['action', 'created_at'])
                ->make(true);
        }

        return view('member-type.index');
    }

    public function create()
    {
        return view('member-type.create');
    }

    public function store(StoreMemberTypeRequest $request)
    {
        $validated = $request->validated();

        DB::beginTransaction();
        try {
            MemberType::create($validated);
            DB::commit();
        } catch (\Exception $error) {
            DB::rollBack();
            Toastr::info('Something went wrong!.', '', ['progressBar' => true]);

            return back();
        }
        Toastr::success('Member Type Created Successfully!.', '', ['progressBar' => true]);

        return redirect()->route('member-types.index');
    }

    public function show($id)
    {
        $memberType = MemberType::findOrFail(decrypt($id));

        return view('member-type.show', compact('memberType'));
    }

    public function edit($id)
    {
        $memberType = MemberType::findOrFail(decrypt($id));

        return view('member-type.edit', compact('memberType'));
    }

    public function update(UpdateMemberTypeRequest $request, $id)
    {
        $validated = $request->validated();
        DB::beginTransaction();
        try {
            MemberType::findOrFail(decrypt($id))->update($validated);
            DB::commit();
        } catch (\Exception $error) {
            DB::rollBack();
            Toastr::info('Something went wrong!.', '', ['progressBar' => true]);

            return back();
        }
        Toastr::success('Member Type Updated Successfully!.', '', ['progressBar' => true]);

        return redirect()->route('member-types.index');
    }

    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            MemberType::findOrFail(decrypt($id))->delete();
            DB::commit();
        } catch (\Exception $error) {
            DB::rollBack();
            Toastr::info('Something went wrong!.', '', ['progressBar' => true]);

            return back();
        }
        Toastr::success('Member Type Deleted Successfully!.', '', ['progressBar' => true]);

        return redirect()->route('member-types.index');
    }
}
