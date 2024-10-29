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
use niklasravnsborg\LaravelPdf\Facades\Pdf;

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
        $report_type = \request()->report_type;


        $from_date = Carbon::parse(\request()->from_date)->format('Y-m-d') ?? Carbon::now()->format('Y-m-d');
        $to_date = Carbon::parse(\request()->to_date)->format('Y-m-d') ?? Carbon::now()->format('Y-m-d');

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
        } elseif ($report_type == 'Outlet Wise Due') {

            $outlet = Outlet::find(\request()->store_id);
            $page_title = 'Outlet Name :: ' . $outlet->name;

            $data = [
                'dateRange' => $dateRange,
                'data' => OthersOutletSale::with('customer', 'outlet')->where('outlet_id', $outlet->id)->where('date', '>=', $from_date)->where('date', '<=', $to_date)->get(),
                'page_title' => $page_title,
                'report_header' => $report_header
            ];
            $pdf = Pdf::loadView('sale.report.all_due', $data);
            $pdf->stream();
        } elseif ($report_type == 'Single Customer Due') {
            $customer = Customer::find(\request()->customer_id);
            $page_title = 'Customer Name :: ' . $customer->name;
            $data = [
                'dateRange' => $dateRange,
                'data' => OthersOutletSale::with('customer', 'outlet')->where('customer_id', $customer->id)->where('date', '>=', $from_date)->where('date', '<=', $to_date)->get(),
                'page_title' => $page_title,
                'report_header' => $report_header
            ];
            $pdf = Pdf::loadView('sale.report.single_due', $data);
            $pdf->stream();
        } elseif ($report_type == 'Outlet Wise Discount') {
            $outlet = Outlet::find(\request()->store_id);
            $page_title = 'Outlet Name :: ' . $outlet->name;
            $data = [
                'dateRange' => $dateRange,
                'data' => Sale::with('customer', 'outlet', 'items')->where(function ($q) {
                    return $q->where('discount', '>', 0)->orWhereHas('items', function ($q) {
                        return $q->where('discount', '>', 0);
                    });
                })->where('outlet_id', $outlet->id)->where('date', '>=', $from_date)->where('date', '<=', $to_date)->get(),
                'page_title' => $page_title,
                'report_header' => $report_header
            ];
            $pdf = Pdf::loadView('sale.report.all_discount', $data);
            $pdf->stream();
        } elseif ($report_type == 'Product Wise Discount') {
            $product = ChartOfInventory::find(\request()->item_id);
            $page_title = 'Product Name :: ' . $product->name;
            $data = [
                'dateRange' => $dateRange,
                'data' => Sale::with(['customer', 'outlet','items'=>function ($q) use ($product){
                    return $q->where('product_id', $product->id);
                }])->whereHas('items', function ($q) use ($product){
                    return $q->where('product_id', $product->id);
                })->where('date', '>=', $from_date)->where('date', '<=', $to_date)->get(),
                'page_title' => $page_title,
                'report_header' => $report_header
            ];
            $pdf = Pdf::loadView('sale.report.product_discount', $data);
            $pdf->stream();
        }elseif ($report_type == 'All Outlet Discount') {
            $page_title = 'All Discounts';
            $data = [
                'dateRange' => $dateRange,
                'data' => Sale::with('customer', 'outlet', 'items')->where(function ($q) {
                    return $q->where('discount', '>', 0)->orWhereHas('items', function ($q) {
                        return $q->where('discount', '>', 0);
                    });
                })->where('date', '>=', $from_date)->where('date', '<=', $to_date)->get(),
                'page_title' => $page_title,
                'report_header' => $report_header
            ];
            return view('sale.report.all_discount', $data);
            $pdf = Pdf::loadView('sale.report.all_discount', $data);
            $pdf->stream();
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
        $pdf = Pdf::loadView('common.report_main', $data);
        $pdf->stream();
    }

    public function getAllSaleQuery($from_date, $to_date)
    {
        $outlet_id = auth()->user()->employee && auth()->user()->employee->outlet_id ? auth()->user()->employee->outlet_id : null;
        if (auth()->user()->is_super) {
            return "
                SELECT
                    SS.invoice_number AS 'Invoice Number',
                    US.name AS 'Seller',
                    IFNULL(SS.waiter_name, '') AS 'Waiter',
                    SS.date AS 'Date',
                    SS.subtotal AS 'Amount',
                    SS.discount AS 'Discount',
                    (SS.subtotal - SS.discount) AS 'After Discount',
                    0 AS 'COGS'
                FROM
                    sales SS
                LEFT JOIN
                    users US ON SS.created_by = US.id
                JOIN
                    account_transactions ATT ON ATT.doc_id = SS.id
                WHERE
                    ATT.doc_type = 'POS'
                    AND ATT.chart_of_account_id = 43
                    AND SS.date >= '$from_date'
                    AND SS.date <= '$to_date'

                UNION ALL

                SELECT
                    '' AS 'Seller',
                    '' AS 'Waiter',
                    'Total' AS 'Invoice Number',
                    '' AS 'Date',
                    SUM(SS.subtotal) AS 'Amount',
                    SUM(SS.discount) AS 'Discount',
                    SUM(SS.subtotal - SS.discount) AS 'After Discount',
                    0 AS 'COGS'
                FROM
                    sales SS
                JOIN
                    account_transactions ATT ON ATT.doc_id = SS.id
                WHERE
                    ATT.doc_type = 'POS'
                    AND ATT.chart_of_account_id = 43
                    AND SS.date >= '$from_date'
                    AND SS.date <= '$to_date'
            ";
        } else {
            return "
                SELECT
                    SS.invoice_number AS 'Invoice Number',
                    US.name AS 'Seller',
                    IFNULL(SS.waiter_name, '') AS 'Waiter',
                    SS.date AS 'Date',
                    SS.subtotal AS 'Amount',
                    SS.discount AS 'Discount',
                    (SS.subtotal - SS.discount) AS 'After Discount',
                    0 AS 'COGS'
                FROM
                    sales SS
                LEFT JOIN
                    users US ON SS.created_by = US.id
                JOIN
                    account_transactions ATT ON ATT.doc_id = SS.id
                WHERE
                    ATT.doc_type = 'POS'
                    AND ATT.chart_of_account_id = 43
                    AND SS.date >= '$from_date'
                    AND SS.date <= '$to_date'
                    AND SS.outlet_id = '$outlet_id'

                UNION ALL

                SELECT
                    '' AS 'Seller',
                    '' AS 'Waiter',
                    'Total' AS 'Invoice Number',
                    '' AS 'Date',
                    SUM(SS.subtotal) AS 'Amount',
                    SUM(SS.discount) AS 'Discount',
                    SUM(SS.subtotal - SS.discount) AS 'After Discount',
                    0 AS 'COGS'
                FROM
                    sales SS
                JOIN
                    account_transactions ATT ON ATT.doc_id = SS.id
                WHERE
                    ATT.doc_type = 'POS'
                    AND ATT.chart_of_account_id = 43
                    AND SS.date >= '$from_date'
                    AND SS.date <= '$to_date'
                    AND SS.outlet_id = '$outlet_id'
            ";
        }
    }


    public function getItemWiseSalesSummary($from_date, $to_date)
    {
        $outlet_id = auth()->user()->employee && auth()->user()->employee->outlet_id ? auth()->user()->employee->outlet_id : null;
        if (auth()->user()->is_super) {
            return "
select COI.name as 'Item', SUM(SI.quantity) as 'Quantity', SUM(SI.quantity * SI.unit_price) as 'Sales Amount'
from sales SS
join sale_items SI
on SI.sale_id = SS.id
join chart_of_inventories COI
on COI.id = SI.product_id
WHERE SS.date >= '$from_date'
AND SS.date <= '$to_date'
group by COI.id";
        } else {
            return "
select COI.name as 'Item', SUM(SI.quantity) as 'Quantity', SUM(SI.quantity * SI.unit_price) as 'Sales Amount'
from sales SS
join sale_items SI
on SI.sale_id = SS.id
join chart_of_inventories COI
on COI.id = SI.product_id
WHERE SS.date >= '$from_date'
AND SS.date <= '$to_date'
AND SS.outlet_id = '$outlet_id'
group by COI.id";
        }
    }

    public function getOutletWiseSalesReport($from_date, $to_date)
    {
        return "
select OT.name as 'Outlet', COI.name as 'Item Name', sum(SI.quantity) as 'Quantity', sum(SI.quantity * SI.unit_price) as 'Sale Value'
from sales SS
join outlets OT
on OT.id = SS.outlet_id
join sale_items SI
on SI.sale_id = SS.id
join chart_of_inventories as COI
on SI.product_id = COI.id
join account_transactions ATT
on ATT.doc_id = SS.id
where ATT.doc_type='POS'
AND ATT.chart_of_account_id = 43
AND SS.date >= '$from_date'
AND SS.date <= '$to_date'
group by SS.outlet_id, SI.product_id
";
    }

    public function getAllCustomerSalesDetails($from_date, $to_date)
    {
        $outlet_id = auth()->user()->employee && auth()->user()->employee->outlet_id ? auth()->user()->employee->outlet_id : null;
        if (auth()->user()->is_super) {
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
        ";
        }

    }

    public function getSinlgeItemDetails($item_id, $from_date, $to_date)
    {
        $outlet_id = auth()->user()->employee && auth()->user()->employee->outlet_id ? auth()->user()->employee->outlet_id : null;
        if (auth()->user()->is_super) {
            return "
        select SS.invoice_number as 'Invoice Number', SS.date as 'Date', SI.quantity as 'Quantity', SI.unit_price as 'Rate', (SI.quantity * SI.unit_price) as 'Sales Value', OT.name as 'Outlet'
from sales SS
join sale_items as SI
on SI.sale_id = SS.id
join outlets as OT
on OT.id = SS.outlet_id
WHERE SI.product_id = $item_id
        AND SS.date >= '$from_date'
AND SS.date <= '$to_date'
        ";
        } else {
            return "
        select SS.invoice_number as 'Invoice Number', SS.date as 'Date', SI.quantity as 'Quantity', SI.unit_price as 'Rate', (SI.quantity * SI.unit_price) as 'Sales Value', OT.name as 'Outlet'
from sales SS
join sale_items as SI
on SI.sale_id = SS.id
join outlets as OT
on OT.id = SS.outlet_id
WHERE SI.product_id = $item_id
        AND SS.date >= '$from_date'
AND SS.date <= '$to_date'
        AND SS.outlet_id = '$outlet_id'
        ";
        }

    }

    public function getSingleCustomerDetails($customer_id, $from_date, $to_date)
    {
        $outlet_id = auth()->user()->employee && auth()->user()->employee->outlet_id ? auth()->user()->employee->outlet_id : null;
        if (auth()->user()->is_super) {
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
}
