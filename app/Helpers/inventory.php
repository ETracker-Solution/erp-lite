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
    return number_format(($data->totalQuantity != 0) ? $data->totalAmount / $data->totalQuantity : 0, 2);
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
    return number_format(($data->totalQuantity != 0) ? $data->totalAmount / $data->totalQuantity : 0, 2);
}

function inventoryAmount(int $store_id = null, int $item_id = null)
{

    $data1 = InventoryTransaction::where(['store_id' => $store_id, 'type' => 1])->sum('amount');
    $data2 = InventoryTransaction::where(['store_id' => $store_id, 'type' => -1])->sum('amount');
    return $data1 - $data2;


}
