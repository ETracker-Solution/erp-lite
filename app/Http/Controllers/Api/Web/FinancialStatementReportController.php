<?php

namespace App\Http\Controllers\Api\Web;

use App\Http\Controllers\Controller;
use App\Models\ChartOfAccount;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use niklasravnsborg\LaravelPdf\Facades\Pdf;

class FinancialStatementReportController extends Controller
{
    public function index()
    {
        return view('financial_statement.index');
    }

    public function create()
    {
        $report_type = \request()->report_type;


        $from_date = Carbon::parse(\request()->from_date)->format('Y-m-d') ?? Carbon::now()->format('Y-m-d');
        $to_date = Carbon::parse(\request()->to_date)->format('Y-m-d') ?? Carbon::now()->format('Y-m-d');

        $asOnDate = Carbon::parse(\request()->as_on_date)->format('Y-m-d') ?? Carbon::now()->format('Y-m-d');

        $page_title = false;
        $report_header = 'Balance Sheet';

        $getData = DB::select($this->balanceSheetQuery($asOnDate));
        $columns = array_keys((array)$getData[0]);

        $data = [
            'dateRange' => 'as On  ' . $asOnDate,
            'data' => $getData,
            'page_title' => $page_title,
            'columns' => $columns,
            'report_header' => $report_header
        ];
//        return view('common.report_main', $data);
        $pdf = Pdf::loadView('common.report_main', $data);
        $pdf->stream();
    }

    public function balanceSheetQuery($as_on_date)
    {
        return "
        WITH RECURSIVE account_hierarchy AS (
    SELECT
        id,
        parent_id,
        name,
        root_account_type,
        0 AS level,
        CAST(name AS CHAR(200)) AS path
    FROM
        chart_of_accounts
    WHERE
        parent_id IS NULL
        AND root_account_type IN ('as', 'li')

    UNION ALL

    SELECT
        c.id,
        c.parent_id,
        c.name,
        c.root_account_type,
        ah.level + 1,
        CONCAT(ah.path, '/', c.name)
    FROM
        chart_of_accounts c
    INNER JOIN
        account_hierarchy ah ON c.parent_id = ah.id
),

net_profit AS (
    SELECT
        32 AS id,
coalesce((select SUM(att.transaction_type * att.amount) * -1 as profit
from account_transactions att
join chart_of_accounts COA
on COA.id = att.chart_of_account_id
AND COA.root_account_type in ('in','ex')
AND att.date <= '$as_on_date'),0)
 AS amount
),

cumulative_balance_cte AS (
    SELECT
        ah.id,
        COALESCE(SUM(att.amount * (CASE  WHEN (att.transaction_type = -1 AND ah.root_account_type = 'li') THEN (att.transaction_type*-1) ELSE att.transaction_type END)), 0) +
            COALESCE(SUM(np.amount), 0) AS balance
    FROM
        account_hierarchy ah
    LEFT JOIN
        account_transactions att ON ah.id = att.chart_of_account_id
        AND  att.date <= '$as_on_date'
    LEFT JOIN
        net_profit np ON ah.id = np.id
    GROUP BY
        ah.id
), finalData as(
SELECT
    CONCAT(REPEAT(SPACE(8), ah.level), ah.name) AS account_name,
    COALESCE(cb.balance, 0) AS balance,
    (
        SELECT
           CONCAT(REPEAT(SPACE(8), ah.level),  CAST(COALESCE(SUM(cb2.balance),0) as CHAR))
        FROM
            cumulative_balance_cte cb2
        JOIN
            account_hierarchy ah2 ON cb2.id = ah2.id
        WHERE
            ah2.path LIKE CONCAT(ah.path, '%')
    ) AS cumulative_balance, ah.path
FROM
    account_hierarchy ah
LEFT JOIN
    cumulative_balance_cte cb ON ah.id = cb.id
ORDER BY
    ah.path, ah.id
)
select account_name as 'Account Head', COALESCE(cumulative_balance ,0) as Amount from finalData
ORDER BY
    finalData.path
        ";
    }
}
