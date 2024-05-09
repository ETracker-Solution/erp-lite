<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RawMaterialOpeningBalance extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function chartOfInventory()
    {
        return $this->belongsTo(ChartOfInventory::class, 'coi_id');
    }
    public function pHouse()
    {
        return $this->belongsTo(Store::class, 'store_id');
    }

    public function inventoryTransaction()
    {
        return $this->hasOne(InventoryTransaction::class, 'doc_id')->where('doc_type','=', 'rmob');
    }
}
