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
        if (request()->ajax()) {
            $paymentVouchers = PaymentVoucher::query()
                ->with([
                    'debitAccount:id,name',
                    'cashBankAccount:id,name'
                ])
                ->select([
                    'id',
                    'uid',
                    'date',
                    'debit_account_id',
                    'credit_account_id',
                    'amount',
                    'created_at'
                ]);
            $paymentVouchers = $this->filter($paymentVouchers, request());
            return DataTables::of($paymentVouchers->latest())
                ->addIndexColumn()
                ->addColumn('action', fn($row) => view('payment_voucher.action-button', compact('row')))
                ->addColumn('created_at', fn($row) => view('common.created_at', compact('row')))
                ->rawColumns(['action'])
                ->make(true);
        }
        $creditAccounts = ChartOfAccount::select('id','name')
            ->where([
                'is_bank_cash' => 'yes',
                'type' => 'ledger',
                'status' => 'active'
            ])->get();
        $debitAccounts = ChartOfAccount::select('id','name')
            ->where([
                'is_bank_cash' => 'no',
                'type' => 'ledger',
                'status' => 'active'
            ])->get();
        return view('payment_voucher.index', compact('creditAccounts','debitAccounts'));
    }
    private function filter($query, $request)
    {
        return $query
            ->when($request->date_range, function ($q) use ($request) {
                searchColumnByDateRange($q, 'date', $request->date_range);
            })
            ->when($request->uid, fn($q) =>
            $q->where('uid', 'like', "%{$request->uid}%")
            )
            ->when($request->debit_account_id, fn($q) =>
            $q->where('debit_account_id', $request->debit_account_id)
            )
            ->when($request->credit_account_id, fn($q) =>
            $q->where('credit_account_id', $request->credit_account_id)
            );
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $chartOfAccounts = ChartOfAccount::where(['is_bank_cash' => 'yes', 'type' => 'ledger', 'status' => 'active'])->get();
        $toChartOfAccounts = ChartOfAccount::where(['is_bank_cash' => 'no', 'type' => 'ledger', 'status' => 'active'])->get();
        $PVno = generateUniqueCode(PaymentVoucher::class,'uid');
        return view('payment_voucher.create', compact('toChartOfAccounts', 'chartOfAccounts', 'PVno'));
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
            if (!isset($validated['products']) || count($validated['products']) < 1) {
                Toastr::info('At Least One Item Required.', '', ["progressBar" => true]);
                return back();
            }

            foreach ($validated['products'] as $product) {
                $product['date'] = $validated['date'];
                $product['narration'] = $validated['narration'];
                $product['uid'] = generateUniqueCode(PaymentVoucher::class, 'uid');

                $voucher = PaymentVoucher::create($product);
                // Accounts Effect
                addAccountsTransaction('PV', $voucher, $voucher->debit_account_id, $voucher->credit_account_id);
            }
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
    public function edit($id)
    {
        $creditAccounts = ChartOfAccount::where(['is_bank_cash' => 'yes', 'type' => 'ledger', 'status' => 'active'])->get();
        $debitAccounts = ChartOfAccount::where(['is_bank_cash' => 'no', 'type' => 'ledger', 'status' => 'active'])->get();
        $paymentVoucher = PaymentVoucher::findOrFail(decrypt($id));
        return view('payment_voucher.edit', compact('paymentVoucher','creditAccounts','debitAccounts'));
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

    public function Pdf($id)
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
                'mode'           => 'utf-8',
                'format'         => 'A4',
                'orientation'    => 'P',  // Portrait
                'margin_top'     => 5,
                'margin_right'   => 5,
                'margin_bottom'  => 5,
                'margin_left'    => 5,
                'default_font'   => 'sans-serif'

            ]
        );
        $name = \Carbon\Carbon::now()->format('d-m-Y');

        return $pdf->stream($name . '.pdf');
    }
}
