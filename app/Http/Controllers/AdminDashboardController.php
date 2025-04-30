<?php

namespace App\Http\Controllers;

use App\Models\AccountTransaction;
use App\Models\ChartOfInventory;
use App\Models\Customer;
use App\Models\InventoryAdjustment;
use App\Models\InventoryTransaction;
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
        // Fetch today's date
        $currentDate = now();
        $today = $currentDate->format('Y-m-d');

        // Batch data fetching using aggregate queries
        $aggregates = [
            'total_sales' => Sale::select(DB::raw('SUM(grand_total) as total'))->whereDate('created_at', $today)->first()->total,
            'outlets' => Outlet::where('status', 'active')->count(),
            'customers' => Customer::where('type', 'regular')->count(),
            'wastage_amount' => InventoryAdjustment::select(DB::raw('SUM(subtotal) as total'))
                ->whereDate('created_at', $today)
                ->where('transaction_type', 'decrease')
                ->first()->total,
            'products' => ChartOfInventory::where('type', 'item')->where('rootAccountType', 'FG')->count(),
            'today_invoice' => Sale::whereDate('created_at', $today)->count(),
        ];
//        return $aggregates;
        $total_sales = Sale::whereDate('created_at', date('Y-m-d'))->sum('grand_total');
        $outlets = Outlet::whereStatus('active')->count();
        $customers = Customer::where('type', 'regular')->count();
        $wastage_amount = InventoryAdjustment::whereDate('created_at', date('Y-m-d'))->where(['transaction_type' => 'decrease'])->sum('subtotal');
        $products = ChartOfInventory::where('type', 'item')->where('rootAccountType', 'FG')->count();
        $todayInvoice = Sale::whereDate('created_at', Carbon::now()->format('Y-m-d'))->count();

        $todayRequisitions = Requisition::whereType('FG')->whereDate('created_at', Carbon::today())->get();

        $currentDate = Carbon::now();

        // Calculate monthly discounts
       $totalDiscounts = Sale::select(
            DB::raw('SUM(discount) as current_month'),
            DB::raw('SUM(CASE WHEN MONTH(created_at) = MONTH(CURRENT_DATE - INTERVAL 1 MONTH) THEN discount ELSE 0 END) as last_month'),
            DB::raw('SUM(CASE WHEN DATE(created_at) = CURRENT_DATE THEN discount ELSE 0 END) as today')
        )->first();

        $totalDiscountThisMonth = $totalDiscounts->current_month;
        $totalDiscountLastMonth = $totalDiscounts->last_month;
        $totalDiscountToday = $totalDiscounts->today;

        $discountPercentage = $totalDiscounts->last_month > 0
            ? (100 * $totalDiscounts->current_month / $totalDiscounts->last_month)
            : 100;


        // Outlet-wise discounts
        $salesDataByOutlet = Sale::whereYear('created_at', $currentDate->year)
            ->whereMonth('created_at', $currentDate->month)
            ->whereDay('created_at', $currentDate->day)
            ->select('outlet_id', DB::raw('SUM(discount) as total_discount'))
            ->groupBy('outlet_id')
            ->pluck('total_discount', 'outlet_id');

        $allOutlets = Outlet::select('id', 'name')->get();

        $outletWiseDiscount = [
            'outletName' => $allOutlets->pluck('name')->toArray(),
            'discount' => $allOutlets->map(fn($ol) => $salesDataByOutlet->get($ol->id, 0))->toArray(),
        ];

        $productWiseStock = [];
        $productWiseStock['products'] = [];
        $productWiseStock['stock'] = [];

        $allProducts = Outlet::select('name', 'id')->get();

        $allStocks = DB::table('inventory_transactions')
            ->join('stores', 'inventory_transactions.store_id', '=', 'stores.id')
            ->where('stores.doc_type', 'outlet')  // Make sure this filters only outlets
            ->select('stores.doc_id', DB::raw('SUM(inventory_transactions.amount * inventory_transactions.type) as total_stock'))
            ->groupBy('stores.doc_id')  // Group by outlet ID
            ->pluck('total_stock', 'stores.doc_id');  // Get total_stock mapped by outlet_id

        $productWiseStock = ['products' => [], 'stock' => []];
        $totalStock = 0;

        foreach ($allProducts as $product) {
            // Retrieve stock for the current product (outlet), or set to 0 if no stock is found
            $stock = $allStocks->get($product->id, 0);

            // Store the product's name and stock in the result array
            $productWiseStock['products'][] = $product->name;
            $productWiseStock['stock'][] = $stock;

            $totalStock += $stock;
        }
        $outletWiseOrders = [];
        $totalOrders = 0;



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
            ->orderByDesc('memberships.point')
            ->select('customers.*')
            ->take(10) // Limit to the top 10 customers
            ->get();


        // Best and slow-selling products
        $productSales = ChartOfInventory::select('chart_of_inventories.*', DB::raw('SUM(sale_items.quantity) as total_sold'))
            ->leftJoin('sale_items', 'chart_of_inventories.id', '=', 'sale_items.product_id')
            ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
            ->whereYear('sales.created_at', $currentDate->year)
            ->whereMonth('sales.created_at', $currentDate->month)
            ->groupBy('chart_of_inventories.id')
            ->orderByDesc('total_sold')
            ->get();

        $bestProducts = [
            'name' => $productSales->pluck('name')->toArray(),
            'qty' => $productSales->pluck('total_sold')->toArray(),
        ];

        $slowSellingProducts = $productSales->sortBy('total_sold')->take(5);

        $storeId = request('store_id');

// Fetch all RM and FG items with their type = 'item'
        $inventoryItems = ChartOfInventory::where('type', 'item')
            ->whereIn('rootAccountType', ['RM', 'FG'])
            ->get(['id', 'rootAccountType']);

// Group by rootAccountType (RM/FG)
        $grouped = $inventoryItems->groupBy('rootAccountType');

        $rmIds = $grouped['RM']?->pluck('id') ?? collect();
        $fgIds = $grouped['FG']?->pluck('id') ?? collect();

// Fetch current stock and rate in one query per item
        $transactions = InventoryTransaction::selectRaw("
        coi_id,
        SUM(type * quantity) as current_stock,
        COALESCE(ROUND(SUM(CASE WHEN type = 1 THEN amount ELSE 0 END) / NULLIF(SUM(CASE WHEN type = 1 THEN quantity ELSE 0 END), 0), 2), 0.00) as rate
    ")
            ->when($storeId, fn($q) => $q->where('store_id', $storeId))
            ->whereIn('coi_id', $inventoryItems->pluck('id'))
            ->groupBy('coi_id')
            ->get()
            ->keyBy('coi_id');

// Calculate RM and FG amounts
        $rmAmount = $rmIds->sum(function ($id) use ($transactions) {
            $t = $transactions[$id] ?? null;
            return $t ? $t->current_stock * $t->rate : 0;
        });

        $fgAmount = $fgIds->sum(function ($id) use ($transactions) {
            $t = $transactions[$id] ?? null;
            return $t ? $t->current_stock * $t->rate : 0;
        });

        $data = [
            'totalSales' => $total_sales,
            'outlets' => $outlets,
            'customers' => $customers,
            'products' => $products,
            'wastageAmount' => round($wastage_amount),
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
            'order' => [
                'total' => $totalOrders,
                'outletWise' => $outletWiseOrders
            ],
            'customersWithPoint' => $customersWithPoint,
            'bestSellingProducts' => $bestProducts,
            'slowSellingProducts' => $slowSellingProducts,
            'todayInvoice' => $todayInvoice,
            'rmStock' => number_format($rmAmount),
            'fgStock' => number_format($fgAmount),
        ];
        return view('dashboard.admin', $data);
    }

}
