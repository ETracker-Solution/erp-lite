<?php

namespace App\Models;

use App\Traits\TracksDeletions;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PreOrder extends Model
{
    use HasFactory;

    use TracksDeletions;

    protected $guarded = ['id'];

    protected $appends = ['readable_sell_date_time'];

    public function items()
    {
        return $this->hasMany(PreOrderItem::class, 'pre_order_id');
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }

    public function outlet()
    {
        return $this->belongsTo(Outlet::class, 'outlet_id');
    }

    public function getReadableSellDateTimeAttribute()
    {
        return getTimeByFormat($this->created_at, 'F d, Y; h:i a');
    }

    public function sale()
    {
        return $this->belongsTo(Sale::class, 'sale_id');
    }

    public function attachments()
    {
        return $this->hasMany(PreOrderAttachment::class, 'pre_order_id');
    }

    public function deliveryPoint()
    {
        return $this->belongsTo(Outlet::class, 'delivery_point_id');
    }

    public function approvalInfo()
    {
        return $this->hasOne(PreOrderApprovalInfo::class, 'pre_order_id');
    }
}
