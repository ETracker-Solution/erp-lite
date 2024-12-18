<?php

namespace App\Models;

use App\Traits\TracksDeletions;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChartOfAccount extends Model
{
    use HasFactory;

    use TracksDeletions;

    protected $guarded = ['id'];

    public function subChartOfAccounts()
    {
        return $this->hasMany(self::class, 'parent_id');
    }

    public function childrens()
    {
        return $this->hasMany(self::class, 'parent_id');
    }

    public function childrensRecursive()
    {
        return $this->hasMany(self::class, 'parent_id');
    }

    public function transactions()
    {
        return $this->hasMany(AccountTransaction::class, 'chart_of_account_id');
    }

    public function currentAmountAs()
    {
        $debit = $this->transactions()->where('type', 'debit')->sum('amount');
        $credit = $this->transactions()->where('type', 'credit')->sum('amount');
        return $debit - $credit;
    }

    public function currentAmountLi()
    {
        $credit = $this->transactions()->where('type', 'credit')->sum('amount');
        $debit = $this->transactions()->where('type', 'debit')->sum('amount');
        return $credit - $debit;
    }

    public function currentAmountIn()
    {
        $debit = $this->transactions()->where('type', 'debit')->sum('amount');
        $credit = $this->transactions()->where('type', 'credit')->sum('amount');
        return $credit - $debit;
    }

    public function currentAmountEx()
    {
        $debit = $this->transactions()->where('type', 'debit')->sum('amount');
        $credit = $this->transactions()->where('type', 'credit')->sum('amount');
        return $debit - $credit;
    }

    public function totalDebitAmount()
    {
        return $this->transactions()->where('type', 'debit')->sum('amount');


    }

    public function totalCreditAmount()
    {
        return $this->transactions()->where('type', 'credit')->sum('amount');
    }

    public function parent()
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function outlets()
    {
        return $this->belongsToMany(Outlet::class,'outlet_accounts', 'coa_id', 'outlet_id');
    }

}
