<?php

namespace App\Http\Controllers;

use App\Models\ChartOfAccount;
use App\Models\ChartOfInventory;
use App\Models\Consumption;
use App\Models\ConsumptionItem;
use App\Models\InventoryTransfer;
use App\Models\OthersOutletSale;
use App\Models\Product;
use App\Models\Production;
use App\Models\Purchase;
use App\Models\Requisition;
use App\Models\RequisitionDelivery;
use App\Models\Sale;
use App\Models\Store;
use App\Models\Supplier;
use App\Models\SupplierTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ApiController extends Controller
{

    public function fetchSupplierDueById($id)
    {
        return SupplierTransaction::where('supplier_id', $id)->sum(DB::raw('amount * transaction_type'));
    }

    public function fetchCustomerDueById($id)
    {
        return CustomerTransaction::where('customer_id', $id)->sum(DB::raw('amount * transaction_type'));
    }

    public function fetchItemById($id)
    {
        $coi = ChartOfInventory::with('unit', 'parent')->findOrFail($id);

        if (auth()->user()->employee->user_of == 'factory') {
            $single_outlet_reqs = Requisition::where(['type' => 'FG', 'status' => 'approved'])
                ->whereIn('delivery_status', ['pending', 'partial'])->get();

            $req_qty = 0;
            $current_stock = 0;
            foreach ($single_outlet_reqs as $req) {
                $req_qty += $req->items()->where('coi_id', $coi->id)->sum('quantity');
            }

            foreach (auth()->user()->employee->factory->stores as $store) {
                $current_stock += availableInventoryBalance($coi->id, $store->id);
            }
            $diff = $req_qty - $current_stock;

            $coi->quantity = max($diff, 0);
        }
        return $coi;
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

    public function fetchItemByIdForPreOrder($id)
    {

        $coi = ChartOfInventory::findOrFail($id);
        $data = [
            'group' => $coi->parent->name,
            'name' => $coi->name,
            'unit' => $coi->unit->name ?? 'No Unit',
            'rate' => $coi->price > 0 ? $coi->price : null,
            'discount' => '',
            'coi_id' => $id,
            'is_readonly' => $coi->price > 0 ? true : false,
        ];
        return $data;
    }

    public function fetchItemByIdForSale($id)
    {
        $coi = ChartOfInventory::findOrFail($id);
        if (\request()->sale_id) {
            $sale = OthersOutletSale::find(\request()->sale_id);
            $item = $sale->items()->where('product_id', $coi->id)->first();
        }
        $data = [
            'group' => $coi->parent->name,
            'parent_id' => $coi->parent_id,
            'name' => $coi->name,
            'unit' => $coi->unit->name ?? 'No Unit',
            'price' => isset($item) ? $item->unit_price : $coi->price,
//            'price' => number_format(averageRMRate($coi->id),2),
            'coi_id' => $id,
            'balance_qty' => availableInventoryBalance($id, request()->store_id),
            'is_readonly' => $coi->price > 0 ? true : false,
        ];
        return $data;
    }

    public function fetchItemByIdForRMRequisition($id)
    {

        $coi = ChartOfInventory::findOrFail($id);
        $data = [
            'group' => $coi->parent->name,
            'name' => $coi->name,
            'unit' => $coi->unit->name ?? 'No Unit',
            'price' => averageRMRate($coi->id),
            'coi_id' => $id,
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

    public function fetchPurchaseById($id)
    {
        $purchase = Purchase::with('items', 'supplier')->where('id', $id)->first();
        $items = [];
        foreach ($purchase->items as $row) {
            $items[] = [
                'purchase_id' => $id,
                'id' => $row->coi_id,
                'unit' => $row->coi->unit->name ?? '',
                'name' => $row->coi->name ?? '',
                'group' => $row->coi->parent->name ?? '',
                'quantity' => $row->quantity,
                'purchase_quantity' => $row->quantity,
                'rate' => $row->rate
            ];
        }
        $data = [
            'purchase' => $purchase,
            'items' => $items,
        ];
        return response()->json($data);
    }

    public function fetchRequisitionById($id, $store_id = null)
    {
        $requisition = Requisition::with('items')->where('id', $id)->first();
        $r_items = $requisition->availableItems();
        $items = [];

        foreach ($r_items as $row) {
            if ($row->quantity > 0) {
                $items[] = [
                    'requisition_id' => $id,
                    'coi_id' => $row->coi_id,
                    'unit' => $row->coi->unit->name ?? '',
                    'name' => $row->coi->name ?? '',
                    'group' => $row->coi->parent->name ?? '',
                    'rm_average_rate' => averageRMRate($row->coi_id, $store_id),
                    'fg_average_rate' => averageFGRate($row->coi_id, $store_id),
                    'balance_quantity' => availableInventoryBalance($row->coi_id, $store_id),
                    'requisition_quantity' => $row->quantity,
                    'quantity' => '',
                ];
            }
        }
        $data = [
            'items' => $items,
            'date' => $requisition->date,
            'from_store_id' => $requisition->from_store_id,
            'to_store_id' => $requisition->to_store_id,
            'reference_no' => $requisition->reference_no,
            'remark' => $requisition->remark,
        ];
        return response()->json($data);
    }

    public function fetchRequisitionDeliveryById($id)
    {
        $requisitionDelivery = RequisitionDelivery::with('items')->where('id', $id)->first();
        $items = [];
        foreach ($requisitionDelivery->items as $row) {
            $items[] = [
                'requisition_id' => $id,
                'coi_id' => $row->coi_id,
                'unit' => $row->coi->unit->name ?? '',
                'name' => $row->coi->name ?? '',
                'group' => $row->coi->parent->name ?? '',
                'fg_average_rate' => averageFGRate($row->coi_id),
                'delivery_quantity' => $row->quantity,
                'quantity' => $row->quantity,
            ];
        }
        $data = [
            'items' => $items,
            'date' => $requisitionDelivery->date,
            'from_store_id' => $requisitionDelivery->from_store_id,
            'to_store_id' => $requisitionDelivery->to_store_id,
            'reference_no' => $requisitionDelivery->reference_no,
            'remark' => $requisitionDelivery->remark,
        ];
        return response()->json($data);
    }

    public function fetchInventoryTransferById($id)
    {
        $requisitionDelivery = InventoryTransfer::with('items')->where('id', $id)->first();
        $items = [];
        foreach ($requisitionDelivery->items as $row) {
            $items[] = [
                'inventory_transfer_id' => $id,
                'coi_id' => $row->coi_id,
                'unit' => $row->coi->unit->name ?? '',
                'name' => $row->coi->name ?? '',
                'group' => $row->coi->parent->name ?? '',
                'fg_average_rate' => averageFGRate($row->coi_id),
                'transfer_quantity' => $row->quantity,
                'quantity' => '',
            ];
        }
        $data = [
            'items' => $items,
            'date' => $requisitionDelivery->date,
            'from_store_id' => $requisitionDelivery->from_store_id,
            'to_store_id' => $requisitionDelivery->to_store_id,
            'reference_no' => $requisitionDelivery->reference_no,
            'remark' => $requisitionDelivery->remark,
        ];
        return response()->json($data);
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

    public function fetch_req_by_store_id($store_id)
    {
        $requisitions = Requisition::availableRequisitions('FG', $store_id);
        return response()->json(['requisitions' => $requisitions]);
    }

    public function getUUIDbyStore(Request $request, $store_id)
    {
        $modelNamespace = 'App\\Models\\';
        $modelName = ucfirst($request->input('model')); // Capitalize the first letter
        $modelClass = $modelNamespace . $modelName;
        $is_factory = $request->is_factory ?? false;
        $is_headOffice = $request->is_headOffice ?? false;
        $store = Store::find($store_id);
        return generateUniqueUUID($store->doc_id, $modelClass, $request->column, $is_factory, $is_headOffice);
    }

    public function fetchAccountDetailsById($id)
    {
        $from_account = ChartOfAccount::with('parent')->findOrFail(\request('from_account_id'));
        $to_account = ChartOfAccount::with('parent')->findOrFail(\request('to_account_id'));
        $data = [
            'from_account_id' => $from_account->id,
            'from_account_name' => $from_account->name,
            'to_account_id' => $to_account->id,
            'to_account_name' => $to_account->name,
        ];
        return response()->json($data);
    }
}
