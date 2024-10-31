<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChartOfInventory extends Model
{
    use HasFactory;
    protected $guarded = ['id'];


    public function subChartOfInventories()
    {
        return $this->hasMany(self::class, 'parent_id');
    }

    public function parent()
    {
        return $this->belongsTo(self::class, 'parent_id');
    }
    public function unit()
    {
        return $this->belongsTo(Unit::class, 'unit_id');
    }

    public function inventoryTransactions()
    {
        return $this->hasMany(InventoryTransaction::class, 'coi_id');
    }

    public function requisitionDeliveryItems()
    {
        return $this->hasMany(RequisitionDeliveryItem::class, 'coi_id');
    }

    public function preOrderItems()
    {
        return $this->hasMany(PreOrderItem::class, 'coi_id');
    }
}
