<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SupplierVoucher extends Model
{
    use HasFactory;
    protected $guarded = ['id'];

    public function creditAccount(){
        return $this->belongsTo(ChartOfAccount::class,'credit_account_id','id');
    }
    public function debitAccount(){
        return $this->belongsTo(ChartOfAccount::class,'debit_account_id','id');
    }
    public function supplier(){
        return $this->belongsTo(Supplier::class);
    }
}
