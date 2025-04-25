<?php

namespace App\Http\Controllers\Api\Web;

use App\Http\Controllers\Controller;
use App\Models\ChartOfInventory;
use App\Models\InventoryTransaction;
use App\Models\Store;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use niklasravnsborg\LaravelPdf\Facades\Pdf;

class FGInventoryReportController extends Controller
{

    public function index()
    {
//        return testFGreport(3, '2024,12-01');
        return view('finish_goods_inventory_report.index');
    }

    public function create()
    {
        ini_set('memory_limit', '600M'); // or '-1' for unlimited

        $asOnDate = Carbon::parse(\request()->as_on_date)->format('Y-m-d') ?? Carbon::now()->format('Y-m-d');

        $page_title = false;
        $report_header = 'FG Inventory Report';
        $run_query = true;

        $report_type = \request()->report_type;
        if ($report_type == 'all_groups') {
            $statement = get_all_groups_report($asOnDate ,'FG');
        } elseif ($report_type == 'single_group_item') {
            $page_title = 'Group Name: ' . ChartOfInventory::find(\request()->group_id)->name;
            $statement = get_all_items_by_group(\request()->group_id, $asOnDate,'FG');
        } elseif ($report_type == 'all_item') {
            $statement = get_all_items($asOnDate,'FG');
        } elseif ($report_type == 'store_group') {
            $report_header .= ' ( All Store Summary)';
            $statement = get_all_stores($asOnDate,'FG');
        } elseif ($report_type == 'store_group_item') {
            $report_header .= ' ( Single Store)';
            $page_title = 'Store Name: ' . Store::find(\request()->store_id)->name;
            $run_query = false;
//            $statement = get_all_items_by_store(\request()->store_id, $asOnDate,'FG');
            $getPost = $this->testFGreport(\request()->store_id, $asOnDate);
        }
        if ($run_query){
            $getPost = DB::select($statement);
        }

        if (!count($getPost) > 0){
            return false;
        }
        $columns = array_keys((array)$getPost[0]);
//        dd($getPost) ;
        $data = [
            'dateRange' => 'as On  ' . $asOnDate,
            'data' => $getPost,
            'page_title' => $page_title,
            'columns' => $columns,
            'report_header'=>$report_header
        ];
        $pdf = Pdf::loadView('common.report_main', $data);
//        return $pdf->stream();
        $pdf->stream();
    }

    function testFGreport($store_id, $date)
    {
        $data = [];
        $grand_total_transit_stock = 0;
        $grand_total_balance_qty = 0;

        // Get distinct parent IDs to process in chunks
        $parentIds = \App\Models\ChartOfInventory::whereHas('parent')
            ->where(['rootAccountType' => 'FG', 'type' => 'item'])
            ->select('parent_id')
            ->distinct()
            ->pluck('parent_id')
            ->toArray();

        // Process parent IDs in chunks
        foreach (array_chunk($parentIds, 10) as $parentIdChunk) {
            // Get items for this chunk of parent IDs
            $parents = \App\Models\ChartOfInventory::with([
                'parent',
                'inventoryTransactions' => function($query) use ($store_id, $date) {
                    $query->where('store_id', $store_id)
                        ->whereDate('date', '<=', $date);
                }])
                ->whereHas('parent')
                ->where(['rootAccountType' => 'FG', 'type' => 'item'])
                ->whereIn('parent_id', $parentIdChunk)
                ->orderBy('parent_id')
                ->get()
                ->groupBy('parent_id');

            foreach ($parents as $parent_id => $parent) {
                $parent_total_transit_stock = 0;
                $parent_total_balance_qty = 0;

                foreach ($parent as $key => $item) {
                    // Process requisition delivery items in chunks
                    $transit_delivery_qty = $this->getRequisitionDeliverySum($item->id, $store_id);

                    // Process inventory transfer items in chunks
                    $transit_transfer_qty = $this->getInventoryTransferSum($item->id, $store_id);

                    // Process pre-order items in chunks
                    $transit_pre_order_qty = $this->getPreOrderSum($item->id, $store_id);

                    $total_transit_stock = $transit_delivery_qty + $transit_transfer_qty + $transit_pre_order_qty;

                    // Calculate main balance from eager-loaded transactions
                    $main_balance = $item->inventoryTransactions->sum(function($transaction) {
                        return $transaction->type * $transaction->quantity;
                    });

                    $parent_total_transit_stock += $total_transit_stock;
                    $parent_total_balance_qty += $main_balance;

                    $data[] = [
                        'Group Name' => $item->parent->name,
                        'Item ID' => $item->id,
                        'Item Name' => $item->name,
                        'Transit Stock' => $total_transit_stock,
                        'Balance Qty' => number_format($main_balance, 2),
                        'Rate' => number_format($item->price, 2, '.', ','),
                        'Value' => number_format($item->price * $main_balance, 2, '.', ','),
                    ];
                }

                $grand_total_transit_stock += $parent_total_transit_stock;
                $grand_total_balance_qty += $parent_total_balance_qty;

                $data[] = [
                    'Group Name' => '',
                    'Item ID' => '',
                    'Item Name' => $parent[0]->parent->name .' Total',
                    'Transit Stock' => $parent_total_transit_stock,
                    'Balance Qty' => number_format($parent_total_balance_qty, 2),
                    'Rate' => '',
                    'Value' => ''
                ];
            }

            // Clear memory after processing each chunk
            unset($parents);
            gc_collect_cycles();
        }

        $data[] = [
            'Group Name' => '',
            'Item ID' => '',
            'Item Name' => 'Grand Total',
            'Transit Stock' => $grand_total_transit_stock,
            'Balance Qty' => number_format($grand_total_balance_qty, 2),
            'Rate' => '',
            'Value' => '0'
        ];

        return $data;
    }

// Helper methods to get sums in chunks
    private function getRequisitionDeliverySum($item_id, $store_id)
    {
        $total = 0;
        $limit = 1000;
        $offset = 0;

        while (true) {
            $requisitionDeliveryItems = \App\Models\RequisitionDeliveryItem::where('chart_of_inventory_id', $item_id)
                ->whereHas('requisitionDelivery', function ($q) use ($store_id) {
                    return $q->where(['status' => 'completed', 'type' => 'FG', 'from_store_id' => $store_id]);
                })
                ->offset($offset)
                ->limit($limit)
                ->get();

            if ($requisitionDeliveryItems->isEmpty()) {
                break;
            }

            $total += $requisitionDeliveryItems->sum('quantity');
            $offset += $limit;

            unset($requisitionDeliveryItems);
        }

        return $total;
    }

    private function getInventoryTransferSum($item_id, $store_id)
    {
        $total = 0;
        $limit = 1000;
        $offset = 0;

        while (true) {
            $inventoryTransferItems = \App\Models\InventoryTransferItem::where('chart_of_inventory_id', $item_id)
                ->whereHas('inventoryTransfer', function ($q) use ($store_id) {
                    return $q->where(['status' => 'pending', 'type' => 'FG', 'from_store_id' => $store_id]);
                })
                ->offset($offset)
                ->limit($limit)
                ->get();

            if ($inventoryTransferItems->isEmpty()) {
                break;
            }

            $total += $inventoryTransferItems->sum('quantity');
            $offset += $limit;

            unset($inventoryTransferItems);
        }

        return $total;
    }

    private function getPreOrderSum($item_id, $store_id)
    {
        $total = 0;
        $limit = 1000;
        $offset = 0;

        while (true) {
            $preOrderItems = \App\Models\PreOrderItem::where('chart_of_inventory_id', $item_id)
                ->whereHas('preOrder', function ($q) use ($store_id) {
                    return $q->where(['status' => 'delivered', 'factory_delivery_store_id' => $store_id]);
                })
                ->offset($offset)
                ->limit($limit)
                ->get();

            if ($preOrderItems->isEmpty()) {
                break;
            }

            $total += $preOrderItems->sum('quantity');
            $offset += $limit;

            unset($preOrderItems);
        }

        return $total;
    }
}
