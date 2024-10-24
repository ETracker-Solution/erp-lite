<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OthersOutletSaleItem extends Model
{
    use HasFactory;
    
    protected $guarded = ['id'];

    public function otherOutletSale()
    {
        return $this->belongsTo(OthersOutletSale::class,'others_outlet_sale_id');
    }

    public function coi(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(ChartOfInventory::class,'product_id','id');
    }
}
