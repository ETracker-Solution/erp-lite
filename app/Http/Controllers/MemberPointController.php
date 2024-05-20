<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\MemberPoint;
use App\Http\Requests\StoreMemberPointRequest;
use App\Http\Requests\UpdateMemberPointRequest;
use App\Models\MemberType;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class MemberPointController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $memberPoints = MemberPoint::with('memberType')->latest();
        if (\request()->ajax()) {
            return DataTables::of($memberPoints)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    return view('member-point.action-button', compact('row'));
                })
                ->addColumn('amount_info', function ($row) {

                    return 'BDT '.$row->from_amount .' - '. 'BDT '.$row->to_amount;

                })
                ->addColumn('created_at', function ($row) {
                    return view('common.created_at', compact('row'));
                })
                ->rawColumns(['action','amount_info'])
                ->make(true);
        }
        return view('member-point.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $memberTypes = MemberType::all();
        return view('member-point.create',compact('memberTypes'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreMemberPointRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreMemberPointRequest $request)
    {
        $validated = $request->validated();

        DB::beginTransaction();
        try {
            MemberPoint::create($validated);
            DB::commit();
        } catch (\Exception $error) {
            DB::rollBack();
            Toastr::info('Something went wrong!.', '', ["progressBar" => true]);
            return back();
        }
        Toastr::success('Member Point Created Successfully!.', '', ["progressBar" => true]);
        return redirect()->route('member-points.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\MemberPoint  $memberPoint
     * @return \Illuminate\Http\Response
     */
    public function show(MemberPoint $memberPoint)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\MemberPoint  $memberPoint
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $memberTypes = MemberType::all();
        $memberPoint = MemberPoint::findOrFail(decrypt($id));
        return view('member-point.edit',compact('memberPoint','memberTypes'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateMemberPointRequest  $request
     * @param  \App\Models\MemberPoint  $memberPoint
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateMemberPointRequest $request, $id)
    {
        $validated = $request->validated();
        DB::beginTransaction();
        try {
            MemberPoint::findOrFail(decrypt($id))->update($validated);
            DB::commit();
        } catch (\Exception $error) {
            DB::rollBack();
            Toastr::info('Something went wrong!.', '', ["progressBar" => true]);
            return back();
        }
        Toastr::success('Member Point Updated Successfully!.', '', ["progressBar" => true]);
        return redirect()->route('member-points.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\MemberPoint  $memberPoint
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            MemberPoint::findOrFail(decrypt($id))->delete();
            DB::commit();
        } catch (\Exception $error) {
            DB::rollBack();
            Toastr::info('Something went wrong!.', '', ["progressBar" => true]);
            return back();
        }
        Toastr::success('Member Point Deleted Successfully!.', '', ["progressBar" => true]);
        return redirect()->route('member-points.index');
    }
}
