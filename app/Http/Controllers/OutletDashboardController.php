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

        $wastage_amount = InventoryAdjustment::whereIn('store_id', $store_ids)->whereDate('date', now()->subDay())->sum('subtotal');

        $requisition_deliveries = RequisitionDelivery::whereHas('requisition', function ($query) use ($outlet_id) {
            $query->where(['outlet_id' => $outlet_id]);
        })->where(['type' => 'FG', 'status' => 'completed'])->get();

        $requisition_deliveries_count = count($requisition_deliveries);

        $otherOutletSales = OthersOutletSale::where('outlet_id', '!=', $outlet_id)
            ->where(['status' => 'pending', 'delivery_point_id' => $outlet_id])
            ->count();

        $products = ChartOfInventory::where(['type' => 'item', 'rootAccountType' => 'FG', 'status' => 'active'])->count();

        $currentDate = Carbon::now();

        $currentMonthAmount = InventoryAdjustment::with('store')
            ->where('transaction_type', 'decrease')
            ->whereMonth('created_at', $currentDate->month)
            ->whereYear('created_at', $currentDate->month)
            ->whereHas('store', function ($query) use ($outlet_id) {
                $query->where('doc_type', 'outlet')
                    ->where('doc_id', $outlet_id);
            })
            ->sum('subtotal');

        $lastMonth = Carbon::now()->subMonth();

        $lastMonthAmount = InventoryAdjustment::with('store')
            ->where('transaction_type', 'decrease')
            ->whereMonth('created_at', $lastMonth->month)
            ->whereYear('created_at', $lastMonth->year)
            ->whereHas('store', function ($query) use ($outlet_id) {
                $query->where('doc_type', 'outlet')
                    ->where('doc_id', $outlet_id);
            })
            ->sum('subtotal');


        $totalDiscountToday = Sale::where('outlet_id', $outlet_id)->whereDate('created_at', Carbon::today())->sum('discount');

        // Get the current year and month
        $currentYear = $currentDate->year;
        $currentMonth = $currentDate->month;

        $allOutlets = Outlet::all();

        $salesData = Sale::whereYear('created_at', $currentYear)
            ->whereMonth('created_at', $currentMonth)
            ->whereIn('outlet_id', $allOutlets->pluck('id')) // Get all outlet ids
            ->select('outlet_id', DB::raw('SUM(discount) as total_discount')) // Aggregate discounts
            ->groupBy('outlet_id')
            ->pluck('total_discount', 'outlet_id'); // Pluck total_discount by outlet_id for efficiency

        $outletWiseDiscount = [
            'outletName' => $allOutlets->pluck('name')->toArray(), // Extract outlet names
            'discount' => $allOutlets->map(fn($outlet) => $salesData[$outlet->id] ?? 0)->toArray(), // Map discounts or default to 0
        ];
        $productWiseStock = [
            'products' => [],
            'stock' => [],
        ];
        $totalStock = 0;

        $allProducts = ChartOfInventory::where(['type' => 'item', 'rootAccountType' => 'FG',])->where('price', '>', 0)->get();

        $inventoryData = InventoryTransaction::whereIn('store_id', $store_ids)
            ->whereIn('coi_id', $allProducts->pluck('id'))
            ->select('coi_id', DB::raw('SUM(quantity * type) as total_stock'))
            ->groupBy('coi_id')
            ->pluck('total_stock', 'coi_id'); // Pluck for efficient key-value pairing

        foreach ($allProducts as $product) {
            $stock = $inventoryData[$product->id] ?? 0; // Use null coalescing for missing products
            $with_price = $stock * $product->price;

            $productWiseStock['products'][] = $product->name;
            $productWiseStock['stock'][] = $with_price;

            $totalStock += $with_price;
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

        $customersWithPoint = Customer::where('type', 'regular')
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


        $salesWastageCompare['sales'][] = 'Last Month';
        $salesWastageCompare['wastage'][] = $lastMonthAmount;
        $salesWastageCompare['sales'][] = 'Current Month';
        $salesWastageCompare['wastage'][] = $currentMonthAmount;

        $outletPettyCashAmount = 0;
        $outletAccounts = OutletAccount::with('coa')->where('outlet_id', $outlet_id)->get();
        foreach ($outletAccounts as $outletAccount) {
            if ($outletAccount->coa->default_type == 'petty_cash') {
                $outletPettyCashAmount = $outletAccount->coa->transactions()->sum(DB::raw('transaction_type* amount'));
            }
        }

        $data = [
            'requisition_deliveries' => $requisition_deliveries,
            'requisition_deliveries_count' => $requisition_deliveries_count,
            'products' => $products,
            'wastageAmount' => round($wastage_amount),
            'latestFiveSales' => $latestFiveSales,
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
            'bestSellingProducts' => $bestProducts,
            'slowSellingProducts' => $slowSellingProducts,
            'salesWastageCompare' => $salesWastageCompare,
            'otherOutletSales' => $otherOutletSales,
            'outletPettyCashAmount' => $outletPettyCashAmount
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
