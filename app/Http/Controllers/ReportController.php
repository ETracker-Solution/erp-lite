<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\Sale;
use App\Models\Purchase;
use App\Classes\Reports\ProfitLoss;
use App\Classes\Reports\AssetInfo;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function dailyReport()
    {
        $data = [
            'title' => "Report"
        ];

        $data['summary'] = ProfitLoss::today();
        $data['expense'] = Expense::whereDay('created_at', now()->day)->sum('amount');
        $data['purchases'] = Purchase::whereDay('created_at', now()->day)->sum('grand_total');

        return view('admin.report.daily-report', $data);
    }
    public function daterange()
    {
        $data = [
            'title' => "Report"
        ];

        return view('admin.report.date-range-report', $data);
    }

    public function fetchByDaterange(Request $request)
    {
        [$start, $end] = clampReportDateRange($request->start_date, $request->end_date, 366);
        $startDate = \Carbon\Carbon::parse($start)->startOfDay();
        $endDate = \Carbon\Carbon::parse($end)->startOfDay();
        if ($endDate->gt(\Carbon\Carbon::today())) {
            $endDate = \Carbon\Carbon::today();
        }

        $profitByDate = collect(ProfitLoss::profitLoss($startDate->format('Y-m-d'), $endDate->format('Y-m-d'), true))
            ->keyBy('date');

        $expenses = Expense::query()
            ->whereBetween('created_at', [$startDate->copy()->startOfDay(), $endDate->copy()->endOfDay()])
            ->selectRaw('DATE(created_at) as d, SUM(amount) as amount')
            ->groupBy('d')
            ->pluck('amount', 'd');

        $purchases = Purchase::query()
            ->whereBetween('created_at', [$startDate->copy()->startOfDay(), $endDate->copy()->endOfDay()])
            ->selectRaw('DATE(created_at) as d, SUM(grand_total) as amount')
            ->groupBy('d')
            ->pluck('amount', 'd');

        $data = [];
        for ($date = $startDate->copy(); $date->lte($endDate); $date->addDay()) {
            $key = $date->format('Y-m-d');
            $profitloss = $profitByDate->get($key) ?: (object) ProfitLoss::$default;
            $data[] = (object) [
                'date' => $key,
                'profitloss' => $profitloss,
                'expense' => $expenses[$key] ?? 0,
                'purchases' => $purchases[$key] ?? 0,
            ];
        }

        return response()->json($data);
    }

    public function daterangeSummery()
    {
        $data = [
            'title' => "Report"
        ];

        return view('admin.report.summary-report', $data);
    }
    public function profitLoss()
    {
        $data = [
            'title' => "Report"
        ];

        // dd(ProfitLoss::profitLoss(4));

        return view('admin.report.profit-loss', $data);
    }
    public function balanceSheet()
    {
        $data = [
            'title' => "Report"
        ];
        return view('admin.report.balance-sheet', $data);
    }
    public function balanceSheetData()
    {
        $data = [
            'supplier_due' => AssetInfo::supplier_due(),
            'customer_due' => AssetInfo::total_customer_due(),
            'closing_stock' => AssetInfo::sumStockAssets(),
        ];
        return response()->json($data);
    }
    public function stockReport()
    {
        $data = [
            'title' => "Report"
        ];
        return view('admin.report.stock-report', $data);
    }
    public function assetSummary() {
        dd(AssetInfo::sumStockAssets());
    }
    public function fetchTodaySummery()
    {
        $profitloss = ProfitLoss::today();

        $data['total_purchases'] = Purchase::whereDay('created_at', now()->day)->sum('grand_total');
        $data['total_sales'] = Sale::whereDay('created_at', now()->day)->sum('grand_total');
        $data['total_profits'] = is_null($profitloss->profitloss) ? 0 : $profitloss->profitloss; - is_null($profitloss->profitloss) ? 0 : $profitloss->profitloss;;
        $data['total_expenses'] = Expense::whereDay('created_at', now()->day)->sum('amount');

        return $data;
    }
    public function fetchYearSummery()
    {
        $profitloss = ProfitLoss::thisyear();

        $data['total_purchases'] = Purchase::whereYear('created_at', now()->year)->sum('grand_total');
        $data['total_sales'] = Sale::whereYear('created_at', now()->year)->sum('grand_total');
        $data['total_profits'] = is_null($profitloss->profitloss) ? 0 : $profitloss->profitloss;
        $data['total_expenses'] = Expense::whereYear('created_at', now()->year)->sum('amount');

        return $data;
    }
    public function fetchMonthSummery()
    {
        $profitloss = ProfitLoss::thismonth();

        $data['total_purchases'] = Purchase::whereMonth('created_at', now()->month)->sum('grand_total');
        $data['total_sales'] = Sale::whereMonth('created_at', now()->month)->sum('grand_total');
        $data['total_profits'] = is_null($profitloss->profitloss) ? 0 : $profitloss->profitloss;
        $data['total_expenses'] = Expense::whereMonth('created_at', now()->month)->sum('amount');

        return $data;
    }

}
