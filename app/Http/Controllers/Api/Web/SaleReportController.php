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

        ini_set('pcre.backtrack_limit', '2000000');
        ini_set('pcre.recursion_limit', '1000000');
        $report_type = \request()->report_type;
        $outlet = Outlet::find(\request()->store_id);


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
        } elseif ($report_type == 'Only Account Wise Sales Report') {
            $query = $this->getPaymentMethodWiseSalesReport($from_date, $to_date);
        } elseif ($report_type == 'Product Group Wise Sales Report') {
            $page_title = "";
            $query = $this->getProductGroupWiseSalesReport($from_date, $to_date);
        } elseif ($report_type == 'Sale Amount Report') {
            $query = $this->getAllOutletAccountSaleReport($from_date, $to_date);
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
        } elseif ($report_type == 'Single Customer Discount') {
            $customer = Customer::find(\request()->customer_id);
            $page_title = 'Customer Name :: ' . $customer->name;
            $data = [
                'dateRange' => $dateRange,
                'data' => Sale::with('customer', 'outlet', 'items')->where(function ($q) {
                    return $q->where('discount', '>', 0)->orWhereHas('items', function ($q) {
                        return $q->where('discount', '>', 0);
                    });
                })->where('customer_id', $customer->id)->where('date', '>=', $from_date)->where('date', '<=', $to_date)->get(),
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
                'data' => Sale::with(['customer', 'outlet', 'items' => function ($q) use ($product) {
                    return $q->where('product_id', $product->id);
                }])->whereHas('items', function ($q) use ($product) {
                    return $q->where('product_id', $product->id);
                })->where('date', '>=', $from_date)->where('date', '<=', $to_date)->get(),
                'page_title' => $page_title,
                'report_header' => $report_header
            ];
            $pdf = Pdf::loadView('sale.report.product_discount', $data);
            $pdf->stream();
        } elseif ($report_type == 'All Outlet Discount') {
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
        $user = auth()->user();

        $requestOutlet = request()->store_id ?? null;
        $employeeOutlet = $user->employee->outlet_id ?? null;

        $outlet_id = $employeeOutlet ?: $requestOutlet;

        $outletFilter = "";

        // Apply filter if outlet exists
        if ($outlet_id) {
            $outletFilter = " AND SS.outlet_id = '$outlet_id' ";
        }

        // Super / HO user → allow request filter
        if ($user->is_super || ($user->employee && $user->employee->user_of == 'ho')) {
            $outletFilter = $requestOutlet ? " AND SS.outlet_id = '$requestOutlet' " : "";
        }

        return "
        SELECT
            SS.invoice_number AS 'Invoice Number',
            US.name AS 'Seller',
            IFNULL(SS.waiter_name, '') AS 'Waiter',
            SS.date AS 'Date',
            SS.subtotal AS 'Subtotal',
            (SS.discount + IFNULL(SS.couponCodeDiscountAmount, 0)) AS 'Total Discount',
            (SS.subtotal - (SS.discount + IFNULL(SS.couponCodeDiscountAmount, 0))) AS 'After Discount',
            SS.delivery_charge AS 'Delivery Charge',
            SS.additional_charge AS 'Additional Charge',
            SS.grand_total AS 'Final Amount',
            0 AS 'COGS'
        FROM sales SS
        LEFT JOIN users US ON SS.created_by = US.id
        JOIN account_transactions ATT ON ATT.doc_id = SS.id
        WHERE
            ATT.doc_type = 'POS'
            AND ATT.chart_of_account_id = 43
            AND SS.date >= '$from_date'
            AND SS.date <= '$to_date'
            $outletFilter

        UNION ALL

        SELECT
            'Total' AS 'Invoice Number',
            '' AS 'Seller',
            '' AS 'Waiter',
            '' AS 'Date',
            SUM(SS.subtotal) AS 'Subtotal',
            SUM(SS.discount + IFNULL(SS.couponCodeDiscountAmount, 0)) AS 'Total Discount',
            SUM(SS.subtotal - (SS.discount + IFNULL(SS.couponCodeDiscountAmount, 0))) AS 'After Discount',
            SUM(SS.delivery_charge) AS 'Delivery Charge',
            SUM(SS.additional_charge) AS 'Additional Charge',
            SUM(SS.grand_total) AS 'Final Amount',
            0 AS 'COGS'
        FROM sales SS
        JOIN account_transactions ATT ON ATT.doc_id = SS.id
        WHERE
            ATT.doc_type = 'POS'
            AND ATT.chart_of_account_id = 43
            AND SS.date >= '$from_date'
            AND SS.date <= '$to_date'
            $outletFilter
    ";
    }


    public function getItemWiseSalesSummary($from_date, $to_date)
    {
        $user = auth()->user();

        $requestOutlet  = request()->store_id ?? null;
        $employeeOutlet = $user->employee->outlet_id ?? null;

        // outlet filter logic
        if ($user->is_super || ($user->employee && $user->employee->user_of == 'ho')) {
            $outletFilter = $requestOutlet ? " AND SS.outlet_id = '$requestOutlet' " : "";
        } else {
            $outletFilter = $employeeOutlet ? " AND SS.outlet_id = '$employeeOutlet' " : "";
        }

        return "
    (
        SELECT
            PG.name AS 'Group Name',
            COI.name AS 'Item',
            SUM(SI.quantity) AS 'Quantity',
            SUM(SI.quantity * SI.unit_price) AS 'Sales Amount'
        FROM sales SS
        JOIN sale_items SI ON SI.sale_id = SS.id
        JOIN chart_of_inventories COI ON COI.id = SI.product_id
        JOIN chart_of_inventories PG ON PG.id = COI.parent_id
        WHERE
            SS.date >= '$from_date'
            AND SS.date <= '$to_date'
            $outletFilter
        GROUP BY COI.id
        ORDER BY PG.id, COI.id
    )

    UNION ALL

    (
        SELECT
            '' AS 'Group Name',
            'Total' AS 'Item',
            SUM(SI.quantity) AS 'Quantity',
            SUM(SI.quantity * SI.unit_price) AS 'Sales Amount'
        FROM sales SS
        JOIN sale_items SI ON SI.sale_id = SS.id
        WHERE
            SS.date >= '$from_date'
            AND SS.date <= '$to_date'
            $outletFilter
    )
    ";
    }










    public function getOutletWiseSalesReport($from_date, $to_date)
    {
        $query = "
select OT.name as 'Outlet', PG.name as 'Group Name', COI.name as 'Item Name', sum(SI.quantity) as 'Quantity', sum(SI.quantity * SI.unit_price) as 'Sale Value'
from sales SS
join outlets OT
on OT.id = SS.outlet_id
join sale_items SI
on SI.sale_id = SS.id
join chart_of_inventories as COI
on SI.product_id = COI.id
join chart_of_inventories as PG
on PG.id = COI.parent_id
join account_transactions ATT
on ATT.doc_id = SS.id
where ATT.doc_type='POS'
AND ATT.chart_of_account_id = 43
AND SS.date >= '$from_date'
AND SS.date <= '$to_date'
";
        // Add the outlet filter if outlet_id is provided
        if (\request()->filled('store_id')) {
            $outlet_id = Outlet::find(\request()->store_id)->id;
            $query .= " AND SS.outlet_id = $outlet_id ";
        }

        // Grouping as per your existing logic
        $query .= " group by SS.outlet_id, SI.product_id";
        return $query;
    }


    public function getAllCustomerSalesDetails($from_date, $to_date)
    {
        $user = auth()->user();

        $requestOutlet = request()->store_id ?? null;
        $employeeOutlet = $user->employee->outlet_id ?? null;

        // outlet filter logic
        if ($user->is_super || ($user->employee && $user->employee->user_of == 'ho')) {
            $outletFilter = $requestOutlet ? " AND SS.outlet_id = '$requestOutlet' " : "";
        } else {
            $outletFilter = $employeeOutlet ? " AND SS.outlet_id = '$employeeOutlet' " : "";
        }

        return "
        SELECT
            SS.invoice_number AS 'Invoice Number',
            SS.date AS 'Date',
            CU.name AS 'Customer Name',
            PG.name AS 'Group Name',
            COI.name AS 'Item Name',
            SI.quantity AS 'Quantity',
            SI.unit_price AS 'Rate',
            (SI.quantity * SI.unit_price) AS 'Value'
        FROM sales SS
        JOIN customers CU ON CU.id = SS.customer_id
        JOIN sale_items SI ON SI.sale_id = SS.id
        JOIN chart_of_inventories COI ON COI.id = SI.product_id
        JOIN chart_of_inventories PG ON PG.id = COI.parent_id
        WHERE
            SS.date >= '$from_date'
            AND SS.date <= '$to_date'
            $outletFilter
    ";
    }






    public function getSinlgeItemDetails($item_id, $from_date, $to_date)
    {
        $user = auth()->user();

        $requestOutlet = request()->store_id ?? null;
        $employeeOutlet = $user->employee->outlet_id ?? null;

        // outlet filter logic
        if ($user->is_super || ($user->employee && $user->employee->user_of == 'ho')) {
            $outletFilter = $requestOutlet ? " AND SS.outlet_id = '$requestOutlet' " : "";
        } else {
            $outletFilter = $employeeOutlet ? " AND SS.outlet_id = '$employeeOutlet' " : "";
        }

        return "
        (
            SELECT
                SS.invoice_number AS 'Invoice Number',
                SS.date AS 'Date',
                SI.quantity AS 'Quantity',
                SI.unit_price AS 'Rate',
                FORMAT((SI.quantity * SI.unit_price),2) AS 'Sales Value',
                OT.name AS 'Outlet'
            FROM sales SS
            JOIN sale_items SI ON SI.sale_id = SS.id
            JOIN outlets OT ON OT.id = SS.outlet_id
            WHERE
                SI.product_id = $item_id
                AND SS.date >= '$from_date'
                AND SS.date <= '$to_date'
                $outletFilter
            ORDER BY OT.id
        )

        UNION ALL

        (
            SELECT
                'TOTAL' AS 'Invoice Number',
                '' AS 'Date',
                SUM(SI.quantity) AS 'Quantity',
                '' AS 'Rate',
                FORMAT(SUM((SI.quantity * SI.unit_price)),2) AS 'Sales Value',
                '' AS 'Outlet'
            FROM sales SS
            JOIN sale_items SI ON SI.sale_id = SS.id
            WHERE
                SI.product_id = $item_id
                AND SS.date >= '$from_date'
                AND SS.date <= '$to_date'
                $outletFilter
        )
    ";
    }


    public function getSingleCustomerDetails($customer_id, $from_date, $to_date)
    {
        $user = auth()->user();

        $requestOutlet = request()->store_id ?? null;
        $employeeOutlet = $user->employee->outlet_id ?? null;

        // outlet filter logic
        if ($user->is_super || ($user->employee && $user->employee->user_of == 'ho')) {
            $outletFilter = $requestOutlet ? " AND SS.outlet_id = '$requestOutlet' " : "";
        } else {
            $outletFilter = $employeeOutlet ? " AND SS.outlet_id = '$employeeOutlet' " : "";
        }

        return "
        SELECT
            SS.invoice_number AS 'Invoice Number',
            SS.date AS 'Date',
            PG.name AS 'Group Name',
            COI.name AS 'Item Name',
            SI.quantity AS 'Quantity',
            SI.unit_price AS 'Rate',
            (SI.quantity * SI.unit_price) AS 'Sales Value',
            OT.name AS 'Outlet'
        FROM sales SS
        JOIN sale_items SI ON SI.sale_id = SS.id
        JOIN customers CU ON CU.id = SS.customer_id
        JOIN outlets OT ON OT.id = SS.outlet_id
        JOIN chart_of_inventories COI ON COI.id = SI.product_id
        JOIN chart_of_inventories PG ON PG.id = COI.parent_id
        WHERE
            SS.customer_id = $customer_id
            AND SS.date >= '$from_date'
            AND SS.date <= '$to_date'
            $outletFilter
    ";
    }

    public function getAllOutletAccountSaleReport($from_date, $to_date)
    {
        return "
    SELECT
        o.name as OutletName,
        coa.name as AccountName,
        SUM(at2.amount) as Amount
    FROM account_transactions at2

    JOIN chart_of_accounts coa
        ON coa.id = at2.chart_of_account_id

    LEFT JOIN customer_receive_vouchers crv
        ON at2.doc_type = 'CRV'
        AND at2.doc_id = crv.id

    JOIN sales s
        ON (
            (at2.doc_type = 'POS' AND at2.doc_id = s.id)
            OR
            (at2.doc_type = 'CRV' AND crv.sale_id = s.id)
        )

    JOIN outlets o
        ON o.id = s.outlet_id

    JOIN outlet_accounts oa
        ON oa.outlet_id = s.outlet_id
        AND coa.id = oa.coa_id

    WHERE at2.doc_type IN ('POS','CRV')
        AND at2.type = 'debit'
        AND s.date >= '$from_date'
        AND s.date <= '$to_date'

    GROUP BY s.outlet_id, coa.id

    UNION ALL

    SELECT
        '' as OutletName,
        'TOTAL' as AccountName,
        SUM(at2.amount) as Amount
    FROM account_transactions at2

    JOIN chart_of_accounts coa
        ON coa.id = at2.chart_of_account_id

    LEFT JOIN customer_receive_vouchers crv
        ON at2.doc_type = 'CRV'
        AND at2.doc_id = crv.id

    JOIN sales s
        ON (
            (at2.doc_type = 'POS' AND at2.doc_id = s.id)
            OR
            (at2.doc_type = 'CRV' AND crv.sale_id = s.id)
        )

    JOIN outlets o
        ON o.id = s.outlet_id

    JOIN outlet_accounts oa
        ON oa.outlet_id = s.outlet_id
        AND coa.id = oa.coa_id

    WHERE at2.doc_type IN ('POS','CRV')
        AND at2.type = 'debit'
        AND s.date >= '$from_date'
        AND s.date <= '$to_date'
    ";
    }

    public function getPaymentMethodWiseSalesReport($from_date, $to_date)
    {
        $outlet_id = auth()->user()->employee && auth()->user()->employee->outlet_id ? auth()->user()->employee->outlet_id : null;
        $outlet_filter = "";
        if (!auth()->user()->is_super && !(auth()->user()->employee && auth()->user()->employee->user_of == 'ho')) {
            if ($outlet_id) {
                $outlet_filter = " AND sales.outlet_id = '$outlet_id' ";
            }
        }

        return "
            SELECT
                payment_method as Account,
                SUM(amount) as 'Total Amount'
            FROM payments
            JOIN sales ON sales.id = payments.sale_id
            WHERE sales.date >= '$from_date' AND sales.date <= '$to_date'
            $outlet_filter
            GROUP BY payment_method

            UNION ALL

            SELECT
                'Total' as Account,
                SUM(amount) as 'Total Amount'
            FROM payments
            JOIN sales ON sales.id = payments.sale_id
            WHERE sales.date >= '$from_date' AND sales.date <= '$to_date'
            $outlet_filter
        ";
    }

    public function getProductGroupWiseSalesReport($from_date, $to_date)
    {
        $outlet_filter = "";

        if (auth()->user()->is_super || (auth()->user()->employee && auth()->user()->employee->user_of == 'ho')) {
            if (\request()->filled('store_id')) {
                $store_id = \request()->store_id;
                $outlet_filter = " AND SS.outlet_id = '$store_id' ";
            }
        } else {
            $outlet_id = auth()->user()->employee && auth()->user()->employee->outlet_id ? auth()->user()->employee->outlet_id : null;
            if ($outlet_id) {
                $outlet_filter = " AND SS.outlet_id = '$outlet_id' ";
            }
        }

        $group_filter = "";
        if (\request()->filled('group_id')) {
            $group_id = \request()->group_id;
            $group_filter = " AND PG.id = '$group_id' ";
        }

        return "
            SELECT `Group Name`, `Item Name`, `Quantity`, `Total Amount` FROM (
                (
                    SELECT
                        PG.name as 'Group Name',
                        '' as 'Item Name',
                        NULL as 'Quantity',
                        NULL as 'Total Amount',
                        PG.id as sort_pg,
                        1 as sort_type,
                        0 as sort_total
                    FROM sales SS
                    JOIN sale_items SI ON SI.sale_id = SS.id
                    JOIN chart_of_inventories COI ON COI.id = SI.product_id
                    JOIN chart_of_inventories PG ON PG.id = COI.parent_id
                    WHERE SS.date >= '$from_date' AND SS.date <= '$to_date'
                    $outlet_filter
                    $group_filter
                    GROUP BY PG.id
                )
                UNION ALL
                (
                    SELECT
                        '' as 'Group Name',
                        COI.name as 'Item Name',
                        SUM(SI.quantity) as 'Quantity',
                        SUM(SI.quantity * SI.unit_price) as 'Total Amount',
                        PG.id as sort_pg,
                        0 as sort_type,
                        0 as sort_total
                    FROM sales SS
                    JOIN sale_items SI ON SI.sale_id = SS.id
                    JOIN chart_of_inventories COI ON COI.id = SI.product_id
                    JOIN chart_of_inventories PG ON PG.id = COI.parent_id
                    WHERE SS.date >= '$from_date' AND SS.date <= '$to_date'
                    $outlet_filter
                    $group_filter
                    GROUP BY COI.id
                )
                UNION ALL
                (
                    SELECT
                        'Total' as 'Group Name',
                        '' as 'Item Name',
                        SUM(SI.quantity) as 'Quantity',
                        SUM(SI.quantity * SI.unit_price) as 'Total Amount',
                        0 as sort_pg,
                        0 as sort_type,
                        1 as sort_total
                    FROM sales SS
                    JOIN sale_items SI ON SI.sale_id = SS.id
                    JOIN chart_of_inventories COI ON COI.id = SI.product_id
                    JOIN chart_of_inventories PG ON PG.id = COI.parent_id
                    WHERE SS.date >= '$from_date' AND SS.date <= '$to_date'
                    $outlet_filter
                    $group_filter
                )
            ) as report_data
            ORDER BY sort_total, sort_pg, sort_type DESC
        ";
    }
}
