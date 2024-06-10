<?php

namespace App\Http\Controllers;


use App\Http\Controllers\Controller;
use App\Models\ChartOfInventory;
use App\Models\InventoryAdjustment;
use App\Models\InventoryTransaction;
use App\Models\Outlet;
use App\Models\Product;
use App\Models\Requisition;
use App\Models\RequisitionDelivery;
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

        //first Section
        $factory_id = Auth::user()->employee->factory_id;
        $store_ids = Store::where(['doc_type'=>'factory','doc_id'=> $factory_id])->pluck('id');


        $monthlyTotalRequisitions = Requisition::where('to_factory_id',$factory_id)->whereMonth('created_at', Carbon::now()->month)->count();        

        $productWiseStock = [];
        $productWiseStock['products'] = [];
        $productWiseStock['stock'] = [];
        $totalStock = 0;
        $allProducts = ChartOfInventory::where(['type'=>'item','rootAccountType'=>'RM'])->select('name', 'id')->get();
        foreach ($allProducts as $product) {
            $stock = InventoryTransaction::whereIn('store_id', $store_ids)->where('coi_id', $product->id)->sum(DB::raw('amount * type'));
            $productWiseStock['products'][] = $product->name;
            $productWiseStock['stock'][] = $stock;
            $totalStock += $stock;
        }

        $fgProductWiseStock = [];
        $fgProductWiseStock['products'] = [];
        $fgProductWiseStock['stock'] = [];
        $fgTotalStock = 0;
        $allProducts = ChartOfInventory::where(['type'=>'item','rootAccountType'=>'FG'])->select('name', 'id')->get();
        foreach ($allProducts as $product) {
            $stock = InventoryTransaction::where('coi_id', $product->id)->sum(DB::raw('amount * type'));
            $fgProductWiseStock['products'][] = $product->name;
            $fgProductWiseStock['stock'][] = $stock;
            $fgTotalStock += $stock;
        }

        $todayTotalRequisitions = Requisition::where('to_factory_id',$factory_id)->whereDate('created_at', Carbon::today())->count();
        $todayTotalDeliveries = RequisitionDelivery::where(['type' => 'FG', 'date' => Carbon::today()])->count();
        $todayTotalWastages = InventoryAdjustment::whereIn('store_id', $store_ids)->sum('subtotal');

        //2nd Section
        $thisMonthTotalWastages = InventoryAdjustment::whereIn('store_id', $store_ids)->whereMonth('created_at', Carbon::now()->month)->sum('subtotal');

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
        $todayRequisitions = Requisition::where('to_factory_id',$factory_id)->whereDate('created_at', Carbon::today())->get();


        //5th Section
        $monthlyRequisitions = Requisition::where('to_factory_id',$factory_id)->whereMonth('created_at', Carbon::now()->month)->get();

        $data = [

            //1st Section
            'monthlyTotalRequisitions' => $monthlyTotalRequisitions,
            'todayTotalRequisitions' => $todayTotalRequisitions,
            'todayTotalDeliveries' => $todayTotalDeliveries,
            'todayTotalWastages' => $todayTotalWastages,
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
                'total' => $totalStock,
                'productWise' => $productWiseStock
            ],
            'fgStock' => [
                'total' => $fgTotalStock,
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
