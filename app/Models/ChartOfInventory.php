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

    public function alterUnit()
    {
        return $this->belongsTo(AlterUnit::class, 'alter_unit_id');
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

    public function inventoryTransferItems()
    {
        return $this->hasMany(InventoryTransferItem::class, 'coi_id');
    }

    public function requisitionItems()
    {
        return $this->hasMany(RequisitionItem::class, 'coi_id');
    }

    public function recipes()
    {
        return $this->hasMany(Recipe::class, 'fg_id');
    }

    public function purchaseItems()
    {
        return $this->hasMany(PurchaseItem::class, 'coi_id');
    }
}
