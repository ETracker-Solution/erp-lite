<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FundTransferVoucher extends Model
{
    use HasFactory;
    protected $guarded = ['id'];

    public function creditAccount(){
        return $this->belongsTo(ChartOfAccount::class,'credit_account_id');
    }
    public function debitAccount(){
        return $this->belongsTo(ChartOfAccount::class,'debit_account_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class,'created_by');
    }
}
