<?php

namespace App\Models;

use App\Traits\TracksDeletions;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SaleItem extends Model
{
    use HasFactory;

    use TracksDeletions;

    protected $guarded = ['id'];

    public function sale()
    {
        return $this->belongsTo(Sale::class,'sale_id');
    }

    public function coi(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(ChartOfInventory::class,'product_id','id');
    }
}
