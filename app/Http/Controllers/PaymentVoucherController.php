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
                ->select([
                    DB::raw('MAX(id) as id'),
                    'uid',
                    DB::raw('MAX(date) as date'),
                    DB::raw('SUM(amount) as amount'),
                    DB::raw('MAX(created_at) as created_at')
                ])->groupBy('uid');
            $paymentVouchers = $this->filter($paymentVouchers, request());
            return DataTables::of($paymentVouchers->latest())
                ->addIndexColumn()
                ->addColumn('debit_account.name', function ($row) {
                    return \App\Models\PaymentVoucher::where('uid', $row->uid)
                        ->join('chart_of_accounts', 'chart_of_accounts.id', '=', 'payment_vouchers.debit_account_id')
                        ->pluck('chart_of_accounts.name')->unique()->implode(', ');
                })
                ->addColumn('cash_bank_account.name', function ($row) {
                    return \App\Models\PaymentVoucher::where('uid', $row->uid)
                        ->join('chart_of_accounts', 'chart_of_accounts.id', '=', 'payment_vouchers.credit_account_id')
                        ->pluck('chart_of_accounts.name')->unique()->implode(', ');
                })
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

            $uid = generateUniqueCode(PaymentVoucher::class, 'uid');
            foreach ($validated['products'] as $product) {
                $product['date'] = $validated['date'];
                $product['narration'] = $validated['narration'];
                $product['uid'] = $uid;

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
        $voucher = PaymentVoucher::findOrFail(decrypt($id));
        $paymentVouchers = PaymentVoucher::where('uid', $voucher->uid)->get();
        return view('payment_voucher.show', compact('paymentVouchers', 'voucher'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Models\PaymentVoucher $paymentVoucher
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $voucher = PaymentVoucher::findOrFail(decrypt($id));
        $paymentVouchers = PaymentVoucher::with('debitAccount', 'cashBankAccount')->where('uid', $voucher->uid)->get();
        $chartOfAccounts = ChartOfAccount::where(['is_bank_cash' => 'yes', 'type' => 'ledger', 'status' => 'active'])->get();
        $toChartOfAccounts = ChartOfAccount::where(['is_bank_cash' => 'no', 'type' => 'ledger', 'status' => 'active'])->get();
        $PVno = $voucher->uid;
        $date = $voucher->date;
        $narration = $voucher->narration;
        return view('payment_voucher.edit', compact('paymentVouchers', 'voucher', 'chartOfAccounts', 'toChartOfAccounts', 'PVno', 'date', 'narration'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \App\Http\Requests\UpdatePaymentVoucherRequest $request
     * @param \App\Models\PaymentVoucher $paymentVoucher
     * @return \Illuminate\Http\Response
     */
    public function update(StorePaymentVoucherRequest $request, $id)
    {
        $validated = $request->validated();
        DB::beginTransaction();
        try {
            if (!isset($validated['products']) || count($validated['products']) < 1) {
                Toastr::info('At Least One Item Required.', '', ["progressBar" => true]);
                return back();
            }

            $voucher = PaymentVoucher::findOrFail($id);
            $uid = $voucher->uid;

            $oldVouchers = PaymentVoucher::where('uid', $uid)->get();
            foreach ($oldVouchers as $oldVoucher) {
                AccountTransaction::where('doc_type', 'PV')->where('doc_id', $oldVoucher->id)->delete();
                $oldVoucher->delete();
            }

            foreach ($validated['products'] as $product) {
                $product['date'] = $validated['date'];
                $product['narration'] = $validated['narration'];
                $product['uid'] = $uid;

                $newVoucher = PaymentVoucher::create($product);
                // Accounts Effect
                addAccountsTransaction('PV', $newVoucher, $newVoucher->debit_account_id, $newVoucher->credit_account_id);
            }
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
            $voucher = PaymentVoucher::findOrFail(decrypt($id));
            $oldVouchers = PaymentVoucher::where('uid', $voucher->uid)->get();
            foreach ($oldVouchers as $oldVoucher) {
                AccountTransaction::where(['doc_type' => 'PV', 'doc_id' => $oldVoucher->id])->delete();
                $oldVoucher->delete();
            }
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
        $voucher = PaymentVoucher::findOrFail(decrypt($id));
        $paymentVouchers = PaymentVoucher::where('uid', $voucher->uid)->get();

        $data = [
            'voucher' => $voucher,
            'paymentVouchers' => $paymentVouchers,
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
