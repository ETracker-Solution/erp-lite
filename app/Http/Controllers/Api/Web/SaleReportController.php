<?php

namespace App\Http\Controllers\Api\Web;

use App\Http\Controllers\Controller;
use App\Models\ChartOfInventory;
use App\Models\Customer;
use App\Models\OthersOutletSale;
use App\Models\Outlet;
use App\Models\Sale;
use App\Models\Store;
use Carbon\Carbon;
use http\Client\Curl\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Mccarlosen\LaravelMpdf\Facades\LaravelMpdf as Pdf;

class SaleReportController extends Controller
{
    public function index()
    {
        return view('sale.report.index');
    }

    public function getAllFGStores()
    {
        if (\auth()->user() && \auth()->user()->employee && \auth()->user()->employee->outlet_id) {
            $outlets = Outlet::where('id', \auth()->user()->employee->outlet_id)->get();
        } else {
            $outlets = Outlet::all();
        }
        return $outlets;
    }

    public function getAllCustomers()
    {
        return Customer::where(['type' => 'regular'])->select('id', DB::raw('CONCAT(name," - ", mobile) as name'))->get();
    }

    public function create()
    {

        ini_set('pcre.backtrack_limit', '2000000');
        ini_set('pcre.recursion_limit', '1000000');
        $report_type = \request()->report_type;


        $from_date = Carbon::parse(\request()->from_date)->format('Y-m-d') ?? Carbon::now()->format('Y-m-d');
        $to_date = Carbon::parse(\request()->to_date)->format('Y-m-d') ?? Carbon::now()->format('Y-m-d');
        [$from_date, $to_date] = clampReportDateRange($from_date, $to_date, 366);

        $asOnDate = Carbon::parse(\request()->as_on_date)->format('Y-m-d') ?? Carbon::now()->format('Y-m-d');

        $page_title = false;
        $report_header = $report_type;

        $query = $this->getAllSaleQuery($from_date, $to_date);
        $dateRange = 'From ' . $from_date . ' To ' . $to_date;
        if ($report_type == 'All Sales Record') {
            $query = $this->getAllSaleQuery($from_date, $to_date);
        } elseif ($report_type == 'Item Wise Sales Summary') {
            $query = $this->getItemWiseSalesSummary($from_date, $to_date);
        } elseif ($report_type == 'Outlet Wise Sales Summary') {
            $query = $this->getOutletWiseSalesReport($from_date, $to_date);
        } elseif ($report_type == 'All Customer Sales Details') {
            $query = $this->getAllCustomerSalesDetails($from_date, $to_date);
        } elseif ($report_type == 'Single Item Sales Details') {
            $item = ChartOfInventory::find(\request()->item_id);
            $page_title = 'Item Name :: ' . $item->name;
            $query = $this->getSinlgeItemDetails($item->id, $from_date, $to_date);
        } elseif ($report_type == 'Single Customer Details') {
            $item = Customer::find(\request()->customer_id);
            $page_title = 'Customer Name :: ' . $item->name;
            $query = $this->getSingleCustomerDetails($item->id, $from_date, $to_date);
        }elseif ($report_type == 'All Outlet Sale Amount Report') {
            $query = $this->getAllOutletAccountSaleReport($from_date, $to_date);
        } elseif ($report_type == 'Outlet Wise Due') {

            $outlet = Outlet::find(\request()->store_id);
            $page_title = 'Outlet Name :: ' . $outlet->name;

            $rows = OthersOutletSale::with(['customer:id,name,mobile', 'outlet:id,name'])
                ->select(['id', 'date', 'invoice_number', 'customer_id', 'outlet_id', 'grand_total', 'receive_amount', 'delivery_point_receive_amount'])
                ->where('outlet_id', $outlet->id)
                ->where('date', '>=', $from_date)
                ->where('date', '<=', $to_date)
                ->limit(5000)
                ->get();

            if (wantsReportExcelExport()) {
                return downloadReportExcel($this->mapDueRowsForExcel($rows), 'Outlet-Wise-Due');
            }

            $data = [
                'dateRange' => $dateRange,
                'data' => $rows,
                'page_title' => $page_title,
                'report_header' => $report_header
            ];
            $pdf = Pdf::loadView('sale.report.all_due', $data);
            return $pdf->stream();
        } elseif ($report_type == 'Single Customer Due') {
            $customer = Customer::find(\request()->customer_id);
            $page_title = 'Customer Name :: ' . $customer->name;
            $rows = OthersOutletSale::with(['customer:id,name,mobile', 'outlet:id,name'])
                ->select(['id', 'date', 'invoice_number', 'customer_id', 'outlet_id', 'grand_total', 'receive_amount', 'delivery_point_receive_amount'])
                ->where('customer_id', $customer->id)
                ->where('date', '>=', $from_date)
                ->where('date', '<=', $to_date)
                ->limit(5000)
                ->get();

            if (wantsReportExcelExport()) {
                return downloadReportExcel($this->mapDueRowsForExcel($rows), 'Single-Customer-Due');
            }

            $data = [
                'dateRange' => $dateRange,
                'data' => $rows,
                'page_title' => $page_title,
                'report_header' => $report_header
            ];
            $pdf = Pdf::loadView('sale.report.single_due', $data);
            return $pdf->stream();
        } elseif ($report_type == 'Outlet Wise Discount') {
            $outlet = Outlet::find(\request()->store_id);
            $page_title = 'Outlet Name :: ' . $outlet->name;
            $rows = $this->discountedSalesQuery()
                ->where('outlet_id', $outlet->id)
                ->where('date', '>=', $from_date)
                ->where('date', '<=', $to_date)
                ->limit(5000)
                ->get();

            if (wantsReportExcelExport()) {
                return downloadReportExcel($this->mapDiscountRowsForExcel($rows), 'Outlet-Wise-Discount');
            }

            $data = [
                'dateRange' => $dateRange,
                'data' => $rows,
                'page_title' => $page_title,
                'report_header' => $report_header
            ];
            $pdf = Pdf::loadView('sale.report.all_discount', $data);
            return $pdf->stream();
        } elseif ($report_type == 'Single Customer Discount') {
            $customer = Customer::find(\request()->customer_id);
            $page_title = 'Customer Name :: ' . $customer->name;
            $rows = $this->discountedSalesQuery()
                ->where('customer_id', $customer->id)
                ->where('date', '>=', $from_date)
                ->where('date', '<=', $to_date)
                ->limit(5000)
                ->get();

            if (wantsReportExcelExport()) {
                return downloadReportExcel($this->mapDiscountRowsForExcel($rows), 'Single-Customer-Discount');
            }

            $data = [
                'dateRange' => $dateRange,
                'data' => $rows,
                'page_title' => $page_title,
                'report_header' => $report_header
            ];
            $pdf = Pdf::loadView('sale.report.all_discount', $data);
            return $pdf->stream();
        } elseif ($report_type == 'Product Wise Discount') {
            $product = ChartOfInventory::find(\request()->item_id);
            $page_title = 'Product Name :: ' . $product->name;
            $rows = Sale::with(['customer', 'outlet', 'items' => function ($q) use ($product) {
                return $q->where('product_id', $product->id);
            }])->whereHas('items', function ($q) use ($product) {
                return $q->where('product_id', $product->id);
            })->where('date', '>=', $from_date)->where('date', '<=', $to_date)->limit(5000)->get();

            if (wantsReportExcelExport()) {
                return downloadReportExcel($this->mapDiscountRowsForExcel($rows), 'Product-Wise-Discount');
            }

            $data = [
                'dateRange' => $dateRange,
                'data' => $rows,
                'page_title' => $page_title,
                'report_header' => $report_header
            ];
            $pdf = Pdf::loadView('sale.report.product_discount', $data);
            return $pdf->stream();
        } elseif ($report_type == 'All Outlet Discount') {
            try {
                $page_title = 'All Discounts';

                // RAW QUERY FOR MAXIMUM SPEED
                $allSales = DB::select("
            SELECT
                s.date,
                s.invoice_number,
                c.name AS customer_name,
                c.mobile AS customer_mobile,
                (
                    s.discount +
                    s.membership_discount_amount +
                    s.special_discount_amount +
                    s.couponCodeDiscountAmount
                ) AS sale_discount,
                IFNULL(items.item_discount, 0) AS item_discount
            FROM sales s
            LEFT JOIN customers c ON c.id = s.customer_id

            LEFT JOIN (
                SELECT sale_id, SUM(discount) AS item_discount
                FROM sale_items
                GROUP BY sale_id
            ) AS items ON items.sale_id = s.id

            WHERE (
                s.discount > 0 OR
                s.membership_discount_amount > 0 OR
                s.special_discount_amount > 0 OR
                s.couponCodeDiscountAmount > 0 OR
                items.item_discount > 0
            )
            AND s.date BETWEEN ? AND ?
            ORDER BY s.date ASC
            LIMIT 5000
        ", [$from_date, $to_date]);


                if (wantsReportExcelExport()) {
                    return downloadReportExcel($allSales, 'All-Outlet-Discount');
                }

                $data = [
                    'dateRange'     => $dateRange,
                    'data'          => $allSales,
                    'page_title'    => $page_title,
                    'report_header' => $report_header
                ];

                // RENDER BLADE TO HTML STRING
                $html = view('sale.report.all_discount_new', $data)->render();

                // CREATE MPDF INSTANCE
                $mpdf = new \Mpdf\Mpdf([
                    'tempDir' => storage_path('app/mpdf'),
                    'autoScriptToLang' => true,
                    'autoLangToFont' => true,
                    'mode' => 'utf-8',
                    'format' => 'A4',
                ]);

                // ---- CHUNK SIZE TO AVOID pcre.backtrack_limit ----
                $chunkSize = 40000; // 40 KB per chunk (safe for large reports)

                // SPLIT HTML INTO SAFE CHUNKS
                $chunks = str_split($html, $chunkSize);

                foreach ($chunks as $chunk) {
                    $mpdf->WriteHTML($chunk, \Mpdf\HTMLParserMode::HTML_BODY);
                }

                return $mpdf->Output('All-Discounts.pdf', 'I');

            } catch (\Throwable $e) {
                Log::error('All Outlet Discount PDF Generation Error', [
                    'message' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                ]);

                return response()->json(['message' => 'Something went wrong generating the PDF report. Please try again.'], 500);
            }
        }

        $getData = DB::select($query);

        if (!isset($getData[0])) {
            return response()->json(['success' => false]);
        }
        $columns = array_keys((array)$getData[0]);

        $data = [
            'dateRange' => $dateRange,
            'data' => $getData,
            'page_title' => $page_title,
            'columns' => $columns,
            'report_header' => $report_header
        ];
//        return view('common.report_main', $data);
        return streamTabularReport($data, 'Sale-Report');
    }

    public function getAllSaleQuery($from_date, $to_date)
    {
        $from_date = sanitizeReportDate($from_date);
        $to_date = sanitizeReportDate($to_date);
        $outletFilter = '';
        $isHo = auth()->user()->is_super || (auth()->user()->employee && auth()->user()->employee->user_of == 'ho');
        if (!$isHo) {
            $outlet_id = (int) (auth()->user()->employee->outlet_id ?? 0);
            if ($outlet_id) {
                $outletFilter = " AND SS.outlet_id = {$outlet_id}";
            }
        }

        return "
                SELECT
                    SS.invoice_number AS 'Invoice Number',
                    US.name AS 'Seller',
                    IFNULL(SS.waiter_name, '') AS 'Waiter',
                    SS.date AS 'Date',
                    SS.subtotal AS 'Amount',
                    (SS.discount + SS.membership_discount_amount + SS.special_discount_amount + SS.couponCodeDiscountAmount) AS 'Discount',
                    (SS.subtotal - (SS.discount + SS.membership_discount_amount + SS.special_discount_amount + SS.couponCodeDiscountAmount)) AS 'After Discount',
                    COALESCE(cogs.total_cogs, 0) AS 'COGS'
                FROM
                    sales SS
                LEFT JOIN
                    users US ON SS.created_by = US.id
                LEFT JOIN (
                    SELECT sale_id, SUM(COALESCE(cogs, 0)) AS total_cogs
                    FROM sale_items
                    GROUP BY sale_id
                ) cogs ON cogs.sale_id = SS.id
                WHERE
                    SS.date >= '$from_date'
                    AND SS.date <= '$to_date'
                    {$outletFilter}

                UNION ALL

                SELECT
                    '' AS 'Invoice Number',
                    '' AS 'Seller',
                    'Total' AS 'Waiter',
                    '' AS 'Date',
                    SUM(SS.subtotal) AS 'Amount',
                    SUM(SS.discount + SS.membership_discount_amount + SS.special_discount_amount + SS.couponCodeDiscountAmount) AS 'Discount',
                    SUM(SS.subtotal - (SS.discount + SS.membership_discount_amount + SS.special_discount_amount + SS.couponCodeDiscountAmount)) AS 'After Discount',
                    SUM(COALESCE(cogs.total_cogs, 0)) AS 'COGS'
                FROM
                    sales SS
                LEFT JOIN (
                    SELECT sale_id, SUM(COALESCE(cogs, 0)) AS total_cogs
                    FROM sale_items
                    GROUP BY sale_id
                ) cogs ON cogs.sale_id = SS.id
                WHERE
                    SS.date >= '$from_date'
                    AND SS.date <= '$to_date'
                    {$outletFilter}
            ";
    }

    private function discountedSalesQuery()
    {
        return Sale::with([
            'customer:id,name,mobile',
            'outlet:id,name',
            'items:id,sale_id,discount,quantity,unit_price,product_id',
        ])
            ->select([
                'id', 'date', 'invoice_number', 'customer_id', 'outlet_id', 'subtotal', 'grand_total',
                'discount', 'membership_discount_amount', 'special_discount_amount', 'couponCodeDiscountAmount',
            ])
            ->where(function ($q) {
                $q->where('discount', '>', 0)
                    ->orWhere('membership_discount_amount', '>', 0)
                    ->orWhere('special_discount_amount', '>', 0)
                    ->orWhere('couponCodeDiscountAmount', '>', 0)
                    ->orWhereHas('items', function ($q) {
                        $q->where('discount', '>', 0);
                    });
            });
    }


    public function getItemWiseSalesSummary($from_date, $to_date)
    {
        $outlet_id = auth()->user()->employee && auth()->user()->employee->outlet_id ? auth()->user()->employee->outlet_id : null;
        if (auth()->user()->is_super || (auth()->user()->employee && auth()->user()->employee->user_of == 'ho')) {
            return "
(
select COI.name as 'Item', SUM(SI.quantity) as 'Quantity', SUM(SI.quantity * SI.unit_price) as 'Sales Amount'
from sales SS
join sale_items SI
on SI.sale_id = SS.id
join chart_of_inventories COI
on COI.id = SI.product_id
WHERE SS.date >= '$from_date'
AND SS.date <= '$to_date'
group by COI.id
order by COI.parent_id, COI.id
)
union all
(
 SELECT 'Total' AS 'Item',
    SUM(SI.quantity) AS 'Quantity',
    SUM(SI.quantity * SI.unit_price) AS 'Sales Amount'
    FROM sales SS
    join sale_items SI
on SI.sale_id = SS.id
    WHERE SS.date >= '$from_date'
    AND SS.date <= '$to_date'

)";

        } else {
            return "
            (
select COI.name as 'Item', SUM(SI.quantity) as 'Quantity', SUM(SI.quantity * SI.unit_price) as 'Sales Amount'
from sales SS
join sale_items SI
on SI.sale_id = SS.id
join chart_of_inventories COI
on COI.id = SI.product_id
WHERE SS.date >= '$from_date'
AND SS.date <= '$to_date'
AND SS.outlet_id = '$outlet_id'
group by COI.id
order by COI.parent_id, COI.id
)
union all
(
 SELECT 'Total' AS 'Item',
    SUM(SI.quantity) AS 'Quantity',
    SUM(SI.quantity * SI.unit_price) AS 'Sales Amount'
    FROM sales SS
    JOIN sale_items SI ON SI.sale_id = SS.id
    WHERE SS.date >= '$from_date'
    AND SS.date <= '$to_date'
    AND SS.outlet_id = '$outlet_id'

)";
        }
    }

    public function getOutletWiseSalesReport($from_date, $to_date)
    {
        $baseQuery = "
        SELECT
            OT.name AS 'Outlet',
            COI.name AS 'Item Name',
            SUM(SI.quantity) AS 'Quantity',
            SUM(SI.quantity * SI.unit_price) AS 'Sale Value'
        FROM sales SS
        JOIN outlets OT ON OT.id = SS.outlet_id
        JOIN sale_items SI ON SI.sale_id = SS.id
        JOIN chart_of_inventories COI ON SI.product_id = COI.id
        WHERE SS.date >= '$from_date'
        AND SS.date <= '$to_date'
    ";

        // Add outlet filter if store_id is provided
        if (\request()->filled('store_id')) {
            $outlet_id = (int) Outlet::find(\request()->store_id)->id;
            $baseQuery .= " AND SS.outlet_id = $outlet_id ";
        }

        // Clone the WHERE clause for use in the Total row
        $totalQuery = str_replace('SELECT
            OT.name AS \'Outlet\',
            COI.name AS \'Item Name\',
            SUM(SI.quantity) AS \'Quantity\',
            SUM(SI.quantity * SI.unit_price) AS \'Sale Value\'',
            "SELECT
            'Total' AS 'Outlet',
            '' AS 'Item Name',
            SUM(SI.quantity) AS 'Quantity',
            SUM(SI.quantity * SI.unit_price) AS 'Sale Value'",
            $baseQuery
        );

        // Final full query with group by and union
        $baseQuery .= " GROUP BY SS.outlet_id, SI.product_id";

        return "$baseQuery
            UNION ALL
            $totalQuery";
    }


    public function getAllCustomerSalesDetails($from_date, $to_date)
    {
        $outlet_id = auth()->user()->employee && auth()->user()->employee->outlet_id ? auth()->user()->employee->outlet_id : null;
        if (auth()->user()->is_super || (auth()->user()->employee && auth()->user()->employee->user_of == 'ho')) {
            return "
select SS.invoice_number as 'Invoice Number', SS.date as 'Date', CU.name as 'Customer Name', COI.name as 'Item Name', SI.quantity as 'Quantity', SI.unit_price  as 'Rate', (SI.quantity * SI.unit_price)  as 'Value'
from sales SS
join customers CU
on CU.id = SS.customer_id
join sale_items as SI
on SI.sale_id = SS.id
join chart_of_inventories as COI
on COI.id = SI.product_id
WHERE SS.date >= '$from_date'
AND SS.date <= '$to_date'
 UNION ALL

    SELECT
        'Total' AS 'Invoice Number',
        '' AS 'Date',
        '' AS 'Customer Name',
        '' AS 'Item Name',
        SUM(SI.quantity) AS 'Quantity',
        '' AS 'Rate',
        SUM(SI.quantity * SI.unit_price) AS 'Value'
    FROM sales SS
    JOIN sale_items AS SI ON SI.sale_id = SS.id
    WHERE SS.date >= '$from_date'
      AND SS.date <= '$to_date'
    ";
        } else {
            return "
select SS.invoice_number as 'Invoice Number', SS.date as 'Date', CU.name as 'Customer Name', COI.name as 'Item Name', SI.quantity as 'Quantity', SI.unit_price  as 'Rate', (SI.quantity * SI.unit_price)  as 'Value'
from sales SS
join customers CU
on CU.id = SS.customer_id
join sale_items as SI
on SI.sale_id = SS.id
join chart_of_inventories as COI
on COI.id = SI.product_id
WHERE SS.date >= '$from_date'
AND SS.date <= '$to_date'
AND SS.outlet_id = '$outlet_id'
UNION ALL

SELECT
    'Total' AS 'Invoice Number',
    '' AS 'Date',
    '' AS 'Customer Name',
    '' AS 'Item Name',
    SUM(SI.quantity) AS 'Quantity',
    '' AS 'Rate',
    SUM(SI.quantity * SI.unit_price) AS 'Value'
FROM sales SS
JOIN sale_items AS SI ON SI.sale_id = SS.id
WHERE SS.date >= '$from_date'
  AND SS.date <= '$to_date'
  AND SS.outlet_id = '$outlet_id'
";
        }

    }

    public function getSinlgeItemDetails($item_id, $from_date, $to_date)
    {
        $item_id = (int) $item_id;
        $outlet_id = auth()->user()->employee && auth()->user()->employee->outlet_id ? auth()->user()->employee->outlet_id : null;
        if (auth()->user()->is_super || (auth()->user()->employee && auth()->user()->employee->user_of == 'ho')) {
            return "
        (
        select SS.invoice_number as 'Invoice Number', SS.date as 'Date', SI.quantity as 'Quantity', SI.unit_price as 'Rate', FORMAT((SI.quantity * SI.unit_price),2) as 'Sales Value', OT.name as 'Outlet'
from sales SS
join sale_items as SI
on SI.sale_id = SS.id
join outlets as OT
on OT.id = SS.outlet_id
WHERE SI.product_id = $item_id
        AND SS.date >= '$from_date'
AND SS.date <= '$to_date'
ORDER BY OT.id
        )
        UNION ALL
        (
        select 'TOTAL' as 'Invoice Number', '' as 'Date', SUM(SI.quantity) as 'Quantity', '' as 'Rate', FORMAT(SUM((SI.quantity * SI.unit_price)),2) as 'Sales Value', '' as 'Outlet'
from sales SS
join sale_items as SI
on SI.sale_id = SS.id
WHERE SI.product_id = $item_id
        AND SS.date >= '$from_date'
AND SS.date <= '$to_date'
        )
        ";
        } else {
            return "
       select SS.invoice_number as 'Invoice Number', SS.date as 'Date', SI.quantity as 'Quantity', SI.unit_price as 'Rate', FORMAT((SI.quantity * SI.unit_price),2) as 'Sales Value', OT.name as 'Outlet'
from sales SS
join sale_items as SI
on SI.sale_id = SS.id
join outlets as OT
on OT.id = SS.outlet_id
WHERE SI.product_id = $item_id
        AND SS.date >= '$from_date'
AND SS.date <= '$to_date'
AND SS.outlet_id = '$outlet_id'
ORDER BY OT.id
        )
        UNION ALL
        (
        select 'TOTAL' as 'Invoice Number', '' as 'Date', SUM(SI.quantity) as 'Quantity', '' as 'Rate', FORMAT(SUM((SI.quantity * SI.unit_price)),2) as 'Sales Value', '' as 'Outlet'
from sales SS
join sale_items as SI
on SI.sale_id = SS.id
WHERE SI.product_id = $item_id
        AND SS.date >= '$from_date'
AND SS.date <= '$to_date'
AND SS.outlet_id = '$outlet_id'
        )";
        }

    }

    public function getSingleCustomerDetails($customer_id, $from_date, $to_date)
    {
        $customer_id = (int) $customer_id;
        $outlet_id = auth()->user()->employee && auth()->user()->employee->outlet_id ? auth()->user()->employee->outlet_id : null;
        if (auth()->user()->is_super || (auth()->user()->employee && auth()->user()->employee->user_of == 'ho')) {
            return "
        select SS.invoice_number as 'Invoice Number', SS.date as 'Date', COI.name as 'Item Name', SI.quantity as 'Quantity', SI.unit_price as 'Rate', (SI.quantity * SI.unit_price) as 'Sales Value', OT.name as 'Outlet'
from sales SS
join sale_items as SI
on SI.sale_id = SS.id
join customers CU
on CU.id = SS.customer_id
join outlets as OT
on OT.id = SS.outlet_id
join chart_of_inventories as COI
on COI.id = SI.product_id
WHERE SS.customer_id= $customer_id
        AND SS.date >= '$from_date'
AND SS.date <= '$to_date'
        ";
        } else {
            return "
        select SS.invoice_number as 'Invoice Number', SS.date as 'Date', COI.name as 'Item Name', SI.quantity as 'Quantity', SI.unit_price as 'Rate', (SI.quantity * SI.unit_price) as 'Sales Value', OT.name as 'Outlet'
from sales SS
join sale_items as SI
on SI.sale_id = SS.id
join customers CU
on CU.id = SS.customer_id
join outlets as OT
on OT.id = SS.outlet_id
join chart_of_inventories as COI
on COI.id = SI.product_id
WHERE SS.customer_id= $customer_id
        AND SS.date >= '$from_date'
AND SS.date <= '$to_date'
                AND SS.outlet_id = '$outlet_id'
        ";
        }
    }

    public function getAllOutletAccountSaleReport($from_date, $to_date)
    {
        return "
SELECT
	o.name as OutletName,
	coa.name as AccountName,
	sum(at2.amount) as Amount
FROM
	account_transactions at2
JOIN chart_of_accounts coa
ON coa.id = at2.chart_of_account_id
JOIN sales s
ON s.id = at2.doc_id
JOIN outlets o
ON o.id = s.outlet_id
JOIN outlet_accounts oa
ON oa.outlet_id  = s.outlet_id
WHERE at2.doc_type ='POS' AND at2.`type` ='debit' AND coa.id in (oa.coa_id)
AND s.date >= '$from_date'
AND s.date <= '$to_date'
GROUP By s.outlet_id, coa.id
UNION ALL
SELECT
	'' as OutletName,
	'TOTAL' as AccountName,
	sum(at2.amount) as Amount
FROM
	account_transactions at2
JOIN chart_of_accounts coa
ON coa.id = at2.chart_of_account_id
JOIN sales s
ON s.id = at2.doc_id
JOIN outlets o
ON o.id = s.outlet_id
JOIN outlet_accounts oa
ON oa.outlet_id  = s.outlet_id
WHERE at2.doc_type ='POS' AND at2.`type` ='debit' AND coa.id in (oa.coa_id)
AND s.date >= '$from_date'
AND s.date <= '$to_date'
        ";
    }

    private function mapDueRowsForExcel($rows): array
    {
        return collect($rows)->map(function ($sale) {
            return [
                'Date' => $sale->date,
                'Invoice' => $sale->invoice_number,
                'Customer' => $sale->customer->name ?? '',
                'Mobile' => $sale->customer->mobile ?? '',
                'Outlet' => $sale->outlet->name ?? '',
                'Grand Total' => $sale->grand_total,
                'Receive Amount' => $sale->receive_amount,
                'Delivery Point Receive' => $sale->delivery_point_receive_amount,
            ];
        })->all();
    }

    private function mapDiscountRowsForExcel($rows): array
    {
        return collect($rows)->map(function ($sale) {
            $discount = ($sale->discount ?? 0)
                + ($sale->membership_discount_amount ?? 0)
                + ($sale->special_discount_amount ?? 0)
                + ($sale->couponCodeDiscountAmount ?? 0);

            return [
                'Date' => $sale->date,
                'Invoice' => $sale->invoice_number,
                'Customer' => $sale->customer->name ?? '',
                'Mobile' => $sale->customer->mobile ?? '',
                'Outlet' => $sale->outlet->name ?? '',
                'Discount' => $discount,
                'Grand Total' => $sale->grand_total ?? '',
            ];
        })->all();
    }
}
