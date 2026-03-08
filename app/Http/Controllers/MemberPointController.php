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
        if (\request()->ajax()) {
            $memberPoints = MemberPoint::with('memberType');
            $memberPoints = $this->filter($memberPoints);

            return DataTables::of($memberPoints)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    return view('member-point.action-button', compact('row'));
                })
                ->addColumn('amount_info', function ($row) {
                    return 'BDT ' . $row->from_amount . ' - ' . 'BDT ' . $row->to_amount;
                })
                ->addColumn('created_at', function ($row) {
                    return view('common.created_at', compact('row'));
                })
                ->rawColumns(['action', 'amount_info'])
                ->make(true);
        }
        return view('member-point.index');
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

        if (request()->filled('type')) {
            $data->whereHas('memberType', function ($query) {
                $query->where('name', 'like', '%' . request('type') . '%');
            });
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
