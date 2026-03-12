<?php

namespace App\Http\Controllers;

use App\Models\InventoryTransaction;
use App\Models\Store;
use Illuminate\Http\Request;
use niklasravnsborg\LaravelPdf\Facades\Pdf;

class ProductionReportController extends Controller
{
    public function index()
    {
        $stores = Store::where('status', 'active')->get();
        return view('production.report.index', compact('stores'));
    }

    public function getReports(Request $request)
    {
        $request->validate([
            'type' => 'required',
            'date_range' => 'required',
            'store_id' => 'required|exists:stores,id',
        ]);

        $type = $request->type;
        $dateRange = $request->date_range;
        $storeId = $request->store_id;
        $exportType = $request->export_type ?? 'pdf';
        
        $store = Store::find($storeId);
        $storeName = $store ? $store->name : '';

        // Handle single date OR date range
        if (strpos($dateRange, ' to ') !== false) {
            [$from, $to] = explode(' to ', $dateRange);
        } else {
            $from = $dateRange;
            $to = $dateRange;
        }

        // Basic date format validation check (or just use carbon to ensure consistency)
        try {
            $from = \Carbon\Carbon::parse($from)->toDateString();
            $to = \Carbon\Carbon::parse($to)->toDateString();
        } catch (\Exception $e) {
            return back()->with('error', 'Invalid date format.');
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

        if (in_array($type, ['all', 'preorder', 'without_preorder', 'total_production'])) {
            
            if ($type == 'preorder') {
                $baseQuery->whereHas('productions', function ($query) {
                    $query->where('remark', 'Auto Production From Pre Order');
                });
            } elseif ($type == 'without_preorder') {
                $baseQuery->whereHas('productions', function ($query) {
                    $query->where(function ($q) {
                        $q->where('remark', '!=', 'Auto Production From Pre Order')
                            ->orWhereNull('remark');
                    });
                });
            }

            $transactions = $baseQuery->get();
            $processedData = $this->processFGConsumptionData($transactions, $type);

            if ($exportType == 'excel') {
                return $this->exportFGToExcel($processedData, $type, $from, $to, $storeName);
            }

            $view = match($type) {
                'preorder' => 'production.report.pre_order_consumption_pdf',
                'without_preorder' => 'production.report.with_out_consumption_pdf',
                'total_production' => 'production.report.total_production_pdf',
                default => 'production.report.all_consumption_pdf'
            };

            $pdf = PDF::loadView($view, [
                'processedData' => $processedData,
                'from' => $from,
                'to' => $to,
                'store' => $storeName
            ]);

            return $pdf->stream("{$type}_report_{$from}_to_{$to}.pdf");
        }

        if ($type == 'total_consumption') {

            $transactions = InventoryTransaction::with([
                'chartOfInventory.unit',
                'chartOfInventory.parent',
                'chartOfInventory.productionRecipes',
                'chartOfInventory.productionRecipes.coi',
                'chartOfInventory.productionRecipes.coi.parent'
            ])
                ->whereIn('doc_type', ['FGP', 'POS', 'PO'])
                ->where('type', '=', '-1')
                ->whereHas('chartOfInventory', function ($query) {
                    $query->where('rootAccountType', 'RM');
                })
                ->whereBetween('date', [$from, $to])
                ->where('store_id', $storeId)->get();
            
            $rmGroupSummary = $this->processRMConsumptionData($transactions);

            if ($exportType == 'excel') {
                return $this->exportRMToExcel($rmGroupSummary, $from, $to, $storeName);
            }

            $pdf = PDF::loadView('production.report.total_consumption_pdf', [
                'rmGroupSummary' => $rmGroupSummary,
                'from' => $from,
                'to' => $to,
                'store' => $storeName
            ]);

            return $pdf->stream("total_consumption_report_{$from}_to_{$to}.pdf");
        }
    }

    private function processFGConsumptionData($transactions, $type)
    {
        $processedData = [];
        foreach ($transactions as $transaction) {
            $fgItem = $transaction->chartOfInventory;
            if (!$fgItem) continue;

            $fgName = $fgItem->name;
            $groupName = $fgItem->parent->name ?? 'Uncategorized';
            $quantity = $transaction->quantity;

            if (!isset($processedData[$groupName])) {
                $processedData[$groupName] = [];
            }

            if (!isset($processedData[$groupName][$fgName])) {
                $processedData[$groupName][$fgName] = [
                    'quantity' => 0,
                    'rm_items' => []
                ];
            }

            $processedData[$groupName][$fgName]['quantity'] += $quantity;

            if ($type != 'total_production') {
                foreach ($fgItem->productionRecipes as $recipe) {
                    $rmItem = $recipe->coi;
                    if (!$rmItem) continue;

                    $rmName = $rmItem->name;
                    $unit = $rmItem->unit->name ?? '';
                    $rmQuantity = $recipe->qty * $quantity;
                    $stockCost = $rmItem->price ?? 0;

                    if (!isset($processedData[$groupName][$fgName]['rm_items'][$rmName])) {
                        $processedData[$groupName][$fgName]['rm_items'][$rmName] = [
                            'unit' => $unit,
                            'stock_cost' => $stockCost,
                            'total_quantity' => 0
                        ];
                    }

                    $processedData[$groupName][$fgName]['rm_items'][$rmName]['total_quantity'] += $rmQuantity;
                }
            }
        }

        ksort($processedData);
        if ($type == 'total_production') {
            foreach ($processedData as &$fgItems) {
                ksort($fgItems);
            }
        }
        return $processedData;
    }

    private function processRMConsumptionData($transactions)
    {
        $rmGroupSummary = [];
        foreach ($transactions as $transaction) {
            $item = $transaction->chartOfInventory;
            if (!$item) continue;

            $groupName = $item->parent->name ?? 'Uncategorized';
            $itemName  = $item->name;
            $unit      = $item->unit->name ?? '';
            $qty       = $transaction->quantity ?? 0;

            if (!isset($rmGroupSummary[$groupName])) {
                $rmGroupSummary[$groupName] = [
                    'total_items' => 0,
                    'items' => []
                ];
            }

            if (!isset($rmGroupSummary[$groupName]['items'][$itemName])) {
                $rmGroupSummary[$groupName]['items'][$itemName] = [
                    'total_quantity' => 0,
                    'unit' => $unit
                ];
            }

            $rmGroupSummary[$groupName]['items'][$itemName]['total_quantity'] += $qty;
            $rmGroupSummary[$groupName]['total_items']++;
        }
        ksort($rmGroupSummary);
        return $rmGroupSummary;
    }

    private function exportFGToExcel($processedData, $type, $from, $to, $storeName)
    {
        $rows = [];
        foreach ($processedData as $groupName => $fgItems) {
            foreach ($fgItems as $fgName => $fgData) {
                if ($type == 'total_production') {
                    $rows[] = [
                        'Group Name' => $groupName,
                        'Item Name' => $fgName,
                        'Quantity' => $fgData['quantity']
                    ];
                } else {
                    $rows[] = [
                        'Group Name' => $groupName,
                        'FG Item' => $fgName,
                        'FG Qty' => $fgData['quantity'],
                        'RM Item' => '',
                        'RM Unit' => '',
                        'RM Consumption Qty' => ''
                    ];
                    foreach ($fgData['rm_items'] as $rmName => $rmData) {
                        $rows[] = [
                            'Group Name' => '',
                            'FG Item' => '',
                            'FG Qty' => '',
                            'RM Item' => $rmName,
                            'RM Unit' => $rmData['unit'],
                            'RM Consumption Qty' => $rmData['total_quantity']
                        ];
                    }
                }
            }
        }
        return (new \Rap2hpoutre\FastExcel\FastExcel($rows))->download("{$type}_report_{$from}_to_{$to}.xlsx");
    }

    private function exportRMToExcel($rmGroupSummary, $from, $to, $storeName)
    {
        $rows = [];
        foreach ($rmGroupSummary as $groupName => $groupData) {
            foreach ($groupData['items'] as $itemName => $itemData) {
                $rows[] = [
                    'RM Group' => $groupName,
                    'RM Item' => $itemName,
                    'Unit' => $itemData['unit'],
                    'Total Quantity' => $itemData['total_quantity']
                ];
            }
        }
        return (new \Rap2hpoutre\FastExcel\FastExcel($rows))->download("total_consumption_report_{$from}_to_{$to}.xlsx");
    }
}
