<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeliveryReceive extends Model
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

    public function items()
    {
        return $this->hasMany(DeliveryReceiveItem::class);
    }

    public function requisitionDelivery()
    {
        return $this->belongsTo(RequisitionDelivery::class, 'requisition_delivery_id');
    }
}
