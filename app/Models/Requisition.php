<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Requisition extends Model
{
    use HasFactory;

    protected $guarded = ['id'];


    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function items()
    {
        return $this->hasMany(RequisitionItem::class);
    }

    public function deliveries()
    {
        return $this->hasMany(RequisitionDelivery::class, 'requisition_id');
    }

//    public static function availableRequisitions()
//    {
//        $requisitions = \App\Models\Requisition::where('production_house_id', auth('factory')->user()->production_house_id)->where('status', '=', 'approved')->get();
//        $available_requisitions = [];
//        foreach ($requisitions as $requisition) {
//            $quantity = 0;
//            foreach ($requisition->deliveries as $delivery) {
//                $quantity += $delivery->items->sum('quantity');
//            }
//            if ($requisition->items->sum('quantity') > $quantity) {
//                $available_requisitions[] = $requisition;
//            }
//        }
//        return $available_requisitions;
//    }
//
//    public function availableItems()
//    {
//        foreach ($this->items as $key => $item) {
//            $this->items[$key]->quantity -= DeliveryItem::where('product_id', $item->product_id)
//                ->where('requisition_id', $this->id)
//                ->sum('quantity');
//        }
//        return $this->items;
//    }

    public static function availableRequisitions($type, $store_id)
    {
        return static::availableRequisitionsQuery()
            ->where(['from_store_id' => $store_id, 'type' => $type, 'status' => 'approved'])
            ->whereIn('delivery_status', ['pending', 'partial'])
            ->get();
    }

    public function availableItems()
    {
        $this->loadMissing('items');
        $delivered = RequisitionDeliveryItem::query()
            ->select('coi_id', DB::raw('SUM(quantity) as qty'))
            ->where('requisition_id', $this->id)
            ->groupBy('coi_id')
            ->pluck('qty', 'coi_id');

        foreach ($this->items as $item) {
            $item->quantity -= $delivered[$item->coi_id] ?? 0;
        }
        return $this->items;
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function outlet()
    {
        return $this->belongsTo(Outlet::class, 'outlet_id');
    }

    public function productionHouse()
    {
        return $this->belongsTo(ProductionHouse::class);
    }

    public function fromStore()
    {
        return $this->belongsTo(Store::class, 'from_store_id');
    }

    public function toStore()
    {
        return $this->belongsTo(Store::class, 'to_store_id');
    }

    public static function availableRequisitionsQuery()
    {
        $delivered = DB::table('requisition_delivery_items as rdi')
            ->join('requisition_deliveries as rd', 'rd.id', '=', 'rdi.requisition_delivery_id')
            ->select('rd.requisition_id', DB::raw('SUM(rdi.quantity) as qty'))
            ->groupBy('rd.requisition_id');

        $requested = DB::table('requisition_items')
            ->select('requisition_id', DB::raw('SUM(quantity) as qty'))
            ->groupBy('requisition_id');

        return static::query()
            ->leftJoinSub($requested, 'req', 'req.requisition_id', '=', 'requisitions.id')
            ->leftJoinSub($delivered, 'del', 'del.requisition_id', '=', 'requisitions.id')
            ->whereRaw('COALESCE(req.qty, 0) > COALESCE(del.qty, 0)')
            ->select('requisitions.*');
    }

    public static function todayFGAvailableRequisitions($to_factory_id)
    {
        return static::availableRequisitionsQuery()
            ->where('to_factory_id', $to_factory_id)
            ->where('type', 'FG')
            ->where('status', 'approved')
            ->whereIn('delivery_status', ['pending', 'partial'])
            ->get()
            ->all();
    }
}
