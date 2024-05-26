<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InventoryAdjustment extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function store()
    {
        return $this->belongsTo(Store::class, 'store_id');
    }

    public function items()
    {

        return $this->hasMany('App\Models\InventoryAdjustmentItem', 'inventory_adjustment_id');

    }
}
