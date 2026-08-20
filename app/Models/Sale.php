<?php

namespace App\Models;

use App\Traits\TracksDeletions;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Sale extends Model
{
    use HasFactory;
    use TracksDeletions;

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
        $sold = DB::table('sale_items')
            ->select('sale_id', DB::raw('SUM(quantity) as qty'))
            ->groupBy('sale_id');

        $returned = DB::table('sales_return_items')
            ->join('sales_returns', 'sales_returns.id', '=', 'sales_return_items.sales_return_id')
            ->select('sales_returns.sale_id', DB::raw('SUM(sales_return_items.quantity) as qty'))
            ->groupBy('sales_returns.sale_id');

        return static::query()
            ->where('status', 'final')
            ->leftJoinSub($sold, 'sold', 'sold.sale_id', '=', 'sales.id')
            ->leftJoinSub($returned, 'returned', 'returned.sale_id', '=', 'sales.id')
            ->whereRaw('COALESCE(sold.qty, 0) > COALESCE(returned.qty, 0)')
            ->select('sales.*')
            ->latest()
            ->get();
    }

    public function availableItems()
    {
        $returned = SalesReturnItem::query()
            ->select('coi_id', DB::raw('SUM(quantity) as returned_qty'))
            ->whereHas('salesReturn', function ($q) {
                $q->where('sale_id', $this->id);
            })
            ->groupBy('coi_id')
            ->pluck('returned_qty', 'coi_id');

        foreach ($this->items as $item) {
            $item->quantity -= $returned[$item->product_id] ?? 0;
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

    public function accountTransactions()
    {
        return $this->hasMany(AccountTransaction::class,'doc_id')->where('doc_type','POS');
    }
}
