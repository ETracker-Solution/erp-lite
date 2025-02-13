<?php

namespace App\Http\Controllers\Welkin;

use App\Http\Controllers\Controller;
use App\Models\ChartOfInventory;
use App\Models\InventoryTransaction;
use App\Models\Store;
use App\Services\ExportService;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;

class StockReportController extends Controller
{
    protected $exportService;

    public function __construct(ExportService $exportService)
    {
        $this->exportService = $exportService;
    }

    public function index()
    {
        // $stores = Store::select('id','name')->get();
        return view('exports.raw_material_inventory_report.welkin.index');
    }

    public function generateReport(Request $request)
    {
       $request->validate([
           'report_type'=>'required'
       ]);
       if(!str_contains($request->report_type,'only_closing') && (!$request->from_date || !$request->from_date)){
           Toastr::error('From Date and To Date are required.');
           return back();
       }
        return $this->exportReport($request->report_type, $request->store_id);
    }

    public function exportReport($report_type, $store_id = null)
    {
        if ($store_id) {
            $store = Store::find($store_id);
        }
        $type = 'pdf';
        $viewFileName = 'raw_material_inventory_report.welkin.closing_balance';
        $filenameToDownload = date('ymdHis') . '_closing_balance';
        $pageOrientation = 'L';
        if(!str_contains($report_type,'pdf')){
            $type = 'xlsx';
        }
        if(str_contains($report_type,'only_closing')){
            $viewFileName = 'raw_material_inventory_report.welkin.closing_balance';
            $filenameToDownload = date('ymdHis') . '_closing_balance';
            $exportableData = $this->getClosingBalanceExportableData();
            $pageOrientation = 'P';
        }
        if(str_contains($report_type,'in_out')){
            $viewFileName = 'raw_material_inventory_report.welkin.inword_outword';
            $filenameToDownload = date('ymdHis') . '_inward_outward';
            $exportableData = $this->getInOutExportableData();
        }
        if(str_contains($report_type,'open_close')){
            $viewFileName = 'raw_material_inventory_report.welkin.opening_closing';
            $filenameToDownload = date('ymdHis') . '_opening_closing_balance';
            $exportableData = $this->getInOutExportableData();
        }
        $data = [
            'products' => $exportableData,
        ];

        if ($store) {
            $data['store'] = $store;
        }

        return $this->exportService->exportFile($type, $viewFileName, $data, $filenameToDownload, $pageOrientation); // L stands for Landscape, if Portrait needed, just remove this params
    }

    public function getClosingBalanceExportableData(){
        $fromDate = null;
        $toDate = null;
        $storeId = request('store_id');
        $rootAccountType = request('rootAccountType');
        return ChartOfInventory::query()
            ->when($rootAccountType, fn($query) => $query->where('rootAccountType', $rootAccountType))
            ->select('id', 'name', 'type', 'rootAccountType', 'price', 'parent_id', 'unit_id', 'alter_unit_id','a_unit_quantity')
            ->with([
                'subChartOfInventories' => function ($query) use ($fromDate, $toDate, $storeId) {
                    $query->select('id', 'name', 'type', 'parent_id', 'unit_id', 'alter_unit_id','a_unit_quantity')
                        ->addSelect([
                            'current_stock' => InventoryTransaction::selectRaw('COALESCE(SUM(type * quantity), 0)')
                                ->whereColumn('coi_id', 'chart_of_inventories.id')
                                ->when($fromDate, function ($query) use ($fromDate) {
                                    return $query->where('date', '>=', $fromDate);
                                })
                                ->when($toDate, function ($query) use ($toDate) {
                                    return $query->where('date', '<=', $toDate);
                                })
                                ->when($storeId, function ($query) use ($storeId) {
                                    return $query->where('store_id', $storeId);
                                })
                                ->groupBy('coi_id'),
                            'rate' => InventoryTransaction::selectRaw('COALESCE(ROUND(SUM(amount) / NULLIF(SUM(type * quantity), 0), 2), 0.00)')
                                ->whereColumn('coi_id', 'chart_of_inventories.id')
                                ->where('type', 1)
                                ->when($fromDate, function ($query) use ($fromDate) {
                                    return $query->where('date', '>=', $fromDate);
                                })
                                ->when($toDate, function ($query) use ($toDate) {
                                    return $query->where('date', '<=', $toDate);
                                })
                                ->when($storeId, function ($query) use ($storeId) {
                                    return $query->where('store_id', $storeId);
                                })
                                ->groupBy('coi_id'),
                        ])
                        ->with(['unit:id,name', 'alterUnit:id,name']);
                },
            ])
            ->whereHas('subChartOfInventories', function ($q) {
                return $q->where('type', 'item');
            })->get();
    }

    public function getInOutExportableData()
    {
        $fromDate = request('from_date');
        $toDate = request('end_date');
        $storeId = request('store_id');
        $rootAccountType = request('rootAccountType');
        return ChartOfInventory::query()
            ->when($rootAccountType, fn($query) => $query->where('rootAccountType', $rootAccountType))
            ->select('id', 'name', 'type', 'rootAccountType', 'price', 'parent_id', 'unit_id', 'alter_unit_id', 'a_unit_quantity')
            ->with([
                'subChartOfInventories' => function ($query) use ($fromDate, $toDate, $storeId) {
                    $query->select('id', 'name', 'type', 'parent_id', 'unit_id', 'alter_unit_id', 'a_unit_quantity')
                        ->addSelect([
                            // Opening Balance (Before fromDate)
                            'opening_stock' => InventoryTransaction::selectRaw('COALESCE(SUM(type * quantity), 0)')
                                ->whereColumn('coi_id', 'chart_of_inventories.id')
                                ->where('date', '<', $fromDate)
                                ->when($storeId, fn($query) => $query->where('store_id', $storeId))
                                ->groupBy('coi_id'),

                            'opening_rate' => InventoryTransaction::selectRaw('COALESCE(ROUND(SUM(amount) / NULLIF(SUM(type * quantity), 0), 2), 0.00)')
                                ->whereColumn('coi_id', 'chart_of_inventories.id')
                                ->where('date', '<', $fromDate)
                                ->where('type', 1)
                                ->when($storeId, fn($query) => $query->where('store_id', $storeId))
                                ->groupBy('coi_id'),

                            // Incoming Stock & Rate (During the given range)
                            'incoming_stock' => InventoryTransaction::selectRaw('COALESCE(SUM(quantity), 0)')
                                ->whereColumn('coi_id', 'chart_of_inventories.id')
                                ->where('type', 1)
                                ->whereBetween('date', [$fromDate, $toDate])
                                ->when($storeId, fn($query) => $query->where('store_id', $storeId))
                                ->groupBy('coi_id'),

                            'incoming_rate' => InventoryTransaction::selectRaw('COALESCE(ROUND(SUM(amount) / NULLIF(SUM(quantity), 0), 2), 0.00)')
                                ->whereColumn('coi_id', 'chart_of_inventories.id')
                                ->where('type', 1)
                                ->whereBetween('date', [$fromDate, $toDate])
                                ->when($storeId, fn($query) => $query->where('store_id', $storeId))
                                ->groupBy('coi_id'),

                            // Outgoing Stock & Rate (During the given range)
                            'outgoing_stock' => InventoryTransaction::selectRaw('COALESCE(SUM(quantity), 0)')
                                ->whereColumn('coi_id', 'chart_of_inventories.id')
                                ->where('type', -1)
                                ->whereBetween('date', [$fromDate, $toDate])
                                ->when($storeId, fn($query) => $query->where('store_id', $storeId))
                                ->groupBy('coi_id'),

                            'outgoing_rate' => InventoryTransaction::selectRaw('COALESCE(ROUND(SUM(amount) / NULLIF(SUM(quantity), 0), 2), 0.00)')
                                ->whereColumn('coi_id', 'chart_of_inventories.id')
                                ->where('type', -1)
                                ->whereBetween('date', [$fromDate, $toDate])
                                ->when($storeId, fn($query) => $query->where('store_id', $storeId))
                                ->groupBy('coi_id'),

                            // Closing Balance (Stock at toDate)
                            'closing_stock' => InventoryTransaction::selectRaw('COALESCE(SUM(type * quantity), 0)')
                                ->whereColumn('coi_id', 'chart_of_inventories.id')
                                ->where('date', '<=', $toDate)
                                ->when($storeId, fn($query) => $query->where('store_id', $storeId))
                                ->groupBy('coi_id'),

                            'closing_rate' => InventoryTransaction::selectRaw('COALESCE(ROUND(SUM(amount) / NULLIF(SUM(type * quantity), 0), 2), 0.00)')
                                ->whereColumn('coi_id', 'chart_of_inventories.id')
                                ->where('type', 1)
                                ->where('date', '<=', $toDate)
                                ->when($storeId, fn($query) => $query->where('store_id', $storeId))
                                ->groupBy('coi_id'),
                        ])
                        ->with(['unit:id,name', 'alterUnit:id,name']);
                },
            ])
            ->whereHas('subChartOfInventories', fn($q) => $q->where('type', 'item'))
            ->get();
    }
}
