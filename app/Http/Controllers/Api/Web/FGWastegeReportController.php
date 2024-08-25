<?php

namespace App\Http\Controllers\Api\Web;

use App\Http\Controllers\Controller;
use App\Models\ChartOfInventory;
use App\Models\InventoryAdjustment;
use App\Models\InventoryTransaction;
use App\Models\Store;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use niklasravnsborg\LaravelPdf\Facades\Pdf;

class FGWastegeReportController extends Controller
{

    public function index()
    {
        return view('finish_goods_wastage_report.index');
    }

    public function create()
    {

        $report_header = 'FG Wastage Report';
        $page_title = false;

        $fromDate = Carbon::parse(\request()->from_date)->format('Y-m-d') ?? Carbon::now()->format('Y-m-d');
        $toDate = Carbon::parse(\request()->to_date)->format('Y-m-d') ?? Carbon::now()->format('Y-m-d');
        $type = 'FG';

        $report_type = \request()->report_type;

        return $fromDate;


        $getPost =InventoryAdjustment::get() ;



        if (!count($getPost) > 0) {
            return false;
        }
        $columns = array_keys((array)$getPost[0]);
        $data = [
            'dateRange' => 'as On  ' . $asOnDate,
            'data' => $getPost,
            'page_title' => $page_title,
            'columns' => $columns,
            'report_header' => $report_header
        ];
        $pdf = Pdf::loadView('common.report_main', $data);
//        return $pdf->stream();
        $pdf->stream();
    }
}
