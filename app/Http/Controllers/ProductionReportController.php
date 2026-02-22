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
    public function getReports(Request $request){
        $type      = $request->type;
        $dateRange = $request->date_range;
        $storeId   = $request->store_id;
        [$from, $to] = explode(' to ', $dateRange);

        if ($type == 'all'){
            $allConsumption = InventoryTransaction::with('chartOfInventory.unit','chartOfInventory.parent','chartOfInventory.productionRecipes','chartOfInventory.productionRecipes.coi')->where('doc_type',['FGP','POS','PO'])->where('type', 1)
                ->whereHas('chartOfInventory', function ($query) {
                    $query->where('rootAccountType', 'FG');
                })
                ->whereBetween('created_at', [$from, $to])
                ->where('store_id',$storeId)
                ->get();
            $pdf = PDF::loadView('production.report.all_consumption_pdf', [
                'allConsumption' => $allConsumption,
                'from' => $from,
                'to' => $to
            ]);
            return $pdf->download('rm_consumption_report_'.$from.'_to_'.$to.'.pdf');

        }elseif ($type == 'preorder'){

            $preOrderConsumption = InventoryTransaction::with('chartOfInventory.unit','chartOfInventory.parent','chartOfInventory.productionRecipes','chartOfInventory.productionRecipes.coi')->where('doc_type',['FGP','POS','PO'])->where('type', 1)
                ->whereHas('chartOfInventory', function ($query) {
                    $query->where('rootAccountType', 'FG');
                })
                ->whereHas('productions', function ($query) {
                    $query->where('remark', 'Production From Pre Order');
              })
                ->whereBetween('created_at', [$from, $to])
                ->where('store_id',$storeId)
                ->get();

            $pdf = PDF::loadView('production.report.pre_order_consumption_pdf', [
                'preOrderConsumption' => $preOrderConsumption,
                'from' => $from,
                'to' => $to
            ]);
            return $pdf->download('pre_order_consumption_report_'.$from.'_to_'.$to.'.pdf');

        }elseif ($type == 'without_preorder'){

            $withOutPreOrderConsumption = InventoryTransaction::with([
                'chartOfInventory.unit',
                'chartOfInventory.parent',
                'chartOfInventory.productionRecipes',
                'chartOfInventory.productionRecipes.coi'
            ])
                ->whereIn('doc_type', ['FGP', 'POS', 'PO'])
                ->where('type', 1)
                ->whereHas('chartOfInventory', function ($query) {
                    $query->where('rootAccountType', 'FG');
                })
                ->whereHas('productions', function ($query) {
                    $query->where(function ($query) {
                        $query->where('remark', '!=', 'Production From Pre Order')
                            ->orWhereNull('remark');
                    });
                })
                ->whereBetween('created_at', [$from, $to])
                ->where('store_id',$storeId)
                ->get();

            $pdf = PDF::loadView('production.report.with_out_consumption_pdf', [
                'withOutPreOrderConsumption' => $withOutPreOrderConsumption,
                'from' => $from,
                'to' => $to
            ]);
            return $pdf->download('without_pre_order_consumption_report_'.$from.'_to_'.$to.'.pdf');


        }elseif ($type == 'total_production'){

            $totalProduction = InventoryTransaction::with('chartOfInventory.unit','chartOfInventory.parent','chartOfInventory.productionRecipes','chartOfInventory.productionRecipes.coi')->where('doc_type',['FGP','POS','PO'])->where('type', 1)
                ->whereHas('chartOfInventory', function ($query) {
                    $query->where('rootAccountType', 'FG');
                })
                ->whereBetween('created_at', [$from, $to])
                ->where('store_id',$storeId)
                ->get();
            $pdf = PDF::loadView('production.report.total_production_pdf', [
                'totalProduction' => $totalProduction,
                'from' => $from,
                'to' => $to
            ]);
            return $pdf->download('total_production_report_'.$from.'_to_'.$to.'.pdf');

        }elseif ($type == 'total_consumption'){

            $totalConsumption = InventoryTransaction::with('chartOfInventory.unit','chartOfInventory.parent','chartOfInventory.productionRecipes','chartOfInventory.productionRecipes.coi','chartOfInventory.productionRecipes.coi.parent')->where('doc_type',['FGP','POS','PO'])->where('type', 1)
                ->whereHas('chartOfInventory', function ($query) {
                    $query->where('rootAccountType', 'FG');
                })
                ->whereBetween('created_at', [$from, $to])
                ->where('store_id',$storeId)
                ->get();

            $pdf = PDF::loadView('production.report.total_consumption_pdf', [
                'totalConsumption' => $totalConsumption,
                'from' => $from,
                'to' => $to
            ]);
            return $pdf->download('total_consumption_report_'.$from.'_to_'.$to.'.pdf');
        }

    }
}
