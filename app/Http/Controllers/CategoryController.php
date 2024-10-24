<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;
use App\Http\Requests\StoreCategoryRequest;
use App\Http\Requests\UpdateCategoryRequest;
use App\Repository\Interfaces\CategoryInterface;
use Brian2694\Toastr\Facades\Toastr;

class CategoryController extends Controller
{
    protected $categoryRepo;
    public function __construct(CategoryInterface $category)
    {
        $this->categoryRepo = $category;
    }
    public function index()
    {
        if (\request()->ajax()) {
            $categories = Category::with('parent')->latest();
            return DataTables::of($categories)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    return view('category.action', compact('row'));
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
        return view('category.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $allCategories = Category::whereNull('parent_id')->get();
        $categories = Category::all();
        return view('category.create',compact('allCategories','categories'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreCategoryRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreCategoryRequest $request)
    {
        $validated = $request->validated();
        DB::beginTransaction();
        try {
            $slug = strtolower(str_replace(" ", "_", $validated['name']));
            $validated['slug'] = $slug ?? '';
            Category::create($validated);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            Toastr::info('Something went wrong!.', '', ["progressBar" => true]);
            return back();
        }
        Toastr::success('Category Created Successfully!.', '', ["progressBar" => true]);
        return redirect()->route('categories.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Category  $category
     * @return \Illuminate\Http\Response
     */
    public function show(Category $category)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Category  $category
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $category = Category::findOrFail(decrypt($id));
        $allCategories = Category::all();
        return view('user.category.edit', compact('category','allCategories'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateCategoryRequest  $request
     * @param  \App\Models\Category  $category
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateCategoryRequest $request, $id)
    {
        $validated = $request->validated();
        DB::beginTransaction();
        try {
            $slug = strtolower(str_replace(" ", "_", $validated['name']));
            $validated['slug'] = $slug ?? '';
            Category::findOrFail(decrypt($id))->update($validated);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            Toastr::info('Something went wrong!.', '', ["progressBar" => true]);
            return back();
        }
        Toastr::success('Category Updated Successfully!.', '', ["progressBar" => true]);
        return redirect()->route('user.categories.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Category  $category
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {

        DB::beginTransaction();
        try {
            Category::findOrFail(decrypt($id))->delete();
            DB::commit();
        } catch (\Exception $error) {
            DB::rollBack();
            Toastr::info('Something went wrong!.', '', ["progressBar" => true]);
            return back();
        }
        Toastr::success('Category Deleted Successfully!.', '', ["progressBar" => true]);
        return redirect()->route('user.categories.index');
    }
    public function trashList(){

        if (\request()->ajax()) {
            $categories = Category::onlyTrashed()->latest();
            return DataTables::of($categories)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    return view('user.category.trash-action-button',compact('row'));
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
        return view('user.category.trash-list');
    }
    public function restore($id){
        $category = Category::withTrashed()->where('id',decrypt($id))->first();
        $category->restore();
        Toastr::success('Category has been Restored Successfully!.', '', ["progressBar" => true]);
        return redirect()->route('user.categories.index');
    }
    public function permanentDelete($id){
        $category = Category::onlyTrashed()->where('id',decrypt($id))->first();
        $category->forceDelete();
        Toastr::success('Category has been Permanent Deleted Successfully!.', '', ["progressBar" => true]);
        return redirect()->route('user.categories.index');
    }
}
