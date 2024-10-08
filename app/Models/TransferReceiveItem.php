<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransferReceiveItem extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function coi()
    {
        return $this->belongsTo(ChartOfInventory::class, 'coi_id', 'id');
    }
}
