<?php

use App\Models\InventoryTransaction;
use App\Models\InventoryTransferItem;
use App\Models\PreOrderItem;
use App\Models\RequisitionDeliveryItem;
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

function availableInventoryBalance(int $item_id, int $store_id = null)
{
    if ($store_id) {
        return InventoryTransaction::where(['coi_id' => $item_id, 'store_id' => $store_id])->select(DB::raw('SUM(quantity * type) AS total_sum'))
            ->value('total_sum') ?? 0;
    }
    return InventoryTransaction::where(['coi_id' => $item_id])->select(DB::raw('SUM(quantity * type) AS total_sum'))
        ->value('total_sum') ?? 0;
}

function averageRMRate(int $item_id, int $store_id = null)
{
    if ($store_id) {
        $data = InventoryTransaction::where(['coi_id' => $item_id, 'store_id' => $store_id, 'type' => 1])->select(DB::raw('SUM(amount) as totalAmount, SUM(quantity) as totalQuantity'))
            ->first();
    }
    $data = InventoryTransaction::where(['coi_id' => $item_id])->select(DB::raw('SUM(amount) as totalAmount, SUM(quantity) as totalQuantity'))
        ->first();


    // Calculate the average price
    return ($data->totalQuantity != 0) ? $data->totalAmount / $data->totalQuantity : 0;
}

function averageFGRate(int $item_id, int $store_id = null)
{
    if ($store_id) {
        $data = InventoryTransaction::where(['coi_id' => $item_id, 'store_id' => $store_id, 'type' => 1])->select(DB::raw('SUM(amount) as totalAmount, SUM(quantity) as totalQuantity'))
            ->first();
    }
    $data = InventoryTransaction::where(['coi_id' => $item_id])->select(DB::raw('SUM(amount) as totalAmount, SUM(quantity) as totalQuantity'))
        ->first();


    // Calculate the average price
    return ($data->totalQuantity != 0) ? $data->totalAmount / $data->totalQuantity : 0;
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

function fetchAverageRates(array $coiIds, int $store_id = null)
{
    // Query for RM rates
    $rmRates = InventoryTransaction::whereIn('coi_id', $coiIds)
        ->where('type', 1)  // Only consider the transactions of type 1 for RM
        ->when($store_id, function ($query) use ($store_id) {
            return $query->where('store_id', $store_id);
        })
        ->select('coi_id', DB::raw('SUM(amount) as totalAmount'), DB::raw('SUM(quantity) as totalQuantity'))
        ->groupBy('coi_id')
        ->get()
        ->keyBy('coi_id');

    // Query for FG rates
    $fgRates = InventoryTransaction::whereIn('coi_id', $coiIds)
        ->where('type', 1)  // Assuming FG also uses type 1 for rate calculation
        ->when($store_id, function ($query) use ($store_id) {
            return $query->where('store_id', $store_id);
        })
        ->select('coi_id', DB::raw('SUM(amount) as totalAmount'), DB::raw('SUM(quantity) as totalQuantity'))
        ->groupBy('coi_id')
        ->get()
        ->keyBy('coi_id');

    // Calculate average rates and store them
    $averageRates = [];
    foreach ($coiIds as $coi_id) {
        $rmData = $rmRates->get($coi_id);
        $fgData = $fgRates->get($coi_id);

        $rmRate = ($rmData && $rmData->totalQuantity != 0) ? $rmData->totalAmount / $rmData->totalQuantity : 0;
        $fgRate = ($fgData && $fgData->totalQuantity != 0) ? $fgData->totalAmount / $fgData->totalQuantity : 0;

        $averageRates[$coi_id] = [
            'rm_rate' => $rmRate,
            'fg_rate' => $fgRate,
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
    if ($optimize) {
        return transactionAbleStockOptimized($product->toArray(), $storeIds);
    }
    $originalStock = fetchStoreAvailableInventoryQuantities($product, $storeIds);
    $requisitionDeliveredQuantity = fetchStoreCompletedRequisitionDeliveryQuantities($product, $storeIds);
    $preOrderDeliveredQuantity = fetchStoreDeliveredPreOrderQuantities($product, $storeIds);
    $InventoryTransferredQuantity = fetchStoreInventoryTransferQuantities($product, $storeIds);
    $stock =  $originalStock - $requisitionDeliveredQuantity - $preOrderDeliveredQuantity - $InventoryTransferredQuantity;
    return max($stock,0);
}

function fetchStoreRequisitionQuantities($product, array $storeIds, $column = 'to_store_id')
{
    return $product->requisitionItems()->whereHas('requisition', function ($q) use ($storeIds, $column) {
        return $q->where('status', 'approved')->whereIn('delivery_status', ['pending', 'partial'])->whereIn($column, $storeIds);
    })->sum('quantity');
}

function transactionAbleStockOptimized($products, array $storeIds)
{
    $productIds = $products->pluck('id');

    // Batch fetch inventory transactions
    $inventoryQuantities = InventoryTransaction::whereIn('coi_id', $productIds)
        ->whereIn('store_id', $storeIds)
        ->select('coi_id', DB::raw('SUM(quantity * type) as total_stock'))
        ->groupBy('coi_id')
        ->pluck('total_stock', 'coi_id');

    // Batch fetch completed requisition delivery quantities
    $requisitionQuantities = RequisitionDeliveryItem::whereHas('requisitionDelivery', function ($query) use ($storeIds) {
        $query->where('status', 'completed')->whereIn('from_store_id', $storeIds);
    })
        ->whereIn('coi_id', $productIds)
        ->select('coi_id', DB::raw('SUM(quantity) as total_quantity'))
        ->groupBy('coi_id')
        ->pluck('total_quantity', 'coi_id');

    // Batch fetch delivered pre-order quantities
    $preOrderQuantities = PreOrderItem::whereHas('preOrder', function ($query) use ($storeIds) {
        $query->where('status', 'delivered')->whereIn('factory_delivery_store_id', $storeIds);
    })
        ->whereIn('coi_id', $productIds)
        ->select('coi_id', DB::raw('SUM(quantity) as total_quantity'))
        ->groupBy('coi_id')
        ->pluck('total_quantity', 'coi_id');

    // Batch fetch pending inventory transfer quantities
    $transferQuantities = InventoryTransferItem::whereHas('inventoryTransfer', function ($query) use ($storeIds) {
        $query->where('status', 'pending')->whereIn('from_store_id', $storeIds)->where('type', 'FG');
    })
        ->whereIn('coi_id', $productIds)
        ->select('coi_id', DB::raw('SUM(quantity) as total_quantity'))
        ->groupBy('coi_id')
        ->pluck('total_quantity', 'coi_id');

    // Calculate stock for each product
    return $products->map(function ($product) use ($inventoryQuantities, $requisitionQuantities, $preOrderQuantities, $transferQuantities) {
        $productId = $product->id;

        $originalStock = $inventoryQuantities[$productId] ?? 0;
        $requisitionDelivered = $requisitionQuantities[$productId] ?? 0;
        $preOrderDelivered = $preOrderQuantities[$productId] ?? 0;
        $inventoryTransferred = $transferQuantities[$productId] ?? 0;

        $stock = $originalStock - $requisitionDelivered - $preOrderDelivered - $inventoryTransferred;

        return [
            'product' => $product,
            'stock' => max($stock, 0),
        ];
    });
}

function getInventoryQuantities($productIds, $storeId)
{
    return InventoryTransaction::whereIn('coi_id', $productIds)
        ->whereIn('store_id', [$storeId])
        ->select('coi_id', DB::raw('SUM(quantity * type) as total_stock'))
        ->groupBy('coi_id')
        ->pluck('total_stock', 'coi_id');
}

function getRequisitionQuantities($productIds, $storeId)
{
    return RequisitionDeliveryItem::whereHas('requisitionDelivery', function ($query) use ($storeId) {
        $query->where('status', 'completed')->whereIn('from_store_id', [$storeId]);
    })
        ->whereIn('coi_id', $productIds)
        ->select('coi_id', DB::raw('SUM(quantity) as total_quantity'))
        ->groupBy('coi_id')
        ->pluck('total_quantity', 'coi_id');
}

function getPreOrderQuantities($productIds, $storeId)
{
    return PreOrderItem::whereHas('preOrder', function ($query) use ($storeId) {
        $query->where('status', 'delivered')->whereIn('factory_delivery_store_id', [$storeId]);
    })
        ->whereIn('coi_id', $productIds)
        ->select('coi_id', DB::raw('SUM(quantity) as total_quantity'))
        ->groupBy('coi_id')
        ->pluck('total_quantity', 'coi_id');
}

function getTransferQuantities($productIds, $storeId)
{
    return InventoryTransferItem::whereHas('inventoryTransfer', function ($query) use ($storeId) {
        $query->where('status', 'pending')->whereIn('from_store_id', [$storeId])->where('type', 'FG');
    })
        ->whereIn('coi_id', $productIds)
        ->select('coi_id', DB::raw('SUM(quantity) as total_quantity'))
        ->groupBy('coi_id')
        ->pluck('total_quantity', 'coi_id');
}
