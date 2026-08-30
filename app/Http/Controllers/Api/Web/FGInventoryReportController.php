<?php

namespace App\Http\Controllers\Api\Web;

use App\Http\Controllers\Controller;
use App\Models\ChartOfInventory;
use App\Models\Store;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Mccarlosen\LaravelMpdf\Facades\LaravelMpdf as Pdf;

class FGInventoryReportController extends Controller
{

    public function index()
    {
//        return testFGreport(3, '2024,12-01');
        return view('finish_goods_inventory_report.index');
    }

    public function create()
    {
        ini_set('pcre.backtrack_limit', 5000000);
        ini_set('pcre.recursion_limit', 5000000);

        $asOnDate = sanitizeReportDate(\request()->as_on_date ?? now());

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
            $statement = get_all_items_by_store(\request()->store_id, $asOnDate,'FG');
        } elseif ($report_type == 'store_item_details') {
            $report_header .= ' ( '. 'ITEM Name: ' . ChartOfInventory::find(\request()->item_id)->name . ' )';
            $page_title = 'Store Name: ' . Store::find(\request()->store_id)->name;
            $statement = get_store_item_details(\request()->store_id,\request()->item_id , $asOnDate,);
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
        $pdf->stream();
    }
}
