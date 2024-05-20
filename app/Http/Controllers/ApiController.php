<?php

namespace App\Http\Controllers;

use App\Models\ChartOfInventory;
use App\Models\Consumption;
use App\Models\ConsumptionItem;
use App\Models\Product;
use App\Models\Production;
use App\Models\Supplier;
use App\Models\PurchaseItem;
use App\Models\SupplierTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ApiController extends Controller
{

    public function fetchSupplierDueById($id)
    {
        return SupplierTransaction::where('supplier_id', $id)->sum(DB::raw('amount * transaction_type'));
    }

    public function fetchItemById($id)
    {
        return ChartOfInventory::with('unit', 'parent')->findOrFail($id);
    }

    public function fetchItemInfoRMConsumption($item_id, $store_id = null)
    {

        $item = ChartOfInventory::with('unit', 'parent')->findOrFail($item_id);
        $data = [
            'item' => $item,
            'average_rate' => averageRMRate($item_id, $store_id),
            'balance' => availableInventoryBalance($item_id, $store_id),
        ];
        return $data;
    }

    public function fetch_product_sale($id)
    {

        $product = Product::findOrFail($id);
        $data = [
            'product_name' => $product->name,
            'sale_price' => $product->selling_price,
            'buy_price' => $product->buying_price,
            'product_id' => $id,
            'stock' => \App\Classes\AvailableProductCalculation::product_id($id),
        ];
        return $data;
    }

    public function fetchItemByIdForSale($id)
    {

        $coi = ChartOfInventory::findOrFail($id);
        $data = [
            'group' => $coi->parent->name,
            'name' => $coi->name,
            'unit' => $coi->unit->name ?? 'No Unit',
            'price' => $coi->price,
            'coi_id' => $id,
            'balance_qty' => 500,
        ];
        return $data;
    }

    public function fetch_products_by_cat_id($id)
    {
        $data = array();
        $products = ChartOfInventory::where(['status' => 'active', 'parent_id' => $id])->get();
        // dd($products);


        foreach ($products as $product) {

            $product['stock'] = 0;
            $product['quantity'] = 0;
            $product['price'] = 0;
            $product['selling_price'] = 0;
        }
        //dd($products);
        $data = [

            'products' => $products,
        ];

        return $data;
    }

    public function fetchSuppliersByGroupId($id)
    {

        $suppliers = Supplier::where(['status' => 'active', 'supplier_group_id' => $id])->get();


        $data = [

            'suppliers' => $suppliers,
        ];

        return $data;
    }

    public function fetchPurchaseProductInfo($id)
    {
        $products = PurchaseItem::with('coi')->where('purchase_id', $id)->get();
        $items = [];
        foreach ($products as $row) {
            $items[] = [
                'purchase_id' => $id,
                'id' => $row->coi_id,
                'unit' => $row->coi->unit->name ?? '',
                'name' => $row->coi->name ?? '',
                'group' => $row->coi->parent->name ?? '',
                'quantity' => $row->quantity,
                'rate' => $row->rate
            ];
        }
        return response()->json($items);
    }

    public function fetchConsumptionById($id)
    {
        $consumption = Consumption::with('items')->where('id', $id)->first();
        $items = [];
        foreach ($consumption->items as $row) {
            $items[] = [
                'consumption_id' => $id,
                'id' => $row->coi_id,
                'name' => $row->coi->name ?? '',
                'unit' => $row->coi->unit->name ?? '',
                'group' => $row->coi->parent->name ?? '',
                'quantity' => $row->quantity,
                'balance' => $row->quantity,
                'rate' => $row->rate
            ];
        }
        $data = [
            'items' => $items,
            'store_id' => $consumption->store_id,
            'batch_id' => $consumption->batch_id,
            'reference_no' => $consumption->reference_no,
            'remark' => $consumption->remark,
            'date' => $consumption->date
        ];
        return response()->json($data);
    }

    public function fetchProductionById($id)
    {
        $production = Production::with('items')->where('id', $id)->first();
        $items = [];
        foreach ($production->items as $row) {
            $items[] = [
                'consumption_id' => $id,
                'id' => $row->coi_id,
                'name' => $row->coi->name ?? '',
                'unit' => $row->coi->unit->name ?? '',
                'group' => $row->coi->parent->name ?? '',
                'quantity' => $row->quantity,
                'balance' => $row->quantity,
                'rate' => $row->rate
            ];
        }
        $data = [
            'items' => $items,
            'store_id' => $production->store_id,
            'batch_id' => $production->batch_id,
            'reference_no' => $production->reference_no,
            'remark' => $production->remark,
            'date' => $production->date
        ];
        return response()->json($data);
    }

    public function fetchItemAvailableBalance($item_id, $store_id = null): \Illuminate\Http\JsonResponse
    {

        $data = [
            'balance' => availableInventoryBalance($item_id, $store_id),
            'store_id' => $store_id,
            'item_id' => $item_id
        ];
        return response()->json($data);
    }
}
