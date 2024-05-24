<?php

namespace App\Classes;

use App\Models\Requisition;

class RequisitionNumber
{
    public static function serial_number()
    {
        $serial = self::getLastSerial() + 1;
        return date('y') .  str_pad($serial, 5, '0', STR_PAD_LEFT);
    }
    private static function getLastSerial()
    {
        return Requisition::max('id');

    }
}
