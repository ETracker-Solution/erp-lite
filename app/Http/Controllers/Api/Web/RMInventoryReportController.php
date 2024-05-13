<?php

namespace App\Http\Controllers\Api\Web;

use App\Http\Controllers\Controller;
use App\Models\ChartOfInventory;
use App\Models\InventoryTransaction;
use App\Models\Store;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use niklasravnsborg\LaravelPdf\Facades\Pdf;

class RMInventoryReportController extends Controller
{
    protected array $visible_columns = [
        'group' => true,
        'item' => false,
        'store' => false,
        'quantity' => true,
    ];
    protected bool $one_title = false;
    protected string $one_value = '';

    public function index()
    {
        return view('raw_material_inventory_report.index');
    }

    public function getInventoryData()
    {
        $fromDate = Carbon::parse(\request()->from_date)->format('Y-m-d') ?? Carbon::now()->format('Y-m-d');
        $toDate = Carbon::parse(\request()->to_date)->format('Y-m-d') ?? Carbon::now()->format('Y-m-d');
        $inventoryTransactions = InventoryTransaction::select(
            'CIP.name as group',
            'CI.name as item',
            DB::raw('SUM(inventory_transactions.quantity * inventory_transactions.type) as total, SUM(amount) as TAmount'),
            'ST.name as store'
        )
            ->join('chart_of_inventories as CI', 'CI.id', '=', 'inventory_transactions.coi_id')
            ->join('chart_of_inventories as CIP', 'CIP.id', '=', 'CI.parent_id')
            ->join('stores as ST', 'inventory_transactions.store_id', '=', 'ST.id')
            ->whereBetween('inventory_transactions.date', [$fromDate, $toDate]);

        return $this->getFilteredDataByReportType($inventoryTransactions);

    }

    public function getFilteredDataByReportType($items)
    {
        $report_type = \request()->report_type;
        if ($report_type == 'all_group') {
            $items = $items->groupBy('CIP.id');
            $this->visible_columns['group'] = true;
        } elseif ($report_type == 'single_group_item') {
            $this->visible_columns['item'] = true;
            $this->visible_columns['group'] = false;
            $this->one_title = true;
            $this->one_value = 'group';
            $items = $items->where('CIP.id', \request()->group_id);
            $items = $items->groupBy('CIP.id', 'CI.id');
        } elseif ($report_type == 'all_item') {
            $this->visible_columns['item'] = true;
            $this->visible_columns['group'] = true;
            $items = $items->groupBy('CI.id');
        } elseif ($report_type == 'store_group') {
            $this->visible_columns['store'] = true;
            $this->visible_columns['group'] = true;
            $this->visible_columns['item'] = true;
            $items = $items->groupBy('ST.id', 'CIP.id', 'CI.id');
        } elseif ($report_type == 'store_group_item') {
            $this->visible_columns['store'] = false;
            $this->visible_columns['group'] = true;
            $this->visible_columns['item'] = true;
            $this->one_title = true;
            $this->one_value = 'store';
            $items = $items->groupBy('ST.id', 'CIP.id', 'CI.id');
        }
        return $items;
    }

    public function create()
    {

        $asOnDate = Carbon::parse(\request()->as_on_date)->format('Y-m-d') ?? Carbon::now()->format('Y-m-d');

        $page_title = false;

        $report_type = \request()->report_type;
        if ($report_type == 'all_groups') {
            $statement = "CALL get_all_groups('" . $asOnDate . "')";
        } elseif ($report_type == 'single_group_item') {
            $page_title = 'Group Name: ' . ChartOfInventory::find(\request()->group_id)->name;
            $statement = "CALL get_all_items_by_group(" . \request()->group_id . ",'" . $asOnDate . "')";
        } elseif ($report_type == 'all_item') {
            $statement = "CALL get_all_items('" . $asOnDate . "')";
        } elseif ($report_type == 'store_group') {
            $statement = "CALL get_all_stores('" . $asOnDate . "')";
        } elseif ($report_type == 'store_group_item') {
            $page_title = 'Store Name: ' . Store::find(\request()->store_id)->name;
            $statement = "CALL get_all_items_by_store(" . \request()->store_id . ",'" . $asOnDate . "')";
        }

        $getPost = DB::select($statement);
        $columns = array_keys((array)$getPost[0]);
        $data = [
            'dateRange' => 'as On  ' . $asOnDate,
            'data' => $getPost,
            'page_title' => $page_title,
            'columns' => $columns
        ];
        $pdf = Pdf::loadView('raw_material_inventory_report.rm_quantity_summary', $data);
//        return $pdf->stream();
        $pdf->stream();
    }

}
