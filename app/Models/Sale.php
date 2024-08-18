<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $appends = ['readable_sell_date_time'];

    public function items()
    {
        return $this->hasMany(SaleItem::class);
    }

    public function saleReturns()
    {
        return $this->hasMany(SaleReturn::class, 'sale_id');
    }

    public static function availableSales()
    {
        $sales = \App\Models\Sale::where(['status' => 'final'])->latest()->get();
        $available_sales = [];
        foreach ($sales as $row) {
            $quantity = 0;
            foreach ($row->saleReturns as $return) {
                $quantity += $return->items->sum('quantity');
            }
            if ($row->items->sum('quantity') > $quantity) {
                $available_sales[] = $row;
            }
        }
        return $available_sales;
    }

    public function availableItems()
    {
        foreach ($this->items as $key => $item) {
            $this->items[$key]->quantity -= SaleReturnItem::where('product_id', $item->product_id)
                ->where('sale_id', $this->id)
                ->sum('quantity');
        }
        return $this->items;
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }

    public function getReadableSellDateTimeAttribute()
    {
        return getTimeByFormat($this->created_at, 'F d, Y; h:i a');
    }

    public function outlet()
    {
        return $this->belongsTo(Outlet::class, 'outlet_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class,'created_by');
    }

    public function payments()
    {
        return $this->hasMany(Payment::class,'sale_id');
    }

    public function membershipPointHistory()
    {
        return $this->hasMany(MembershipPointHistory::class,'sale_id');
    }

    public function preOrder()
    {
        return $this->hasOne(PreOrder::class,'sale_id');
    }

    public function salesReturns()
    {
        return $this->hasMany(SalesReturn::class,'sale_id');
    }
}
