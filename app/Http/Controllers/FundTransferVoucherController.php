<?php

namespace App\Http\Controllers;

use App\Models\AccountTransaction;
use App\Models\FundTransferVoucher;
use App\Http\Requests\StoreFundTransferVoucherRequest;
use App\Http\Requests\UpdateFundTransferVoucherRequest;
use App\Models\ChartOfAccount;
use App\Models\OthersOutletSale;
use App\Models\Outlet;
use App\Models\OutletAccount;
use App\Models\OutletTransactionConfig;
use App\Models\Sale;
use App\Models\Transaction;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Termwind\Components\Raw;
use Yajra\DataTables\Facades\DataTables;
use niklasravnsborg\LaravelPdf\Facades\Pdf;

class FundTransferVoucherController extends Controller
{
    public function index()
    {
        $outlet_accounts = [];

        if (\auth()->user() && \auth()->user()->employee && \auth()->user()->employee->outlet_id) {
            $outlet_id = auth()->user()->employee->outlet_id;

            $oas = OutletAccount::with('coa')
                ->where('outlet_id', $outlet_id)
                ->get();

            $coaIds = $oas->pluck('coa_id')->toArray();

            $originalBalances = AccountTransaction::select('chart_of_account_id', DB::raw('SUM(amount * transaction_type) as balance'))
                ->whereIn('chart_of_account_id', $coaIds)
                ->groupBy('chart_of_account_id')
                ->pluck('balance', 'chart_of_account_id');

            // Bulk query: pending fund transfers per coa_id
            $pendingAmounts = FundTransferVoucher::select('credit_account_id', DB::raw('SUM(amount) as pending'))
                ->whereIn('credit_account_id', $coaIds)
                ->where('status', 'pending')
                ->groupBy('credit_account_id')
                ->pluck('pending', 'credit_account_id');

            $otherOutletSalesBalances = [];
            foreach ($coaIds as $coaId) {
                $otherOutletSalesBalances[$coaId] = accountBalanceForOtherOutletSales($coaId);
            }

            // Build result
            $outlet_accounts = $oas->map(function ($row) use ($originalBalances, $pendingAmounts, $otherOutletSalesBalances) {
                $coa_id = $row->coa_id;

                $original_balance = $originalBalances[$coa_id] ?? 0;
                $pending = $pendingAmounts[$coa_id] ?? 0;
                $other_outlet = $otherOutletSalesBalances[$coa_id] ?? 0;

                return [
                    'name' => $row->coa->name ?? 'N/A',
                    'balance' => $original_balance - $other_outlet,
                    'other_outlet_balance' => $other_outlet,
                    'pending' => $pending,
                ];
            })->toArray();

        }

//        foreach ($oas as $key => $row) {
//            $original_account_balance = AccountTransaction::where('chart_of_account_id', $row->coa_id)->sum(\DB::raw('amount * transaction_type'));
//            $other_outlet_sales_balance = accountBalanceForOtherOutletSales($row->coa_id);
//            $outlet_accounts[$key]['name'] = $row->coa->name;
//            $outlet_accounts[$key]['balance'] = $original_account_balance - $other_outlet_sales_balance;
//            $outlet_accounts[$key]['other_outlet_balance'] = $other_outlet_sales_balance;
//        }

        $outlets = Outlet::all();
        $accounts = ChartOfAccount::where('type', 'ledger')
        ->whereIn('id', function ($query) {
            $query->select('coa_id')->from('outlet_accounts');
        })
        ->select('id', 'name')
        ->get();

        $toAccounts = ChartOfAccount::where(['default_type' => 'office_account', 'is_bank_cash' => 'yes', 'type' => 'ledger', 'status' => 'active'])->get();
        if (\request()->ajax()) {
            $fundTransferVouchers = $this->getFilteredData();
            return DataTables::of($fundTransferVouchers)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    return view('fund_transfer_voucher.action-button', compact('row'));
                })
                ->addColumn('created_at', function ($row) {
                    return view('common.created_at', compact('row'));
                })
                ->editColumn('status', function ($row) {
                    return showStatus($row->status);
                })
                ->rawColumns(['action', 'created_at', 'status'])
                ->make(true);
        }
        return view('fund_transfer_voucher.index', compact('outlets', 'outlet_accounts','accounts','toAccounts'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (\auth()->user() && \auth()->user()->employee && \auth()->user()->employee->outlet_id) {

            //    $cons = OutletTransactionConfig::with('coa')->where('outlet_id', \auth()->user()->employee->outlet_id)->get();
            //     foreach ($cons as $con) {
            //         $chartOfAccounts[] = $con->coa;
            //     }
            $chartOfAccounts = OutletAccount::with(['coa'])->whereHas('coa', function ($coa) {
                return $coa->whereNull('default_type');
            })->where('outlet_id', \auth()->user()->employee->outlet_id)->get();
            $toChartOfAccounts = ChartOfAccount::where(['default_type' => 'office_account', 'is_bank_cash' => 'yes', 'type' => 'ledger', 'status' => 'active'])->get();

        } else {
            $chartOfAccounts = ChartOfAccount::where(['is_bank_cash' => 'yes', 'type' => 'ledger', 'status' => 'active'])->get();
            $toChartOfAccounts = ChartOfAccount::where(['is_bank_cash' => 'yes', 'type' => 'ledger', 'status' => 'active'])->orWhere(['default_type' => 'petty_cash', 'type' => 'ledger', 'status' => 'active'])->get();

        }

        $lastValue = FundTransferVoucher::latest()->pluck('uid')->first();
        if ($lastValue !== null) {
            $FTVno = (int)$lastValue + 1;
        } else {
            $FTVno = 1; // Set the default value to 1
        }
        return view('fund_transfer_voucher.create', compact('chartOfAccounts', 'FTVno', 'toChartOfAccounts'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \App\Http\Requests\StoreFundTransferVoucherRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(StoreFundTransferVoucherRequest $request)
    {
        $validated = $request->validated();
        DB::beginTransaction();
        try {
            if (count($validated['products']) < 1) {
                Toastr::info('At Least One Product Required.', '', ["progressBar" => true]);
                return back();
            }

            foreach ($validated['products'] as $product) {
                $creditAccount = ChartOfAccount::find($product['credit_account_id']);
                $pendingAmount = FundTransferVoucher::where(['credit_account_id' => $product['credit_account_id'], 'status' => 'pending'])->sum('amount');
                $currentBalance = AccountTransaction::where('chart_of_account_id', $product['credit_account_id'])->sum(\DB::raw('amount * transaction_type'));
                $other_outlet_sales_balance = accountBalanceForOtherOutletSales($product['credit_account_id']);
                $actualBalance = $currentBalance - $pendingAmount - $other_outlet_sales_balance;
                if (max($actualBalance, 0) < $product['amount']) {
                    Toastr::warning("No Available Balance in " . $creditAccount->name);
                    return back();
                }
                $product['date'] = $validated['date'];
                $product['narration'] = $validated['narration'];
                $product['created_by'] = $validated['created_by'];
                FundTransferVoucher::create($product);
            }
            DB::commit();
        } catch (\Exception $error) {
            DB::rollBack();
            return $error;
            Toastr::info('Something went wrong!.', '', ["progressBar" => true]);
            return back();
        }
        Toastr::success('Fund Transfer Voucher Created Successfully!.', '', ["progressBar" => true]);
        return redirect()->route('fund-transfer-vouchers.index');
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Models\FundTransferVoucher $fundTransferVoucher
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $fundTransferVoucher = FundTransferVoucher::findOrFail(decrypt($id));
        return view('fund_transfer_voucher.show', compact('fundTransferVoucher'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Models\FundTransferVoucher $fundTransferVoucher
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if (\auth()->user() && \auth()->user()->employee && \auth()->user()->employee->outlet_id) {

            $cons = OutletTransactionConfig::with('coa')->where('outlet_id', \auth()->user()->employee->outlet_id)->get();
            foreach ($cons as $con) {
                $chartOfAccounts[] = $con->coa;
            }

        } else {
            $chartOfAccounts = ChartOfAccount::where(['is_bank_cash' => 'yes', 'type' => 'ledger', 'status' => 'active'])->get();

        }
        $toChartOfAccounts = ChartOfAccount::where(['is_bank_cash' => 'yes', 'type' => 'ledger', 'status' => 'active'])->get();
        $fundTransferVoucher = FundTransferVoucher::findOrFail(decrypt($id));
        return view('fund_transfer_voucher.edit', compact('fundTransferVoucher', 'chartOfAccounts', 'toChartOfAccounts'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \App\Http\Requests\UpdateFundTransferVoucherRequest $request
     * @param \App\Models\FundTransferVoucher $fundTransferVoucher
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateFundTransferVoucherRequest $request, FundTransferVoucher $fundTransferVoucher)
    {
        $validated = $request->validated();
        DB::beginTransaction();
        try {
            $creditAccount = ChartOfAccount::find($fundTransferVoucher->credit_account_id);
            $pendingAmount = FundTransferVoucher::where('id', '!=', $fundTransferVoucher->id)->where(['credit_account_id' => $fundTransferVoucher->credit_account_id, 'status' => 'pending'])->sum('amount');
            $currentBalance = AccountTransaction::where('chart_of_account_id', $fundTransferVoucher->credit_account_id)->sum(\DB::raw('amount * transaction_type'));
            $actualBalance = $currentBalance - $pendingAmount;
            if (max($actualBalance, 0) < $validated['amount']) {
                Toastr::warning("No Available Balance in " . $creditAccount->name);
                return back();
            }
            $fundTransferVoucher->update($validated);
            // Accounts Effect
            AccountTransaction::where('doc_type', 'FTV')->where('doc_id', $fundTransferVoucher->id)->delete();
//            addAccountsTransaction('FTV', $fundTransferVoucher, $fundTransferVoucher->debit_account_id, $fundTransferVoucher->credit_account_id);
            DB::commit();
        } catch (\Exception $error) {
            DB::rollBack();
            return $error;
            Toastr::info('Something went wrong!.', '', ["progressBar" => true]);
            return back();
        }
        Toastr::success('Fund Transfer Voucher Update Successfully!.', '', ["progressBar" => true]);
        return redirect()->route('fund-transfer-vouchers.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Models\FundTransferVoucher $fundTransferVoucher
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            FundTransferVoucher::findOrFail(decrypt($id))->delete();
            AccountTransaction::where('doc_type', 'FTV')->where('doc_id', decrypt($id))->delete();
            DB::commit();
        } catch (\Exception $error) {
            DB::rollBack();
            Toastr::info('Something went wrong!.', '', ["progressBar" => true]);
            return back();
        }
        Toastr::success('Fund Transfer Voucher Deleted Successfully!.', '', ["progressBar" => true]);
        return redirect()->route('fund-transfer-vouchers.index');
    }

    public function receive($id)
    {
        DB::beginTransaction();
        try {
            $voucher = FundTransferVoucher::findOrFail(decrypt($id));
            $voucher->status = "received";
            $voucher->save();

            if (!AccountTransaction::where('doc_type', 'FTV')->where('doc_id', $voucher->id)->exists()) {
                addAccountsTransaction('FTV', $voucher, $voucher->debit_account_id, $voucher->credit_account_id);

            }
            DB::commit();
        } catch (\Exception $error) {
            DB::rollBack();
            Toastr::info('Something went wrong!.', '', ["progressBar" => true]);
            return back();
        }
        Toastr::success('Fund Transfer Voucher Received Successful!.', '', ["progressBar" => true]);
        return redirect()->route('fund-transfer-vouchers.index');
    }

    public function Pdf($id)
    {
        $fundTransferVoucher = FundTransferVoucher::findOrFail(decrypt($id));
        $data = [
            'fundTransferVoucher' => $fundTransferVoucher,
        ];

        $pdf = PDF::loadView(
            'fund_transfer_voucher.pdf',
            $data,
            [],
            [
                'format' => 'A4-P',
                'orientation' => 'P',
                'margin-left' => 1,

                '', // mode - default ''
                '', // format - A4, for example, default ''
                0, // font size - default 0
                '', // default font family
                1, // margin_left
                1, // margin right
                1, // margin top
                1, // margin bottom
                1, // margin header
                1, // margin footer
                'L', // L - landscape, P - portrait

            ]
        );
        $name = \Carbon\Carbon::now()->format('d-m-Y');

        return $pdf->stream($name . '.pdf');
    }

    public function getFilteredData()
    {
        if (\auth()->user() && \auth()->user()->employee && \auth()->user()->employee->outlet_id) {
            $fundTransferVoucher = FundTransferVoucher::where('created_by', \auth()->user()->id)->with('creditAccount', 'debitAccount')->latest();

        } else {
            $fundTransferVoucher = FundTransferVoucher::with('creditAccount', 'debitAccount')->latest();
        }
        // if (\request()->filled('status')) {
        //     $fundTransferVoucher->where('status', \request()->status);
        // }
        if (\request()->filled('outlet_id')) {
            $fundTransferVoucher->whereHas('creditAccount', function ($q) {
                $q->whereHas('outlets', function ($query) {
                    $query->where('outlet_id', \request()->outlet_id);
                });
            });
        }
        if (\request()->filled('account_id')) {
            $fundTransferVoucher->where('credit_account_id',\request()->account_id);
        }
        if (\request()->filled('to_account_id')) {
            $fundTransferVoucher->where('debit_account_id',\request()->to_account_id);
        }
        if (\request()->filled('date_range')) {
            searchColumnByDateRange($fundTransferVoucher,'date');
        }
        return $fundTransferVoucher->latest();
    }

    public function receiveReport(Request $request)
    {
        $data = FundTransferVoucher::query()->with('creditAccount.outlets', 'debitAccount', 'createdBy.employee.outlet')
            ->whereHas('creditAccount', function($query) {
                $query->whereHas('outlets');
            })
            ->where('status', 'received');
        if (\request()->filled('date_range') && $request->date_range != null) {
            $data = searchColumnByDateRange($data,'date');
        }
        if (\request()->filled('outlet_id')) {
            $data->whereHas('creditAccount', function ($q) {
                $q->whereHas('outlets', function ($query) {
                    $query->where('outlet_id', \request()->outlet_id);
                });
            });
        }

        if (\request()->filled('account_id')) {
            $data->where('credit_account_id',\request()->account_id);
        }

        if (\request()->filled('to_account_id')) {
            $data->where('debit_account_id',\request()->to_account_id);
        }
        if (\request()->filled('date_range')) {
            searchColumnByDateRange($data,'date');
        }

        $totalAmount = $data->sum('amount');

        $passVariable = [
            'transactions' => $data->get()->sortBy(function ($transaction) {
                if (isset($transaction->creditAccount->outlets[0])){
                    return $transaction->creditAccount->outlets[0]->id;
                }
            }),
            'totalAmount' => $totalAmount,
        ];

        $pdf = PDF::loadView(
            'fund_transfer_voucher.report-pdf',
            $passVariable,
            [],
            [
                'format' => 'A4-P',
                'orientation' => 'P',
                'margin-left' => 1,

                '', // mode - default ''
                '', // format - A4, for example, default ''
                0, // font size - default 0
                '', // default font family
                1, // margin_left
                1, // margin right
                1, // margin top
                1, // margin bottom
                1, // margin header
                1, // margin footer
                'L', // L - landscape, P - portrait

            ]
        );
        $name = 'fund_transfer_receive_report_' . \Carbon\Carbon::now()->format('ymdHis');

        return $pdf->stream($name . '.pdf');
    }
}
