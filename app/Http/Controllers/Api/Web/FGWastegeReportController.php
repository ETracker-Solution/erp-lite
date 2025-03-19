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
        $report_header = 'FG Wastage Report | '.$report_type;

        $statement = '';
        if ($report_type == 'Store Wise Summary'){
            $store = Store::find($store_id);
            $page_title = 'Store Name :: '.$store->name;
            $statement = $this->storeWiseReportStatement($store_id,$startDate, $endDate);
        }
        if ($report_type == 'Product Wise'){
            $statement = $this->productWiseReportStatement($startDate, $endDate);
        }
        if ($report_type == 'All Store'){
            $statement = $this->allStoreReportStatement($startDate, $endDate);
        }
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

    private function storeWiseReportStatement($store_id,$startDate,$endDate){
       return  "SELECT
     ia.created_at as Date,
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
UNION ALL
SELECT
     'Total' as Date,
    '' as ItemName,
    '' as Rate,
    '' AS Qty,
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
";
    }

    public function productWiseReportStatement($startDate, $endDate)
    {
        return "SELECT
    coi.name as ItemName,
    iat.rate as Rate,
    SUM(iat.quantity) AS Qty,
    SUM(iat.quantity * iat.rate) AS Value
FROM
    inventory_adjustments ia
JOIN
    inventory_adjustment_items iat ON ia.id = iat.inventory_adjustment_id
 JOIN
    chart_of_inventories coi ON coi.id = iat.coi_id
WHERE ia.transaction_type='decrease' AND ia.date >= '$startDate' AND ia.date <= '$endDate'
GROUP BY
    iat.coi_id
UNION ALL
SELECT
    'Total' AS ItemName,
    '' AS Rate,
    '' AS TotalQty,
    SUM(iat.quantity * iat.rate) AS TotalValue
FROM
    inventory_adjustments ia
JOIN
    inventory_adjustment_items iat ON ia.id = iat.inventory_adjustment_id
WHERE
    ia.transaction_type = 'decrease'
    AND ia.date >= '$startDate'
    AND ia.date <= '$endDate';";
    }

    public function allStoreReportStatement($startDate, $endDate)
    {
        return "
(
SELECT
    s.name as StoreName,
    FORMAT(SUM(iat.quantity * iat.rate),2) AS Value
FROM
    inventory_adjustments ia
JOIN
stores s ON ia.store_id = s.id
JOIN
    inventory_adjustment_items iat ON ia.id = iat.inventory_adjustment_id
 JOIN
    chart_of_inventories coi ON coi.id = iat.coi_id
WHERE ia.transaction_type='decrease' AND ia.date >= '$startDate' AND ia.date <= '$endDate' AND ia.status = 'adjusted'
GROUP BY
    ia.store_id
ORDER BY  s.doc_id
)
UNION ALL
(
SELECT
    'Total' AS StoreName,
    FORMAT(SUM(iat.quantity * iat.rate),2) AS TotalValue
FROM
    inventory_adjustments ia
    JOIN
stores s ON ia.store_id = s.id
JOIN
    inventory_adjustment_items iat ON ia.id = iat.inventory_adjustment_id
WHERE
    ia.transaction_type = 'decrease'
    AND ia.date >= '$startDate'
    AND ia.date <= '$endDate'
    AND ia.status = 'adjusted'
);";
    }
}
