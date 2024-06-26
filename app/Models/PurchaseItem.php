<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseItem extends Model
{
    use HasFactory;
    protected $guarded = ['id'];

    public function product(){

        return $this->belongsTo('App\Models\Product', 'product_id');

    } 
    public function coi(){

        return $this->belongsTo('App\Models\ChartOfInventory', 'coi_id');

    }
}
