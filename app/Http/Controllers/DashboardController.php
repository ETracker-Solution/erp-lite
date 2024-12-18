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
        ini_set('memory_limit', '512M');
        if(auth()->user()->is_super){
            return (new AdminDashboardController())->adminDashboard();
        }else{
            if(auth()->user()->employee){
                if(auth()->user()->employee->user_of == 'factory'){
                    return (new FactoryDashboardController())->factoryDashboard();
                }elseif(auth()->user()->employee->user_of == 'outlet'){
                    return (new OutletDashboardController())->outletDashboard();
                }else{
                    return (new AdminDashboardController())->adminDashboard();
                }
            }
        }
        return (new AdminDashboardController())->adminDashboard();

        $stock_ins = StockIn::select(DB::raw('count(*) as product_count, product_id'))
            ->groupBy('product_id')
            ->get();
        $products_name = [];
        $products_stock = [];
        foreach ($stock_ins as $row) {
            array_push($products_name, $row->product->name);
            array_push($products_stock, AvailableProductCalculation::product_id($row->product_id));
        }
        // dd($stock_detail);
        $products_name = json_encode($products_name);


        $products_stock = json_encode($products_stock);
        $data = [
            'title' => 'Dashboard',
            'totalPurchase' => Purchase::sum('grand_total'),
            'totalExpense' => 0,
            'totalSale' => Sale::sum('grand_total'),
            'products_name' =>   $products_name,
            'products_stock' =>  $products_stock,
        ];
        return view('dashboard', $data);
    }

}
