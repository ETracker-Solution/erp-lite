<?php

namespace App\Http\Controllers;

use App\Models\AccountTransaction;
use App\Models\ChartOfAccount;
use App\Models\ChartOfInventory;
use App\Models\Consumption;
use App\Models\ConsumptionItem;
use App\Models\CustomerTransaction;
use App\Models\FundTransferVoucher;
use App\Models\InventoryTransaction;
use App\Models\InventoryTransfer;
use App\Models\OthersOutletSale;
use App\Models\Outlet;
use App\Models\OutletAccount;
use App\Models\Product;
use App\Models\Production;
use App\Models\Purchase;
use App\Models\Requisition;
use App\Models\RequisitionDelivery;
use App\Models\RequisitionItem;
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

//    public function fetchItemById($id)
//    {
//        ini_set('memory_limit', '512M');
//        $coi = ChartOfInventory::with('unit', 'parent')->findOrFail($id);
//        $products = ChartOfInventory::with('unit', 'parent')->where('id', $id)->get();
//        $needToProduction = 0;
//        if (auth()->user()->employee->user_of == 'factory') {
//            $all_requisitions = \App\Models\Requisition::todayFGAvailableRequisitions(auth('web')->user()->employee->factory_id);
//
//            $outlet_ids = collect($all_requisitions)->pluck('outlet_id')->toArray();
//            $outlets = Outlet::with(['requisitions.items'])->select('id', 'name')->whereIn('id', $outlet_ids)->get();
//            $requisitions = Requisition::whereIn('outlet_id', $outlet_ids)
//                ->where('type', 'FG')
//                ->where('status', 'approved')
//                ->whereIn('delivery_status', ['pending', 'partial'])
//                ->with(['items', 'deliveries.items'])
//                ->get()
//                ->groupBy('outlet_id');
//
//
//            foreach ($products as $product) {
//                $reqLeft = 0;
//                if (auth()->user()->employee->user_of == 'factory') {
//                    $storeIds = auth()->user()->employee->factory->stores()->where('type', 'FG')->pluck('id')->toArray();
//                    $current_stock = transactionAbleStock($product, $storeIds);
//
//                    foreach ($outlets as $outlet) {
//                        $outlet_req_qty = 0;
//                        $outlet_req_delivery_qty = 0;
//                        if (isset($requisitions[$outlet->id])) {
//                            foreach ($requisitions[$outlet->id] as $req) {
//                                $outlet_req_qty += $req->items->where('coi_id', $product->id)->sum('quantity');
//                                foreach ($req->deliveries as $delivery) {
//                                    $outlet_req_delivery_qty += $delivery->items->where('coi_id', $product->id)->sum('quantity');
//                                }
//                            }
//                        }
//                        $reqLeft += ($outlet_req_qty - $outlet_req_delivery_qty);
//                    }
//
//                    $needToProduction = $reqLeft - $current_stock;
//                }
//            }
//
//            $coi->quantity = max($needToProduction, 0);
//        }
//        return $coi;
//    }

    public function fetchItemById($id)
    {
        ini_set('memory_limit', '512M');

        $coi = ChartOfInventory::with('unit', 'parent')->findOrFail($id);

        $needToProduction = 0;

        if (auth()->user()->employee->user_of == 'factory') {
            $factoryId = auth('web')->user()->employee->factory_id;

            // Get store IDs once
            $storeIds = auth()->user()->employee->factory->stores()
                ->where('type', 'FG')
                ->pluck('id')
                ->toArray();

            // Calculate current stock for this single product
            $currentStock = transactionAbleStock($coi, $storeIds);

            // Calculate requisition left for this single product efficiently
            $reqLeft = $this->calculateSingleProductReqLeft($id, $factoryId);

            $needToProduction = $reqLeft - $currentStock;
            $coi->quantity = max($needToProduction, 0);
        }

        return $coi;
    }

    /**
     * Calculate requisition left for a single product efficiently
     */
    private function calculateSingleProductReqLeft(int $productId, int $factoryId): float
    {
        // Get total requisition quantity for this product in one query
        $totalReqQty = \App\Models\RequisitionItem::whereHas('requisition', function ($query) use ($factoryId) {
            $query->where('to_factory_id', $factoryId)
                ->where('type', 'FG')
                ->where('status', 'approved')
                ->whereIn('delivery_status', ['pending', 'partial']);
        })
            ->where('coi_id', $productId)
            ->sum('quantity');

        // Get total delivered quantity for this product in one query
        $totalDeliveredQty = \App\Models\RequisitionDeliveryItem::whereHas('requisitionDelivery', function ($query) use ($factoryId) {
            $query->whereHas('requisition', function ($q) use ($factoryId) {
                $q->where('to_factory_id', $factoryId)
                    ->where('type', 'FG')
                    ->where('status', 'approved')
                    ->whereIn('delivery_status', ['pending', 'partial']);
            });
        })
            ->where('coi_id', $productId)
            ->sum('quantity');

        return max($totalReqQty - $totalDeliveredQty, 0);
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

    public function fetchItemsByGroupIdRMConsumption($group_id, $store_id = null): array
    {
        $items = ChartOfInventory::where(['status' => 'active', 'parent_id' => $group_id])->get();
        foreach ($items as $item) {

            $item['id'] = $item ? $item->id : '';
            $item['name'] = $item ? $item->name : '';
            $item['group'] = $item->parent ? $item->parent->name : '';
            $item['uom'] = $item->unit ? $item->unit->name : '';
            $item['balance'] = availableInventoryBalance($item->id, $store_id);
            $item['quantity'] = '';
            $item['rate'] = round(averageRMRate($item->id, $store_id), 2);

        }
        return [
            'products' => $items,
        ];

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
        $current_stock = transactionAbleStock($coi, [\request()->store_id]);
//        if (\request()->from == 'adjustment'){
//            $store = Store::find(\request()->sale_id);
//            if($store->doc_type == 'factory'){
//                $storeIds = [$store->id];
//                $current_stock = transactionAbleStock($coi, $storeIds);
//            }
//        }
        $data = [
            'group' => $coi->parent->name,
            'parent_id' => $coi->parent_id,
            'name' => $coi->name,
            'unit' => $coi->unit->name ?? 'No Unit',
            'uom' => $coi->unit->name ?? 'No Unit',
            'price' => isset($item) ? $item->unit_price : $coi->price,
            'discountable' => !$coi->parent->non_discountable,
            'coi_id' => $id,
            'balance_qty' => round($current_stock, 2),
            'is_readonly' => $coi->price > 0 ? true : false,
            'product_discount' => isset($item) ? $item->discount : 0,
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
        ini_set('memory_limit', '512M');

        $factoryId = auth('web')->user()->employee->factory_id;

        // Get outlet IDs efficiently without loading all requisitions
        $outletIds = \App\Models\Requisition::where('to_factory_id', $factoryId)
            ->where('type', 'FG')
            ->where('status', 'approved')
            ->whereIn('delivery_status', ['pending', 'partial'])
            ->distinct()
            ->pluck('outlet_id')
            ->toArray();

        // Get products efficiently
        $products = ChartOfInventory::where(['status' => 'active', 'parent_id' => $id])
            ->with(['parent:id,name', 'unit:id,name'])
            ->get();

        $productIds = $products->pluck('id')->toArray();

        // Get factory stores once
        $storeIds = auth()->user()->employee->factory->stores()
            ->where('type', 'FG')
            ->pluck('id')
            ->toArray();

        // Batch calculate transactionable stock for all products
        $stockData = $this->batchCalculateStock($productIds, $storeIds);

        // Batch calculate requisition left quantities
        $reqLeftData = $this->batchCalculateReqLeft($productIds, $outletIds);

        // Map results to products
        foreach ($products as $product) {
            $currentStock = $stockData[$product->id] ?? 0;
            $reqLeft = $reqLeftData[$product->id] ?? 0;
            $needToProduction = $reqLeft - $currentStock;

            $product['quantity'] = max($needToProduction, 0);
            $product['group'] = $product->parent ? $product->parent->name : '';
            $product['uom'] = $product->unit ? $product->unit->name : '';
            $product['coi_id'] = $product->id;
            $product['stock'] = '';
            $product['price'] = $product->price;
            $product['rate'] = $product->price;
            $product['selling_price'] = '';
        }

        return [
            'products' => $products
        ];
    }

    private function batchCalculateStock(array $productIds, array $storeIds): array
    {
        if (empty($productIds) || empty($storeIds)) {
            return [];
        }

        // Get inventory quantities
        $inventoryQuantities = \App\Models\InventoryTransaction::whereIn('coi_id', $productIds)
            ->whereIn('store_id', $storeIds)
            ->select('coi_id', DB::raw('SUM(quantity * type) as total_stock'))
            ->groupBy('coi_id')
            ->pluck('total_stock', 'coi_id')
            ->toArray();

        // Get requisition delivery quantities
        $requisitionQuantities = \App\Models\RequisitionDeliveryItem::whereHas('requisitionDelivery', function ($query) use ($storeIds) {
            $query->where('status', 'completed')->whereIn('from_store_id', $storeIds);
        })
            ->whereIn('coi_id', $productIds)
            ->select('coi_id', DB::raw('SUM(quantity) as total_quantity'))
            ->groupBy('coi_id')
            ->pluck('total_quantity', 'coi_id')
            ->toArray();

        // Get pre-order quantities
        $preOrderQuantities = \App\Models\PreOrderItem::whereHas('preOrder', function ($query) use ($storeIds) {
            $query->where('status', 'delivered')->whereIn('factory_delivery_store_id', $storeIds);
        })
            ->whereIn('coi_id', $productIds)
            ->select('coi_id', DB::raw('SUM(quantity) as total_quantity'))
            ->groupBy('coi_id')
            ->pluck('total_quantity', 'coi_id')
            ->toArray();

        // Get transfer quantities
        $transferQuantities = \App\Models\InventoryTransferItem::whereHas('inventoryTransfer', function ($query) use ($storeIds) {
            $query->where('status', 'pending')->whereIn('from_store_id', $storeIds)->where('type', 'FG');
        })
            ->whereIn('coi_id', $productIds)
            ->select('coi_id', DB::raw('SUM(quantity) as total_quantity'))
            ->groupBy('coi_id')
            ->pluck('total_quantity', 'coi_id')
            ->toArray();

        // Calculate stock for each product
        $stockData = [];
        foreach ($productIds as $productId) {
            $originalStock = $inventoryQuantities[$productId] ?? 0;
            $requisitionDelivered = $requisitionQuantities[$productId] ?? 0;
            $preOrderDelivered = $preOrderQuantities[$productId] ?? 0;
            $inventoryTransferred = $transferQuantities[$productId] ?? 0;

            $stock = $originalStock - $requisitionDelivered - $preOrderDelivered - $inventoryTransferred;
            $stockData[$productId] = max($stock, 0);
        }

        return $stockData;
    }

    /**
     * Batch calculate requisition left quantities for multiple products
     */
    private function batchCalculateReqLeft(array $productIds, array $outletIds): array
    {
        if (empty($productIds) || empty($outletIds)) {
            return [];
        }

        // Get all requisition items in one query
        $requisitionItems = \App\Models\RequisitionItem::whereHas('requisition', function ($query) use ($outletIds) {
            $query->where('type', 'FG')
                ->where('status', 'approved')
                ->whereIn('delivery_status', ['pending', 'partial'])
                ->whereIn('outlet_id', $outletIds);
        })
            ->whereIn('coi_id', $productIds)
            ->select('coi_id', DB::raw('SUM(quantity) as total_quantity'))
            ->groupBy('coi_id')
            ->pluck('total_quantity', 'coi_id')
            ->toArray();

        // Get all delivery items in one query
        $deliveryItems = \App\Models\RequisitionDeliveryItem::whereHas('requisitionDelivery', function ($query) use ($outletIds) {
            $query->whereHas('requisition', function ($q) use ($outletIds) {
                $q->where('type', 'FG')
                    ->where('status', 'approved')
                    ->whereIn('delivery_status', ['pending', 'partial'])
                    ->whereIn('outlet_id', $outletIds);
            });
        })
            ->whereIn('coi_id', $productIds)
            ->select('coi_id', DB::raw('SUM(quantity) as total_quantity'))
            ->groupBy('coi_id')
            ->pluck('total_quantity', 'coi_id')
            ->toArray();

        // Calculate req left for each product
        $reqLeftData = [];
        foreach ($productIds as $productId) {
            $reqQty = $requisitionItems[$productId] ?? 0;
            $deliveryQty = $deliveryItems[$productId] ?? 0;
            $reqLeftData[$productId] = max($reqQty - $deliveryQty, 0);
        }

        return $reqLeftData;
    }


//    public function fetch_products_by_cat_id($id)
//    {
//        ini_set('memory_limit', '512M');
//        $all_requisitions = \App\Models\Requisition::todayFGAvailableRequisitions(auth('web')->user()->employee->factory_id);
//
//        $outlet_ids = collect($all_requisitions)->pluck('outlet_id')->toArray();
//        $outlets = Outlet::with(['requisitions.items'])->select('id', 'name')->whereIn('id', $outlet_ids)->get();
//
//        $requisition_ids = collect($all_requisitions)->pluck('id')->toArray();
//        $product_ids = RequisitionItem::whereIn('requisition_id', $requisition_ids)->whereNotNull('coi_id')->pluck('coi_id')->toArray();
//
//        $products = ChartOfInventory::where(['status' => 'active', 'parent_id' => $id])->get();
//        $needToProduction = 0;
//
//        $requisitions = Requisition::whereIn('outlet_id', $outlet_ids)
//            ->where('type', 'FG')
//            ->where('status', 'approved')
//            ->whereIn('delivery_status', ['pending', 'partial'])
//            ->with(['items', 'deliveries.items'])
//            ->get()
//            ->groupBy('outlet_id'); // Group by outlet_id for easier access later
//        foreach ($products as $product) {
//            $reqLeft = 0;
//            if (auth()->user()->employee->user_of == 'factory') {
//                $storeIds = auth()->user()->employee->factory->stores()->where('type', 'FG')->pluck('id')->toArray();
//                $current_stock = transactionAbleStock($product, $storeIds);
//
//                foreach ($outlets as $outlet) {
//                    $outlet_req_qty = 0;
//                    $outlet_req_delivery_qty = 0;
//                    if (isset($requisitions[$outlet->id])) {
//                        foreach ($requisitions[$outlet->id] as $req) {
//                            $outlet_req_qty += $req->items->where('coi_id', $product->id)->sum('quantity');
//                            foreach ($req->deliveries as $delivery) {
//                                $outlet_req_delivery_qty += $delivery->items->where('coi_id', $product->id)->sum('quantity');
//                            }
//                        }
//                    }
//                    $reqLeft += ($outlet_req_qty - $outlet_req_delivery_qty);
//                }
//
//                $needToProduction = $reqLeft - $current_stock;
//            }
//
//            $product['quantity'] = max($needToProduction, 0);
//            $product['group'] = $product->parent ? $product->parent->name : '';
//            $product['uom'] = $product->unit ? $product->unit->name : '';
//            $product['coi_id'] = $product->id;
//            $product['stock'] = '';
//            $product['price'] = $product->price;
//            $product['rate'] = $product->price;
//            $product['selling_price'] = '';
//        }
//        return [
//            'products' => $products
//        ];
//    }

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
        ini_set('memory_limit', '512M');

        $requisition = Requisition::with(['items.coi.unit', 'items.coi.parent', 'deliveries.items', 'items.coi.requisitionDeliveryItems.requisitionDelivery', 'items.coi.preOrderItems.preOrder'])
            ->where('id', $id)
            ->firstOrFail();

        // Collect all coi_ids from requisition items
        $coiIds = $requisition->items->pluck('coi_id')->toArray();

        // Pre-fetch average rates for all items at once (for both RM and FG)
        $averageRates = fetchAverageRates($coiIds, $store_id);

        $items = [];
        foreach ($requisition->items as $row) {
            if ($row->quantity > 0) {
                $current_stock = transactionAbleStock($row->coi, [$store_id]);

                $req_qty = $row->quantity;
                $delivered_qty = 0;
                foreach ($requisition->deliveries as $delivery) {
                    $delivered_qty += $delivery->items->where('coi_id', $row->coi->id)->sum('quantity');
                }

                $totalRequisitionLeft = $req_qty - $delivered_qty;
                $balance_quantity = $current_stock;

                // Determine final quantity to show based on balances
                if ($balance_quantity <= 0) {
                    $quantity = '';
                } else {
                    $quantity = min($balance_quantity, $totalRequisitionLeft);
                }
                if ($totalRequisitionLeft > 0) {
                    // Populate the items array with necessary details
                    $items[] = [
                        'requisition_id' => $id,
                        'coi_id' => $row->coi_id,
                        'unit' => $row->coi->unit->name ?? '',
                        'name' => $row->coi->name ?? '',
                        'group' => $row->coi->parent->name ?? '',
                        'rm_average_rate' => $averageRates[$row->coi_id]['rm_rate'] ?? 0,
                        'fg_average_rate' => $averageRates[$row->coi_id]['rm_rate'] ?? 0,
                        'balance_quantity' => $balance_quantity > 0 ? max(($balance_quantity), 0) : $balance_quantity,
                        'requisition_quantity' => $totalRequisitionLeft,
                        'quantity' => $quantity > 0 ? number_format($quantity, 2) : 0,
                    ];
                }
            }
        }

        // Prepare the final response data
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

    public function fetchFromAccountBalanceById($coa_id)
    {
        $other_outlet_sales_balance = accountBalanceForOtherOutletSales($coa_id);
        $main_balance = AccountTransaction::where('chart_of_account_id', $coa_id)->sum(\DB::raw('amount * transaction_type'));
        $pending_amount = FundTransferVoucher::where([
            'credit_account_id' => $coa_id,
            'status' => 'pending'
        ])->sum('amount');
        $data = [
            'from_ac_balance' => $main_balance - $other_outlet_sales_balance - $pending_amount
        ];
        return response()->json($data);
    }
}
