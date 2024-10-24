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

        return $this->belongsTo('App\Models\Purchase', 'purchase_id');

    }
}
