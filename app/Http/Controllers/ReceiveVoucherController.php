<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\ChartOfAccount;
use App\Models\ReceiveVoucher;
use Illuminate\Support\Facades\DB;
use Brian2694\Toastr\Facades\Toastr;
use Yajra\DataTables\Facades\DataTables;
use App\Http\Requests\StoreReceiveVoucherRequest;
use App\Http\Requests\UpdateReceiveVoucherRequest;
use App\Models\AccountTransaction;
use Illuminate\Http\Request;
use niklasravnsborg\LaravelPdf\Facades\Pdf;

class ReceiveVoucherController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (\request()->ajax()) {
            $journalVouchers = ReceiveVoucher::with('debitAccount', 'creditAccount')->latest();
            return DataTables::of($journalVouchers)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    return view('receive_voucher.action-button', compact('row'));
                })
                ->addColumn('created_at', function ($row) {
                    return view('common.created_at', compact('row'));
                })
                ->rawColumns(['action'])
                ->make(true);
        }
        return view('receive_voucher.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $debitAccounts = ChartOfAccount::where(['is_bank_cash' => 'yes', 'type' => 'ledger', 'status' => 'active'])->get();
        $creditAccounts = ChartOfAccount::where(['is_bank_cash' => 'no', 'type' => 'ledger', 'status' => 'active'])->get();
        $lastValue = ReceiveVoucher::latest()->pluck('uid')->first();
        if ($lastValue !== null) {
            $RVno = (int)$lastValue + 1;
        } else {
            $RVno = 1; // Set the default value to 1
        }
        return view('receive_voucher.create', compact('debitAccounts', 'creditAccounts', 'RVno'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \App\Http\Requests\StoreReceiveVoucherRequest $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreReceiveVoucherRequest $request)
    {
        $validated = $request->validated();
        DB::beginTransaction();
        try {
            $voucher = ReceiveVoucher::create($validated);
            // Accounts Effect
            addAccountsTransaction('RV', $voucher, $voucher->debit_account_id, $voucher->credit_account_id);
            DB::commit();
        } catch (\Exception $error) {
            DB::rollBack();
            Toastr::info('Something went wrong!.', '', ["progressBar" => true]);
            return back();
        }
        Toastr::success('Receive Voucher Created Successfully!.', '', ["progressBar" => true]);
        return redirect()->route('receive-vouchers.index');
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Models\ReceiveVoucher $receiveVoucher
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $receiveVoucher = ReceiveVoucher::findOrFail(decrypt($id));
        return view('receive_voucher.show', compact('receiveVoucher'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Models\ReceiveVoucher $receiveVoucher
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $debitAccounts = ChartOfAccount::where(['is_bank_cash' => 'yes', 'type' => 'ledger', 'status' => 'active'])->get();
        $creditAccounts = ChartOfAccount::where(['is_bank_cash' => 'no', 'type' => 'ledger', 'status' => 'active'])->get();
        $receiveVoucher = ReceiveVoucher::findOrFail(decrypt($id));
        return view('receive_voucher.edit', compact('receiveVoucher', 'debitAccounts', 'creditAccounts'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \App\Http\Requests\UpdateReceiveVoucherRequest $request
     * @param \App\Models\ReceiveVoucher $receiveVoucher
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateReceiveVoucherRequest $request, ReceiveVoucher $receiveVoucher)
    {
        $validated = $request->validated();
        DB::beginTransaction();
        try {
            $receiveVoucher->update($validated);
            // Accounts Effect
            AccountTransaction::where('doc_type', 'RV')->where('doc_id', $receiveVoucher->id)->delete();
            addAccountsTransaction('RV', $receiveVoucher, $validated['debit_account_id'], $validated['credit_account_id']);
            DB::commit();
        } catch (\Exception $error) {
            DB::rollBack();
            Toastr::info('Something went wrong!.', '', ["progressBar" => true]);
            return back();
        }
        Toastr::success('Receive Voucher Update Successfully!.', '', ["progressBar" => true]);
        return redirect()->route('receive-vouchers.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Models\ReceiveVoucher $receiveVoucher
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            ReceiveVoucher::findOrFail(decrypt($id))->delete();
            AccountTransaction::where('doc_type', 'RV')->where('doc_id', decrypt($id))->delete();

            DB::commit();
        } catch (\Exception $error) {
            DB::rollBack();
            Toastr::info('Something went wrong!.', '', ["progressBar" => true]);
            return back();
        }
        Toastr::success('Receive Voucher Deleted Successfully!.', '', ["progressBar" => true]);
        return redirect()->route('receive-vouchers.index');
    }

    public function receiveVoucherPdf($id)
    {
        $receiveVoucher = ReceiveVoucher::findOrFail(decrypt($id));
        $data = [
            'receiveVoucher' => $receiveVoucher,
        ];

        $pdf = PDF::loadView(
            'receive_voucher.pdf',
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
