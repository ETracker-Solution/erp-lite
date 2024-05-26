<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SaleItem extends Model
{
    use HasFactory;
    protected $guarded = ['id'];

    public function product(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(ChartOfInventory::class,'product_id','id');
    }

    public function sale()
    {
        return $this->belongsTo(Sale::class,'sale_id');
    }
}
