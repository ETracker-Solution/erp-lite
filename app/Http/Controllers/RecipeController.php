<?php

namespace App\Http\Controllers;

use App\Models\ChartOfInventory;
use App\Models\Recipe;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class RecipeController extends Controller
{
    public function index()
    {
        if (\request()->ajax()) {
            $recipes = Recipe::with('coi', 'item')->groupBy('uid')->latest();
            return DataTables::of($recipes)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    return view('recipe.action', compact('row'));
                })
                ->addColumn('created_at', function ($row) {
                    return view('common.created_at', compact('row'));
                })
                ->editColumn('status', function ($row) {
                    return showStatus($row->status);
                })
                ->rawColumns(['action', 'created_at', 'status'])
                ->make(true);
        }
        return view('recipe.index');
    }

    public function create()
    {
        $serial_no = null;
        if (auth()->user() && auth()->user()->employee && auth()->user()->employee->user_of == 'ho') {
            $serial_no = generateUniqueUUID(null, Recipe::class, 'uid', false, true);
        }
        $data = [
            'rm_groups' => ChartOfInventory::where(['type' => 'group', 'rootAccountType' => 'RM'])->get(),
            'fg_groups' => ChartOfInventory::where(['type' => 'group', 'rootAccountType' => 'FG'])->get(),
            'uid' => $serial_no,

        ];

        return view('recipe.create', $data);
    }

    public function store(Request $request)
    {
        // dd($request->all());
        $validated = $request->validate([
            'fg_item_id' => 'required|numeric',
            'products' => 'array',

        ]);

        DB::beginTransaction();
        try {
            if (count($validated['products']) < 1) {
                Toastr::info('At Least One Product Required.', '', ["progressBar" => true]);
                return back();
            }
            $uid = generateUniqueUUID(null, Recipe::class, 'uid', false, true);
            foreach ($validated['products'] as $product) {

                $exists = Recipe::query()
                    ->where('fg_id', $validated['fg_item_id'])
                    ->exists();

                if ($exists) {
                    Toastr::info('Item Already Exists.', '', ["progressBar" => true]);
                    return back();
                }

                Recipe::query()->create([
                    'uid' => $uid,
                    'fg_id' => $validated['fg_item_id'],
                    'qty' => $product['quantity'],
                    'rm_id' => $product['coi_id'],
                    'created_by' => auth()->user()->id,
                ]);
            }
            DB::commit();
        } catch (\Exception $exception) {
            DB::rollBack();
            Toastr::info('Something went wrong!.', '', ["progressBar" => true]);
            return back();
        }
        Toastr::success('Purchase Created Successfully!.', '', ["progressBar" => true]);
        return redirect()->route('recipes.index');
    }

    public function show($uid)
    {
        $data = [
            'model' => Recipe::where('uid', decrypt($uid))->get(),

        ];
        return view('recipe.show', $data);
    }

    public function edit($uid)
    {
        $data = [
            'rm_groups' => ChartOfInventory::where(['type' => 'group', 'rootAccountType' => 'RM'])->get(),
            'fg_groups' => ChartOfInventory::where(['type' => 'group', 'rootAccountType' => 'FG'])->get(),
            'recipes' => Recipe::where('uid', decrypt($uid))->get(),

        ];
        return view('recipe.edit', $data);
    }

    public function update(Request $request, $uid)
    {
        try {
            $validated = $request->validate([
                'recipes.*.qty' => 'required|numeric|min:1',
                'recipes.*.status' => 'required|string|in:active,inactive',
            ]);

            foreach ($request->input('recipes') as $key => $updatedItem) {
                $item = Recipe::find($key);
                $item->qty = $updatedItem['qty'];
                $item->status = $updatedItem['status'];
                $item->save();
            }

            toastr()->success('Recipe updated successfully');
        } catch (\Exception $e) {
            toastr()->error('Failed to update the recipe. Please try again.');
        }
        return redirect()->route('recipes.index');
    }
}
