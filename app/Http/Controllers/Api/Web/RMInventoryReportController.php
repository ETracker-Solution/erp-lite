<?php

namespace App\Http\Controllers\Api\Web;

use App\Http\Controllers\Controller;
use App\Models\ChartOfInventory;
use App\Models\Store;
use Illuminate\Support\Facades\DB;
use Mccarlosen\LaravelMpdf\Facades\LaravelMpdf as Pdf;

class RMInventoryReportController extends Controller
{
    public function index()
    {
        $isAdmin = (bool) auth()->user()?->is_super;

        $employee = auth()->user()?->employee;
        $storesQuery = Store::query()->whereType('RM')->where('status', 'active')->orderBy('name');
        if ($employee?->outlet_id) {
            $storesQuery->where(['doc_type' => 'outlet', 'doc_id' => $employee->outlet_id]);
        } elseif ($employee?->factory_id) {
            $storesQuery->where(['doc_type' => 'factory', 'doc_id' => $employee->factory_id]);
        }

        return view('raw_material_inventory_report.index', [
            'isAdmin' => $isAdmin,
            'stores' => $storesQuery->get(['id', 'name']),
            'groups' => ChartOfInventory::query()
                ->where(['type' => 'group', 'rootAccountType' => 'RM'])
                ->orderBy('name')
                ->get(['id', 'name']),
        ]);
    }

    public function create()
    {
        ini_set('pcre.backtrack_limit', '5000000');
        ini_set('pcre.recursion_limit', '5000000');
        ini_set('memory_limit', '512M');
        set_time_limit(120);

        $asOnDate = sanitizeReportDate(request('as_on_date') ?? now());
        $reportType = (string) request('report_type');
        $pageTitle = false;
        $reportHeader = 'RM Inventory Report';
        $statement = null;

        if ($reportType === 'all_groups') {
            $statement = get_all_groups_report($asOnDate, 'RM');
        } elseif ($reportType === 'single_group_item') {
            $group = ChartOfInventory::query()->find(request('group_id'));
            if (!$group) {
                return response('Group required', 422);
            }
            $pageTitle = 'Group Name: ' . $group->name;
            $statement = get_all_items_by_group((int) request('group_id'), $asOnDate, 'RM');
        } elseif ($reportType === 'all_item') {
            $statement = get_all_items($asOnDate, 'RM');
        } elseif ($reportType === 'store_group') {
            $reportHeader .= ' (All Store Summary)';
            $statement = get_all_stores($asOnDate, 'RM');
        } elseif ($reportType === 'store_group_item') {
            $store = Store::query()->find(request('store_id'));
            if (!$store) {
                return response('Store required', 422);
            }
            $reportHeader .= ' (Single Store)';
            $pageTitle = 'Store Name: ' . $store->name;
            $statement = get_all_items_by_store(
                (int) request('store_id'),
                $asOnDate,
                'RM',
                request('group_id'),
                request('item_id')
            );
        } else {
            return response('Invalid report type', 422);
        }

        $rows = DB::select($statement);
        if (count($rows) === 0) {
            return response('No Data to Generate Report', 204);
        }

        $columns = array_keys((array) $rows[0]);

        return streamTabularReport([
            'dateRange' => 'As On ' . $asOnDate,
            'data' => $rows,
            'page_title' => $pageTitle,
            'columns' => $columns,
            'report_header' => $reportHeader,
        ], 'RM-Inventory-Report-' . $asOnDate);
    }
}
