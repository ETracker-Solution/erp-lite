<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GeneralLedgerOpeningBalance extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function chartOfAccount(){
        return $this->belongsTo(ChartOfAccount::class,'coia_id');
    }

}
