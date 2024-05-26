<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PreOrderItem extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function preOrder()
    {
        return $this->belongsTo(PreOrder::class,'pre_order_id');
    }

    public function product()
    {
        return $this->belongsTo(ChartOfInventory::class, 'coi_id');
    }
}
