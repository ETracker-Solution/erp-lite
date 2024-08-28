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

        $startDate = Carbon::parse(\request()->from_date)->format('Y-m-d') ?? Carbon::now()->format('Y-m-d');
        $endDate = Carbon::parse(\request()->to_date)->format('Y-m-d') ?? Carbon::now()->format('Y-m-d');
        $type = 'FG';

        $report_type = \request()->report_type;
        $store_id = \request()->store_id;





        $statement = "SELECT
     ia.created_at as Date,
    s.name as StoreName,
    coi.name as ItemName,
    iat.rate as Rate,
    SUM(iat.quantity) AS Qty,
    SUM(iat.quantity * iat.rate) AS Value
FROM
    inventory_adjustments ia
JOIN
    stores s ON ia.store_id = s.id

JOIN
    inventory_adjustment_items iat ON ia.id = iat.inventory_adjustment_id
 JOIN
    chart_of_inventories coi ON coi.id = iat.coi_id
             WHERE ia.store_id='$store_id'AND ia.transaction_type='decrease' AND ia.date >= '$startDate' AND ia.date <= '$endDate'
GROUP BY
    ia.store_id, ia.created_at, iat.coi_id
ORDER BY
    ia.store_id, ia.created_at";

        $getPost = DB::select($statement);

        if (!count($getPost) > 0) {
            return false;
        }
        $columns = array_keys((array)$getPost[0]);
        $data = [
            'dateRange' => $endDate. ' -  ' . $startDate,
            'data' => $getPost,
            'page_title' => $page_title,
            'columns' => $columns,
            'report_header' => $report_header
        ];
        $pdf = Pdf::loadView('common.report_main', $data);
        $pdf->stream();
    }
}
