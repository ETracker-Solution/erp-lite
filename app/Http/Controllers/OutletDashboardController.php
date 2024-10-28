<?php

namespace App\Http\Controllers;

use App\Models\ChartOfInventory;
use App\Models\Customer;
use App\Models\InventoryAdjustment;
use App\Models\InventoryTransaction;
use App\Models\OthersOutletSale;
use App\Models\Outlet;
use App\Models\OutletAccount;
use App\Models\RequisitionDelivery;
use App\Models\Sale;
use App\Models\Store;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class OutletDashboardController extends Controller
{

    public function outletDashboard()
    {
        $outlet_id = Auth::user()->employee->outlet_id;
        $store_ids = Store::where(['doc_type' => 'outlet', 'doc_id' => $outlet_id])->pluck('id');

        $wastage_amount = InventoryAdjustment::whereIn('store_id', $store_ids)->sum('subtotal');

        $requisition_deliveries = RequisitionDelivery::whereHas('requisition', function ($query) use($outlet_id) {
            $query->where(['outlet_id' => $outlet_id]);
        })->where(['type' => 'FG', 'status' => 'completed'])->get();

        $requisition_deliveries_count = $requisition_deliveries->count();

        $otherOutletSales = OthersOutletSale::where(['status' => 'delivered','outlet_id' => $outlet_id])->count();

        $products = ChartOfInventory::where(['type' => 'item', 'rootAccountType' => 'FG','status'=>'active'])->count();

        $lastMonthExpense = 0;
        $currentMonthExpense = 0;
        $expensePercentage = $lastMonthExpense === 0 ? 100 : (100 / $lastMonthExpense) * $currentMonthExpense;

        $currentDate = Carbon::now();
        $startDate = $currentDate->copy()->subMonth()->startOfMonth();
        $endDate = $currentDate->copy()->subMonth()->endOfMonth();
        $noOfWeeks = $currentDate->copy()->subMonth()->weekOfMonth;
        $totalDays = $startDate->daysInMonth;

        $currentMonthStartDate = $currentDate->copy()->startOfMonth();
        $currentMonthEndDate = $currentDate->copy()->endOfMonth();

        $currentMonthAmount = Sale::where('outlet_id', $outlet_id)->whereBetween('created_at', [$currentMonthStartDate, $currentMonthEndDate])
            ->sum('grand_total');

        // Calculate the number of days in each part
        $daysPerPart = ceil($totalDays / $noOfWeeks);

        // Initialize an array to store sales data for each part
        $parts = [];
        $lastMonthSaleAmount = 0;

        // Pre-fetch sales data for the entire date range
        $salesData = Sale::where('outlet_id', $outlet_id)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->select('created_at', 'grand_total')
            ->get();

        // Iterate over each part
        for ($part = 0; $part < $noOfWeeks; $part++) {
            // Calculate the start and end dates of the current part
            $partStartDate = $startDate->copy()->addDays($part * $daysPerPart);
            $partEndDate = $partStartDate->copy()->addDays($daysPerPart - 1)->endOfDay();

            if ($partEndDate->gt($endDate)) {
                $partEndDate = $endDate->copy()->endOfDay();
            }

            // Filter the sales data for the current part
            $salesTotal = $salesData->filter(function ($sale) use ($partStartDate, $partEndDate) {
                return $sale->created_at->between($partStartDate, $partEndDate);
            })->sum('grand_total');

            $parts[] = $salesTotal;
            $lastMonthSaleAmount += $salesTotal;
        }

        $totalDiscountToday = Sale::where('outlet_id', $outlet_id)->whereDate('created_at', Carbon::today())->sum('discount');


        // Get the current year and month
        $currentYear = $currentDate->year;
        $currentMonth = $currentDate->month;

        $allOutlets = Outlet::all();

        // Fetch discounts for the current month and year
        $salesData = Sale::whereYear('created_at', $currentYear)
            ->whereMonth('created_at', $currentMonth)
            ->whereIn('outlet_id', $allOutlets->pluck('id')) // Get all outlet ids
            ->select('outlet_id', DB::raw('SUM(discount) as total_discount')) // Aggregate discounts
            ->groupBy('outlet_id')
            ->get()
            ->keyBy('outlet_id'); // Key by outlet_id for easy access

// Populate the outlet-wise discount array
        foreach ($allOutlets as $ol) {
            $outletWiseDiscount['outletName'][] = $ol->name;
            $outletWiseDiscount['discount'][] = $salesData->get($ol->id)->total_discount ?? 0; // Use null coalescing for safety
        }
        // Initialize the product-wise stock array
        $productWiseStock = [
            'products' => [],
            'stock' => [],
        ];
        $totalStock = 0;

// Fetch all products with the specified criteria
        $allProducts = ChartOfInventory::where(['type' => 'item', 'rootAccountType' => 'FG'])->get();

// Pre-fetch inventory transaction data for the relevant products and stores
        $inventoryData = InventoryTransaction::whereIn('store_id', $store_ids)
            ->whereIn('coi_id', $allProducts->pluck('id')) // Get all product IDs
            ->select('coi_id', DB::raw('SUM(quantity * type) as total_stock')) // Aggregate stock
            ->groupBy('coi_id')
            ->get()
            ->keyBy('coi_id'); // Key by coi_id for easy access

// Populate the product-wise stock array
        foreach ($allProducts as $product) {
            $stock = $inventoryData->get($product->id)->total_stock ?? 0; // Use null coalescing for safety
            $productWiseStock['products'][] = $product->name;
            $productWiseStock['stock'][] = $stock;
            $totalStock += $stock;
        }

        if (\request()->ajax()) {
            $requisitions = $this->getReq();
            return DataTables::of($requisitions)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    if ($row->type == 'outlet') {
                        return view('admin.requisition.action-button', compact('row'));
                    } else {
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

        $customersWithPoint = Customer::where('type','regular')
            ->has('membership')
            ->with(['membership', 'sales'])
            ->join('memberships', 'customers.id', '=', 'memberships.customer_id')
            ->orderByDesc('memberships.point')
            ->select('customers.*')
            ->take(10) // Limit to the top 10 customers
            ->get();
        $latestFiveSales = Sale::where('outlet_id', $outlet_id)->take(5)->latest()->get();
        $todaySale = Sale::where('outlet_id', $outlet_id)->whereDate('created_at', Carbon::now()->format('Y-m-d'))->sum('grand_total');
        $todayInvoice = Sale::where('outlet_id', $outlet_id)->whereDate('created_at', Carbon::now()->format('Y-m-d'))->count();
        $todayExpense = 0;

        $bestProducts = [];
        $bestSellingProducts = ChartOfInventory::where(['type' => 'item', 'rootAccountType' => 'FG'])->select('chart_of_inventories.*', DB::raw('SUM(sale_items.quantity) as total_sold'))
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

        $salesCompare['month'][] = 'Last Month';
        $salesCompare['sale'][] = $lastMonthSaleAmount;
        $salesCompare['month'][] = 'Current Month';
        $salesCompare['sale'][] = $currentMonthAmount;

        $salesWastageCompare['sales'][] = 'Sales';
        $salesWastageCompare['wastage'][] = $currentMonthAmount;
        $salesWastageCompare['sales'][] = 'Wastage';
        $salesWastageCompare['wastage'][] = $wastage_amount;

        $outletPettyCashAmount = 0;
        $outletAccounts = OutletAccount::with('coa')->where('outlet_id',$outlet_id)->get();
        foreach ($outletAccounts as $outletAccount) {
            if ($outletAccount->coa->default_type == 'petty_cash'){
                $outletPettyCashAmount =  $outletAccount->coa->transactions()->sum(DB::raw('transaction_type* amount'));
            }
        }

        $data = [
            'requisition_deliveries' => $requisition_deliveries,
            'requisition_deliveries_count' => $requisition_deliveries_count,
            'products' => $products,
            'wastageAmount' => round($wastage_amount),
            'latestFiveSales' => $latestFiveSales,
            'lastMonthSales' => $parts,
            'expensePercentage' => $expensePercentage,
            'discount' => [
                'thisDay' => $totalDiscountToday,
                'outletWiseDiscount' => $outletWiseDiscount
            ],
            'stock' => [
                'total' => round($totalStock),
                'productWise' => $productWiseStock
            ],
            'customersWithPoint' => $customersWithPoint,
            'todaySale' => $todaySale,
            'todayInvoice' => $todayInvoice,
            'todayExpense' => $todayExpense,
            'bestSellingProducts' => $bestProducts,
            'slowSellingProducts' => $slowSellingProducts,
            'salesComparision' => $salesCompare,
            'salesWastageCompare' => $salesWastageCompare,
            'otherOutletSales' => $otherOutletSales,
            'outletPettyCashAmount'=>$outletPettyCashAmount
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
