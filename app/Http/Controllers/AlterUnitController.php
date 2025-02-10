<?php

namespace App\Http\Controllers;

use App\Models\AlterUnit;
use App\Http\Requests\StoreUnitRequest;
use App\Http\Requests\UpdateUnitRequest;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class AlterUnitController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $alter_units = AlterUnit::all();
        $alter_unit_count = AlterUnit::latest()->first() ? AlterUnit::latest()->first()->id : 0 ;
        $alter_unit_no = $alter_unit_count + 1 ;
        if (\request()->ajax()) {
            return DataTables::of($alter_units)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    return view('alter_unit.action', compact('row'));
                })
                ->addColumn('created_at', function ($row) {
                    return view('common.created_at', compact('row'));
                })
                ->addColumn('status', function ($row) {
                    return showStatus($row->status);
                })
                ->rawColumns(['action','status'])
                ->make(true);
        }
        return view('alter_unit.index',compact('alter_unit_no'));
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
            AlterUnit::create($validated);
            DB::commit();
        } catch (\Exception $error) {
            DB::rollBack();
            Toastr::info('Something went wrong!.', '', ["progressBar" => true]);
            return back();
        }
        Toastr::success('AlterUnit Created Successfully!.', '', ["progressBar" => true]);
        return redirect()->route('alter_units.index');
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Models\AlterUnit $alter_unit
     * @return \Illuminate\Http\Response
     */
    public function show(AlterUnit $alter_unit)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Models\AlterUnit $alter_unit
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $alter_unit = AlterUnit::findOrFail(decrypt($id));
        return view('alter_unit.index', compact('alter_unit'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \App\Http\Requests\UpdateUnitRequest $request
     * @param \App\Models\AlterUnit $alter_unit
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateUnitRequest $request, $id)
    {
        $validated = $request->validated();
        DB::beginTransaction();
        try {
            AlterUnit::findOrFail($id)->update($validated);
            DB::commit();
        } catch (\Exception $error) {
            DB::rollBack();
            Toastr::info('Something went wrong!.', '', ["progressBar" => true]);
            return back();
        }
        Toastr::success('AlterUnit Updated Successfully!.', '', ["progressBar" => true]);
        return redirect()->route('alter_units.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Models\AlterUnit $alter_unit
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            AlterUnit::findOrFail($id)->delete();
            DB::commit();
        } catch (\Exception $error) {
            DB::rollBack();
            Toastr::info('Something went wrong!.', '', ["progressBar" => true]);
            return back();
        }
        Toastr::success('AlterUnit Deleted Successfully!.', '', ["progressBar" => true]);
        return redirect()->route('alter_units.index');
    }

    public function trashList()
    {

        if (\request()->ajax()) {
            $alter_units = AlterUnit::onlyTrashed()->latest();
            return DataTables::of($alter_units)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    return view('user.alter_unit.trash-action-button', compact('row'));
                })
                ->addColumn('created_at', function ($row) {
                    return view('user.common.created_at', compact('row'));
                })
                ->rawColumns(['action', 'created_at'])
                ->make(true);
        }
        return view('alter_unit.trash-list');
    }

    public function restore($id)
    {
        $alter_unit = AlterUnit::withTrashed()->where('id', decrypt($id))->first();
        $alter_unit->restore();
        Toastr::success('AlterUnit has been Restored Successfully!.', '', ["progressBar" => true]);
        return redirect()->route('alter_units.index');
    }

    public function permanentDelete($id)
    {
        $alter_unit = AlterUnit::onlyTrashed()->where('id', decrypt($id))->first();
        $alter_unit->forceDelete();
        Toastr::success('AlterUnit has been Permanent Deleted Successfully!.', '', ["progressBar" => true]);
        return redirect()->route('alter_units.index');
    }
}
