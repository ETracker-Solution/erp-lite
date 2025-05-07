<?php

namespace App\Models;

use App\Traits\TracksDeletions;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OthersOutletSaleItem extends VatScopedModel
{
    use HasFactory;

    use TracksDeletions;

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
