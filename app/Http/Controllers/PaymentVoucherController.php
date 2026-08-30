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
use Mccarlosen\LaravelMpdf\Facades\LaravelMpdf as Pdf;

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
        $creditAccounts = ChartOfAccount::query()
            ->where(['is_bank_cash' => 'yes', 'type' => 'ledger', 'status' => 'active'])
            ->orderBy('name')
            ->get(['id', 'name']);
        $debitAccounts = ChartOfAccount::query()
            ->where(['is_bank_cash' => 'no', 'type' => 'ledger', 'status' => 'active'])
            ->orderBy('name')
            ->get(['id', 'name']);
        $PVno = PaymentVoucher::nextUid();

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
        $paymentVoucher = PaymentVoucher::with(['debitAccount', 'cashBankAccount'])
            ->findOrFail(decrypt($id));

        return view('payment_voucher.show', compact('paymentVoucher'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Models\PaymentVoucher $paymentVoucher
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $creditAccounts = ChartOfAccount::query()
            ->where(['is_bank_cash' => 'yes', 'type' => 'ledger', 'status' => 'active'])
            ->orderBy('name')
            ->get(['id', 'name']);
        $debitAccounts = ChartOfAccount::query()
            ->where(['is_bank_cash' => 'no', 'type' => 'ledger', 'status' => 'active'])
            ->orderBy('name')
            ->get(['id', 'name']);
        $paymentVoucher = PaymentVoucher::findOrFail(decrypt($id));

        return view('payment_voucher.edit', compact('paymentVoucher', 'creditAccounts', 'debitAccounts'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \App\Http\Requests\UpdatePaymentVoucherRequest $request
     * @param \App\Models\PaymentVoucher $paymentVoucher
     * @return \Illuminate\Http\Response
     */
    public function update(UpdatePaymentVoucherRequest $request, PaymentVoucher $paymentVoucher)
    {
        $validated = $request->validated();
        DB::beginTransaction();
        try {
            $paymentVoucher->update($validated);
            // Accounts Effect
            AccountTransaction::where('doc_type', 'PV')->where('doc_id', $paymentVoucher->id)->delete();
            addAccountsTransaction('PV', $paymentVoucher, $validated['debit_account_id'], $validated['credit_account_id']);
            DB::commit();
        } catch (\Exception $error) {
            DB::rollBack();
            Toastr::info('Something went wrong!.', '', ["progressBar" => true]);
            return back();
        }
        Toastr::success('Payment Voucher Update Successfully!.', '', ["progressBar" => true]);
        return redirect()->route('payment-vouchers.index');
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
            $voucherId = decrypt($id);
            PaymentVoucher::findOrFail($voucherId)->delete();
            AccountTransaction::where(['doc_type' => 'PV', 'doc_id' => $voucherId])->delete();
            DB::commit();
        } catch (\Exception $error) {
            DB::rollBack();
            Toastr::info('Something went wrong!.', '', ["progressBar" => true]);
            return back();
        }
        Toastr::success('Payment Voucher Deleted Successfully!.', '', ["progressBar" => true]);
        return redirect()->route('payment-vouchers.index');
    }

    public function Pdf($id)
    {
        $paymentVoucher = PaymentVoucher::with(['debitAccount', 'cashBankAccount'])
            ->findOrFail(decrypt($id));

        $pdf = PDF::loadView(
            'payment_voucher.pdf',
            compact('paymentVoucher'),
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

        return $pdf->stream('PV-' . ($paymentVoucher->uid ?: $paymentVoucher->id) . '.pdf');
    }
}
