<?php

namespace App\Http\Controllers;

use App\Models\AccountTransaction;
use App\Models\FundTransferVoucher;
use App\Http\Requests\StoreFundTransferVoucherRequest;
use App\Http\Requests\UpdateFundTransferVoucherRequest;
use App\Models\ChartOfAccount;
use App\Models\OutletTransactionConfig;
use App\Models\Transaction;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;
use niklasravnsborg\LaravelPdf\Facades\Pdf;

class FundTransferVoucherController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (\request()->ajax()) {
            if (\auth()->user() && \auth()->user()->employee && \auth()->user()->employee->outlet_id) {
                $fundTransferVouchers = FundTransferVoucher::where('created_by', \auth()->user()->id)->with('creditAccount', 'debitAccount')->latest();

            } else {
                $fundTransferVouchers = FundTransferVoucher::with('creditAccount', 'debitAccount')->latest();
            }
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
        return view('fund_transfer_voucher.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
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
     * @return \Illuminate\Http\Response
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
                $product['date'] = $validated['date'];
                $product['narration'] = $validated['narration'];
                $product['created_by'] = $validated['created_by'];
                FundTransferVoucher::create($product);
            }
            DB::commit();
        } catch (\Exception $error) {
            DB::rollBack();
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
            $fundTransferVoucher->update($validated);
            // Accounts Effect
            AccountTransaction::where('doc_type', 'FTV')->where('doc_id', $fundTransferVoucher->id)->delete();
            addAccountsTransaction('FTV', $fundTransferVoucher, $validated['debit_account_id'], $validated['credit_account_id']);
            DB::commit();
        } catch (\Exception $error) {
            DB::rollBack();
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

            // Accounts Effect
            addAccountsTransaction('FTV', $voucher, $voucher->debit_account_id, $voucher->credit_account_id);
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
}
