<?php

namespace App\Models;

use App\Traits\TracksDeletions;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InventoryTransaction extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    use TracksDeletions;

    public function chartOfInventory()
    {
        return $this->belongsTo(ChartOfInventory::class, 'coi_id', 'id');
    }

     public function productions()
    {
        return $this->belongsTo(Production::class, 'doc_id', 'id');
    }
}
