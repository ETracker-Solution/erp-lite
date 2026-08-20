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

        $salesQuery = DB::table('sales')
            ->whereBetween('date', [$start_date, $end_date]);

        $cogsQuery = DB::table('sale_items')
            ->join('sales', 'sales.id', '=', 'sale_items.sale_id')
            ->whereBetween('sales.date', [$start_date, $end_date]);

        if ($set_group_by) {
            $salesAgg = (clone $salesQuery)
                ->select(
                    'date',
                    DB::raw('SUM(grand_total) as total_grandtotal'),
                    DB::raw('SUM(discount) as total_discount')
                )
                ->groupBy('date')
                ->orderBy('date', 'desc')
                ->get();

            $cogsByDate = (clone $cogsQuery)
                ->select('sales.date', DB::raw('SUM(COALESCE(sale_items.cogs, 0)) as cogs'))
                ->groupBy('sales.date')
                ->pluck('cogs', 'date');

            return $salesAgg->map(function ($row) use ($cogsByDate) {
                $row->profitloss = ($row->total_grandtotal ?? 0) - ($cogsByDate[$row->date] ?? 0);
                return $row;
            })->all();
        }

        $salesAgg = (clone $salesQuery)
            ->select(
                DB::raw('GROUP_CONCAT(DISTINCT date SEPARATOR ",") AS date'),
                DB::raw('SUM(grand_total) as total_grandtotal'),
                DB::raw('SUM(discount) as total_discount')
            )
            ->first();

        $cogs = (clone $cogsQuery)
            ->selectRaw('SUM(COALESCE(sale_items.cogs, 0)) as cogs')
            ->value('cogs') ?? 0;

        if (!$salesAgg) {
            return [];
        }

        $salesAgg->profitloss = ($salesAgg->total_grandtotal ?? 0) - $cogs;

        return [$salesAgg];
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
