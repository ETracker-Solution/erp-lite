<?php

namespace App\Http\Controllers;


use App\Models\ChartOfInventory;
use App\Models\InventoryAdjustment;
use App\Models\PreOrder;
use App\Models\Requisition;
use App\Models\RequisitionDelivery;
use App\Models\Sale;
use App\Models\Store;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class FactoryDashboardController extends Controller
{
    public function factoryDashboard()
    {
        $today = Carbon::today()->format('Y-m-d');
        $factory_id = Auth::user()->employee->factory_id;
        $store_ids = Store::where(['doc_type' => 'factory', 'doc_id' => $factory_id])->pluck('id');

        $monthlyTotalRequisitions = Requisition::where('to_factory_id', $factory_id)->whereMonth('created_at', Carbon::now()->month)->count();

        // Aggregate FG and RM product-wise stock and related requisition data in a single query
//        $productStockData = ChartOfInventory::whereIn('rootAccountType', ['RM', 'FG'])
//            ->where('type', 'item')
//            ->where('price','>',0)
//            ->with(['inventoryTransactions' => function ($query) use ($store_ids) {
//                $query->whereIn('store_id', $store_ids);
//            }])
//            ->select('id', 'name', 'rootAccountType')
//            ->get();
//
//        // Process product-wise stock data
//        $productWiseStock = ['products' => [], 'stock' => []];
//        $fgProductWiseStock = ['products' => [], 'stock' => []];
//        $totalStock = 0;
//        $fgTotalStock = 0;
//
//        foreach ($productStockData as $product) {
//            $stock = $product->inventoryTransactions->sum(function ($transaction) {
//                return $transaction->amount * $transaction->type;
//            });
//
//            if ($product->rootAccountType == 'RM') {
//                $productWiseStock['products'][] = $product->name;
//                $productWiseStock['stock'][] = $stock;
//                $totalStock += $stock;
//            } else {
//                $fgProductWiseStock['products'][] = $product->name;
//                $fgProductWiseStock['stock'][] = $stock;
//                $fgTotalStock += $stock;
//            }
//        }

        // Combine requisition counts and wastage totals into one query for the current day
        $todayData = [
            'TotalNewRequisitions' => Requisition::where('to_factory_id', $factory_id)
                ->whereType('FG')
                ->whereIn('status', ['approved'])
                ->where('delivery_status', 'pending')
                ->count(),

            'todayTotalRequisitions' => Requisition::where('to_factory_id', $factory_id)
                ->whereDate('created_at', Carbon::today())
                ->whereIn('status', ['pending'])
                ->count(),

            'todayTotalDeliveries' => RequisitionDelivery::where(['type' => 'FG', 'date' => $today])->count(),

            'todayTotalWastages' => InventoryAdjustment::whereIn('store_id', $store_ids)
                ->where(['date' => $today, 'transaction_type' => 'decrease'])
                ->sum('subtotal'),

            'todayPreOrderDeliveries' => PreOrder::where(['status' => 'pending', 'delivery_date' => $today])->count(),

            'todayInvoice' => Sale::whereDate('created_at', Carbon::now()->format('Y-m-d'))->count(),

            'thisMonthTotalWastages' => InventoryAdjustment::whereIn('store_id', $store_ids)
                ->whereMonth('created_at', Carbon::now()->month)
                ->where('transaction_type', 'decrease')
                ->sum('subtotal'),
        ];
        $currentDate = Carbon::now();
        $startDate = $currentDate->copy()->subMonth()->startOfMonth();
        $endDate = $currentDate->copy()->subMonth()->endOfMonth();
        $noOfWeeks = $currentDate->copy()->subMonth()->weekOfMonth;
        $totalDays = $startDate->daysInMonth;

        $daysPerPart = ceil($totalDays / $noOfWeeks);

        $parts = [];

        for ($part = 0; $part < $noOfWeeks; $part++) {
            // Calculate the start and end dates of the current part
            $partStartDate = $startDate->copy()->addDays($part * $daysPerPart);
            $partEndDate = $startDate->copy()->addDays(($part + 1) * $daysPerPart - 1)->endOfDay();

            if ($partEndDate->gt($endDate)) {
                $partEndDate = $endDate->copy()->endOfDay();
            }
            $parts[] = InventoryAdjustment::whereIn('store_id', $store_ids)
                ->whereBetween('created_at', [$partStartDate, $partEndDate])
                ->where('transaction_type', 'decrease')
                ->sum('subtotal');
        }

        $monthlyDeliveries = RequisitionDelivery::where('type', 'FG')->select('from_store_id', DB::raw('count(id) as total'))
            ->whereMonth('created_at', Carbon::now()->month)
            ->orderBy('total', 'DESC')
            ->groupBy('from_store_id')
            ->with('fromStore')
            ->get();
        $todayRequisitions = Requisition::where('to_factory_id', $factory_id)->whereDate('created_at', Carbon::now()->format('Y-m-d'))->get();

        $data = [

            'TotalNewRequisitions' => $todayData['TotalNewRequisitions'],
            'monthlyTotalRequisitions' => $monthlyTotalRequisitions,
            'todayTotalRequisitions' => $todayData['todayTotalRequisitions'],
            'todayTotalDeliveries' => $todayData['todayTotalDeliveries'],
            'todayTotalWastages' => round($todayData['todayTotalWastages']),
            'todayPreOrderDeliveries' => $todayData['todayPreOrderDeliveries'],
            'todayInvoice' => $todayData['todayInvoice'],
            'thisMonthTotalWastages' => $todayData['thisMonthTotalWastages'],
            'thisMonthWastages' => $parts,
            'monthlyTotalDeliveries' => 0,
//            'stock' => [
//                'total' => round($totalStock),
//                'productWise' => $productWiseStock
//            ],
//            'fgStock' => [
//                'total' => round($fgTotalStock),
//                'productWise' => $fgProductWiseStock
//            ],
            'todayRequisitions' => $todayRequisitions,
            'monthlyDeliveries' => $monthlyDeliveries,
        ];
        return view('dashboard.factory', $data);

    }
}
