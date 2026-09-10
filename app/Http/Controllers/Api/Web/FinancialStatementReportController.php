<?php

namespace App\Http\Controllers\Api\Web;

use App\Http\Controllers\Controller;
use App\Models\ChartOfAccount;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class FinancialStatementReportController extends Controller
{
    public function index()
    {
        return view('financial_statement.index');
    }

    public function initialInfo()
    {
        return response()->json(['success' => true]);
    }

    public function create()
    {
        try {
            $reportType = (string) request('report_type', 'balance_sheet');

            $fromDate = sanitizeReportDate(request('from_date', Carbon::now()->toDateString()));
            $toDate = sanitizeReportDate(request('to_date', Carbon::now()->toDateString()));
            [$fromDate, $toDate] = clampReportDateRange($fromDate, $toDate, 730);

            $asOnDate = sanitizeReportDate(request('as_on_date', $toDate));

            if ($reportType === 'income_statement') {
                return $this->streamReport(
                    $this->incomeStatementQuery($fromDate, $toDate),
                    'Income Statement',
                    'For the Period ' . $fromDate . ' to ' . $toDate
                );
            }

            if ($reportType === 'cash_flow') {
                return $this->streamReport(
                    $this->cashFlowQuery($fromDate, $toDate),
                    'Cash Flow Statement',
                    'For the Period ' . $fromDate . ' to ' . $toDate
                );
            }

            if ($reportType === 'trial_balance') {
                return $this->streamReport(
                    $this->trialBalanceQuery($asOnDate),
                    'Trial Balance',
                    'As On ' . $asOnDate,
                    true
                );
            }

            return $this->streamReport(
                $this->balanceSheetQuery($asOnDate),
                'Balance Sheet',
                'As On ' . $asOnDate
            );
        } catch (\Throwable $e) {
            report($e);

            return response()->json([
                'success' => false,
                'message' => 'Unable to generate financial statement. ' . (
                    str_contains($e->getMessage(), 'pcre.backtrack_limit') || str_contains($e->getMessage(), 'HTML code size')
                        ? 'Report is too large — try a shorter period.'
                        : 'Please try again.'
                ),
            ], 500);
        }
    }

    private function streamReport(array $getData, string $reportHeader, string $dateRange, bool $showDebitCredit = false)
    {
        if (wantsReportExcelExport()) {
            return downloadFinancialStatementExcel($getData, $reportHeader, $dateRange, $showDebitCredit);
        }

        @ini_set('pcre.backtrack_limit', '5000000');
        @ini_set('memory_limit', '512M');

        $mpdf = new \Mpdf\Mpdf([
            'tempDir' => storage_path('app'),
            'mode' => 'utf-8',
            'format' => 'A4',
            'orientation' => 'P',
            'margin_left' => 8,
            'margin_right' => 8,
            'margin_top' => 28,
            'margin_bottom' => 18,
        ]);

        $mpdf->SetTitle($reportHeader);
        $mpdf->SetHTMLHeader(
            '<div style="text-align:center;font-family:DejaVuSans,sans-serif;border-bottom:1px solid #333;padding-bottom:4px;">'
            . '<div style="font-size:14px;font-weight:bold;">' . e(config('app.name', 'ERP Lite')) . '</div>'
            . '<div style="font-size:12px;font-weight:bold;margin-top:2px;">' . e($reportHeader) . '</div>'
            . '<div style="font-size:10px;margin-top:2px;">' . e($dateRange) . '</div>'
            . '</div>'
        );
        $mpdf->SetHTMLFooter(
            '<div style="font-size:8px;font-family:DejaVuSans,sans-serif;border-top:1px solid #ccc;padding-top:3px;text-align:right;">'
            . 'Page {PAGENO} of {nbpg} &nbsp;|&nbsp; Generated ' . e(now()->format('Y-m-d H:i'))
            . '</div>'
        );

        $html = view('common.financial_statement_pdf', [
            'getData' => $getData,
            'showDebitCredit' => $showDebitCredit,
        ])->render();

        $chunkSize = 120000;
        $length = strlen($html);
        for ($offset = 0; $offset < $length; $offset += $chunkSize) {
            $mpdf->WriteHTML(
                substr($html, $offset, $chunkSize),
                $offset === 0 ? \Mpdf\HTMLParserMode::DEFAULT_MODE : \Mpdf\HTMLParserMode::HTML_BODY
            );
        }

        return response($mpdf->Output('', 'S'), 200)
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'inline; filename="' . str_replace(' ', '_', $reportHeader) . '.pdf"');
    }

    private function balanceSheetQuery(string $asOnDate): array
    {
        $rows = [];
        $totalAssets = 0.0;
        $totalLiabilities = 0.0;

        $rows[] = $this->sectionRow('ASSETS');

        $assetRoots = ChartOfAccount::query()
            ->where('root_account_type', 'as')
            ->whereNull('parent_id')
            ->where('status', 'active')
            ->orderBy('id')
            ->get();

        foreach ($assetRoots as $root) {
            [$sectionRows, $sectionTotal] = $this->buildHierarchyRows($root, $asOnDate, null, null, 'as', 0);
            $rows = array_merge($rows, $sectionRows);
            $totalAssets += $sectionTotal;
        }

        $rows[] = $this->totalRow('Total Assets', $totalAssets, 0);

        $rows[] = $this->sectionRow('LIABILITIES & EQUITY');

        $liabilityRoots = ChartOfAccount::query()
            ->where('root_account_type', 'li')
            ->whereNull('parent_id')
            ->where('status', 'active')
            ->orderBy('id')
            ->get();

        foreach ($liabilityRoots as $root) {
            [$sectionRows, $sectionTotal] = $this->buildHierarchyRows($root, $asOnDate, null, null, 'li', 0);
            $rows = array_merge($rows, $sectionRows);
            $totalLiabilities += $sectionTotal;
        }

        $retainedEarningsId = $this->resolveRetainedEarningsAccountId();
        if ($retainedEarningsId) {
            $profitLoss = (float) DB::table('account_transactions')
                ->whereDate('date', '<=', $asOnDate)
                ->where('chart_of_account_id', $retainedEarningsId)
                ->selectRaw("COALESCE(SUM(CASE WHEN type = 'credit' THEN amount ELSE -amount END), 0) as balance")
                ->value('balance');

            $rows[] = [
                'name' => 'Net Profit / (Loss)',
                'amount' => $profitLoss,
                'level' => 1,
                'is_group' => false,
                'is_total' => false,
            ];
            $totalLiabilities += $profitLoss;
        }

        $rows[] = $this->totalRow('Total Liabilities & Equity', $totalLiabilities, 0);

        return $rows;
    }

    private function incomeStatementQuery(string $fromDate, string $toDate): array
    {
        $rows = [];
        $totalIncome = 0.0;
        $totalExpense = 0.0;

        $rows[] = $this->sectionRow('INCOME');

        $incomeRoots = ChartOfAccount::query()
            ->where('root_account_type', 'in')
            ->whereNull('parent_id')
            ->where('status', 'active')
            ->orderBy('id')
            ->get();

        foreach ($incomeRoots as $root) {
            [$sectionRows, $sectionTotal] = $this->buildHierarchyRows($root, null, $fromDate, $toDate, 'in', 0);
            $rows = array_merge($rows, $sectionRows);
            $totalIncome += $sectionTotal;
        }

        $rows[] = $this->totalRow('Total Income', $totalIncome, 0);

        $rows[] = $this->sectionRow('EXPENSES');

        $expenseRoots = ChartOfAccount::query()
            ->where('root_account_type', 'ex')
            ->whereNull('parent_id')
            ->where('status', 'active')
            ->orderBy('id')
            ->get();

        foreach ($expenseRoots as $root) {
            [$sectionRows, $sectionTotal] = $this->buildHierarchyRows($root, null, $fromDate, $toDate, 'ex', 0);
            $rows = array_merge($rows, $sectionRows);
            $totalExpense += $sectionTotal;
        }

        $rows[] = $this->totalRow('Total Expenses', $totalExpense, 0);
        $rows[] = $this->totalRow('Net Profit / (Loss)', $totalIncome - $totalExpense, 0);

        return $rows;
    }

    /**
     * Direct cash flow from bank/cash/payment-method ledgers (is_bank_cash = yes).
     * Internal fund transfers (FTV) are shown separately so they do not inflate receipts/payments.
     */
    private function cashFlowQuery(string $fromDate, string $toDate): array
    {
        $cashIds = ChartOfAccount::query()
            ->where('is_bank_cash', 'yes')
            ->where('type', 'ledger')
            ->where('status', 'active')
            ->where('root_account_type', 'as')
            ->pluck('id');

        if ($cashIds->isEmpty()) {
            return [
                $this->sectionRow('CASH FLOW'),
                [
                    'name' => 'No active cash/bank accounts found (is_bank_cash)',
                    'amount' => null,
                    'level' => 1,
                    'is_group' => false,
                    'is_total' => false,
                ],
            ];
        }

        $opening = $this->cashAccountsBalance($cashIds, null, $fromDate); // date < from
        $closing = $this->cashAccountsBalance($cashIds, $toDate, null); // date <= to

        $movements = DB::table('account_transactions')
            ->whereIn('chart_of_account_id', $cashIds)
            ->whereDate('date', '>=', $fromDate)
            ->whereDate('date', '<=', $toDate)
            ->selectRaw("
                COALESCE(NULLIF(doc_type, ''), 'Other') as doc_type,
                COALESCE(SUM(CASE WHEN type = 'debit' THEN amount ELSE 0 END), 0) as receipts,
                COALESCE(SUM(CASE WHEN type = 'credit' THEN amount ELSE 0 END), 0) as payments
            ")
            ->groupBy(DB::raw("COALESCE(NULLIF(doc_type, ''), 'Other')"))
            ->orderBy('doc_type')
            ->get();

        $rows = [];
        $rows[] = $this->sectionRow('OPENING');
        $rows[] = [
            'name' => 'Opening Cash & Bank',
            'amount' => $opening,
            'level' => 1,
            'is_group' => false,
            'is_total' => false,
        ];

        $totalReceipts = 0.0;
        $totalPayments = 0.0;
        $internalReceipts = 0.0;
        $internalPayments = 0.0;

        $receiptRows = [];
        $paymentRows = [];

        foreach ($movements as $move) {
            $docType = (string) $move->doc_type;
            $receipts = (float) $move->receipts;
            $payments = (float) $move->payments;
            $isInternal = in_array(strtoupper($docType), ['FTV'], true);

            if ($isInternal) {
                $internalReceipts += $receipts;
                $internalPayments += $payments;
                continue;
            }

            if ($receipts >= 0.00001) {
                $receiptRows[] = [
                    'name' => $this->docTypeLabel($docType),
                    'amount' => $receipts,
                    'level' => 1,
                    'is_group' => false,
                    'is_total' => false,
                ];
                $totalReceipts += $receipts;
            }

            if ($payments >= 0.00001) {
                $paymentRows[] = [
                    'name' => $this->docTypeLabel($docType),
                    'amount' => $payments,
                    'level' => 1,
                    'is_group' => false,
                    'is_total' => false,
                ];
                $totalPayments += $payments;
            }
        }

        $rows[] = $this->sectionRow('CASH RECEIPTS');
        $rows = array_merge($rows, $receiptRows);
        $rows[] = $this->totalRow('Total Cash Receipts', $totalReceipts, 0);

        $rows[] = $this->sectionRow('CASH PAYMENTS');
        $rows = array_merge($rows, $paymentRows);
        $rows[] = $this->totalRow('Total Cash Payments', $totalPayments, 0);

        $netChange = $totalReceipts - $totalPayments;
        $rows[] = $this->sectionRow('SUMMARY');
        $rows[] = $this->totalRow('Net Increase / (Decrease) in Cash', $netChange, 0);
        $rows[] = [
            'name' => 'Closing Cash & Bank',
            'amount' => $closing,
            'level' => 0,
            'is_group' => false,
            'is_total' => true,
        ];

        $computedClosing = $opening + $netChange;
        $rows[] = [
            'name' => 'Check: Opening + Net Change',
            'amount' => $computedClosing,
            'level' => 1,
            'is_group' => false,
            'is_total' => false,
        ];

        if (abs($internalReceipts) >= 0.00001 || abs($internalPayments) >= 0.00001) {
            $rows[] = $this->sectionRow('INTERNAL TRANSFERS (excluded from flows)');
            $rows[] = [
                'name' => 'Fund Transfer In (FTV)',
                'amount' => $internalReceipts,
                'level' => 1,
                'is_group' => false,
                'is_total' => false,
            ];
            $rows[] = [
                'name' => 'Fund Transfer Out (FTV)',
                'amount' => $internalPayments,
                'level' => 1,
                'is_group' => false,
                'is_total' => false,
            ];
        }

        return $rows;
    }

    private function cashAccountsBalance($cashIds, ?string $asOnDate, ?string $beforeDate): float
    {
        $query = DB::table('account_transactions')
            ->whereIn('chart_of_account_id', $cashIds);

        if ($beforeDate) {
            $query->whereDate('date', '<', $beforeDate);
        } elseif ($asOnDate) {
            $query->whereDate('date', '<=', $asOnDate);
        }

        return (float) $query
            ->selectRaw("COALESCE(SUM(CASE WHEN type = 'debit' THEN amount ELSE -amount END), 0) as balance")
            ->value('balance');
    }

    private function docTypeLabel(string $docType): string
    {
        $map = [
            'POS' => 'POS Sales',
            'PRE_ORDER' => 'Pre-Order',
            'PV' => 'Payment Voucher',
            'RV' => 'Receive Voucher',
            'CRV' => 'Cash Receive Voucher',
            'SPV' => 'Supplier Payment',
            'JV' => 'Journal Voucher',
            'FTV' => 'Fund Transfer',
            'DCT' => 'Delivery Cash Transfer',
            'FGIA' => 'FG Inventory Adjustment',
            'FGOB' => 'FG Opening Balance',
            'FGP' => 'FG Production',
            'GLOB' => 'GL Opening Balance',
            'GPB' => 'GL Posting',
            'RMIA' => 'RM Inventory Adjustment',
            'RMOB' => 'RM Opening Balance',
        ];

        $key = strtoupper($docType);

        return $map[$key] ?? $docType;
    }

    private function trialBalanceQuery(string $asOnDate): array
    {
        $accounts = ChartOfAccount::query()
            ->where('type', 'ledger')
            ->where('status', 'active')
            ->orderBy('root_account_type')
            ->orderBy('id')
            ->get(['id', 'name', 'root_account_type']);

        if ($accounts->isEmpty()) {
            return [];
        }

        $balances = DB::table('account_transactions')
            ->whereDate('date', '<=', $asOnDate)
            ->whereIn('chart_of_account_id', $accounts->pluck('id'))
            ->selectRaw("
                chart_of_account_id,
                COALESCE(SUM(CASE WHEN type = 'debit' THEN amount ELSE 0 END), 0) as debit_total,
                COALESCE(SUM(CASE WHEN type = 'credit' THEN amount ELSE 0 END), 0) as credit_total
            ")
            ->groupBy('chart_of_account_id')
            ->get()
            ->keyBy('chart_of_account_id');

        $rows = [];
        $totalDebit = 0.0;
        $totalCredit = 0.0;

        foreach ($accounts as $account) {
            $bal = $balances->get($account->id);
            $debit = (float) ($bal->debit_total ?? 0);
            $credit = (float) ($bal->credit_total ?? 0);

            if (abs($debit) < 0.00001 && abs($credit) < 0.00001) {
                continue;
            }

            $net = $debit - $credit;
            $showDebit = $net > 0 ? $net : 0.0;
            $showCredit = $net < 0 ? abs($net) : 0.0;

            $rows[] = [
                'name' => $account->name,
                'debit' => $showDebit,
                'credit' => $showCredit,
                'level' => 0,
                'is_group' => false,
                'is_total' => false,
            ];

            $totalDebit += $showDebit;
            $totalCredit += $showCredit;
        }

        $rows[] = [
            'name' => 'Total',
            'debit' => $totalDebit,
            'credit' => $totalCredit,
            'level' => 0,
            'is_group' => false,
            'is_total' => true,
        ];

        return $rows;
    }

    /**
     * Build indented hierarchy rows for PDF.
     * Groups show aggregated child totals; ledgers show own balance.
     *
     * @return array{0: array<int, array>, 1: float}
     */
    private function buildHierarchyRows(
        ChartOfAccount $account,
        ?string $asOnDate,
        ?string $fromDate,
        ?string $toDate,
        string $rootType,
        int $level
    ): array {
        $rows = [];
        $total = 0.0;

        if ($account->type === 'group') {
            $children = ChartOfAccount::query()
                ->where('parent_id', $account->id)
                ->where('status', 'active')
                ->orderBy('id')
                ->get();

            $childRows = [];
            foreach ($children as $child) {
                [$nestedRows, $childTotal] = $this->buildHierarchyRows($child, $asOnDate, $fromDate, $toDate, $rootType, $level + 1);
                $childRows = array_merge($childRows, $nestedRows);
                $total += $childTotal;
            }

            // Parent group first (bold), then children indented under it
            if (abs($total) >= 0.00001 || count($childRows) > 0) {
                $rows[] = [
                    'name' => $account->name,
                    'amount' => $total,
                    'level' => $level,
                    'is_group' => true,
                    'is_total' => false,
                ];
                $rows = array_merge($rows, $childRows);
            }

            return [$rows, $total];
        }

        $balance = $this->accountBalance($account->id, $asOnDate, $fromDate, $toDate, $rootType);
        if (abs($balance) < 0.00001) {
            return [[], 0.0];
        }

        $rows[] = [
            'name' => $account->name,
            'amount' => $balance,
            'level' => $level,
            'is_group' => false,
            'is_total' => false,
        ];

        return [$rows, $balance];
    }

    private function accountBalance(
        int $accountId,
        ?string $asOnDate,
        ?string $fromDate,
        ?string $toDate,
        string $rootType
    ): float {
        $query = DB::table('account_transactions')->where('chart_of_account_id', $accountId);

        if ($asOnDate) {
            $query->whereDate('date', '<=', $asOnDate);
        } else {
            $query->whereDate('date', '>=', $fromDate)
                ->whereDate('date', '<=', $toDate);
        }

        if (in_array($rootType, ['as', 'ex'], true)) {
            return (float) $query
                ->selectRaw("COALESCE(SUM(CASE WHEN type = 'debit' THEN amount ELSE -amount END), 0) as balance")
                ->value('balance');
        }

        return (float) $query
            ->selectRaw("COALESCE(SUM(CASE WHEN type = 'credit' THEN amount ELSE -amount END), 0) as balance")
            ->value('balance');
    }

    private function sectionRow(string $title): array
    {
        return [
            'name' => $title,
            'amount' => null,
            'level' => 0,
            'is_group' => true,
            'is_total' => false,
            'is_section' => true,
        ];
    }

    private function totalRow(string $title, float $amount, int $level): array
    {
        return [
            'name' => $title,
            'amount' => $amount,
            'level' => $level,
            'is_group' => false,
            'is_total' => true,
        ];
    }

    private function resolveRetainedEarningsAccountId(): ?int
    {
        $id = ChartOfAccount::query()
            ->where('name', 'Retained Earning')
            ->where('type', 'ledger')
            ->value('id');

        if ($id) {
            return (int) $id;
        }

        $fallback = ChartOfAccount::query()->where('id', 53)->value('id');

        return $fallback ? (int) $fallback : null;
    }
}
