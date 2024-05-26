<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RequisitionDeliveryItem extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function requisition(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Requisition::class);
    }

    public function coi(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(ChartOfInventory::class, 'coi_id');
    }
}
