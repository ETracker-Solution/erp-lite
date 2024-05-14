<?php

namespace App\Http\Controllers;

use App\Models\AccountTransaction;
use App\Models\FundTransferVoucher;
use App\Http\Requests\StoreFundTransferVoucherRequest;
use App\Http\Requests\UpdateFundTransferVoucherRequest;
use App\Models\ChartOfAccount;
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
            $fundTransferVouchers = FundTransferVoucher::with('creditAccount', 'debitAccount')->latest();
            return DataTables::of($fundTransferVouchers)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    return view('fund_transfer_voucher.action-button', compact('row'));
                })
                ->addColumn('created_at', function ($row) {
                    return view('common.created_at', compact('row'));
                })
                ->rawColumns(['action'])
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
        $chartOfAccounts = ChartOfAccount::where(['is_bank_cash' => 'yes', 'type' => 'ledger', 'status' => 'active'])->get();
        $lastValue = FundTransferVoucher::latest()->pluck('ftv_no')->first();
        if ($lastValue !== null) {
            $FTVno = (int)$lastValue + 1;
        } else {
            $FTVno = 1; // Set the default value to 1
        }
        return view('fund_transfer_voucher.create', compact('chartOfAccounts', 'FTVno'));
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
            $voucher = FundTransferVoucher::create($validated);
            // Accounts Effect
            addAccountsTransaction('FTV', $voucher, $voucher->debit_account_id, $voucher->credit_account_id);
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
    public function edit(FundTransferVoucher $fundTransferVoucher)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \App\Http\Requests\UpdateFundTransferVoucherRequest $request
     * @param \App\Models\FundTransferVoucher $fundTransferVoucher
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, FundTransferVoucher $fundTransferVoucher)
    {
        //
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

    public function fundTransferVoucherPdf($id)
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
