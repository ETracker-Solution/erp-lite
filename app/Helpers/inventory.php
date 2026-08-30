<?php

use App\Models\InventoryTransaction;
use App\Models\InventoryTransferItem;
use App\Models\PreOrderItem;
use App\Models\RequisitionDeliveryItem;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

function addInventoryTransaction(int $type, string $doc_type, $doc)
{
    InventoryTransaction::query()->create([
        'date' => $doc->date,
        'type' => $type,
        'quantity' => $doc->quantity,
        'rate' => $doc->rate,
        'amount' => $doc->amount,
        'store_id' => $doc->store_id,
        'coi_id' => $doc->coi_id,
        'doc_type' => $doc_type,
        'doc_id' => $doc->id,
        'created_at' => now()
    ]);
}

function availableInventoryBalance(int $item_id, ?int $store_id = null, bool $lock = false)
{
    $query = InventoryTransaction::where('coi_id', $item_id);
    if ($store_id) {
        $query->where('store_id', $store_id);
    }
    if ($lock) {
        $query->lockForUpdate();
    }
    return $query->select(DB::raw('SUM(quantity * type) AS total_sum'))
        ->value('total_sum') ?? 0;
}

function averageInventoryRate(int $item_id, ?int $store_id = null)
{
    $query = InventoryTransaction::where(['coi_id' => $item_id, 'type' => 1]);
    if ($store_id) {
        $query->where('store_id', $store_id);
    }
    $data = $query->select(DB::raw('SUM(amount) as totalAmount, SUM(quantity) as totalQuantity'))->first();

    return ($data && $data->totalQuantity != 0) ? $data->totalAmount / $data->totalQuantity : 0;
}

function averageRMRate(int $item_id, ?int $store_id = null)
{
    return averageInventoryRate($item_id, $store_id);
}

function averageFGRate(int $item_id, ?int $store_id = null)
{
    return averageInventoryRate($item_id, $store_id);
}

/**
 * Batch average FG rates for many products (matches averageFGRate()).
 */
function averageFGRates($productIds, ?int $store_id = null): array
{
    $productIds = collect($productIds)->filter()->unique()->values();
    if ($productIds->isEmpty()) {
        return [];
    }

    $query = InventoryTransaction::query()
        ->from(DB::raw('inventory_transactions FORCE INDEX (inv_txn_coi_store_idx)'))
        ->whereIn('coi_id', $productIds)
        ->where('type', 1);
    if ($store_id) {
        $query->where('store_id', $store_id);
    }

    return $query
        ->select('coi_id', DB::raw('SUM(amount) as totalAmount'), DB::raw('SUM(quantity) as totalQuantity'))
        ->groupBy('coi_id')
        ->get()
        ->mapWithKeys(function ($row) {
            $rate = ($row->totalQuantity != 0) ? ($row->totalAmount / $row->totalQuantity) : 0;
            return [$row->coi_id => $rate];
        })
        ->all();
}

function inventoryAmount(int $outletId)
{

    $totalStock = DB::table('inventory_transactions')
        ->join('stores', 'inventory_transactions.store_id', '=', 'stores.id')
        ->where('stores.doc_type', 'outlet')
        ->where('stores.doc_id', $outletId)
        ->select(DB::raw('SUM(inventory_transactions.amount * inventory_transactions.type) as total_stock'))
        ->value('total_stock') ?? 0;
    return $totalStock;
}


function fetchStoreProductBalances(array $productIds, array $storeIds)
{
    // Fetch balances for all products and stores in one query
    $inventoryTransactions = InventoryTransaction::whereIn('coi_id', $productIds)
        ->whereIn('store_id', $storeIds)
        ->select('coi_id', 'store_id', DB::raw('SUM(quantity * type) AS total_sum'))
        ->groupBy('coi_id', 'store_id')
        ->get();

    // Organize the results into a [store_id][product_id] => total_sum array
    $storeProductBalances = [];
    foreach ($inventoryTransactions as $transaction) {
        $storeProductBalances[$transaction->store_id][$transaction->coi_id] = max($transaction->total_sum, 0);
    }

    return $storeProductBalances;
}

function fetchAverageRates(array $coiIds, ?int $store_id = null)
{
    $rates = InventoryTransaction::whereIn('coi_id', $coiIds)
        ->where('type', 1)  // Assuming FG also uses type 1 for rate calculation
        ->when($store_id, function ($query) use ($store_id) {
            return $query->where('store_id', $store_id);
        })
        ->select('coi_id', DB::raw('SUM(amount) as totalAmount'), DB::raw('SUM(quantity) as totalQuantity'))
        ->groupBy('coi_id')
        ->get()
        ->keyBy('coi_id');

    $averageRates = [];
    foreach ($coiIds as $coi_id) {
        $rateData = $rates->get($coi_id);

        $rate = ($rateData && $rateData->totalQuantity != 0)
            ? $rateData->totalAmount / $rateData->totalQuantity
            : 0;

        $averageRates[$coi_id] = [
            'rm_rate' => $rate,
            'fg_rate' => $rate,
        ];
    }

    return $averageRates;
}

function fetchStoreCompletedRequisitionDeliveryQuantities($product, array $storeIds, $column = 'from_store_id')
{
    return $product->requisitionDeliveryItems()->whereHas('requisitionDelivery', function ($q) use ($storeIds, $column) {
        return $q->where('status', 'completed')->whereIn($column, $storeIds);
    })->sum('quantity');
}

function fetchStoreReceivedRequisitionDeliveryQuantities($product, array $storeIds, $column = 'from_store_id')
{
    return $product->requisitionDeliveryItems()->whereHas('requisitionDelivery', function ($q) use ($storeIds, $column) {
        return $q->where('status', 'received')->whereIn($column, $storeIds);
    })->sum('quantity');
}

function fetchStoreDeliveredPreOrderQuantities($product, array $storeIds)
{
    return $product->preOrderItems()->whereHas('preOrder', function ($q) use ($storeIds) {
        return $q->where('status', 'delivered')->whereIn('factory_delivery_store_id', $storeIds);
    })->sum('quantity');
}

function fetchStoreInventoryTransferQuantities($product, array $storeIds)
{
    return $product->inventoryTransferItems()->whereHas('inventoryTransfer', function ($q) use ($storeIds) {
        return $q->where('status', 'pending')->whereIn('from_store_id', $storeIds)->where('type', 'FG');
    })->sum('quantity');
}

function fetchStoreAvailableInventoryQuantities($product, array $storeIds)
{
    return $product->inventoryTransactions()->whereIn('store_id', $storeIds)->sum(DB::raw('quantity * type'));
}

function transactionAbleStock($product, array $storeIds, $optimize = false)
{
    $isCollection = $product instanceof \Illuminate\Support\Collection;
    $products = $isCollection ? $product : collect([$product]);
    $results = transactionAbleStockOptimized($products, $storeIds);
    if ($optimize || $isCollection) {
        return $results;
    }
    return $results->first()['stock'] ?? 0;
}

function fetchStoreRequisitionQuantities($product, array $storeIds, $column = 'to_store_id')
{
    return $product->requisitionItems()->whereHas('requisition', function ($q) use ($storeIds, $column) {
        return $q->where('status', 'approved')->whereIn('delivery_status', ['pending', 'partial'])->whereIn($column, $storeIds);
    })->sum('quantity');
}

function transactionAbleStockOptimized($products, array $storeIds)
{
    $products = collect($products);
    $productIds = $products->map(function ($product) {
        if (is_array($product)) {
            return $product['id'] ?? null;
        }
        return $product->id ?? null;
    })->filter()->unique()->values();

    if ($productIds->isEmpty()) {
        return $products->map(fn ($product) => ['product' => $product, 'stock' => 0]);
    }

    // Batch fetch inventory transactions
    $inventoryQuantities = InventoryTransaction::whereIn('coi_id', $productIds)
        ->from(DB::raw('inventory_transactions FORCE INDEX (inv_txn_coi_store_idx)'))
        ->whereIn('store_id', $storeIds)
        ->select(
            'coi_id',
            DB::raw('SUM(quantity * type) as total_stock'),
            DB::raw('SUM(CASE WHEN type = 1 THEN amount ELSE 0 END) as total_amount'),
            DB::raw('SUM(CASE WHEN type = 1 THEN quantity ELSE 0 END) as total_quantity')
        )
        ->groupBy('coi_id')
        ->get()
        ->keyBy('coi_id');

    $storeId = (int) $storeIds[0];
    $requisitionQuantities = sumRequisitionDeliveryQuantities($storeId, $productIds);
    $preOrderQuantities = sumPreOrderQuantities($storeId, $productIds);
    $transferQuantities = sumInventoryTransferQuantities($storeId, $productIds);

    // Calculate stock for each product
    return $products->map(function ($product) use ($inventoryQuantities, $requisitionQuantities, $preOrderQuantities, $transferQuantities) {
        $productId = is_array($product) ? ($product['id'] ?? null) : ($product->id ?? null);

        $inventory = $inventoryQuantities->get($productId);
        $originalStock = $inventory->total_stock ?? 0;
        $requisitionDelivered = $requisitionQuantities[$productId] ?? 0;
        $preOrderDelivered = $preOrderQuantities[$productId] ?? 0;
        $inventoryTransferred = $transferQuantities[$productId] ?? 0;

        $stock = $originalStock - $requisitionDelivered - $preOrderDelivered - $inventoryTransferred;

        return [
            'product' => $product,
            'stock' => max($stock, 0),
            'average_rate' => $inventory && (float) $inventory->total_quantity != 0
                ? (float) $inventory->total_amount / (float) $inventory->total_quantity
                : 0,
        ];
    });
}

function getInventoryQuantities($productIds, $storeId, $lock = false)
{
    $productIds = collect($productIds)->filter()->unique()->values();
    if ($productIds->isEmpty()) {
        return collect();
    }

    $query = InventoryTransaction::query()
        ->from(DB::raw('inventory_transactions FORCE INDEX (inv_txn_coi_store_idx)'))
        ->whereIn('coi_id', $productIds)
        ->where('store_id', $storeId);
    if ($lock) {
        $query->lockForUpdate();
    }

    return $query
        ->select('coi_id', DB::raw('SUM(quantity * type) as total_stock'))
        ->groupBy('coi_id')
        ->pluck('total_stock', 'coi_id');
}

function getRequisitionQuantities($productIds, $storeId)
{
    return sumRequisitionDeliveryQuantities((int) $storeId, $productIds);
}

function getPreOrderQuantities($productIds, $storeId)
{
    return sumPreOrderQuantities((int) $storeId, $productIds);
}

function getTransferQuantities($productIds, $storeId)
{
    return sumInventoryTransferQuantities((int) $storeId, $productIds);
}

function sumRequisitionDeliveryQuantities(int $storeId, $productIds = null): Collection
{
    $query = RequisitionDeliveryItem::query()
        ->join('requisition_deliveries', 'requisition_delivery_items.requisition_delivery_id', '=', 'requisition_deliveries.id')
        ->where('requisition_deliveries.status', 'completed')
        ->where('requisition_deliveries.from_store_id', $storeId);

    if ($productIds !== null) {
        $productIds = collect($productIds)->filter()->unique()->values();
        if ($productIds->isEmpty()) {
            return collect();
        }
        $query->whereIn('requisition_delivery_items.coi_id', $productIds);
    }

    return $query
        ->select('requisition_delivery_items.coi_id', DB::raw('SUM(requisition_delivery_items.quantity) as total_quantity'))
        ->groupBy('requisition_delivery_items.coi_id')
        ->pluck('total_quantity', 'coi_id');
}

function sumPreOrderQuantities(int $storeId, $productIds = null): Collection
{
    $query = PreOrderItem::query()
        ->join('pre_orders', 'pre_order_items.pre_order_id', '=', 'pre_orders.id')
        ->where('pre_orders.status', 'delivered')
        ->where('pre_orders.factory_delivery_store_id', $storeId);

    if ($productIds !== null) {
        $productIds = collect($productIds)->filter()->unique()->values();
        if ($productIds->isEmpty()) {
            return collect();
        }
        $query->whereIn('pre_order_items.coi_id', $productIds);
    }

    return $query
        ->select('pre_order_items.coi_id', DB::raw('SUM(pre_order_items.quantity) as total_quantity'))
        ->groupBy('pre_order_items.coi_id')
        ->pluck('total_quantity', 'coi_id');
}

function sumInventoryTransferQuantities(int $storeId, $productIds = null): Collection
{
    $query = InventoryTransferItem::query()
        ->join('inventory_transfers', 'inventory_transfer_items.inventory_transfer_id', '=', 'inventory_transfers.id')
        ->where('inventory_transfers.status', 'pending')
        ->where('inventory_transfers.from_store_id', $storeId)
        ->where('inventory_transfers.type', 'FG');

    if ($productIds !== null) {
        $productIds = collect($productIds)->filter()->unique()->values();
        if ($productIds->isEmpty()) {
            return collect();
        }
        $query->whereIn('inventory_transfer_items.coi_id', $productIds);
    }

    return $query
        ->select('inventory_transfer_items.coi_id', DB::raw('SUM(inventory_transfer_items.quantity) as total_quantity'))
        ->groupBy('inventory_transfer_items.coi_id')
        ->pluck('total_quantity', 'coi_id');
}

function getPosTransactionAbleStockMap(int $storeId): array
{
    static $memo = [];
    if (isset($memo[$storeId])) {
        return $memo[$storeId];
    }

    $memo[$storeId] = Cache::remember("pos_stock_map:{$storeId}", 30, function () use ($storeId) {
        $inventoryQuantities = InventoryTransaction::query()
            ->from(DB::raw('inventory_transactions FORCE INDEX (inv_txn_store_date_idx)'))
            ->where('store_id', $storeId)
            ->select('coi_id', DB::raw('SUM(quantity * type) as total_stock'))
            ->groupBy('coi_id')
            ->pluck('total_stock', 'coi_id');

        $requisitionQuantities = sumRequisitionDeliveryQuantities($storeId);
        $preOrderQuantities = sumPreOrderQuantities($storeId);
        $transferQuantities = sumInventoryTransferQuantities($storeId);

        $stockMap = [];
        foreach ($inventoryQuantities as $coiId => $stock) {
            $netStock = (float) $stock
                - (float) ($requisitionQuantities[$coiId] ?? 0)
                - (float) ($preOrderQuantities[$coiId] ?? 0)
                - (float) ($transferQuantities[$coiId] ?? 0);

            $stockMap[$coiId] = round(max($netStock, 0), 2);
        }

        return $stockMap;
    });

    return $memo[$storeId];
}

function forgetPosTransactionAbleStockMap(int $storeId): void
{
    Cache::forget("pos_stock_map:{$storeId}");
}
