<?php


namespace App\Classes;

use App\Models\Discount;

class AvailableDiscountCalculation
{
    public static function product_id($id)
    {
        $today = date('Y-m-d');
        $discount = Discount::where(['product_id'=>$id])->whereDate('start_date','<=', $today)
        ->whereDate('end_date','>=', $today)
        ->first();
    

        if ($discount){
            return $discount->discount;
        }else{
            return '0';
        }
    }
}
