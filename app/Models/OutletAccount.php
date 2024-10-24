<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OutletAccount extends Model
{
    use HasFactory;
    protected $guarded = ['id'];

    public function outlet()
    {
        return $this->belongsTo(Outlet::class);
    }

    public function coa()
    {
        return $this->belongsTo(ChartOfAccount::class);
    }
}
