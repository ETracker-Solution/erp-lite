<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InventoryTransfer extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function fromStore()
    {

        return $this->belongsTo(Store::class, 'from_store_id');
    }

    public function toStore()
    {

        return $this->belongsTo(Store::class, 'to_store_id');
    }
}
