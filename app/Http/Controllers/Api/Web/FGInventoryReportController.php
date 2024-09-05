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
        return view('finish_goods_inventory_report.index');
    }

    public function create()
    {

        $asOnDate = Carbon::parse(\request()->as_on_date)->format('Y-m-d') ?? Carbon::now()->format('Y-m-d');

        $page_title = false;
        $report_header = 'FG Inventory Report';

        $report_type = \request()->report_type;
        if ($report_type == 'all_groups') {
            $statement = get_all_groups_report($asOnDate ,'FG');
        } elseif ($report_type == 'single_group_item') {
            $page_title = 'Group Name: ' . ChartOfInventory::find(\request()->group_id)->name;
            $statement = get_all_items_by_group(\request()->group_id, $asOnDate,'FG');
        } elseif ($report_type == 'all_item') {
            $statement = get_all_items($asOnDate,'FG');
        } elseif ($report_type == 'store_group') {
            $statement = get_all_stores($asOnDate,'FG');
        } elseif ($report_type == 'store_group_item') {
            $page_title = 'Store Name: ' . Store::find(\request()->store_id)->name;
            $statement = get_all_items_by_store(\request()->store_id, $asOnDate,'FG');
        }

        $getPost = DB::select($statement);

        if (!count($getPost) > 0){
            return false;
        }
        $columns = array_keys((array)$getPost[0]);
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
}
