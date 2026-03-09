<?php

namespace App\Http\Controllers\Api\Web;

use App\Http\Controllers\Controller;
use App\Models\ChartOfAccount;
use App\Models\ChartOfInventory;
use App\Models\Customer;
use App\Models\Store;
use App\Models\Supplier;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Mpdf\Mpdf;
use niklasravnsborg\LaravelPdf\Facades\Pdf;

class LedgerReportController extends Controller
{

    public function initialInfo()
    {
        return response()->json([
            'accounts' => ChartOfAccount::where(['type' => 'ledger'])->get(),
            'suppliers' => Supplier::query()->get(),
            'customers' => Customer::query()->get(),
            'success' => true
        ]);
    }

    public function index()
    {
        //
    }


    public function create()
    {
        $report_type = \request()->report_type;

        $from_date = Carbon::parse(\request()->from_date)->format('Y-m-d') ?? Carbon::now()->format('Y-m-d');
        $to_date   = Carbon::parse(\request()->to_date)->format('Y-m-d')   ?? Carbon::now()->format('Y-m-d');

        $page_title    = false;
        $report_header = 'Ledger Report';

        if ($report_type === 'account_ledger') {
            $page_title = ' Account Head  ::     ' . ChartOfAccount::find(\request()->account_id)->name;
            $getData    = $this->ledgerReportQuery(\request()->account_id, $from_date, $to_date);
        } elseif ($report_type === 'supplier_ledger') {
            $supplier      = Supplier::find(\request()->supplier_id);
            $page_title    = ' Supplier  ::     ' . $supplier->name;
            $report_header = 'Supplier Ledger Report';
            $getData       = $this->supplierLedgerQuery(\request()->supplier_id, $from_date, $to_date);
        } elseif ($report_type === 'customer_ledger') {
            $customer      = Customer::find(\request()->customer_id);
            $page_title    = ' Customer  ::     ' . $customer->name;
            $report_header = 'Customer Ledger Report';
            $getData       = $this->customerLedgerQuery(\request()->customer_id, $from_date, $to_date);
        } else {
            return response()->json(['success' => false, 'message' => 'Invalid Report Type']);
        }

        if (!isset($getData[0])) {
            return response()->json(['success' => false]);
        }

        $columns = array_keys((array)$getData[0]);

        // ---------------------------------------------------------------
        // Use mPDF directly and write HTML in chunks to avoid the
        // "HTML code size is larger than pcre.backtrack_limit" error.
        // ---------------------------------------------------------------
        $mpdf = new Mpdf([
            'format'      => 'A4-L',
            'orientation' => 'L',
            'margin_left' => 5,
            'margin_right'  => 5,
            'margin_top'    => 10,
            'margin_bottom' => 10,
        ]);

        $dateRange = ' For the Period ' . $from_date . ' to ' . $to_date;

        // --- 1. Write the full opening HTML (head + styles + company header + table open + column headers)
        //        Use default mode (0) so mPDF processes the <head> section for styles.
        $headerHtml = view('common.report_main_header', [
            'report_header' => $report_header,
            'dateRange'     => $dateRange,
            'page_title'    => $page_title,
            'columns'       => $columns,
        ])->render();

        $mpdf->WriteHTML($headerHtml); // mode 0 = full HTML document (default)

        // --- 2. Write data rows in chunks of 100 rows ---
        //        Use HTMLParserMode::HTML_BODY (2) for raw <tr> fragments.
        $chunkSize = 100;
        $chunks    = array_chunk($getData, $chunkSize);

        foreach ($chunks as $chunk) {
            $rowsHtml = '';
            foreach ($chunk as $item) {
                $rowsHtml .= '<tr class="items">';
                foreach ($columns as $column) {
                    $value     = isset($item->$column) ? $item->$column : (is_array($item) ? ($item[$column] ?? '') : '');
                    $value     = str_replace(' ', '&nbsp;', htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8'));
                    $rowsHtml .= '<td style="white-space:pre;padding:0.5rem;border-bottom:1px solid #dfdfdf;font-size:11px;">' . $value . '</td>';
                }
                $rowsHtml .= '</tr>';
            }
            $mpdf->WriteHTML($rowsHtml, \Mpdf\HTMLParserMode::HTML_BODY);
        }

        // --- 3. Write the closing HTML (close table/div/body/html)
        $footerHtml = view('common.report_main_footer')->render();
        $mpdf->WriteHTML($footerHtml, \Mpdf\HTMLParserMode::HTML_BODY);

        return response($mpdf->Output('ledger_report.pdf', 'S'), 200, [
            'Content-Type'        => 'application/pdf',
            'Content-Disposition' => 'inline; filename="ledger_report.pdf"',
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    public function supplierLedgerQuery($supplier_id, $start_date, $end_date)
    {
        return DB::select("WITH OpeningBalance AS (
            SELECT
                '$start_date' AS Date,
                ' ' AS 'Account Head',
                ' ' AS 'Voucher Type',
                ' ' AS 'Voucher Number',
                'Opening Balance' AS Particulars,
                ' ' AS Debit,
                ' ' AS Credit,
                COALESCE(SUM(amount * transaction_type), 0) AS Balance
            FROM supplier_transactions
            WHERE supplier_id = $supplier_id
            AND date < '$start_date'
            LIMIT 1
        )

        SELECT * FROM OpeningBalance

        UNION ALL

        SELECT
            TR.date AS Date,
            COA.name AS 'Account Head',
            doc_type AS 'Voucher Type',
            COALESCE(P.uid, SPV.uid, CAST(TR.doc_id AS CHAR)) AS 'Voucher Number',
            TR.description AS Particulars,
            CASE WHEN transaction_type = -1 THEN amount ELSE 0 END AS Debit,
            CASE WHEN transaction_type = 1 THEN amount ELSE 0 END AS Credit,
            SUM(amount * transaction_type) OVER (ORDER BY TR.date, TR.id) + (SELECT Balance FROM OpeningBalance) AS Balance
        FROM supplier_transactions TR
        LEFT JOIN chart_of_accounts COA ON COA.id = TR.chart_of_account_id
        LEFT JOIN purchases P ON P.id = TR.doc_id AND TR.doc_type = 'GPB'
        LEFT JOIN supplier_payment_vouchers SPV ON SPV.id = TR.doc_id AND TR.doc_type = 'SPV'
        WHERE TR.supplier_id = $supplier_id
        AND TR.date >= '$start_date' AND TR.date <= '$end_date'
        ");
    }

    public function customerLedgerQuery($customer_id, $start_date, $end_date)
    {
        return DB::select("WITH OpeningBalance AS (
            SELECT
                '$start_date' AS Date,
                ' ' AS 'Account Head',
                ' ' AS 'Voucher Type',
                ' ' AS 'Voucher Number',
                'Opening Balance' AS Particulars,
                ' ' AS Debit,
                ' ' AS Credit,
                COALESCE(SUM(amount * transaction_type), 0) AS Balance
            FROM customer_transactions
            WHERE customer_id = $customer_id
            AND date < '$start_date'
            LIMIT 1
        )

        SELECT * FROM OpeningBalance

        UNION ALL

        SELECT
            TR.date AS Date,
            COA.name AS 'Account Head',
            doc_type AS 'Voucher Type',
            COALESCE(S.invoice_number, CRV.uid, CAST(TR.doc_id AS CHAR)) AS 'Voucher Number',
            TR.description AS Particulars,
            CASE WHEN transaction_type = 1 THEN amount ELSE 0 END AS Debit,
            CASE WHEN transaction_type = -1 THEN amount ELSE 0 END AS Credit,
            SUM(amount * transaction_type) OVER (ORDER BY TR.date, TR.id) + (SELECT Balance FROM OpeningBalance) AS Balance
        FROM customer_transactions TR
        LEFT JOIN chart_of_accounts COA ON COA.id = TR.chart_of_account_id
        LEFT JOIN sales S ON S.id = TR.doc_id AND TR.doc_type = 'SALE'
        LEFT JOIN customer_receive_vouchers CRV ON CRV.id = TR.doc_id AND TR.doc_type = 'CRV'
        WHERE TR.customer_id = $customer_id
        AND TR.date >= '$start_date' AND TR.date <= '$end_date'
        ");
    }

    public function ledgerReportQuery($account_id, $start_date, $end_date)
    {
        $account = ChartOfAccount::find($account_id);
        if ($account->root_account_type == 'as' || $account->root_account_type == 'li') {
            return $result = DB::select("WITH OpeningBalance AS (
    SELECT
        '$start_date' AS Date,
        ' ' AS 'Account Head',
        ' ' AS 'Voucher Type',
        ' ' AS 'Voucher Number',
        'Opening Balance' AS Particulars,
        ' ' AS Debit,
        ' ' AS Credit,
        IF(COA.root_account_type='as',COALESCE(SUM(CASE WHEN TR.type = 'debit' THEN TR.amount ELSE -TR.amount END),0),COALESCE(SUM(CASE WHEN TR.type = 'debit' THEN -TR.amount ELSE TR.amount END),0)) AS Balance
    FROM account_transactions TR
    LEFT JOIN chart_of_accounts COA ON COA.id = TR.chart_of_account_id
    WHERE TR.chart_of_account_id = $account_id
    AND TR.date < '$start_date'
    LIMIT 1
)

SELECT * FROM OpeningBalance

UNION ALL

SELECT
     CASE WHEN TR.date < '$start_date' THEN '$start_date' ELSE TR.date END AS Date,
     CASE WHEN TR.date < '$start_date' THEN ' ' ELSE COA.name END AS 'Account Head',
     doc_type AS 'Voucher Type',
     COALESCE(RV.uid, PV.uid, JV.uid, FTV.uid, SPV.uid, CRV.uid, P.uid, S.invoice_number, CAST(TR.doc_id AS CHAR)) AS 'Voucher Number',
    CASE WHEN TR.date < '$start_date' THEN 'Opening Balance' ELSE IFNULL(TR.narration, '') END AS Particulars,
    CASE WHEN TR.date < '$start_date' THEN ' ' ELSE CASE WHEN TR.type = 'debit' THEN TR.amount ELSE 0 END END AS Debit,
    CASE WHEN TR.date < '$start_date' THEN ' ' ELSE CASE WHEN TR.type = 'credit' THEN TR.amount ELSE 0 END END AS Credit,

     IF(COAM.root_account_type = 'as', SUM(CASE WHEN TR.type = 'debit' THEN TR.amount ELSE -TR.amount END) OVER (ORDER BY TR.date, TR.id) + (SELECT Balance FROM OpeningBalance), (SUM(CASE WHEN TR.type = 'debit' THEN TR.amount ELSE -TR.amount END) OVER (ORDER BY TR.date, TR.id) + (SELECT Balance FROM OpeningBalance))*-1) AS Balance

FROM account_transactions TR
LEFT JOIN receive_vouchers RV ON (RV.id = TR.doc_id AND TR.doc_type = 'RV')
LEFT JOIN payment_vouchers PV ON PV.id = TR.doc_id AND TR.doc_type = 'PV'
LEFT JOIN journal_vouchers JV ON JV.id = TR.doc_id AND TR.doc_type = 'JV'
LEFT JOIN fund_transfer_vouchers FTV ON FTV.id = TR.doc_id AND TR.doc_type = 'FTV'
LEFT JOIN supplier_payment_vouchers SPV ON SPV.id = TR.doc_id AND TR.doc_type = 'SPV'
LEFT JOIN customer_receive_vouchers CRV ON CRV.id = TR.doc_id AND TR.doc_type = 'CRV'
LEFT JOIN purchases P ON P.id = TR.doc_id AND TR.doc_type = 'GPB'
LEFT JOIN sales S ON S.id = TR.doc_id AND TR.doc_type = 'SALE'
LEFT JOIN chart_of_accounts COA ON COA.id = (
CASE
WHEN TR.doc_type = 'RV' THEN (CASE WHEN TR.type = 'credit' THEN RV.debit_account_id ELSE RV.credit_account_id END)
WHEN TR.doc_type = 'PV' THEN (CASE WHEN TR.type = 'debit' THEN PV.credit_account_id ELSE PV.debit_account_id END)
WHEN TR.doc_type = 'JV' THEN (CASE WHEN TR.type = 'debit' THEN JV.credit_account_id ELSE JV.debit_account_id END)
WHEN TR.doc_type = 'FTV' THEN (CASE WHEN TR.type = 'debit' THEN FTV.credit_account_id ELSE FTV.debit_account_id END)
WHEN TR.doc_type = 'SPV' THEN (CASE WHEN TR.type = 'debit' THEN SPV.credit_account_id ELSE SPV.debit_account_id END)
WHEN TR.doc_type = 'CRV' THEN (CASE WHEN TR.type = 'credit' THEN CRV.debit_account_id ELSE CRV.credit_account_id END)
WHEN TR.doc_type = 'GPB' THEN (CASE WHEN TR.type = 'debit' THEN 22 ELSE 15 END)
WHEN TR.doc_type = 'SALE' THEN (CASE WHEN TR.type = 'credit' THEN 1 ELSE 12 END)
ELSE NULL
END
)
LEFT JOIN chart_of_accounts COAM ON COAM.id=TR.chart_of_account_id
WHERE TR.chart_of_account_id = $account_id
AND TR.date >= '$start_date' AND TR.date <= '$end_date'
");
        } else {
            $q = "WITH OpeningBalance AS (
    SELECT
        '$start_date' AS Date,
        ' ' AS 'Account Head',
        ' ' AS 'Voucher Type',
        ' ' AS 'Voucher Number',
        'Opening Balance' AS Particulars,
        ' ' AS Debit,
        ' ' AS Credit,
        IF(COA.root_account_type='ex',COALESCE(SUM(CASE WHEN TR.type = 'debit' THEN TR.amount ELSE -TR.amount END),0),COALESCE(SUM(CASE WHEN TR.type = 'debit' THEN -TR.amount ELSE TR.amount END),0)) AS Balance
    FROM account_transactions TR
    LEFT JOIN chart_of_accounts COA ON COA.id = TR.chart_of_account_id
    WHERE TR.chart_of_account_id = $account_id
    AND TR.date < '$start_date'
    LIMIT 1
)

SELECT * FROM OpeningBalance

UNION ALL

SELECT
     CASE WHEN TR.date < '$start_date' THEN '$start_date' ELSE TR.date END AS Date,
     CASE WHEN TR.date < '$start_date' THEN ' ' ELSE COA.name END AS 'Account Head',
     doc_type AS 'Voucher Type',
     COALESCE(RV.uid, PV.uid, JV.uid, FTV.uid, SPV.uid, CRV.uid, P.uid, S.invoice_number, CAST(TR.doc_id AS CHAR)) AS 'Voucher Number',
    CASE WHEN TR.date < '$start_date' THEN 'Opening Balance' ELSE IFNULL(TR.narration, '') END AS Particulars,
    CASE WHEN TR.date < '$start_date' THEN ' ' ELSE CASE WHEN TR.type = 'debit' THEN TR.amount ELSE 0 END END AS Debit,
    CASE WHEN TR.date < '$start_date' THEN ' ' ELSE CASE WHEN TR.type = 'credit' THEN TR.amount ELSE 0 END END AS Credit,
    IF(COAM.root_account_type='ex', SUM(CASE WHEN TR.type = 'debit' THEN TR.amount ELSE -TR.amount END) OVER (ORDER BY TR.date, TR.id) + (SELECT Balance FROM OpeningBalance), (SUM(CASE WHEN TR.type = 'debit' THEN TR.amount ELSE -TR.amount END) OVER (ORDER BY TR.date, TR.id) + (SELECT Balance FROM OpeningBalance))*-1) AS Balance
FROM account_transactions TR
LEFT JOIN receive_vouchers RV ON (RV.id = TR.doc_id AND TR.doc_type = 'RV')
LEFT JOIN payment_vouchers PV ON PV.id = TR.doc_id AND TR.doc_type = 'PV'
LEFT JOIN journal_vouchers JV ON JV.id = TR.doc_id AND TR.doc_type = 'JV'
LEFT JOIN fund_transfer_vouchers FTV ON FTV.id = TR.doc_id AND TR.doc_type = 'FTV'
LEFT JOIN supplier_payment_vouchers SPV ON SPV.id = TR.doc_id AND TR.doc_type = 'SPV'
LEFT JOIN customer_receive_vouchers CRV ON CRV.id = TR.doc_id AND TR.doc_type = 'CRV'
LEFT JOIN purchases P ON P.id = TR.doc_id AND TR.doc_type = 'GPB'
LEFT JOIN sales S ON S.id = TR.doc_id AND TR.doc_type = 'SALE'
LEFT JOIN chart_of_accounts COA ON COA.id = (
CASE
WHEN TR.doc_type = 'RV' THEN (CASE WHEN TR.type = 'credit' THEN RV.debit_account_id ELSE RV.credit_account_id END)
WHEN TR.doc_type = 'PV' THEN (CASE WHEN TR.type = 'debit' THEN PV.credit_account_id ELSE PV.debit_account_id END)
WHEN TR.doc_type = 'JV' THEN (CASE WHEN TR.type = 'debit' THEN JV.credit_account_id ELSE JV.debit_account_id END)
WHEN TR.doc_type = 'FTV' THEN (CASE WHEN TR.type = 'debit' THEN FTV.credit_account_id ELSE FTV.debit_account_id END)
WHEN TR.doc_type = 'SPV' THEN (CASE WHEN TR.type = 'debit' THEN SPV.credit_account_id ELSE SPV.debit_account_id END)
WHEN TR.doc_type = 'CRV' THEN (CASE WHEN TR.type = 'credit' THEN CRV.debit_account_id ELSE CRV.credit_account_id END)
WHEN TR.doc_type = 'GPB' THEN (CASE WHEN TR.type = 'debit' THEN 22 ELSE 15 END)
WHEN TR.doc_type = 'SALE' THEN (CASE WHEN TR.type = 'credit' THEN 1 ELSE 12 END)
ELSE NULL
END
)
LEFT JOIN chart_of_accounts COAM ON COAM.id=TR.chart_of_account_id
WHERE TR.chart_of_account_id = $account_id
AND TR.date >= '$start_date' AND TR.date <= '$end_date'
";
            return $result = DB::select($q);
        }
    }
}
