<?php

namespace App\Http\Controllers;

use App\Models\Unit;
use App\Http\Requests\StoreUnitRequest;
use App\Http\Requests\UpdateUnitRequest;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class UnitController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $units = Unit::all();
        $unit_count = Unit::latest()->first() ? Unit::latest()->first()->id : 0 ;
        $unit_no = $unit_count+1 ;
        if (\request()->ajax()) {
            return DataTables::of($units)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    return view('unit.action', compact('row'));
                })
                ->addColumn('created_at', function ($row) {
                    return view('common.created_at', compact('row'));
                })
                ->rawColumns(['action'])
                ->make(true);
        }
        return view('unit.index',compact('unit_no'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \App\Http\Requests\StoreUnitRequest $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreUnitRequest $request)
    {
        $validated = $request->validated();

        DB::beginTransaction();
        try {
            Unit::create($validated);
            DB::commit();
        } catch (\Exception $error) {
            DB::rollBack();
            Toastr::info('Something went wrong!.', '', ["progressBar" => true]);
            return back();
        }
        Toastr::success('Unit Created Successfully!.', '', ["progressBar" => true]);
        return redirect()->route('units.index');
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Models\Unit $unit
     * @return \Illuminate\Http\Response
     */
    public function show(Unit $unit)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Models\Unit $unit
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $unit = Unit::findOrFail(decrypt($id));
        return view('unit.index', compact('unit'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \App\Http\Requests\UpdateUnitRequest $request
     * @param \App\Models\Unit $unit
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateUnitRequest $request, $id)
    {
        $validated = $request->validated();
        DB::beginTransaction();
        try {
            Unit::findOrFail($id)->update($validated);
            DB::commit();
        } catch (\Exception $error) {
            DB::rollBack();
            Toastr::info('Something went wrong!.', '', ["progressBar" => true]);
            return back();
        }
        Toastr::success('Unit Updated Successfully!.', '', ["progressBar" => true]);
        return redirect()->route('units.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Models\Unit $unit
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            Unit::findOrFail($id)->delete();
            DB::commit();
        } catch (\Exception $error) {
            DB::rollBack();
            Toastr::info('Something went wrong!.', '', ["progressBar" => true]);
            return back();
        }
        Toastr::success('Unit Deleted Successfully!.', '', ["progressBar" => true]);
        return redirect()->route('units.index');
    }

    public function trashList()
    {

        if (\request()->ajax()) {
            $units = Unit::onlyTrashed()->latest();
            return DataTables::of($units)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    return view('user.unit.trash-action-button', compact('row'));
                })
                ->addColumn('created_at', function ($row) {
                    return view('user.common.created_at', compact('row'));
                })
                ->rawColumns(['action', 'created_at'])
                ->make(true);
        }
        return view('unit.trash-list');
    }

    public function restore($id)
    {
        $unit = Unit::withTrashed()->where('id', decrypt($id))->first();
        $unit->restore();
        Toastr::success('Unit has been Restored Successfully!.', '', ["progressBar" => true]);
        return redirect()->route('units.index');
    }

    public function permanentDelete($id)
    {
        $unit = Unit::onlyTrashed()->where('id', decrypt($id))->first();
        $unit->forceDelete();
        Toastr::success('Unit has been Permanent Deleted Successfully!.', '', ["progressBar" => true]);
        return redirect()->route('units.index');
    }
}
