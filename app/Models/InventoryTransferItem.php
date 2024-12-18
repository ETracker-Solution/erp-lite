<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InventoryTransferItem extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function coi()
    {

        return $this->belongsTo('App\Models\ChartOfInventory', 'coi_id');

    }

    public function inventoryTransfer()
    {
        return $this->belongsTo(InventoryTransfer::class, 'inventory_transfer_id');
    }
}
