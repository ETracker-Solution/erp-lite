<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Purchase extends Model
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

        return $this->hasMany('App\Models\PurchaseItem', 'purchase_id');

    }

    public function stock_items()
    {

        return $this->hasMany('App\Models\StockIn', 'purchase_id');

    }
}
