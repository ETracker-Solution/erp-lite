<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Purchase extends Model
{
    use HasFactory;
    public function supplier(){

        return $this->belongsTo('App\Models\Supplier', 'supplier_id');

    }
    public function items(){

        return $this->hasMany('App\Models\PurchaseItem', 'purchase_id');

    }
    public function stock_items(){

        return $this->hasMany('App\Models\StockIn', 'purchase_id');

    }
}
