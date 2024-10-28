<?php

namespace App\Http\Controllers;

use App\Models\AccountTransaction;
use App\Models\ChartOfInventory;
use App\Models\Customer;
use App\Models\InventoryAdjustment;
use App\Models\Outlet;
use App\Models\Purchase;
use App\Models\Requisition;
use App\Models\Sale;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class AdminDashboardController extends Controller
{
    public function adminDashboard()
    {
        $total_sales = Sale::whereDate('created_at', date('Y-m-d'))->sum('grand_total');
        $outlets = Outlet::whereStatus('active')->count();
        $customers = Customer::where('type', 'regular')->count();
        $wastage_amount = InventoryAdjustment::whereDate('created_at', date('Y-m-d'))->where(['transaction_type' => 'decrease'])->sum('subtotal');
        $products = ChartOfInventory::where('type', 'item')->where('rootAccountType', 'FG')->count();
        $todayInvoice = Sale::whereDate('created_at', Carbon::now()->format('Y-m-d'))->count();

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

        // Retrieve all sales in a single query
        $sales = Sale::whereBetween('created_at', [$startDate->startOfDay(), $endDate->endOfDay()])
            ->select('created_at', 'grand_total')
            ->get();

// Initialize an array to store the totals for each part
        $parts = [];

// Iterate over each part
        for ($part = 0; $part < $noOfWeeks; $part++) {
            // Calculate the start and end dates of the current part
            $partStartDate = $startDate->copy()->addDays($part * $daysPerPart)->startOfDay();
            $partEndDate = $startDate->copy()->addDays(($part + 1) * $daysPerPart - 1)->endOfDay();

            // Adjust the end date if it exceeds the overall end date
            if ($partEndDate->gt($endDate)) {
                $partEndDate = $endDate->copy()->endOfDay();
            }

            // Filter the sales for the current part based on the created_at date
            $salesForPart = $sales->filter(function ($sale) use ($partStartDate, $partEndDate) {
                return $sale->created_at->between($partStartDate, $partEndDate);
            });

            // Sum the grand_total for the current part
            $salesTotal = $salesForPart->sum('grand_total');
            $parts[] = $salesTotal;
        }

        $totalDiscountThisMonth = Sale::whereYear('created_at', $currentDate->year)->whereMonth('created_at', $currentDate->month)->sum('discount');
        $totalDiscountLastMonth = Sale::whereYear('created_at', $year)->whereMonth('created_at', $lastMonth->month)->sum('discount');
        $totalDiscountToday = Sale::whereYear('created_at', $year)->whereDate('created_at', date('Y-m-d'))->sum('discount');

        if ($totalDiscountLastMonth === 0) {
            $discountPercentage = 100;
        } else {
            $discountPercentage = (100 / $totalDiscountLastMonth) * $totalDiscountThisMonth;
        }

        $outletWiseDiscount = [];
        // Get all outlets
        $allOutlets = Outlet::all();

// Retrieve all sales data for the current year, month, and day in a single query
        $salesData = Sale::whereYear('created_at', $currentDate->year)
            ->whereMonth('created_at', $currentDate->month)
            ->whereDay('created_at', $currentDate->day)
            ->select('outlet_id', Sale::raw('SUM(discount) as total_discount'))
            ->groupBy('outlet_id')
            ->get();

// Create a collection for easy access to discount totals by outlet
        $salesDataByOutlet = $salesData->keyBy('outlet_id');

// Initialize the outlet-wise discount array
        $outletWiseDiscount = ['outletName' => [], 'discount' => []];

// Iterate over all outlets
        foreach ($allOutlets as $ol) {
            $outletWiseDiscount['outletName'][] = $ol->name;

            // Check if the outlet has sales data and get the discount, otherwise set it to 0
            $discount = $salesDataByOutlet->has($ol->id) ? $salesDataByOutlet->get($ol->id)->total_discount : 0;

            $outletWiseDiscount['discount'][] = $discount;
        }
        $productWiseStock = [];
        $productWiseStock['products'] = [];
        $productWiseStock['stock'] = [];
        $totalStock = 0;

// Get all products (outlets in your case)
        $allProducts = Outlet::select('name', 'id')->get();

// Fetch all stock data for all outlets in a single query
        $allStocks = DB::table('inventory_transactions')
            ->join('stores', 'inventory_transactions.store_id', '=', 'stores.id')
            ->where('stores.doc_type', 'outlet')  // Make sure this filters only outlets
            ->select('stores.doc_id', DB::raw('SUM(inventory_transactions.amount * inventory_transactions.type) as total_stock'))
            ->groupBy('stores.doc_id')  // Group by outlet ID
            ->pluck('total_stock', 'stores.doc_id');  // Get total_stock mapped by outlet_id

// Initialize variables for product-wise stock
        $productWiseStock = ['products' => [], 'stock' => []];
        $totalStock = 0;

// Loop through each product and get stock from the pre-fetched stock data
        foreach ($allProducts as $product) {
            // Retrieve stock for the current product (outlet), or set to 0 if no stock is found
            $stock = $allStocks->get($product->id, 0);

            // Store the product's name and stock in the result array
            $productWiseStock['products'][] = $product->name;
            $productWiseStock['stock'][] = $stock;

            // Accumulate the total stock
            $totalStock += $stock;
        }
        $outletWiseExpense = [];
        $outletWiseOrders = [];
        $totalExpense = 0;
        $totalOrders = 0;
        // Get the current year and month for filtering
        $currentYear = $currentDate->year;
        $currentMonth = $currentDate->month;

// Fetch all outlets and their corresponding order counts in one query
        $allOutlets = Outlet::withCount([
            'preOrders' => function ($query) use ($currentYear, $currentMonth) {
                $query->whereYear('created_at', $currentYear)
                    ->whereMonth('created_at', $currentMonth);
            }
        ])->get();

// Initialize variables
        $outletWiseOrders = [];
        $totalOrders = 0;

// Loop through each outlet to build the result
        foreach ($allOutlets as $outlet) {
            $outletWiseOrders[$outlet->name] = $outlet->pre_orders_count; // This uses the count from the eager loading
            $totalOrders += $outlet->pre_orders_count; // Aggregate the total orders
        }

        // Initialize variables
        $outletWiseSales = [];
        $totalSales = 0;

// Get the start and end of the month
        $startOfMonth = Carbon::now()->startOfMonth();
        $endOfMonth = Carbon::now();

// Fetch sales grouped by date in a single query
        $salesData = Sale::selectRaw('DATE(created_at) as sale_date, SUM(grand_total) as total')
            ->whereBetween('created_at', [$startOfMonth, $endOfMonth])
            ->groupBy('sale_date')
            ->orderBy('sale_date')
            ->get();

// Prepare the result
        foreach ($salesData as $sale) {
            $outletWiseSales['sales'][] = $sale->total;
            $outletWiseSales['date'][] = Carbon::parse($sale->sale_date)->isoFormat('Do');
            $totalSales += $sale->total; // Aggregate total sales
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

        $customersWithPoint = Customer::where('type', 'regular')
            ->join('memberships', 'customers.id', '=', 'memberships.customer_id')
            ->with(['membership', 'sales'])
            ->has('membership')
            ->orderByDesc('memberships.point')
            ->select('customers.*')
            ->take(10) // Limit to the top 10 customers
            ->get();

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

        $slowSellingProducts = ChartOfInventory::where(['type' => 'item', 'rootAccountType' => 'FG'])->select('chart_of_inventories.*', DB::raw('COALESCE(SUM(sale_items.quantity), 0) as total_sold'))
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
            'expensePercentage' => round($expensePercentage),
            'currentMonthExpense' => round($currentMonthExpense),
            'expenseMessage' => $expenseMessage,
            'lastMonthSales' => $parts,
            'todayRequisitions' => $todayRequisitions,
            'discount' => [
                'thisMonth' => $totalDiscountThisMonth,
                'today' => $totalDiscountToday,
                'lastMonth' => $totalDiscountLastMonth,
                'percentage' => $discountPercentage,
                'outletWiseDiscount' => $outletWiseDiscount
            ],
            'stock' => [
                'total' => round($totalStock),
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
            'bestSellingProducts' => $bestProducts,
            'slowSellingProducts' => $slowSellingProducts,
            'todayInvoice' => $todayInvoice
        ];
        return view('dashboard.admin', $data);
    }

}
