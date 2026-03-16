<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SupplierTransaction extends Model
{
    use HasFactory;
    protected $guarded = ['id'];

    public function purchase()
    {
        return $this->belongsTo(\App\Models\Purchase::class, 'doc_id', 'id');
    }
    public function document()
    {
        return $this->morphTo(null, 'doc_type', 'doc_id');
    }
    public function chartOfAccount()
    {
        return $this->belongsTo(ChartOfAccount::class);
    }
}
