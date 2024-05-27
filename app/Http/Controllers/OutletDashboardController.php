<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Expense;
use App\Models\Outlet;
use App\Models\Product;
use App\Models\Sale;
use App\Repository\Interfaces\AdminInterface;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class OutletDashboardController extends Controller
{

    protected $adminRepo;

    // public function __construct(AdminInterface $admin)
    // {
    //     $this->adminRepo = $admin;
    // }

    public function outletDashboard()
    {


        $total_sales = Sale::sum('grand_total');
        $outlets = Outlet::count();
        $customers = Customer::where('type', 'regular')->count();
        $wastages = 0;
        $wastage_amount = 0;

        $products = Product::count();

        $year = Carbon::now()->month == 1 ? Carbon::now()->subYear()->year : Carbon::now()->year;
        $lastMonth = Carbon::now()->subMonth();
        $lastMonthExpense = 0;
        $currentMonthExpense = 0;
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
        $allProducts = Product::select('name', 'id')->get();
        // foreach ($allProducts as $product) {
        //     $stock = factoryOrOutletStock($product->id,Outlet::class,authUser()->outlet_id);
        //     $productWiseStock['products'][] = $product->name;
        //     $productWiseStock['stock'][] = $stock;
        //     $totalStock += $stock;
        // }

        $outletWiseExpense = [];
        $outletWiseOrders = [];
        $totalExpense = 0;
        $totalOrders = 0;
        $allOutlets = Outlet::all();

        foreach ($allOutlets as $ol) {
            $expense = Expense::whereYear('created_at', $currentDate->year)->whereMonth('created_at', $currentDate->month)->sum('amount');
            $outletWiseExpense['outletName'][] = $ol->name;
            $outletWiseExpense['expense'][] = $expense;
            $totalExpense += $expense;
            $order = Sale::whereYear('created_at', $currentDate->year)->whereMonth('created_at', $currentDate->month)->count();
            $outletWiseOrders[$ol->name] = $order;
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

        if (\request()->ajax()) {
            $requisitions = $this->getReq();
            return DataTables::of($requisitions)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    if ($row->type == 'outlet'){
                        return view('admin.requisition.action-button', compact('row'));
                    }else{
                        return view('admin.raw-requisition.action-button', compact('row'));
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

        $customersWithPoint = Customer::whereHas('membership')->with('membership','sales')->get();
        $latestFiveSales = Sale::take(5)->latest()->get();
        $todaySale = Sale::whereDate('created_at',Carbon::now()->format('Y-m-d'))->sum('grand_total');
        $todayExpense = 0;

        $bestProducts = [];
        $bestSellingProducts = Product::select('products.*', DB::raw('SUM(sale_items.quantity) as total_sold'))
            ->join('sale_items', 'products.id', '=', 'sale_items.product_id')
            ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
            ->whereYear('sales.created_at', '=', now()->year)
            ->whereMonth('sales.created_at', '=', now()->month)
            ->groupBy('products.id')
            ->orderByDesc('total_sold')
            ->get();

        $slowSellingProducts = Product::select('products.*', DB::raw('COALESCE(SUM(sale_items.quantity), 0) as total_sold'))
            ->leftJoin('sale_items', function ($join) {
                $join->on('products.id', '=', 'sale_items.product_id')
                    ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
                    ->whereYear('sales.created_at', '=', now()->year)
                    ->whereMonth('sales.created_at', '=', now()->month);
            })
            ->groupBy('products.id')
            ->orderBy('total_sold')
            ->limit(5)
            ->get();

        foreach ($bestSellingProducts as $product)
        {
            $bestProducts['name'][]=$product->name;
            $bestProducts['qty'][]=$product->total_sold;
        }

        $data = [
            'totalSales' => $total_sales,
            'outlets' => $outlets,
            'customers' => $customers,
            'products' => $products,
            'wastageAmount' => $wastage_amount,
            'latestFiveSales' => $latestFiveSales,
            'lastMonthExpense' => $lastMonthExpense,
            'expensePercentage' => $expensePercentage,
            'currentMonthExpense' => $currentMonthExpense,
            'expenseMessage' => $expenseMessage,
            'lastMonthSales' => $parts,
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
            'customersWithPoint'=>$customersWithPoint,
            'todaySale'=>$todaySale,
            'todayExpense'=>$todayExpense,
            'bestSellingProducts'=>$bestProducts,
            'slowSellingProducts'=>$slowSellingProducts,
        ];
//        return $data;
        return view('dashboard.outlet', $data);
    }

    protected function getReq()
    {
        $q = DB::select(
            "select id,requisition_number, status,date,created_at, 'outlet' as type
from requisitions
UNION ALL
select id,requisition_number, status,date,created_at, 'factory' as type
from raw_requisitions"
        );
        return $requisitions = collect($q)->sortByDesc('created_at')->values()->all();

    }
}