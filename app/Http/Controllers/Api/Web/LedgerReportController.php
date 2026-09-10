<?php

namespace App\Http\Controllers\Api\Web;

use App\Http\Controllers\Controller;
use App\Models\ChartOfAccount;
use App\Models\Customer;
use App\Models\Supplier;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LedgerReportController extends Controller
{
    /** Soft cap so PDF HTML stays under mPDF pcre.backtrack_limit. */
    private const MAX_PDF_ROWS = 2000;

    public function initialInfo()
    {
        // Keep page open fast: only active ledgers. Parties are searched on demand.
        $accounts = ChartOfAccount::query()
            ->select(['id', 'name'])
            ->where('type', 'ledger')
            ->where('status', 'active')
            ->orderBy('name')
            ->get();

        return response()->json([
            'accounts' => $accounts,
            'suppliers' => [],
            'customers' => [],
            'success' => true,
        ]);
    }

    public function searchSuppliers(Request $request)
    {
        $q = trim((string) $request->get('q', ''));
        $query = Supplier::query()->select(['id', 'name', 'mobile']);
        if ($q !== '') {
            $query->where(function ($builder) use ($q) {
                $builder->where('name', 'like', '%' . $q . '%')
                    ->orWhere('mobile', 'like', '%' . $q . '%');
            });
        }

        return response()->json([
            'results' => $query->orderBy('name')->limit(30)->get(),
        ]);
    }

    public function searchCustomers(Request $request)
    {
        $q = trim((string) $request->get('q', ''));
        $query = Customer::query()->select(['id', 'name', 'mobile']);
        if ($q !== '') {
            $query->where(function ($builder) use ($q) {
                $builder->where('name', 'like', '%' . $q . '%')
                    ->orWhere('mobile', 'like', '%' . $q . '%');
            });
        }

        return response()->json([
            'results' => $query->orderBy('name')->limit(30)->get(),
        ]);
    }

    public function index()
    {
        //
    }

    public function create()
    {
        try {
            $reportType = (string) request('report_type', 'account_ledger');

            $fromDate = sanitizeReportDate(request('from_date', Carbon::now()->toDateString()));
            $toDate = sanitizeReportDate(request('to_date', Carbon::now()->toDateString()));
            [$fromDate, $toDate] = clampReportDateRange($fromDate, $toDate, 730);

            if ($reportType === 'supplier_ledger') {
                return $this->streamPartyLedger('supplier', (int) request('supplier_id'), $fromDate, $toDate);
            }
            if ($reportType === 'customer_ledger') {
                return $this->streamPartyLedger('customer', (int) request('customer_id'), $fromDate, $toDate);
            }

            $accountId = (int) request('account_id');
            if ($accountId < 1) {
                return response()->json([
                    'success' => false,
                    'message' => 'Please select a ledger account.',
                ], 422);
            }

            $account = ChartOfAccount::query()
                ->select(['id', 'name', 'root_account_type'])
                ->find($accountId);

            if (!$account) {
                return response()->json([
                    'success' => false,
                    'message' => 'Selected ledger account was not found.',
                ], 404);
            }

            $getData = $this->ledgerReportQuery($accountId, $fromDate, $toDate);

            return $this->streamLedgerPdf(
                $getData,
                'Account Head :: ' . $account->name,
                $fromDate,
                $toDate
            );
        } catch (\Throwable $e) {
            report($e);

            $message = 'Unable to generate ledger report.';
            if (str_contains($e->getMessage(), 'pcre.backtrack_limit') || str_contains($e->getMessage(), 'HTML code size')) {
                $message = 'Report HTML is too large for PDF. Please use a shorter date range or a less active account.';
            }

            return response()->json([
                'success' => false,
                'message' => $message,
            ], 500);
        }
    }

    private function streamPartyLedger(string $party, int $partyId, string $fromDate, string $toDate)
    {
        if ($partyId < 1) {
            return response()->json([
                'success' => false,
                'message' => $party === 'supplier' ? 'Please select a supplier.' : 'Please select a customer.',
            ], 422);
        }

        if ($party === 'supplier') {
            $model = Supplier::query()->select(['id', 'name'])->find($partyId);
            $label = 'Supplier';
        } else {
            $model = Customer::query()->select(['id', 'name'])->find($partyId);
            $label = 'Customer';
        }

        if (!$model) {
            return response()->json([
                'success' => false,
                'message' => $label . ' not found.',
            ], 404);
        }

        $getData = $this->partyLedgerQuery($party, $partyId, $fromDate, $toDate);

        return $this->streamLedgerPdf(
            $getData,
            $label . ' :: ' . $model->name,
            $fromDate,
            $toDate
        );
    }

    private function streamLedgerPdf(array $getData, string $pageTitle, string $fromDate, string $toDate)
    {
        if (count($getData) > self::MAX_PDF_ROWS && !wantsReportExcelExport()) {
            return response()->json([
                'success' => false,
                'message' => 'Too many rows (' . count($getData) . '). Please shorten the date range (max ' . self::MAX_PDF_ROWS . ' rows), or export as Excel.',
            ], 422);
        }

        if ($getData === []) {
            $getData = [(object) [
                'Date' => $fromDate,
                'Account Head' => '',
                'Doc Type' => '',
                'Doc No' => '',
                'Particulars' => 'No transactions found for the selected period',
                'Debit' => '0.00',
                'Credit' => '0.00',
                'Balance' => '0.00',
            ]];
        }

        // Truncate long particulars so HTML stays small for mPDF.
        $getData = array_map(function ($row) {
            $row = (object) (array) $row;
            if (isset($row->Particulars) && is_string($row->Particulars) && strlen($row->Particulars) > 120) {
                $row->Particulars = substr($row->Particulars, 0, 117) . '...';
            }

            return $row;
        }, $getData);

        $columns = array_keys((array) $getData[0]);

        $data = [
            'dateRange' => 'For the Period ' . $fromDate . ' to ' . $toDate,
            'data' => $getData,
            'page_title' => $pageTitle,
            'columns' => $columns,
            'report_header' => 'Ledger Report',
        ];

        if (wantsReportExcelExport()) {
            return downloadReportExcel($getData, 'Ledger-Report', $columns);
        }

        $html = view('common.ledger_report_pdf', $data)->render();

        $tempDir = storage_path('app/mpdf');
        if (!is_dir($tempDir)) {
            mkdir($tempDir, 0755, true);
        }

        // Lean HTML + higher backtrack limit avoids mPDF pcre failures on busy ledgers.
        $previousLimit = ini_get('pcre.backtrack_limit');
        @ini_set('pcre.backtrack_limit', '10000000');

        try {
            $mpdf = new \Mpdf\Mpdf([
                'tempDir' => $tempDir,
                'mode' => 'utf-8',
                'format' => 'A4-L',
                'orientation' => 'L',
                'margin_left' => 8,
                'margin_right' => 8,
                'margin_top' => 8,
                'margin_bottom' => 8,
            ]);

            foreach (str_split($html, 30000) as $index => $chunk) {
                $mode = $index === 0
                    ? \Mpdf\HTMLParserMode::DEFAULT_MODE
                    : \Mpdf\HTMLParserMode::HTML_BODY;
                $mpdf->WriteHTML($chunk, $mode);
            }

            return response($mpdf->Output('ledger-report.pdf', 'S'), 200, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'inline; filename="ledger-report.pdf"',
            ]);
        } finally {
            if ($previousLimit !== false) {
                @ini_set('pcre.backtrack_limit', (string) $previousLimit);
            }
        }
    }

    public function store(Request $request)
    {
        //
    }

    public function show(string $id)
    {
        //
    }

    public function edit(string $id)
    {
        //
    }

    public function update(Request $request, string $id)
    {
        //
    }

    public function destroy(string $id)
    {
        //
    }

    public function partyLedgerQuery(string $party, int $partyId, string $startDate, string $endDate): array
    {
        $startDate = sanitizeReportDate($startDate);
        $endDate = sanitizeReportDate($endDate);
        $partyId = (int) $partyId;

        if ($party === 'supplier') {
            $table = 'supplier_transactions';
            $fk = 'supplier_id';
        } elseif ($party === 'customer') {
            $table = 'customer_transactions';
            $fk = 'customer_id';
        } else {
            return [];
        }

        return DB::select("
            WITH OpeningBalance AS (
                SELECT
                    ? AS Date,
                    ' ' AS `Account Head`,
                    ' ' AS `Doc Type`,
                    ' ' AS `Doc No`,
                    'Opening Balance' AS Particulars,
                    ' ' AS Debit,
                    ' ' AS Credit,
                    COALESCE(SUM(amount * transaction_type), 0) AS Balance
                FROM {$table}
                WHERE {$fk} = ?
                  AND date < ?
                LIMIT 1
            )
            SELECT * FROM OpeningBalance
            UNION ALL
            SELECT
                DATE_FORMAT(t.date, '%Y-%m-%d') AS Date,
                IFNULL(coa.name, '') AS `Account Head`,
                IFNULL(t.doc_type, '') AS `Doc Type`,
                IFNULL(t.doc_id, '') AS `Doc No`,
                IFNULL(t.description, '') AS Particulars,
                CASE WHEN t.transaction_type > 0 THEN t.amount ELSE 0 END AS Debit,
                CASE WHEN t.transaction_type < 0 THEN t.amount ELSE 0 END AS Credit,
                (
                    (SELECT Balance FROM OpeningBalance)
                    + SUM(t.amount * t.transaction_type) OVER (ORDER BY t.date, t.id)
                ) AS Balance
            FROM {$table} t
            LEFT JOIN chart_of_accounts coa ON coa.id = t.chart_of_account_id
            WHERE t.{$fk} = ?
              AND t.date >= ?
              AND t.date <= ?
            ORDER BY Date, `Doc No`
        ", [$startDate, $partyId, $startDate, $partyId, $startDate, $endDate]);
    }

    public function ledgerReportQuery($account_id, $start_date, $end_date)
    {
        $account_id = (int) $account_id;
        $start_date = sanitizeReportDate($start_date);
        $end_date = sanitizeReportDate($end_date);
        $account = ChartOfAccount::query()
            ->select(['id', 'root_account_type'])
            ->find($account_id);

        if (!$account) {
            return [];
        }

        if ($account->root_account_type == 'as' || $account->root_account_type == 'li') {
            return DB::select("WITH OpeningBalance AS (
    SELECT
        ? AS Date,
        ' ' AS `Account Head`,
        ' ' AS `Doc Type`,
        ' ' AS `Doc No`,
        'Opening Balance' AS Particulars,
        ' ' AS Debit,
        ' ' AS Credit,
        IF(COA.root_account_type='as',COALESCE(SUM(CASE WHEN TR.type = 'debit' THEN TR.amount ELSE -TR.amount END),0),COALESCE(SUM(CASE WHEN TR.type = 'debit' THEN -TR.amount ELSE TR.amount END),0)) AS Balance
    FROM account_transactions TR
    LEFT JOIN chart_of_accounts COA ON COA.id = TR.chart_of_account_id
    WHERE TR.chart_of_account_id = ?
    AND TR.date < ?
    LIMIT 1
)

SELECT * FROM OpeningBalance

UNION ALL

SELECT
     CASE WHEN TR.date < ? THEN ? ELSE TR.date END AS Date,
     CASE WHEN TR.date < ? THEN ' ' ELSE COA.name END AS `Account Head`,
     doc_type AS `Doc Type`,
     CASE
        WHEN TR.doc_type = 'POS' THEN IFNULL(S.invoice_number, TR.doc_id)
        ELSE TR.doc_id
     END AS `Doc No`,
    CASE WHEN TR.date < ? THEN 'Opening Balance' ELSE IFNULL(TR.narration, '') END AS Particulars,
    CASE WHEN TR.date < ? THEN ' ' ELSE CASE WHEN TR.type = 'debit' THEN TR.amount ELSE 0 END END AS Debit,
    CASE WHEN TR.date < ? THEN ' ' ELSE CASE WHEN TR.type = 'credit' THEN TR.amount ELSE 0 END END AS Credit,

     IF(COAM.root_account_type = 'as', SUM(CASE WHEN TR.type = 'debit' THEN TR.amount ELSE -TR.amount END) OVER (ORDER BY TR.date, TR.id) + (SELECT Balance FROM OpeningBalance), (SUM(CASE WHEN TR.type = 'debit' THEN TR.amount ELSE -TR.amount END) OVER (ORDER BY TR.date, TR.id) + (SELECT Balance FROM OpeningBalance))*-1) AS Balance

FROM account_transactions TR
LEFT JOIN sales S ON S.id = TR.doc_id AND TR.doc_type = 'POS'
LEFT JOIN receive_vouchers RV ON (RV.id = TR.doc_id AND TR.doc_type = 'RV')
LEFT JOIN payment_vouchers PV ON PV.id = TR.doc_id AND TR.doc_type = 'PV'
LEFT JOIN journal_vouchers JV ON JV.id = TR.doc_id AND TR.doc_type = 'JV'
LEFT JOIN fund_transfer_vouchers FTV ON FTV.id = TR.doc_id AND TR.doc_type = 'FTV'
LEFT JOIN chart_of_accounts COA ON COA.id = (
CASE
WHEN RV.uid IS NULL AND JV.uid IS NULL AND FTV.uid IS NULL THEN (CASE WHEN TR.type = 'debit' THEN PV.credit_account_id ELSE PV.debit_account_id END)
WHEN RV.uid IS NULL AND PV.uid IS NULL AND FTV.uid IS NULL THEN (CASE WHEN TR.type = 'debit' THEN JV.credit_account_id ELSE JV.debit_account_id END)
WHEN RV.uid IS NULL AND PV.uid IS NULL AND JV.uid IS NULL THEN (CASE WHEN TR.type = 'debit' THEN FTV.credit_account_id ELSE FTV.debit_account_id END)
ELSE (CASE WHEN TR.type = 'credit' THEN RV.debit_account_id ELSE RV.credit_account_id END)
END
)
LEFT JOIN chart_of_accounts COAM ON COAM.id=TR.chart_of_account_id
WHERE TR.chart_of_account_id = ?
AND TR.date >= ? AND TR.date <= ?
", [
                $start_date,
                $account_id,
                $start_date,
                $start_date,
                $start_date,
                $start_date,
                $start_date,
                $start_date,
                $start_date,
                $account_id,
                $start_date,
                $end_date,
            ]);
        }

        return DB::select("SELECT
    CASE WHEN TR.date < ? THEN ? ELSE TR.date END AS Date,
     CASE WHEN TR.date < ? THEN ' ' ELSE COA.name END AS `Account Head`,
     CASE WHEN TR.date < ? THEN ' ' ELSE TR.doc_type END AS `Doc Type`,
     CASE
        WHEN TR.date < ? THEN ' '
        WHEN TR.doc_type = 'POS' THEN IFNULL(S.invoice_number, TR.doc_id)
        ELSE TR.doc_id
     END AS `Doc No`,
    CASE WHEN TR.date < ? THEN 'Opening Balance' ELSE IFNULL(TR.narration, '') END AS Particulars,
    CASE WHEN TR.date < ? THEN ' ' ELSE CASE WHEN TR.type = 'debit' THEN TR.amount ELSE 0 END END AS Debit,
    CASE WHEN TR.date < ? THEN ' ' ELSE CASE WHEN TR.type = 'credit' THEN TR.amount ELSE 0 END END AS Credit,
    IF(COAM.root_account_type='ex', SUM(CASE WHEN TR.type = 'debit' THEN TR.amount ELSE -TR.amount END) OVER (ORDER BY TR.date, TR.id),(SUM(CASE WHEN TR.type = 'debit' THEN TR.amount ELSE -TR.amount END) OVER (ORDER BY TR.date, TR.id))*-1) AS Balance
FROM account_transactions TR
LEFT JOIN sales S ON S.id = TR.doc_id AND TR.doc_type = 'POS'
LEFT JOIN receive_vouchers RV ON (RV.id = TR.doc_id AND TR.doc_type = 'RV')
LEFT JOIN payment_vouchers PV ON PV.id = TR.doc_id AND TR.doc_type = 'PV'
LEFT JOIN chart_of_accounts COA ON COA.id = (CASE WHEN RV.uid IS NULL THEN (CASE WHEN TR.type = 'debit' THEN PV.credit_account_id ELSE PV.debit_account_id END) ELSE (CASE WHEN TR.type = 'credit' THEN RV.debit_account_id ELSE RV.credit_account_id END) END)
LEFT JOIN chart_of_accounts COAM ON COAM.id=TR.chart_of_account_id
WHERE TR.chart_of_account_id = ?
AND TR.date >= ? AND TR.date <= ?
", [
            $start_date,
            $start_date,
            $start_date,
            $start_date,
            $start_date,
            $start_date,
            $start_date,
            $start_date,
            $account_id,
            $start_date,
            $end_date,
        ]);
    }
}
