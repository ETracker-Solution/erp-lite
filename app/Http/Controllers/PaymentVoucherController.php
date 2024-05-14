<?php

namespace App\Http\Controllers;

use App\Models\AccountTransaction;
use App\Models\PaymentVoucher;
use App\Http\Requests\StorePaymentVoucherRequest;
use App\Http\Requests\UpdatePaymentVoucherRequest;
use App\Models\ChartOfAccount;
use App\Models\Transaction;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;
use niklasravnsborg\LaravelPdf\Facades\Pdf;

class PaymentVoucherController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (\request()->ajax()) {
            $paymentVouchers = PaymentVoucher::with('debitAccount', 'cashBankAccount')->latest();
            return DataTables::of($paymentVouchers)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    return view('payment_voucher.action-button', compact('row'));
                })
                ->addColumn('created_at', function ($row) {
                    return view('common.created_at', compact('row'));
                })
                ->rawColumns(['action'])
                ->make(true);
        }
        return view('payment_voucher.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $creditAccounts = ChartOfAccount::where(['is_bank_cash' => 'yes', 'type' => 'ledger', 'status' => 'active'])->get();
        $debitAccounts = ChartOfAccount::where(['is_bank_cash' => 'no', 'type' => 'ledger', 'status' => 'active'])->get();
        $lastValue = PaymentVoucher::latest()->pluck('pv_no')->first();
        if ($lastValue !== null) {
            $PVno = (int)$lastValue + 1;
        } else {
            $PVno = 1; // Set the default value to 1
        }
        return view('payment_voucher.create', compact('debitAccounts', 'creditAccounts', 'PVno'));

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \App\Http\Requests\StorePaymentVoucherRequest $request
     * @return \Illuminate\Http\Response
     */
    public function store(StorePaymentVoucherRequest $request)
    {
        $validated = $request->validated();
        DB::beginTransaction();
        try {
            $voucher = PaymentVoucher::create($validated);
            // Accounts Effect
            addAccountsTransaction('PV', $voucher, $voucher->debit_account_id, $voucher->credit_account_id);
            DB::commit();
        } catch (\Exception $error) {
            DB::rollBack();
            Toastr::error('Something went wrong!.', '', ["progressBar" => true]);
            return back();
        }
        Toastr::success('Payment Voucher Created Successfully!.', '', ["progressBar" => true]);
        return redirect()->route('payment-vouchers.index');
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Models\PaymentVoucher $paymentVoucher
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $paymentVoucher = PaymentVoucher::findOrFail(decrypt($id));
        return view('payment_voucher.show', compact('paymentVoucher'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Models\PaymentVoucher $paymentVoucher
     * @return \Illuminate\Http\Response
     */
    public function edit(PaymentVoucher $paymentVoucher)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \App\Http\Requests\UpdatePaymentVoucherRequest $request
     * @param \App\Models\PaymentVoucher $paymentVoucher
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, PaymentVoucher $paymentVoucher)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Models\PaymentVoucher $paymentVoucher
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            PaymentVoucher::findOrFail(decrypt($id))->delete();
            AccountTransaction::where(['doc_type' => 'PV', 'doc_id' => decrypt($id)])->delete();
            DB::commit();
        } catch (\Exception $error) {
            DB::rollBack();
            Toastr::info('Something went wrong!.', '', ["progressBar" => true]);
            return back();
        }
        Toastr::success('Payment Voucher Deleted Successfully!.', '', ["progressBar" => true]);
        return redirect()->route('payment-vouchers.index');
    }

    public function paymentVoucherPdf($id)
    {
        $paymentVoucher = PaymentVoucher::findOrFail(decrypt($id));
        $data = [
            'paymentVoucher' => $paymentVoucher,
        ];

        $pdf = PDF::loadView(
            'payment_voucher.pdf',
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
