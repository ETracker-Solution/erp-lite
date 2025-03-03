<?php

namespace App\Models;

use App\Traits\TracksDeletions;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class ChartOfAccount extends Model
{
    use HasFactory;

    use TracksDeletions;

    protected $guarded = ['id'];

    protected $appends = ['balance','total_balance'];

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
        return $this->childrens()->with('childrensRecursive')->orderBy('name', 'asc');
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

    public function getBalanceAttribute()
    {
        return $this->transactions()->sum(DB::raw("amount * transaction_type"));
    }

    public function getTotalBalanceAttribute()
    {
        // Sum transactions of the current account with the CASE statement for adjusting transaction_type
        $balance = $this->transactions()
            ->join('chart_of_accounts as coa', 'coa.id', '=', 'account_transactions.chart_of_account_id')
            ->select(DB::raw(
                "SUM(CASE WHEN (account_transactions.transaction_type = -1 AND coa.root_account_type = 'li') THEN (account_transactions.transaction_type * -1) ELSE account_transactions.transaction_type END * account_transactions.amount) as balance"
            ))
            ->value('balance');

        // Get all nested child account IDs
        $childIds = $this->getAllChildIds();

        if ($this->name === 'Retained Earning') {
            $balance += $this->calculateRetainedEarnings();
        }

        // If there are child accounts, sum their balances in ONE query with the CASE statement
        if (!empty($childIds)) {
            $childBalance = DB::table('account_transactions as att')
                ->join('chart_of_accounts as coa', 'coa.id', '=', 'att.chart_of_account_id')
                ->whereIn('att.chart_of_account_id', $childIds)
                ->select(DB::raw(
                    "SUM(CASE WHEN (att.transaction_type = -1 AND coa.root_account_type = 'li') THEN (att.transaction_type * -1) ELSE att.transaction_type END * att.amount) as balance"
                ))
                ->value('balance'); // Getting the result as a single value

            $balance += $childBalance;
        }
        if (in_array('53',$childIds)){
            $balance += $this->calculateRetainedEarnings();
        }

        return $balance;
    }


    /**
     * Get all child account IDs in one query.
     */
    public function getAllChildIds()
    {
        $childIds = $this->childrens()->pluck('id')->toArray();

        // Recursively get all children's child IDs
        foreach ($this->childrens as $child) {
            $childIds = array_merge($childIds, $child->getAllChildIds());
        }

        return $childIds;
    }

    public function calculateRetainedEarnings()
    {
        return AccountTransaction::join('chart_of_accounts as coa', 'coa.id', '=', 'account_transactions.chart_of_account_id')
            ->whereIn('coa.root_account_type', ['in', 'ex'])
            ->selectRaw('SUM(account_transactions.transaction_type * account_transactions.amount) * -1 as profit')
            ->value('profit') ;
    }

}
