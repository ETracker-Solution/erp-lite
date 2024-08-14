<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\AccountTransaction;
use App\Models\ChartOfAccount;
use App\Models\ChartOfInventory;
use App\Models\Customer;
use App\Models\Expense;
use App\Models\InventoryAdjustment;
use App\Models\InventoryTransaction;
use App\Models\Outlet;
use App\Models\PreOrder;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\Requisition;
use App\Models\Sale;
use App\Models\Store;
use App\Repository\Interfaces\AdminInterface;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class AdminDashboardController extends Controller
{

    protected $adminRepo;

    // public function __construct(AdminInterface $admin)
    // {
    //     $this->adminRepo = $admin;
    // }

    public function adminDashboard()
    {
        $total_sales = Sale::whereDate('created_at', date('Y-m-d'))->sum('grand_total');
        $outlets = Outlet::whereStatus('active')->count();
        $customers = Customer::where('type', 'regular')->count();
        $wastage_amount = InventoryAdjustment::sum('subtotal');
        $products = ChartOfInventory::where('type', 'item')->where('rootAccountType', 'FG')->count();

        $todayRequisitions = Requisition::whereType('FG')->whereDate('created_at', Carbon::today())->get();

        $year = Carbon::now()->month == 1 ? Carbon::now()->subYear()->year : Carbon::now()->year;
        $lastMonth = Carbon::now()->subMonth();

        $lastMonthExpense = AccountTransaction::with('chartOfAccount')->whereHas('chartOfAccount', function ($query) {
            $query->where(['root_account_type' => 'ex']);
        })->whereYear('date', $year)->whereMonth('date', $lastMonth->month)->sum('amount');

        $currentMonthExpense = AccountTransaction::with('chartOfAccount')->whereHas('chartOfAccount', function ($query) {
            $query->where(['root_account_type' => 'ex']);
        })->whereYear('date', Carbon::now()->year)->whereMonth('date', Carbon::now()->month)->sum('amount');
        $expenseMessage = 'No Expense Added';
        if ($lastMonthExpense === 0) {
            $expensePercentage = 100;
        } else {
            $expensePercentage = (100 / $lastMonthExpense) * $currentMonthExpense;
        }
        if ($currentMonthExpense > $lastMonthExpense) {
            $expenseMessage = ($currentMonthExpense - $lastMonthExpense) . ' BDT more than last month';
        } elseif ($currentMonthExpense < $lastMonthExpense) {
            $expenseMessage = ($currentMonthExpense - $lastMonthExpense) . ' BDT less than last month';
        }


        $currentDate = Carbon::now();
        $startDate = $currentDate->copy()->subMonth()->startOfMonth();
        $endDate = $currentDate->copy()->subMonth()->endOfMonth();
        $noOfWeeks = $currentDate->copy()->subMonth()->weekOfMonth;
        $totalDays = $startDate->daysInMonth;

        // Calculate the number of days in each part
        $daysPerPart = ceil($totalDays / $noOfWeeks);

        // Initialize an array to store sales data for each part
        $parts = [];

        // Iterate over each part
        for ($part = 0; $part < $noOfWeeks; $part++) {
            // Calculate the start and end dates of the current part
            $partStartDate = $startDate->copy()->addDays($part * $daysPerPart);
            $partEndDate = $startDate->copy()->addDays(($part + 1) * $daysPerPart - 1)->endOfDay();

            if ($partEndDate->gt($endDate)) {
                $partEndDate = $endDate->copy()->endOfDay();
            }
            $salesTotal = Sale::whereBetween('created_at', [$partStartDate, $partEndDate])
                ->sum('grand_total');
            $parts[] = $salesTotal;
        }

        $totalDiscountThisMonth = Sale::whereYear('created_at', $currentDate->year)->whereMonth('created_at', $currentDate->month)->sum('discount');
        $totalDiscountLastMonth = Sale::whereYear('created_at', $year)->whereMonth('created_at', $lastMonth->month)->sum('discount');

        if ($totalDiscountLastMonth === 0) {
            $discountPercentage = 100;
        } else {
            $discountPercentage = (100 / $totalDiscountLastMonth) * $totalDiscountThisMonth;
        }

        $outletWiseDiscount = [];
        $allOutlets = Outlet::all();

        foreach ($allOutlets as $ol) {
            $outletWiseDiscount['outletName'][] = $ol->name;
            $outletWiseDiscount['discount'][] = Sale::whereYear('created_at', $currentDate->year)->whereMonth('created_at', $currentDate->month)->where('outlet_id', $ol->id)->sum('discount');
        }

        $productWiseStock = [];
        $productWiseStock['products'] = [];
        $productWiseStock['stock'] = [];
        $totalStock = 0;
        $allProducts = Outlet::select('name', 'id')->get();
        foreach ($allProducts as $product) {
            $stock = inventoryAmount($product->id);
            $productWiseStock['products'][] = $product->name;
            $productWiseStock['stock'][] = $stock;
            $totalStock += $stock;
        }

        $outletWiseExpense = [];
        $outletWiseOrders = [];
        $totalExpense = 0;
        $totalOrders = 0;
        $allOutlets = Outlet::all();

        foreach ($allOutlets as $outlet) {
            // $expense = Expense::whereYear('created_at', $currentDate->year)->whereMonth('created_at', $currentDate->month)->where('expenseable_type', Outlet::class)->where('expenseable_id', $ol->id)->sum('amount');
            // $outletWiseExpense['outletName'][] = $ol->name;
            // $outletWiseExpense['expense'][] = $expense;
            // $totalExpense += $expense;
            $order = PreOrder::whereYear('created_at', $currentDate->year)->whereMonth('created_at', $currentDate->month)->where('outlet_id', $outlet->id)->count();
            $outletWiseOrders[$outlet->name] = $order;
            $totalOrders += $order;
        }

        $outletWiseSales = [];
        $totalSales = 0;
        $all_dates_till_today = CarbonPeriod::create(Carbon::now()->startOfMonth(), Carbon::now())->toArray();
        foreach ($all_dates_till_today as $item) {
            $sale = Sale::whereDate('created_at', $item->format('Y-m-d'))->sum('grand_total');
            $outletWiseSales['sales'][] = $sale;
            $outletWiseSales['date'][] = $item->isoFormat('Do');
            $totalSales += $sale;
        }

        // return $requisitions = Requisition::get();

        if (\request()->ajax()) {
            $requisitions = Requisition::all();
            return DataTables::of($requisitions)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    if ($row->type == 'FG') {
                        return view('requisition.action', compact('row'));
                    } else {
                        return view('rm_requisition.action', compact('row'));
                    }
                })
                ->editColumn('status', function ($requisition) {
                    return showStatus($requisition->status);
                })
                ->addColumn('created_at', function ($requisition) {
//                    return $requisition->created_at->format('Y-m-d');
                })
                ->rawColumns(['status', 'action'])
                ->make(true);
        }

        $customersWithPoint = Customer::whereHas('membership')->with('membership', 'sales')->get();

        $todaySale = Sale::whereDate('created_at', Carbon::now()->format('Y-m-d'))->sum('grand_total');
        $todayPurchase = Purchase::whereDate('date', Carbon::now()->format('Y-m-d'))->sum('net_payable');
        // $todayExpense = Expense::whereDate('date',Carbon::now()->format('Y-m-d'))->sum('amount');
        // $todayExpense = 0;

        $bestProducts = [];
        $bestSellingProducts = ChartOfInventory::select('chart_of_inventories.*', DB::raw('SUM(sale_items.quantity) as total_sold'))
            ->join('sale_items', 'chart_of_inventories.id', '=', 'sale_items.product_id')
            ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
            ->whereYear('sales.created_at', '=', now()->year)
            ->whereMonth('sales.created_at', '=', now()->month)
            ->groupBy('chart_of_inventories.id')
            ->orderByDesc('total_sold')
            ->get();

        $slowSellingProducts = ChartOfInventory::where(['type'=>'item','rootAccountType'=>'FG'])->select('chart_of_inventories.*', DB::raw('COALESCE(SUM(sale_items.quantity), 0) as total_sold'))
            ->leftJoin('sale_items', function ($join) {
                $join->on('chart_of_inventories.id', '=', 'sale_items.product_id')
                    ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
                    ->whereYear('sales.created_at', '=', now()->year)
                    ->whereMonth('sales.created_at', '=', now()->month);
            })
            ->groupBy('chart_of_inventories.id')
            ->orderBy('total_sold')
            ->limit(5)
            ->get();

        foreach ($bestSellingProducts as $product) {
            $bestProducts['name'][] = $product->name;
            $bestProducts['qty'][] = $product->total_sold;
        }

        $data = [
            'totalSales' => $total_sales,
            'outlets' => $outlets,
            'customers' => $customers,
            'products' => $products,
            'wastageAmount' => round($wastage_amount),
            'lastMonthExpense' => $lastMonthExpense,
            'expensePercentage' => round($expensePercentage, 2),
            'currentMonthExpense' => $currentMonthExpense,
            'expenseMessage' => $expenseMessage,
            'lastMonthSales' => $parts,
            'todayRequisitions' => $todayRequisitions,
            'discount' => [
                'thisMonth' => $totalDiscountThisMonth,
                'lastMonth' => $totalDiscountLastMonth,
                'percentage' => $discountPercentage,
                'outletWiseDiscount' => $outletWiseDiscount
            ],
            'stock' => [
                'total' => $totalStock,
                'productWise' => $productWiseStock
            ],
            'expense' => [
                'total' => $totalExpense,
                'outletWise' => $outletWiseExpense
            ],
            'sales' => [
                'total' => $totalSales,
                'outletWise' => $outletWiseSales
            ],
            'order' => [
                'total' => $totalOrders,
                'outletWise' => $outletWiseOrders
            ],
            'customersWithPoint' => $customersWithPoint,
            'todaySale' => $todaySale,
            'todayPurchase' => $todayPurchase,
            // 'todayExpense' => $todayExpense,
            'bestSellingProducts' => $bestProducts,
            'slowSellingProducts' => $slowSellingProducts,
        ];
//        return $data;
        return view('dashboard.admin', $data);
    }

//     protected function getReq()
//     {
//         $q = DB::select(
//             "select id,uid, status,date,created_at, 'FG' as type
// from requisitions
// UNION ALL
// select id,uid, status,date,created_at, 'RM' as type
// from requisitions"
//         );
//         return $requisitions = collect($q)->sortByDesc('created_at')->values()->all();

//     }
}
