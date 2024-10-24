<?php


namespace App\Classes;

use App\Models\Purchase;

class BatchNumber
{
    public static function serial_number()
    {
        do {
            $batch_number = date('m') . mt_rand(100000, 999999);
        } while (Purchase::where("batch_number", $batch_number)->first());

        return $batch_number;
    }
}
