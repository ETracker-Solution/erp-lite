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
