<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SalesReturnItem extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $table = 'sales_return_items';

    public function coi()
    {

        return $this->belongsTo('App\Models\ChartOfInventory', 'coi_id');

    }

    public function salesReturn()
    {
        return $this->belongsTo(SalesReturn::class, 'sales_return_id');
    }
}
