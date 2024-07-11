<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreStoreRequest;
use App\Http\Requests\UpdateStoreRequest;
use App\Models\Factory;
use App\Models\Outlet;
use App\Models\Store;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class StoreController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $outlets = Outlet::all();
        $factories = Factory::all();
        $serial_count = Store::first() ? Store::max('id') : 0;
        $serial_no = $serial_count + 1;
        $stores = Store::all();
        if (\request()->ajax()) {
            return DataTables::of($stores)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    return view('store.action', compact('row'));
                })
                ->editColumn('type', function ($row) {
                    return strtoupper($row->type);
                })
                ->addColumn('created_at', function ($row) {
                    return view('common.created_at', compact('row'));
                })
                ->rawColumns(['action'])
                ->make(true);
        }
        return view('store.index',compact('serial_no','outlets','factories'));
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
    public function store(StoreStoreRequest $request)
    {
        $validated = $request->validated();

        DB::beginTransaction();
        try {
            $validated['name'] = 'Store '. $request->type.' ' . $request->name;
            
            Store::create($validated);
            DB::commit();
        } catch (\Exception $error) {
            DB::rollBack();
            Toastr::info('Something went wrong!.', '', ["progressBar" => true]);
            return back();
        }
        Toastr::success('Store Created Successfully!.', '', ["progressBar" => true]);
        return redirect()->route('stores.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(Store $store)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $outlets = Outlet::all();
        $factories = Factory::all();
        $store = Store::findOrFail(decrypt($id));
        return view('store.index', compact('store','outlets','factories'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateStoreRequest $request, $id)
    {
        $validated = $request->validated();
        DB::beginTransaction();
        try {
            Store::findOrFail($id)->update($validated);
            DB::commit();
        } catch (\Exception $error) {
            DB::rollBack();
            Toastr::info('Something went wrong!.', '', ["progressBar" => true]);
            return back();
        }
        Toastr::success('Store Updated Successfully!.', '', ["progressBar" => true]);
        return redirect()->route('stores.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            Store::findOrFail(decrypt($id))->delete();
            DB::commit();
        } catch (\Exception $error) {
            DB::rollBack();
            Toastr::info('Something went wrong!.', '', ["progressBar" => true]);
            return back();
        }
        Toastr::success('Store Deleted Successfully!.', '', ["progressBar" => true]);
        return redirect()->route('stores.index');
    }
}
