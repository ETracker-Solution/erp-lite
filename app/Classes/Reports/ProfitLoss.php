<?php

namespace App\Classes\Reports;

use Illuminate\Support\Facades\DB;

class ProfitLoss {

    public static $default = [
                'total_grandtotal' => 0,
                'total_discount' => 0,
                'profitloss' => 0,
            ];

    public static function profitLoss($start_date, $end_date, $set_group_by = true) {

        $group_by = $set_group_by ? 'GROUP BY sales.date' : '';
        $attributes = $set_group_by ? 'sales.date' : 'GROUP_CONCAT(DISTINCT sales.date SEPARATOR ",") AS date';

        $sql = "SELECT
                    $attributes,
                    SUM(sales.grand_total) AS total_grandtotal,
                    SUM(sales.discount) AS total_discount,
                    SUM((sale_items.sale_price - purchase_items.buying_price) * sale_items.quantity) AS profitloss
                FROM sales
                LEFT JOIN sale_items ON sale_items.sale_id = sales.id
                LEFT JOIN purchase_items ON purchase_items.batch_number = sale_items.batch_number AND purchase_items.product_id = sale_items.product_id
                WHERE sales.date BETWEEN '$start_date' AND '$end_date'
                AND sale_items.deleted_at IS NULL
                AND sales.deleted_at IS NULL
                $group_by
                ORDER BY sales.created_at DESC";

        $result = DB::select($sql);

        return $result;
    }

    static function today() {

        $result = self::profitLoss(date('Y-m-d'), date('Y-m-d'));

        if (count($result) > 0) {
            return $result[0];
        } else {
            return (Object) self::$default;
        }
    }

    static function date($date) {

        if ( !strtotime($date) ) {
            return (Object) self::$default;
        }

        $result = self::profitLoss($date, $date);

        if (count($result) > 0) {
            return $result[0];
        } else {
            return (Object) self::$default;
        }
    }

    static function daterange($start_date, $end_date, $mode) {

        if ( !strtotime($start_date) || !strtotime($end_date) ) {
            return (Object) self::$default;
        }

        $result = self::profitLoss($start_date, $end_date, $mode);

        if (count($result) > 0 && !$mode) {
            return $result[0];
        } else if (count($result) > 0 && $mode) {
            return $result;
        } else {
            return (Object) self::$default;
        }
    }

    static function thismonth($mode = false) {

        $result = self::profitLoss(date('Y-m-01'), date('Y-m-d'), $mode);

        if ( count($result) > 0  && !$mode) {
            return $result[0];
        } else if ( count($result) > 0 && $mode) {
            return $result;
        } else {
            return (Object) self::$default;
        }
    }

    static function thisyear($mode = false) {

        $result = self::profitLoss(date('Y-01-01'), date('Y-m-d'), $mode);

        if ( count($result) > 0  && !$mode) {
            return $result[0];
        } else if ( count($result) > 0 && $mode) {
            return $result;
        } else {
            return (Object) self::$default;
        }
    }
}
