<?php

namespace App\Http\Controllers\Api\Web;

use App\Http\Controllers\Controller;
use App\Models\ChartOfInventory;
use Illuminate\Http\Request;

class InventoryController extends Controller
{

    public function inventoryItems()
    {
        $allChartOfInventories = ChartOfInventory::whereNull('parent_id')->get();
        return view('chart_of_inventory.items', compact('allChartOfInventories',));
    }

    public function inventoryDetails($id)
    {
        $inventory = ChartOfInventory::with('parent')->find($id);
        $data = [
            'item_id' => $inventory->id,
            'item_name' => $inventory->name,
            'item_type' => $inventory->type,
            'group_name' => $inventory->parent_id && $inventory->parent ? $inventory->parent->name : '',
            'account_type' => $inventory->rootAccountType,
            'unit_id' => $inventory->unit_id,
            'price' => $inventory->price,
        ];
        return response()->json($data);
    }

    public function inventoryUpdate($id)
    {
        try {
            $inventory = ChartOfInventory::find($id);
            if (\request()->filled('item_name')) {
                $inventory->name = \request()->item_name;
            }
            if ($inventory->type == 'item') {
                $inventory->unit_id = \request()->unit;
                $inventory->price = \request()->price;
            }
            $inventory->updated_by = auth()->user()->id;
            $inventory->update();
        } catch (\Exception $exception) {
            return response()->json([
                'message' => $exception->getMessage(),
                'success' => false
            ]);
        }
        return response()->json([
            'message' => 'Updated',
            'success' => true
        ]);
    }

    public function inventoryStore($id)
    {
        try {
            $inventory = ChartOfInventory::find($id);
            $inventory->subChartOfInventories()->create([
                'name' => \request()->item_name,
                'type' => \request()->item_type,
                'rootAccountType' => $inventory->rootAccountType,
                'unit_id' => \request()->unit ?? null,
                'price' => \request()->price ?? 0,
                'created_by' => auth()->user()->id,
            ]);
        } catch (\Exception $exception) {
            return response()->json([
                'message' => $exception->getMessage(),
                'success' => false
            ]);
        }
        return response()->json([
            'message' => 'Added',
            'success' => true
        ]);
    }

    public function inventoryDelete($id)
    {
        try {
            $inventory = ChartOfInventory::find($id);
            $inventory->delete();
        } catch (\Exception $exception) {
            return response()->json([
                'message' => $exception->getMessage(),
                'success' => false
            ]);
        }
        return response()->json([
            'message' => 'Deleted',
            'success' => true
        ]);
    }
}
