<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OutletTransactionConfig extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function coa()
    {
        return $this->belongsTo(ChartOfAccount::class, 'coa_id', 'id');
    }
}
