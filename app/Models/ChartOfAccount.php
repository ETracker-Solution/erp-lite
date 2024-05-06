<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChartOfAccount extends Model
{
    use HasFactory;

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
        return $this->hasMany(Transaction::class, 'chart_of_account_id');
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

}
