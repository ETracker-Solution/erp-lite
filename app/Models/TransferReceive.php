<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransferReceive extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function inventoryTransfer()
    {

        return $this->belongsTo(InventoryTransfer::class, 'inventory_transfer_id');
    }

    public function fromStore()
    {

        return $this->belongsTo(Store::class, 'from_store_id');
    }

    public function toStore()
    {

        return $this->belongsTo(Store::class, 'to_store_id');
    }

    public function items()
    {
        return $this->hasMany(TransferReceiveItem::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
