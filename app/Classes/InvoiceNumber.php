<?php


namespace App\Classes;

use App\Models\Order;
use App\Models\Sale;
use Illuminate\Support\Str;

class InvoiceNumber
{
    public static function serial_number()
    {
        $serial = self::count_last_serial_for_training() + 1;
        return $serial_number = date('Y') . date('m') . str_pad($serial, 3, '0', STR_PAD_LEFT);

    }
    private static function count_last_serial_for_training()
    {
        return Order::whereYear('created_at', date('Y'))
            ->whereMonth('created_at', date('m'))
            ->count();
    }

    public static function generateInvoiceNumber($outlet_id)
    {
        $prefix = date('y').str_pad($outlet_id,2,'0',STR_PAD_LEFT);
        return  $prefix.(new InvoiceNumber)->getLastInvoiceSerial($prefix);
    }

    public function getLastInvoiceSerial($prefix)
    {
        $old =  Sale::where('invoice_number','like',$prefix.'%')->first();
        $new_id = 0;
        if($old){
            $new_id = substr($old->invoice_number,-($this->lastPartCount()));
            $new_id = (int) $new_id;
        }
        return str_pad($new_id +1,$this->lastPartCount(),'0',STR_PAD_LEFT);
    }

    public function lastPartCount()
    {
        return 5;
    }
}
