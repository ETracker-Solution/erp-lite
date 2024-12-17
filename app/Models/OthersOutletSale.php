<?php

namespace App\Models;

use App\Traits\TracksDeletions;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OthersOutletSale extends Model
{
    use HasFactory;

    use TracksDeletions;
    protected $guarded = ['id'];
    protected $appends = ['readable_sell_date_time'];

    public function items()
    {
        return $this->hasMany(OthersOutletSaleItem::class);
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
        return $this->hasOne(MembershipPointHistory::class,'sale_id');
    }

    public function deliveryPoint()
    {
        return $this->belongsTo(Outlet::class,'delivery_point_id');
    }
}
