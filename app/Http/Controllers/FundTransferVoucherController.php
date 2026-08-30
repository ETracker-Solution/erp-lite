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
use Mccarlosen\LaravelMpdf\Facades\LaravelMpdf as Pdf;

class FundTransferVoucherController extends Controller
{
    public function index()
    {
        // DataTables AJAX hits this same route — skip expensive page bootstrap work.
        if (request()->ajax()) {
            $onlyData = [];
            if (auth()->user()?->employee?->outlet_id) {
                $onlyData = OutletAccount::where('outlet_id', auth()->user()->employee->outlet_id)
                    ->pluck('coa_id')
                    ->toArray();
            }

            $fundTransferVouchers = $this->getFilteredData($onlyData);

            return DataTables::eloquent($fundTransferVouchers)
                ->addIndexColumn()
                ->editColumn('uid', function ($row) {
                    return ($row->uid !== null && $row->uid !== '') ? $row->uid : $row->id;
                })
                ->editColumn('status', function ($row) {
                    $class = $row->status === 'received' ? 'success' : 'warning';

                    return '<span class="badge badge-' . $class . '">' . ucfirst($row->status) . '</span>';
                })
                ->addColumn('action', function ($row) {
                    return view('fund_transfer_voucher.action-button', compact('row'))->render();
                })
                ->rawColumns(['action', 'status'])
                ->make(true);
        }

        $outlet_accounts = [];
        if (auth()->user()?->employee?->outlet_id) {
            $outlet_id = auth()->user()->employee->outlet_id;

            $oas = OutletAccount::with('coa:id,name')
                ->where('outlet_id', $outlet_id)
                ->get();

            $coaIds = $oas->pluck('coa_id')->toArray();

            $originalBalances = empty($coaIds)
                ? collect()
                : AccountTransaction::select('chart_of_account_id', DB::raw('SUM(amount * transaction_type) as balance'))
                    ->whereIn('chart_of_account_id', $coaIds)
                    ->groupBy('chart_of_account_id')
                    ->pluck('balance', 'chart_of_account_id');

            $pendingAmounts = empty($coaIds)
                ? collect()
                : FundTransferVoucher::select('credit_account_id', DB::raw('SUM(amount) as pending'))
                    ->whereIn('credit_account_id', $coaIds)
                    ->where('status', 'pending')
                    ->groupBy('credit_account_id')
                    ->pluck('pending', 'credit_account_id');

            $otherOutletSalesBalances = accountBalancesForOtherOutletSales($coaIds);

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

        $outlets = collect();
        $accounts = collect();
        $toAccounts = collect();

        // Filter dropdowns are only used by HO users with accounts-ft-voucher-filter permission.
        if (!auth()->user()?->employee?->outlet_id) {
            $outlets = Outlet::query()->select(['id', 'name'])->orderBy('name')->get();
            $accounts = ChartOfAccount::query()
                ->where('type', 'ledger')
                ->whereIn('id', function ($query) {
                    $query->select('coa_id')->from('outlet_accounts');
                })
                ->select('id', 'name')
                ->orderBy('name')
                ->get();

            $toAccounts = officeAccountQuery()
                ->select('id', 'name')
                ->orderBy('name')
                ->get();
        }

        return view('fund_transfer_voucher.index', compact('outlets', 'outlet_accounts', 'accounts', 'toAccounts'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $officeAccountsMissing = false;

        if (auth()->user()?->employee?->outlet_id) {
            $fromAccounts = OutletAccount::query()
                ->with(['coa:id,name,default_type'])
                ->whereHas('coa', function ($coa) {
                    transferableOutletCoaConstraint($coa);
                })
                ->where('outlet_id', auth()->user()->employee->outlet_id)
                ->where('status', 'active')
                ->get(['id', 'outlet_id', 'coa_id'])
                ->filter(fn ($row) => $row->coa)
                ->map(fn ($row) => ['id' => $row->coa->id, 'name' => $row->coa->name])
                ->values();

            $toAccounts = officeAccountQuery()
                ->select('id', 'name')
                ->orderBy('name')
                ->get()
                ->map(fn ($row) => ['id' => $row->id, 'name' => $row->name])
                ->values();
            $officeAccountsMissing = $toAccounts->isEmpty();
        } else {
            $fromAccounts = ChartOfAccount::query()
                ->where([
                    'is_bank_cash' => 'yes',
                    'type' => 'ledger',
                    'status' => 'active',
                ])
                ->select('id', 'name')
                ->orderBy('name')
                ->get()
                ->map(fn ($row) => ['id' => $row->id, 'name' => $row->name])
                ->values();

            $toAccounts = ChartOfAccount::query()
                ->where('type', 'ledger')
                ->where('status', 'active')
                ->where(function ($q) {
                    $q->where('is_bank_cash', 'yes')
                        ->orWhere('default_type', 'petty_cash');
                })
                ->select('id', 'name')
                ->orderBy('name')
                ->get()
                ->map(fn ($row) => ['id' => $row->id, 'name' => $row->name])
                ->values();
        }

        return view('fund_transfer_voucher.create', compact('fromAccounts', 'toAccounts', 'officeAccountsMissing'));
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
                
                // Extra safety: Check if an identical voucher was recently created (within last 10 seconds)
                $duplicateExists = FundTransferVoucher::where([
                    'credit_account_id' => $product['credit_account_id'],
                    'debit_account_id' => $product['debit_account_id'],
                    'amount' => $product['amount'],
                    'created_by' => $validated['created_by'],
                ])
                ->where('created_at', '>=', now()->subSeconds(10))
                ->exists();

                if ($duplicateExists) {
                    Toastr::warning('Duplicate fund transfer voucher detected. Please wait.', '', ["progressBar" => true]);
                    DB::rollBack();
                    return back();
                }

                $pendingAmount = FundTransferVoucher::where(['credit_account_id' => $product['credit_account_id'], 'status' => 'pending'])->sum('amount');
                $currentBalance = AccountTransaction::where('chart_of_account_id', $product['credit_account_id'])->sum(\DB::raw('amount * transaction_type'));
                $other_outlet_sales_balance = accountBalanceForOtherOutletSales($product['credit_account_id']);
                $actualBalance = $currentBalance - $pendingAmount - $other_outlet_sales_balance;
                if (max($actualBalance, 0) < $product['amount']) {
                    Toastr::warning("No Available Balance in " . $creditAccount->name);
                    DB::rollBack();
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
            \Illuminate\Support\Facades\Log::error('Error storing Fund Transfer Voucher: ' . $error->getMessage());
            Toastr::error('Something went wrong! Please try again.', '', ["progressBar" => true]);
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
        $fundTransferVoucher = FundTransferVoucher::query()
            ->with([
                'creditAccount:id,name',
                'debitAccount:id,name',
                'createdBy:id,name',
            ])
            ->findOrFail(decrypt($id));

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
            $voucherId = decrypt($id);
            FundTransferVoucher::findOrFail($voucherId)->delete();
            AccountTransaction::where('doc_type', 'FTV')->where('doc_id', $voucherId)->delete();
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
        $fundTransferVoucher = FundTransferVoucher::query()
            ->with(['creditAccount:id,name', 'debitAccount:id,name'])
            ->findOrFail(decrypt($id));

        $pdf = PDF::loadView(
            'fund_transfer_voucher.pdf',
            compact('fundTransferVoucher'),
            [],
            [
                'format' => 'A4-P',
                'orientation' => 'P',
                'margin_left' => 1,
                'margin_right' => 1,
                'margin_top' => 1,
                'margin_bottom' => 1,
            ]
        );

        return $pdf->stream('FTV-' . ($fundTransferVoucher->uid ?: $fundTransferVoucher->id) . '.pdf');
    }

    public function getFilteredData($coas = null)
    {
        $fundTransferVoucher = FundTransferVoucher::query()
            ->select([
                'id', 'uid', 'date', 'amount', 'status', 'narration',
                'credit_account_id', 'debit_account_id', 'created_by', 'created_at',
            ])
            ->with([
                'creditAccount:id,name',
                'debitAccount:id,name',
            ]);

        // Outlet users only see vouchers touching their outlet COAs.
        // Admin / HO (no outlet_id) sees all vouchers.
        if (auth()->user()?->employee?->outlet_id) {
            $coas = is_array($coas) ? $coas : [];
            if (empty($coas)) {
                $fundTransferVoucher->whereRaw('1 = 0');
            } else {
                $fundTransferVoucher->where(function ($q) use ($coas) {
                    $q->whereIn('credit_account_id', $coas)
                        ->orWhereIn('debit_account_id', $coas);
                });
            }
        }

        if (request()->filled('outlet_id')) {
            $outletCoaIds = OutletAccount::query()
                ->where('outlet_id', request()->outlet_id)
                ->pluck('coa_id');
            $fundTransferVoucher->whereIn('credit_account_id', $outletCoaIds);
        }
        if (request()->filled('account_id')) {
            $fundTransferVoucher->where('credit_account_id', request()->account_id);
        }
        if (request()->filled('to_account_id')) {
            $fundTransferVoucher->where('debit_account_id', request()->to_account_id);
        }

        if (request()->filled('date_range')) {
            // Do not clampReportDateRange here — 366-day clamp was hiding older FTV rows for admin.
            searchColumnByDateRange($fundTransferVoucher, 'date');
        } else {
            $fundTransferVoucher->whereBetween('date', [
                now()->subMonths(36)->toDateString(),
                now()->toDateString(),
            ]);
        }

        return $fundTransferVoucher->latest('id');
    }

    private function applyVoucherDateRange($query, string $column = 'date')
    {
        if (request()->filled('date_range')) {
            // Reports can use a wider window than the shared 366-day clamp.
            [$from, $to] = getDatesArrayFromDateRange(request('date_range'));
            [$from, $to] = clampReportDateRange(sanitizeReportDate($from), sanitizeReportDate($to), 1100);
        } else {
            $from = now()->subMonths(36)->toDateString();
            $to = now()->toDateString();
        }

        return $query->whereBetween($column, [$from, $to]);
    }

    public function receiveReport(Request $request)
    {
        $outletCoaIds = OutletAccount::query()
            ->when($request->filled('outlet_id'), function ($q) use ($request) {
                $q->where('outlet_id', $request->outlet_id);
            })
            ->pluck('coa_id');

        $officeAccountIds = officeAccountQuery()->pluck('id');

        $data = FundTransferVoucher::query()
            ->select([
                'id', 'uid', 'date', 'amount', 'status',
                'credit_account_id', 'debit_account_id',
            ])
            ->with([
                'creditAccount:id,name',
                'creditAccount.outlets' => function ($q) {
                    $q->select('outlets.id', 'outlets.name');
                },
                'debitAccount:id,name',
            ])
            ->where('status', 'received')
            ->whereIn('credit_account_id', $outletCoaIds)
            ->whereIn('debit_account_id', $officeAccountIds);

        $this->applyVoucherDateRange($data);

        if ($request->filled('account_id')) {
            $data->where('credit_account_id', $request->account_id);
        }
        if ($request->filled('to_account_id')) {
            $data->where('debit_account_id', $request->to_account_id);
        }

        $totalAmount = (clone $data)->sum('amount');

        $passVariable = [
            'transactions' => $data->get()->sortBy(function ($transaction) {
                return $transaction->creditAccount->outlets[0]->id ?? 0;
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
