<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SalesReturn extends Model
{
    use HasFactory;
    protected $guarded = ['id'];
    public function items()
    {
        return $this->hasMany('App\Models\SalesReturnItem', 'sales_return_id');
    }

    public function sale()
    {
        return $this->belongsTo('App\Models\Sale', 'sale_id');
    }

    public function store()
    {
        return $this->belongsTo('App\Models\Store', 'store_id');
    }
}
