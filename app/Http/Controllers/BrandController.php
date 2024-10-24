<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\StoreBrandRequest;
use Yajra\DataTables\Facades\DataTables;
use App\Http\Requests\UpdateBrandRequest;
use App\Repository\Interfaces\BrandInterface;
use Brian2694\Toastr\Facades\Toastr;

class BrandController extends Controller
{
    protected $brandRepo;
    public function __construct(BrandInterface $brand)
    {
        $this->brandRepo = $brand;
    }

    public function index()
    {
        $brands = Brand::all();
        if (\request()->ajax()) {
            return DataTables::of($brands)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    return view('brand.action', compact('row'));
                })
                ->addColumn('created_at', function ($row) {
                    return view('common.created_at',compact('row'));
                })
                ->editColumn('status', function ($row) {
                    return showStatus($row->status);
                })
                ->rawColumns(['action','created_at','status'])
                ->make(true);
        }
        return view('brand.index');
    }
    public function create()
    {

        return view('brand.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreBrandRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreBrandRequest $request)
    {
        $validated = $request->validated();
        DB::beginTransaction();
        try {
            Brand::create($validated);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            Toastr::info('Something went wrong!.', '', ["progressBar" => true]);
            return back();
        }
        Toastr::success('Brand Created Successfully!.', '', ["progressBar" => true]);
        return redirect()->route('brands.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Brand  $brand
     * @return \Illuminate\Http\Response
     */
    public function show(Brand $brand)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Brand  $brand
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $brand = Brand::findOrFail(decrypt($id));
        return view('user.brand.edit', compact('brand'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateBrandRequest  $request
     * @param  \App\Models\Brand  $brand
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateBrandRequest $request, $id)
    {
        $validated = $request->validated();
        DB::beginTransaction();
        try {
            Brand::findOrFail(decrypt($id))->update($validated);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            Toastr::info('Something went wrong!.', '', ["progressBar" => true]);
            return back();
        }
        Toastr::success('Brand Updated Successfully!.', '', ["progressBar" => true]);
        return redirect()->route('user.brands.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Brand  $brand
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            Brand::findOrFail(decrypt($id))->delete();
            DB::commit();
        } catch (\Exception $error) {
            DB::rollBack();
            Toastr::info('Something went wrong!.', '', ["progressBar" => true]);
            return back();
        }
        Toastr::success('Brand Deleted Successfully!.', '', ["progressBar" => true]);
        return redirect()->route('user.brands.index');
    }
    public function trashList(){

        if (\request()->ajax()) {
            $brands = Brand::onlyTrashed()->latest();
            return DataTables::of($brands)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    return view('user.brand.trash-action-button',compact('row'));
                })
                // ->editColumn('status', function ($row) {
                //     return showStatus($row->status);
                // })
                ->addColumn('created_at', function ($row) {
                    return view('user.common.created_at', compact('row'));
                })
                ->rawColumns(['action','created_at'])
                ->make(true);
        }
        return view('user.brand.trash-list');
    }
    public function restore($id){
        $brand = Brand::withTrashed()->where('id',decrypt($id))->first();
        $brand->restore();
        Toastr::success('Brand has been Restored Successfully!.', '', ["progressBar" => true]);
        return redirect()->route('user.brands.index');
    }
    public function permanentDelete($id){
        $brand = Brand::onlyTrashed()->where('id',decrypt($id))->first();
        $brand->forceDelete();
        Toastr::success('Brand has been Permanent Deleted Successfully!.', '', ["progressBar" => true]);
        return redirect()->route('user.brands.index');
    }
}
