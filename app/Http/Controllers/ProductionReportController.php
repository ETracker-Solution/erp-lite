<?php

namespace App\Http\Controllers;

use App\Models\InventoryTransaction;
use App\Models\Store;
use Illuminate\Http\Request;
use niklasravnsborg\LaravelPdf\Facades\Pdf;

class ProductionReportController extends Controller
{
    public function index(){
       $stores = Store::where('status', 'active')->get();
      return view('production.report.index',compact('stores'));
    }
    public function getReports(Request $request)
    {
        $type      = $request->type;
        $dateRange = $request->date_range;
        $storeId   = $request->store_id;

        // Handle single date OR date range
        if (strpos($dateRange, ' to ') !== false) {
            [$from, $to] = explode(' to ', $dateRange);
        } else {
            $from = $dateRange;
            $to   = $dateRange;
        }

        $baseQuery = InventoryTransaction::with([
            'chartOfInventory.unit',
            'chartOfInventory.parent',
            'chartOfInventory.productionRecipes',
            'chartOfInventory.productionRecipes.coi',
            'chartOfInventory.productionRecipes.coi.parent'
        ])
            ->whereIn('doc_type', ['FGP', 'POS', 'PO'])
            ->where('type', 1)
            ->whereHas('chartOfInventory', function ($query) {
                $query->where('rootAccountType', 'FG');
            })
            ->whereBetween('date', [$from, $to])
            ->where('store_id', $storeId);


        if ($type == 'all') {

            $data = $baseQuery->get();

            $pdf = PDF::loadView('production.report.all_consumption_pdf', [
                'allConsumption' => $data,
                'from' => $from,
                'to'   => $to
            ]);

            return $pdf->download("rm_consumption_report_{$from}_to_{$to}.pdf");
        }


        if ($type == 'preorder') {

            $data = $baseQuery
                ->whereHas('productions', function ($query) {
                    $query->where('remark', 'Auto Production From Pre Order');
                })
                ->get();

            $pdf = PDF::loadView('production.report.pre_order_consumption_pdf', [
                'preOrderConsumption' => $data,
                'from' => $from,
                'to'   => $to
            ]);

            return $pdf->download("pre_order_consumption_report_{$from}_to_{$to}.pdf");
        }


        if ($type == 'without_preorder') {

            $data = $baseQuery
                ->whereHas('productions', function ($query) {
                    $query->where(function ($q) {
                        $q->where('remark', '!=', 'Auto Production From Pre Order')
                            ->orWhereNull('remark');
                    });
                })
                ->get();

            $pdf = PDF::loadView('production.report.with_out_consumption_pdf', [
                'withOutPreOrderConsumption' => $data,
                'from' => $from,
                'to'   => $to
            ]);

            return $pdf->download("without_pre_order_consumption_report_{$from}_to_{$to}.pdf");
        }


        if ($type == 'total_production') {

            $data = $baseQuery->get();

            $pdf = PDF::loadView('production.report.total_production_pdf', [
                'totalProduction' => $data,
                'from' => $from,
                'to'   => $to
            ]);

            return $pdf->download("total_production_report_{$from}_to_{$to}.pdf");
        }


        if ($type == 'total_consumption') {

            $data = InventoryTransaction::with([
                'chartOfInventory.unit',
                'chartOfInventory.parent',
                'chartOfInventory.productionRecipes',
                'chartOfInventory.productionRecipes.coi',
                'chartOfInventory.productionRecipes.coi.parent'
            ])
                ->whereIn('doc_type', ['FGP', 'POS', 'PO'])
                ->where('type','=', '-1')
                ->whereHas('chartOfInventory', function ($query) {
                    $query->where('rootAccountType', 'RM');
                })
                ->whereBetween('date', [$from, $to])
                ->where('store_id', $storeId)->get();

            $pdf = PDF::loadView('production.report.total_consumption_pdf', [
                'totalConsumption' => $data,
                'from' => $from,
                'to'   => $to
            ]);

            return $pdf->download("total_consumption_report_{$from}_to_{$to}.pdf");
        }
    }
}
