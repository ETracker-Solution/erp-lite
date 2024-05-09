<?php

use App\Models\InventoryTransaction;

function addInventoryTransaction(int $type, string $doc_type, $doc)
{
    InventoryTransaction::query()->create([
        'tr_date'=>$doc->date,
        'tr_type'=>$type,
        'qty'=>$doc->qty,
        'rate'=>$doc->rate,
        'amount'=>$doc->amount,
        'store_id' => $doc->store_id,
        'coi_id' => $doc->coi_id,
        'doc_type'=>$doc_type,
        'doc_id'=>$doc->id
    ]);
}
