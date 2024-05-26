<?php

namespace App\Http\Controllers;


use App\Http\Controllers\Controller;

use App\Models\Product;
use App\Models\Requisition;
use App\Repository\Interfaces\AdminInterface;
use Carbon\Carbon;

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
        $monthlyTotalRequisitions = Requisition::whereMonth('created_at', Carbon::now()->month)->count();
        $todayTotalStocks = 0;
        $todayTotalRequisitions = Requisition::whereDate('created_at', Carbon::today())->count();
        $todayTotalDeliveries = 0;
        $todayTotalWastages = 0;

        //2nd Section
        $thisMonthTotalWastages = 0;
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

        $monthlyWastages = 0;
        $outletWiseWastage = 0;
        //4th Section
        $monthlyDeliveries = 0;
        $todayRequisitions = Requisition::whereDate('created_at', Carbon::today())->get();


        //5th Section
        $monthlyRequisitions = Requisition::whereMonth('created_at', Carbon::now()->month)->get();

        $data = [

            //1st Section
            'monthlyTotalRequisitions' => $monthlyTotalRequisitions,
            'todayTotalStocks' => $todayTotalStocks,
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
