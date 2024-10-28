<?php

use App\Models\InventoryTransaction;
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
        'doc_id' => $doc->id
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
        $storeProductBalances[$transaction->store_id][$transaction->coi_id] = $transaction->total_sum;
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
