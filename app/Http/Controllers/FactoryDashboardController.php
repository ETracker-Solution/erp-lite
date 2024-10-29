<?php

namespace App\Http\Controllers;


use App\Http\Controllers\Controller;
use App\Models\ChartOfInventory;
use App\Models\InventoryAdjustment;
use App\Models\InventoryTransaction;
use App\Models\Outlet;
use App\Models\PreOrder;
use App\Models\Product;
use App\Models\Requisition;
use App\Models\RequisitionDelivery;
use App\Models\Sale;
use App\Models\Store;
use App\Models\User;
use App\Repository\Interfaces\AdminInterface;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class FactoryDashboardController extends Controller
{

    protected $adminRepo;

    // public function __construct(AdminInterface $admin)
    // {
    //     $this->adminRepo = $admin;
    // }

    public function factoryDashboard()
    {
        $today = Carbon::today()->format('Y-m-d');
        //first Section
        $factory_id = Auth::user()->employee->factory_id;
        $store_ids = Store::where(['doc_type' => 'factory', 'doc_id' => $factory_id])->pluck('id');


        $monthlyTotalRequisitions = Requisition::where('to_factory_id', $factory_id)->whereMonth('created_at', Carbon::now()->month)->count();

        // Get all products with their respective stock in a single query
        $allProducts = ChartOfInventory::where(['type' => 'item', 'rootAccountType' => 'RM'])
            ->with(['inventoryTransactions' => function ($query) use ($store_ids) {
                $query->whereIn('store_id', $store_ids);
            }])
            ->select('name', 'id')
            ->get();

// Initialize the productWiseStock array and totalStock
        $productWiseStock = ['products' => [], 'stock' => []];
        $totalStock = 0;

// Calculate stock for each product
        foreach ($allProducts as $product) {
            $stock = $product->inventoryTransactions->sum(function ($transaction) {
                return $transaction->amount * $transaction->type; // Assuming type is either 1 or -1
            });

            $productWiseStock['products'][] = $product->name;
            $productWiseStock['stock'][] = $stock;
            $totalStock += $stock;
        }

        // Initialize the stock arrays
        $fgProductWiseStock = [
            'products' => [],
            'stock' => [],
        ];
        $fgTotalStock = 0;

// Fetch all products with their respective stock in a single query
        $allProducts = ChartOfInventory::where(['type' => 'item', 'rootAccountType' => 'FG'])
            ->with(['inventoryTransactions']) // Eager load the inventory transactions
            ->select('name', 'id')
            ->get();

// Calculate stock for each product
        foreach ($allProducts as $product) {
            // Calculate stock using the eager loaded transactions
            $stock = $product->inventoryTransactions->sum(function ($transaction) {
                return $transaction->amount * $transaction->type; // Assuming type is either 1 or -1
            });

            // Populate the stock arrays
            $fgProductWiseStock['products'][] = $product->name;
            $fgProductWiseStock['stock'][] = $stock;
            $fgTotalStock += $stock;
        }

        $TotalNewRequisitions = Requisition::where('to_factory_id', $factory_id)->whereType('FG')->whereIn('status', ['approved'])->where('delivery_status', 'pending')->count();

        $todayTotalRequisitions = Requisition::where('to_factory_id', $factory_id)->whereDate('created_at', Carbon::today())->whereIn('status', ['pending'])->count();
        $todayTotalDeliveries = RequisitionDelivery::where(['type' => 'FG', 'date' => $today])->count();
        $todayTotalWastages = InventoryAdjustment::whereIn('store_id', $store_ids)->where(['date' => $today, 'transaction_type' => 'decrease'])->sum('subtotal');
        $todayPreOrderDeliveries = PreOrder::where(['status' => 'pending', 'delivery_date' => $today])->count();

        $todayInvoice = Sale::whereDate('created_at', Carbon::now()->format('Y-m-d'))->count();

        //2nd Section
        $thisMonthTotalWastages = InventoryAdjustment::whereIn('store_id', $store_ids)->whereMonth('created_at', Carbon::now()->month)->where('transaction_type', 'decrease')->sum('subtotal');

//Expenses
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
//Expenses

        //Wastage start
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
            $wastageTotal = 0;
            $parts[] = $wastageTotal;
        }
        //Wastage end


        $dailyRawProductWiseStock = [];
        $dailyTotalStock = 0;
        $allProducts = Product::select('name', 'id')->get();
        foreach ($allProducts as $product) {
            $stock = 0;
            $dailyRawProductWiseStock['products'][] = $product->name;
            $dailyRawProductWiseStock['stock'][] = $stock;
            $dailyTotalStock += $stock;
        }

        $monthlyTotalDeliveries = 0;

        //3rd Section
        $monthlyRawProductWiseStock = [];
        $monthlyTotalStock = 0;
        $allProducts = Product::select('name', 'id')->get();
        foreach ($allProducts as $product) {
            $stock = 0;
            $monthlyRawProductWiseStock['products'][] = $product->name;
            $monthlyRawProductWiseStock['stock'][] = $stock;
            $monthlyTotalStock += $stock;
        }

        $monthlyWastages = InventoryAdjustment::whereIn('store_id', $store_ids)
            ->whereMonth('created_at', Carbon::now()->month)
            ->orderBy('total', 'DESC');

        $outletWiseWastage = [];
        foreach ($monthlyWastages as $row) {
            $outletWiseWastage['outlet'][] = $row->outlet->name;
            $outletWiseWastage['total'][] = $row->total;
        }
        //4th Section
        $monthlyDeliveries = RequisitionDelivery::where('type', 'FG')->select('from_store_id', DB::raw('count(id) as total'))
            ->whereMonth('created_at', Carbon::now()->month)
            ->orderBy('total', 'DESC')
            ->groupBy('from_store_id')
            ->with('fromStore')
            ->get();
        $todayRequisitions = Requisition::where('to_factory_id', $factory_id)->whereDate('created_at', Carbon::now()->format('Y-m-d'))->get();


        //5th Section
        $monthlyRequisitions = Requisition::where('to_factory_id', $factory_id)->whereMonth('created_at', Carbon::now()->month)->get();

        $data = [

            'TotalNewRequisitions' => $TotalNewRequisitions,
            //1st Section
            'monthlyTotalRequisitions' => $monthlyTotalRequisitions,
            'todayTotalRequisitions' => $todayTotalRequisitions,
            'todayTotalDeliveries' => $todayTotalDeliveries,
            'todayTotalWastages' => round($todayTotalWastages),
            'todayPreOrderDeliveries' => $todayPreOrderDeliveries,
            'todayInvoice' => $todayInvoice,
            //2nd Section
            'thisMonthTotalWastages' => $thisMonthTotalWastages,
            'thisMonthWastages' => $parts,
            'lastMonthExpense' => $lastMonthExpense,
            'expensePercentage' => $expensePercentage,
            'currentMonthExpense' => $currentMonthExpense,
            'expenseMessage' => $expenseMessage,
            'dailyRawProductWiseStock' => $dailyRawProductWiseStock,
            'monthlyTotalDeliveries' => $monthlyTotalDeliveries,
            //3rd Section
            'monthlyRawProductWiseStock' => $monthlyRawProductWiseStock,
            'monthlyWastages' => $monthlyWastages,
            'outletWiseWastage' => $outletWiseWastage,
            'stock' => [
                'total' => round($totalStock),
                'productWise' => $productWiseStock
            ],
            'fgStock' => [
                'total' => round($fgTotalStock),
                'productWise' => $fgProductWiseStock
            ],

            //4th Section
            'todayRequisitions' => $todayRequisitions,
            'monthlyDeliveries' => $monthlyDeliveries,
            //5th Section
            'monthlyRequisitions' => $monthlyRequisitions,
        ];
//        return $data;
        return view('dashboard.factory', $data);

    }
}
