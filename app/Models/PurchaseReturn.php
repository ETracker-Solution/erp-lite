<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseReturn extends Model
{
    use HasFactory;
    protected $guarded = ['id'];

    public function store()
    {

        return $this->belongsTo('App\Models\Store', 'store_id');

    }

    public function supplier()
    {

        return $this->belongsTo('App\Models\Supplier', 'supplier_id');

    }

    public function supplierTransactions()
    {
        return $this->hasMany('App\Models\SupplierTransaction', 'purchase_id');
    }

    public function inventoryTransactions()
    {
        return $this->hasMany('App\Models\InventoryTransaction', 'purchase_id');
    }

    public function items()
    {

        return $this->hasMany('App\Models\PurchaseReturnItem', 'purchase_return_id');

    }
}
