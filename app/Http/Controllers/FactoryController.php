<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreFactoryRequest;
use App\Http\Requests\UpdateFactoryRequest;
use App\Models\Factory;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class FactoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $factories = Factory::all();
        $factory_count = Factory::latest()->first() ? Factory::latest()->first()->id : 0 ;
        $uid = $factory_count+1 ;
        if (\request()->ajax()) {
            return DataTables::of($factories)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    return view('factory.action', compact('row'));
                })
                ->addColumn('created_at', function ($row) {
                    return view('common.created_at', compact('row'));
                })
                ->rawColumns(['action'])
                ->make(true);
        }
        return view('factory.index',compact('uid'));
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
    public function store(StoreFactoryRequest $request)
    {
        $validated = $request->validated();

        DB::beginTransaction();
        try {
            Factory::create($validated);
            DB::commit();
        } catch (\Exception $error) {
            DB::rollBack();
            Toastr::info('Something went wrong!.', '', ["progressBar" => true]);
            return back();
        }
        Toastr::success('Factory Created Successfully!.', '', ["progressBar" => true]);
        return redirect()->route('factories.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(Factory $factory)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $factory = Factory::findOrFail(decrypt($id));
        return view('factory.index', compact('factory'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateFactoryRequest $request, $id)
    {
        $validated = $request->validated();
        DB::beginTransaction();
        try {
            Factory::findOrFail($id)->update($validated);
            DB::commit();
        } catch (\Exception $error) {
            DB::rollBack();
            Toastr::info('Something went wrong!.', '', ["progressBar" => true]);
            return back();
        }
        Toastr::success('Factory Updated Successfully!.', '', ["progressBar" => true]);
        return redirect()->route('factories.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            Factory::findOrFail($id)->delete();
            DB::commit();
        } catch (\Exception $error) {
            DB::rollBack();
            Toastr::info('Something went wrong!.', '', ["progressBar" => true]);
            return back();
        }
        Toastr::success('Factory Deleted Successfully!.', '', ["progressBar" => true]);
        return redirect()->route('factories.index');
    }
}
