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
        return $this->hasMany(Delivery::class, 'requisition_id');
    }
    public static function availableRequisitions()
    {
        $requisitions = \App\Models\Requisition::where('production_house_id',auth('factory')->user()->production_house_id)->where('status','=','approved')->get();
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
            $this->items[$key]->quantity -= DeliveryItem::where('product_id', $item->product_id)
                ->where('requisition_id', $this->id)
                ->sum('quantity');
        }
        return $this->items;
    }

//    public static function availableRequisitions()
//    {
//        $requisitions = \App\Models\Requisition::where(['status'=>'approved'])->get();
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
//    public function availableItems()
//    {
//        foreach ($this->items as $key => $item) {
//            $this->items[$key]->quantity -= DeliveryItem::where('product_id', $item->product_id)
//                ->where('requisition_id', $this->id)
//                ->sum('quantity');
//        }
//        return $this->items;
//    }

    public function createdBy(){
        return $this->morphTo('created_by','created_type','created_id');
    }
    public function outlet(){
        return $this->morphTo('outlet','model_type','model_id');
    }
    public function productionHouse(){
        return $this->belongsTo(ProductionHouse::class);
    }
    public function store(){
        return $this->belongsTo(Store::class);
    }
}
