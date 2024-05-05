<?php

namespace App\Libraries;

use App\Models\StockIn;
use App\Models\StockOut;

class SaleUtil {



    public static function fifo_batch($product_id, $quantity) {
        $sold_qty = StockOut::where(['product_id'=>$product_id])->sum('quantity');;
        $stockin_history = StockIn::select('batch_number', 'quantity')->where(['product_id'=>$product_id])->get()->toArray();

        $batch_map = [];
        
        for ( $i = 0; $i < $quantity; $i++ ) {
            $batch_number = SaleUtil::fifo_batch_mapper($sold_qty+$i, $stockin_history);
            $key = array_search($batch_number, array_column($batch_map, 'batch_number'));

            if ($key === false) {
                array_push($batch_map, 
                    [
                        'batch_number' => $batch_number,
                        'quantity' => 1,
                    ]
                );
            } else {
                $batch_map[$key]['quantity']++;
            }
        }

        return $batch_map;
    }

    public static function fifo_batch_mapper($sold_qty, $stockin_history) {
        $sum = 0;
        $batch_number = null;
        
        foreach( $stockin_history as $row ) {
            $sum += $row['quantity'];

            if ($sum > $sold_qty) {
                $batch_number = $row['batch_number'];
                break;
            }
        }

        return $batch_number;
    }


}