<?php

namespace App\Http\Controllers\Api\Web;

use App\Http\Controllers\Controller;
use App\Models\ChartOfInventory;

class InventoryController extends Controller
{
    public function inventoryItems()
    {
        $flat = ChartOfInventory::query()
            ->select(['id', 'name', 'type', 'parent_id', 'rootAccountType', 'status', 'price', 'unit_id'])
            ->orderByRaw("FIELD(type, 'fixed', 'group', 'item')")
            ->orderBy('name')
            ->get();

        $allChartOfInventories = nestByParentId($flat, 'subChartOfInventories');

        return view('chart_of_inventory.items', compact('allChartOfInventories'));
    }

    public function inventoryDetails($id)
    {
        $inventory = ChartOfInventory::query()
            ->select(['id', 'name', 'type', 'parent_id', 'rootAccountType', 'unit_id', 'price', 'status', 'non_discountable'])
            ->with('parent:id,name')
            ->findOrFail($id);

        return response()->json([
            'item_id' => $inventory->id,
            'item_name' => $inventory->name,
            'item_type' => $inventory->type,
            'group_name' => $inventory->parent?->name ?? '',
            'account_type' => $inventory->rootAccountType,
            'unit_id' => $inventory->unit_id,
            'price' => $inventory->price,
            'status' => $inventory->status,
            'non_discountable' => $inventory->non_discountable,
        ]);
    }

    public function inventoryUpdate($id)
    {
        try {
            $inventory = ChartOfInventory::query()->findOrFail($id);
            if (request()->filled('item_name')) {
                $inventory->name = request()->item_name;
                $inventory->non_discountable = request()->non_discountable;
            }
            if ($inventory->type == 'item') {
                $inventory->unit_id = request()->unit;
                $inventory->status = request()->status;
                $inventory->price = request()->price;
            }
            $inventory->updated_by = auth()->id();
            $inventory->save();
        } catch (\Exception $exception) {
            return response()->json([
                'message' => $exception->getMessage(),
                'success' => false,
            ]);
        }

        return response()->json([
            'message' => 'Updated',
            'success' => true,
        ]);
    }

    public function inventoryStore($id)
    {
        try {
            $inventory = ChartOfInventory::query()->findOrFail($id);
            $inventory->subChartOfInventories()->create([
                'name' => request()->item_name,
                'type' => request()->item_type,
                'rootAccountType' => $inventory->rootAccountType,
                'unit_id' => request()->unit ?? null,
                'status' => request()->status ?? 'active',
                'price' => request()->price ?? 0,
                'created_by' => auth()->id(),
                'non_discountable' => request()->non_discountable ? 1 : 0,
            ]);
        } catch (\Exception $exception) {
            return response()->json([
                'message' => $exception->getMessage(),
                'success' => false,
            ]);
        }

        return response()->json([
            'message' => 'Added',
            'success' => true,
        ]);
    }

    public function inventoryDelete($id)
    {
        try {
            ChartOfInventory::query()->findOrFail($id)->delete();
        } catch (\Exception $exception) {
            return response()->json([
                'message' => $exception->getMessage(),
                'success' => false,
            ]);
        }

        return response()->json([
            'message' => 'Deleted',
            'success' => true,
        ]);
    }
}
