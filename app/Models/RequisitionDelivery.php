<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RequisitionDelivery extends Model
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
        return $this->hasMany(RequisitionDeliveryItem::class);
    }

    public function requisition()
    {
        return $this->belongsTo(Requisition::class, 'requisition_id');
    }
}
