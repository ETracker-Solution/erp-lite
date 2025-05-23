<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
        $requisitions = \App\Models\Requisition::where(['from_store_id' => $store_id])->where(['type' => $type, 'status' => 'approved'])->whereIn('delivery_status', ['pending', 'partial'])->get();
        $available_requisitions = [];
        foreach ($requisitions as $requisition) {
            $quantity = 0;
            foreach ($requisition->deliveries as $delivery) {
                $quantity += $delivery->items->sum('quantity');
            }
            if ($requisition->items->sum('quantity') > $quantity) {
                $available_requisitions[] = $requisition;
            }
        }
        return $available_requisitions;
    }

    public function availableItems()
    {
        foreach ($this->items as $key => $item) {
            $this->items[$key]->quantity -= RequisitionDeliveryItem::where('coi_id', $item->coi_id)
                ->where('requisition_id', $this->id)
                ->sum('quantity');
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

    public static function todayFGAvailableRequisitions($to_factory_id)
    {
        $requisitions = Requisition::with(['deliveries.items','items'])
            ->where('to_factory_id', $to_factory_id)
            ->where('type', 'FG')
            ->where('status', 'approved')
            ->whereIn('delivery_status', ['pending', 'partial'])
            ->get();

        $available_requisitions = $requisitions->filter(function ($requisition) {
            $deliveredQuantity = $requisition->deliveries->flatMap(function ($delivery) {
                return $delivery->items;
            })->sum('quantity');

            return $requisition->items->sum('quantity') > $deliveredQuantity;
        });

// Return the available requisitions as an array
        return $available_requisitions->values()->all();
    }
}
