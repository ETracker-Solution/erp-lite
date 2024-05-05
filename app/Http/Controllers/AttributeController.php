<?php

namespace App\Http\Controllers;

use App\Models\Attribute;
use App\Http\Requests\StoreAttributeRequest;
use App\Http\Requests\UpdateAttributeRequest;
use App\Models\AttributeOption;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class AttributeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // return $attribute = Attribute::with('user', 'options')->get();
        if (\request()->ajax()) {
            $attributes = Attribute::with('user', 'options')->latest();
            return DataTables::of($attributes)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    return view('attribute.action', compact('row'));
                })
                ->addColumn('created_at', function ($row) {
                    return view('common.created_at', compact('row'));
                })
                ->editColumn('options', function ($row) {
                    return implode(', ', $row->options()->pluck('value')->toArray());
                    // return $options;
                })
                ->rawColumns(['action', 'created_at', 'options'])
                ->make(true);
        }
        return view('attribute.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('attribute.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreAttributeRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreAttributeRequest $request)
    {
        $validated = $request->validated();
        DB::beginTransaction();
        try {
            $attribute = Attribute::create($validated);
            if (count($validated['values']) > 0) {
                $valuesData = [
                    'attribute_id' => $attribute->id,
                    'values' => $validated['values']
                ];
                $attribute_id = $valuesData['attribute_id'] ?? null;
                foreach ($valuesData['values'] as $item) {
                    AttributeOption::insert([
                        'value' => $item,
                        'attribute_id' => $attribute_id,
                    ]);
                }
            }
            DB::commit();
        } catch (\Exception $error) {
            DB::rollBack();
            Toastr::info('Something went wrong!.', '', ["progressBar" => true]);
            return back();
        }
        Toastr::success('Attribute Created Successfully!.', '', ["progressBar" => true]);
        return redirect()->route('attributes.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Attribute  $attribute
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $attributes = Attribute::findOrFail(decrypt($id));
        return view('user.attribute.show', compact('attributes'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Attribute  $attribute
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $attribute = Attribute::findOrFail(decrypt($id));
        $attributeOptions = $attribute->options()->pluck('value');
        return view('user.attribute.edit', compact('attribute', 'attributeOptions'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateAttributeRequest  $request
     * @param  \App\Models\Attribute  $attribute
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateAttributeRequest $request, Attribute $attribute)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Attribute  $attribute
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            Attribute::findOrFail(decrypt($id))->delete();
            DB::commit();
        } catch (\Exception $error) {
            DB::rollBack();
            Toastr::info('Something went wrong!.', '', ["progressBar" => true]);
            return back();
        }
        Toastr::success('Attribute Deleted Successfully!.', '', ["progressBar" => true]);
        return redirect()->route('user.attributes.index');
    }

    public function trashList()
    {
        if (\request()->ajax()) {
            $attribute = Attribute::onlyTrashed()->with('user')->latest();
            return DataTables::of($attribute)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    return view('user.attribute.trash-action-button', compact('row'));
                })
                ->addColumn('created_at', function ($row) {
                    return view('user.common.created_at', compact('row'));
                })
                ->rawColumns(['action', 'created_at'])
                ->make(true);
        }
        return view('user.attribute.trash-list');
    }

    public function restore($id){
        $attributes = Attribute::withTrashed()->where('id',decrypt($id))->first();
        $attributes->restore();
        Toastr::success('Attribute has been Restored Successfully!.', '', ["progressBar" => true]);
        return redirect()->route('user.attributes.index');
    }

    public function permanentDelete($id){
        $attributes = Attribute::onlyTrashed()->where('id',decrypt($id))->first();
        $attributes->forceDelete();
        Toastr::success('Attribute has been Permanent Deleted Successfully!.', '', ["progressBar" => true]);
        return redirect()->route('user.attributes.index');
    }
}
