<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PreOrder extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function items()
    {
        return $this->hasMany(PreOrderItem::class,'pre_order_id');
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class,'customer_id');
    }
}
