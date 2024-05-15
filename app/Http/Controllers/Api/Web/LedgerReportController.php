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
        $to_date = Carbon::parse(\request()->to_date)->format('Y-m-d') ?? Carbon::now()->format('Y-m-d');

        $page_title = false;
        $report_header = 'General Ledger Account Report';

        $page_title = 'Ledger Account Head  ::     ' . ChartOfAccount::find(\request()->account_id)->name;
        $getPost=$this->ledgerReportQuery(\request()->account_id, $from_date, $to_date);
        $columns = array_keys((array)$getPost[0]);


        $data = [
            'dateRange' => ' For the Period ' . $from_date . ' to ' . $to_date,
            'data' =>  $getPost,
            'page_title' => $page_title,
            'columns' => $columns,
            'report_header' => $report_header
        ];
        $pdf = Pdf::loadView(
            'common.ledger_report_view', $data,
            [],
            [
                'format' => 'A4-L',
                'orientation' => 'L',
                'margin-left' => 0,

                '', // mode - default ''
                '', // format - A4, for example, default ''
                0, // font size - default 0
                '', // default font family
                0, // margin_left
                1, // margin right
                1, // margin top
                1, // margin bottom
                0, // margin header
                1, // margin footer
                'L', // L - landscape, P - portrait

            ]
        );
//        return $pdf->stream();
        $pdf->stream();
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

    public function ledgerReportQuery($account_id, $start_date, $end_date)
    {
        $account = ChartOfAccount::find($account_id);
        if ($account->parent_account_type == 'as' || $account->parent_account_type == 'li') {
            return $result = DB::select("WITH OpeningBalance AS (
    SELECT
        '$start_date' AS DATE,
        NULL AS VOUCHER,
        '-' AS ACCOUNT,
         '-' AS PARENT_ACCOUNT_TYPE,
        'Opening Balance' AS DESCRIPTION,
        '-' AS DEBIT,
        '-' AS CREDIT,
        ifnull(SUM(CASE WHEN TR.type = 'debit' THEN TR.amount ELSE -TR.amount END),0) AS Balance,
        NULL AS chart_of_account_id,
        NULL AS TRAccount
    FROM account_transactions TR
    WHERE TR.chart_of_account_id = $account_id
    AND TR.date < '$start_date'
    LIMIT 1
)

SELECT * FROM OpeningBalance

UNION ALL

SELECT
    CASE WHEN TR.date < '$start_date' THEN '$start_date' ELSE TR.date END AS DATE,
     CASE
     WHEN RV.rv_no IS NULL AND JV.jv_no IS NULL AND FTV.ftv_no IS NULL THEN concat('PV-',PV.pv_no)
     WHEN RV.rv_no IS NULL AND PV.pv_no IS NULL AND FTV.ftv_no IS NULL THEN concat('JV-',JV.jv_no)
     WHEN RV.rv_no IS NULL AND PV.pv_no IS NULL AND JV.jv_no IS NULL THEN concat('FTV-',FTV.ftv_no)
     ELSE concat('RV-',RV.rv_no)
     END AS VOUCHER,
    CASE WHEN TR.date < '$start_date' THEN '-' ELSE COA.name END AS ACCOUNT,
    COAM.parent_account_type AS PARENT_ACCOUNT_TYPE,
    CASE WHEN TR.date < '$start_date' THEN 'Opening Balance' ELSE TR.narration END AS DESCRIPTION,
    CASE WHEN TR.date < '$start_date' THEN '-' ELSE CASE WHEN TR.type = 'debit' THEN TR.amount ELSE 0 END END AS DEBIT,
    CASE WHEN TR.date < '$start_date' THEN '-' ELSE CASE WHEN TR.type = 'credit' THEN TR.amount ELSE 0 END END AS CREDIT,
    SUM(CASE WHEN TR.type = 'debit' THEN TR.amount ELSE -TR.amount END) OVER (ORDER BY TR.date, TR.id) + (SELECT Balance FROM OpeningBalance) AS Balance,
    chart_of_account_id,
    CASE
    WHEN RV.rv_no IS NULL AND JV.jv_no IS NULL AND FTV.ftv_no IS NULL THEN PV.debit_account_id
    WHEN RV.rv_no IS NULL AND PV.pv_no IS NULL AND FTV.ftv_no IS NULL THEN JV.debit_account_id
    WHEN RV.rv_no IS NULL AND PV.pv_no IS NULL AND JV.jv_no IS NULL THEN FTV.debit_account_id

    ELSE RV.credit_account_id
    END AS TRAccount
FROM account_transactions TR
LEFT JOIN receive_vouchers RV ON (RV.id = TR.doc_id AND TR.doc_type = 'RV')
LEFT JOIN payment_vouchers PV ON PV.id = TR.doc_id AND TR.doc_type = 'PV'
LEFT JOIN journal_vouchers JV ON JV.id = TR.doc_id AND TR.doc_type = 'JV'
LEFT JOIN fund_transfer_vouchers FTV ON FTV.id = TR.doc_id AND TR.doc_type = 'FTV'
LEFT JOIN chart_of_accounts COA ON COA.id = (
CASE
WHEN RV.rv_no IS NULL AND JV.jv_no IS NULL AND FTV.ftv_no IS NULL THEN (CASE WHEN TR.type = 'debit' THEN PV.credit_account_id ELSE PV.debit_account_id END)
WHEN RV.rv_no IS NULL AND PV.pv_no IS NULL AND FTV.ftv_no IS NULL THEN (CASE WHEN TR.type = 'debit' THEN JV.credit_account_id ELSE JV.debit_account_id END)
WHEN RV.rv_no IS NULL AND PV.pv_no IS NULL AND JV.jv_no IS NULL THEN (CASE WHEN TR.type = 'debit' THEN FTV.credit_account_id ELSE FTV.debit_account_id END)
ELSE (CASE WHEN TR.type = 'credit' THEN RV.debit_account_id ELSE RV.credit_account_id END)
END
)
LEFT JOIN chart_of_accounts COAM ON COAM.id=TR.chart_of_account_id
WHERE TR.chart_of_account_id = $account_id
AND TR.date >= '$start_date' AND TR.date <= '$end_date'
");
        } else {
            $q = "SELECT
    CASE WHEN TR.date < '$start_date' THEN '$start_date' ELSE TR.date END AS DATE,
     CASE WHEN RV.rv_no IS NULL THEN concat('PV-',PV.pv_no) ELSE concat('RV-',RV.rv_no) END AS VOUCHER,
    CASE WHEN TR.date < '$start_date' THEN '-' ELSE COA.name END AS ACCOUNT,
    COAM.parent_account_type AS PARENT_ACCOUNT_TYPE,
    CASE WHEN TR.date < '$start_date' THEN 'Opening Balance' ELSE TR.narration END AS DESCRIPTION,
    CASE WHEN TR.date < '$start_date' THEN '-' ELSE CASE WHEN TR.type = 'debit' THEN TR.amount ELSE 0 END END AS DEBIT,
    CASE WHEN TR.date < '$start_date' THEN '-' ELSE CASE WHEN TR.type = 'credit' THEN TR.amount ELSE 0 END END AS CREDIT,
    SUM(CASE WHEN TR.type = 'debit' THEN TR.amount ELSE -TR.amount END) OVER (ORDER BY TR.date, TR.id) AS Balance,
    chart_of_account_id,
    CASE WHEN RV.rv_no IS NULL THEN PV.debit_account_id ELSE RV.credit_account_id END AS TRAccount
FROM account_transactions TR
LEFT JOIN receive_vouchers RV ON (RV.id = TR.doc_id AND TR.doc_type = 'RV')
LEFT JOIN payment_vouchers PV ON PV.id = TR.doc_id AND TR.doc_type = 'PV'
LEFT JOIN chart_of_accounts COA ON COA.id = (CASE WHEN RV.rv_no IS NULL THEN (CASE WHEN TR.type = 'debit' THEN PV.credit_account_id ELSE PV.debit_account_id END) ELSE (CASE WHEN TR.type = 'credit' THEN RV.debit_account_id ELSE RV.credit_account_id END) END)
                                                                                  LEFT JOIN chart_of_accounts COAM ON COAM.id=TR.chart_of_account_id
WHERE TR.chart_of_account_id = $account_id
AND TR.date >= '$start_date' AND TR.date <= '$end_date'
";
            return $result = DB::select($q);
        }
    }
}
