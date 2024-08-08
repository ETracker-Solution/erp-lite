<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SalesReturnItem extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function coi()
    {

        return $this->belongsTo('App\Models\ChartOfInventory', 'coi_id');

    }
}
