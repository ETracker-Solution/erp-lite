<?php

namespace App\Services;

use App\Models\ChartOfInventory;
use App\Models\Customer;
use App\Models\InventoryAdjustment;
use App\Models\Outlet;
use App\Models\PreOrder;
use App\Models\Requisition;
use App\Models\Sale;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class AdminDashboardService
{
    private const CACHE_SECONDS = 60;

    public function getData(): array
    {
        $today = Carbon::today();
        $todayStr = $today->toDateString();

        $todayMetrics = $this->todayMetrics($todayStr);
        $cached = Cache::remember("admin_dashboard:v2:{$todayStr}", self::CACHE_SECONDS, function () use ($today) {
            return $this->cachedMetrics($today);
        });

        return array_merge($todayMetrics, $cached, $this->actionLists($todayStr));
    }

    private function todayMetrics(string $today): array
    {
        $outletSales = Sale::query()
            ->join('outlets', 'sales.outlet_id', '=', 'outlets.id')
            ->where('sales.status', 'final')
            ->where('sales.date', $today)
            ->select(
                'sales.outlet_id',
                'outlets.name',
                DB::raw('COUNT(*) as invoice_count'),
                DB::raw('COALESCE(SUM(sales.grand_total), 0) as total_sales'),
                DB::raw('COALESCE(SUM(sales.discount), 0) as total_discount')
            )
            ->groupBy('sales.outlet_id', 'outlets.name')
            ->get()
            ->keyBy('outlet_id');

        $activeOutlets = Cache::remember('admin_dashboard:active_outlets', 300, function () {
            return Outlet::where('status', 'active')->orderBy('name')->get(['id', 'name']);
        });

        $totalSales = (float) $outletSales->sum('total_sales');
        $todayInvoice = (int) $outletSales->sum('invoice_count');
        $todayDiscount = (float) $outletSales->sum('total_discount');

        $todaySalesByOutlet = $activeOutlets->map(function ($outlet) use ($outletSales, $totalSales) {
            $row = $outletSales->get($outlet->id);
            $total = (float) ($row->total_sales ?? 0);

            return [
                'name' => $outlet->name,
                'total' => $total,
                'invoices' => (int) ($row->invoice_count ?? 0),
                'discount' => (float) ($row->total_discount ?? 0),
                'share' => $totalSales > 0 ? round(($total / $totalSales) * 100, 1) : 0.0,
            ];
        })->sortByDesc('total')->values();

        $wastage = InventoryAdjustment::query()
            ->where('date', $today)
            ->where('transaction_type', 'decrease')
            ->sum('subtotal');

        return [
            'totalSales' => $totalSales,
            'todayInvoice' => $todayInvoice,
            'todayDiscount' => $todayDiscount,
            'wastageAmount' => round((float) $wastage),
            'todaySalesByOutlet' => $todaySalesByOutlet,
            'outletsWithSalesToday' => $todaySalesByOutlet->where('total', '>', 0)->count(),
            'outletsWithNoSalesToday' => $todaySalesByOutlet->where('total', 0)->count(),
        ];
    }

    private function cachedMetrics(Carbon $today): array
    {
        $todayStr = $today->toDateString();
        $monthStart = $today->copy()->startOfMonth()->toDateString();
        $monthEnd = $today->toDateString();
        $lastMonthStart = $today->copy()->subMonthNoOverflow()->startOfMonth()->toDateString();
        $lastMonthEnd = $today->copy()->subMonthNoOverflow()->endOfMonth()->toDateString();
        $trendStart = $today->copy()->subDays(6)->toDateString();

        $monthSales = Sale::query()
            ->where('status', 'final')
            ->whereBetween('date', [$lastMonthStart, $monthEnd])
            ->selectRaw('
                COALESCE(SUM(CASE WHEN date BETWEEN ? AND ? THEN grand_total ELSE 0 END), 0) as this_month,
                COALESCE(SUM(CASE WHEN date BETWEEN ? AND ? THEN grand_total ELSE 0 END), 0) as last_month
            ', [$monthStart, $monthEnd, $lastMonthStart, $lastMonthEnd])
            ->first();

        $thisMonthSales = (float) ($monthSales->this_month ?? 0);
        $lastMonthSales = (float) ($monthSales->last_month ?? 0);
        $monthChangePct = $lastMonthSales > 0
            ? round((($thisMonthSales - $lastMonthSales) / $lastMonthSales) * 100, 1)
            : ($thisMonthSales > 0 ? 100.0 : 0.0);

        $trendRows = Sale::query()
            ->where('status', 'final')
            ->whereBetween('date', [$trendStart, $todayStr])
            ->selectRaw('date, COALESCE(SUM(grand_total), 0) as total')
            ->groupBy('date')
            ->pluck('total', 'date');

        $salesTrend = ['labels' => [], 'values' => []];
        for ($i = 6; $i >= 0; $i--) {
            $day = $today->copy()->subDays($i)->toDateString();
            $salesTrend['labels'][] = Carbon::parse($day)->format('M d');
            $salesTrend['values'][] = (float) ($trendRows[$day] ?? 0);
        }

        $preOrderStats = PreOrder::query()
            ->where('status', 'pending')
            ->selectRaw('
                SUM(CASE WHEN delivery_date = ? THEN 1 ELSE 0 END) as due_today,
                SUM(CASE WHEN delivery_date < ? THEN 1 ELSE 0 END) as overdue,
                COUNT(*) as pending_total,
                SUM(CASE WHEN delivery_date BETWEEN ? AND ? THEN 1 ELSE 0 END) as due_this_week
            ', [
                $todayStr,
                $todayStr,
                $todayStr, $today->copy()->addDays(6)->toDateString(),
            ])
            ->first();

        $pendingRequisitions = Requisition::query()
            ->where('type', 'FG')
            ->whereIn('status', ['pending', 'approved'])
            ->whereIn('delivery_status', ['pending', 'partial'])
            ->count();

        $staticCounts = Cache::remember('admin_dashboard:static_counts', 300, function () {
            return [
                'outlets' => Outlet::where('status', 'active')->count(),
                'customers' => Customer::where('type', 'regular')->count(),
                'products' => ChartOfInventory::where('type', 'item')->where('rootAccountType', 'FG')->count(),
            ];
        });

        return [
            'outlets' => $staticCounts['outlets'],
            'customers' => $staticCounts['customers'],
            'products' => $staticCounts['products'],
            'monthSales' => $thisMonthSales,
            'lastMonthSales' => $lastMonthSales,
            'monthChangePct' => $monthChangePct,
            'salesTrend' => $salesTrend,
            'preOrderDueToday' => (int) ($preOrderStats->due_today ?? 0),
            'preOrderOverdue' => (int) ($preOrderStats->overdue ?? 0),
            'preOrderPending' => (int) ($preOrderStats->pending_total ?? 0),
            'preOrderDueThisWeek' => (int) ($preOrderStats->due_this_week ?? 0),
            'pendingRequisitions' => $pendingRequisitions,
            'cacheRefreshedAt' => now()->format('h:i A'),
        ];
    }

    private function actionLists(string $today): array
    {
        $preOrdersDueToday = PreOrder::query()
            ->select(['id', 'order_number', 'delivery_date', 'delivery_time', 'status', 'grand_total', 'customer_id', 'delivery_point_id'])
            ->with([
                'customer:id,name,mobile',
                'deliveryPoint:id,name',
            ])
            ->where('status', 'pending')
            ->where('delivery_date', $today)
            ->orderBy('delivery_time')
            ->limit(5)
            ->get();

        $overduePreOrders = PreOrder::query()
            ->select(['id', 'order_number', 'delivery_date', 'status', 'grand_total', 'customer_id', 'delivery_point_id'])
            ->with([
                'customer:id,name',
                'deliveryPoint:id,name',
            ])
            ->where('status', 'pending')
            ->where('delivery_date', '<', $today)
            ->orderBy('delivery_date')
            ->limit(5)
            ->get();

        $pendingRequisitionRows = Requisition::query()
            ->select(['id', 'uid', 'date', 'status', 'delivery_status', 'from_store_id', 'created_at'])
            ->with('fromStore:id,name')
            ->where('type', 'FG')
            ->whereIn('status', ['pending', 'approved'])
            ->whereIn('delivery_status', ['pending', 'partial'])
            ->latest()
            ->limit(5)
            ->get();

        return [
            'preOrdersDueToday' => $preOrdersDueToday,
            'overduePreOrders' => $overduePreOrders,
            'pendingRequisitionRows' => $pendingRequisitionRows,
        ];
    }
}
