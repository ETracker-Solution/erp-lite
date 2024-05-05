<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
    use HasFactory;

    public function items()
    {

        return $this->hasMany('App\Models\SaleItem', 'sale_id');

    }

    public function stock_out_items()
    {

        return $this->hasMany('App\Models\StockOut', 'sale_id');

    }

    public function user()
    {

        return $this->belongsTo('App\Models\User', 'created_by');

    }

    public function customer()
    {

        return $this->belongsTo('App\Models\Customer', 'customer_id');

    }
}
