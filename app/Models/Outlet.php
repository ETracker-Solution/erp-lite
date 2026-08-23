<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Outlet extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function stores()
    {
        return $this->hasMany(Store::class, 'doc_id')->where('doc_type', 'outlet');
    }

    public function requisitions()
    {
        return $this->hasMany(Requisition::class, 'outlet_id');
    }

    public function outletAccounts()
    {
        return $this->hasMany(OutletAccount::class);
    }

    public function transactionConfigs()
    {
        return $this->hasMany(OutletTransactionConfig::class);
    }

    public function accounts()
    {
        return $this->belongsToMany(ChartOfAccount::class, 'outlet_accounts', 'outlet_id', 'coa_id');
    }

    public function preOrders()
    {
        return $this->hasMany(PreOrder::class, 'outlet_id');
    }
}
