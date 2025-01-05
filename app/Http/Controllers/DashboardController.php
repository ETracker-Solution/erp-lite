<?php

namespace App\Http\Controllers;

use App\Classes\AvailableProductCalculation;
use App\Models\Purchase;
use App\Models\Sale;
use App\Models\StockIn;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        if ($user->is_super) {
            return (new AdminDashboardController())->adminDashboard();
        }

        $employee = $user->employee;

        if ($employee) {
            switch ($employee->user_of) {
                case 'factory':
                    return (new FactoryDashboardController())->factoryDashboard();
                case 'outlet':
                    return (new OutletDashboardController())->outletDashboard();
            }
        }

        return (new AdminDashboardController())->adminDashboard();
    }

}
