<?php

namespace App\Http\Controllers;

use App\Models\ChartOfAccount;
use App\Models\ReceiveVoucher;
use Illuminate\Support\Facades\DB;
use Brian2694\Toastr\Facades\Toastr;
use Yajra\DataTables\Facades\DataTables;
use App\Http\Requests\StoreReceiveVoucherRequest;
use App\Http\Requests\UpdateReceiveVoucherRequest;
use App\Models\AccountTransaction;
use Mccarlosen\LaravelMpdf\Facades\LaravelMpdf as Pdf;

class ReceiveVoucherController extends Controller
{
    public function index()
    {
        if (request()->ajax()) {
            $vouchers = ReceiveVoucher::with('debitAccount', 'creditAccount')->latest();

            return DataTables::eloquent($vouchers)
                ->addIndexColumn()
                ->editColumn('amount', fn ($row) => number_format((float) $row->amount, 2))
                ->addColumn('action', function ($row) {
                    return view('receive_voucher.action-button', compact('row'));
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('receive_voucher.index');
    }

    public function create()
    {
        $debitAccounts = ChartOfAccount::query()
            ->where(['is_bank_cash' => 'yes', 'type' => 'ledger', 'status' => 'active'])
            ->orderBy('name')
            ->get(['id', 'name']);

        $creditAccounts = ChartOfAccount::query()
            ->where(['is_bank_cash' => 'no', 'type' => 'ledger', 'status' => 'active'])
            ->orderBy('name')
            ->get(['id', 'name']);

        $RVno = ReceiveVoucher::nextUid();

        return view('receive_voucher.create', compact('debitAccounts', 'creditAccounts', 'RVno'));
    }

    public function store(StoreReceiveVoucherRequest $request)
    {
        $validated = $request->validated();
        $validated['created_by'] = auth()->id();

        DB::beginTransaction();
        try {
            $voucher = ReceiveVoucher::create($validated);
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

    public function show($id)
    {
        $receiveVoucher = ReceiveVoucher::with(['debitAccount', 'creditAccount', 'createdBy'])
            ->findOrFail(decrypt($id));

        return view('receive_voucher.show', compact('receiveVoucher'));
    }

    public function edit($id)
    {
        $debitAccounts = ChartOfAccount::query()
            ->where(['is_bank_cash' => 'yes', 'type' => 'ledger', 'status' => 'active'])
            ->orderBy('name')
            ->get(['id', 'name']);

        $creditAccounts = ChartOfAccount::query()
            ->where(['is_bank_cash' => 'no', 'type' => 'ledger', 'status' => 'active'])
            ->orderBy('name')
            ->get(['id', 'name']);

        $receiveVoucher = ReceiveVoucher::findOrFail(decrypt($id));

        return view('receive_voucher.edit', compact('receiveVoucher', 'debitAccounts', 'creditAccounts'));
    }

    public function update(UpdateReceiveVoucherRequest $request, ReceiveVoucher $receiveVoucher)
    {
        $validated = $request->validated();

        DB::beginTransaction();
        try {
            $receiveVoucher->update($validated);
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

    public function destroy($id)
    {
        $voucherId = decrypt($id);

        DB::beginTransaction();
        try {
            ReceiveVoucher::findOrFail($voucherId)->delete();
            AccountTransaction::where('doc_type', 'RV')->where('doc_id', $voucherId)->delete();
            DB::commit();
        } catch (\Exception $error) {
            DB::rollBack();
            Toastr::info('Something went wrong!.', '', ["progressBar" => true]);
            return back();
        }

        Toastr::success('Receive Voucher Deleted Successfully!.', '', ["progressBar" => true]);
        return redirect()->route('receive-vouchers.index');
    }

    public function Pdf($id)
    {
        $receiveVoucher = ReceiveVoucher::with(['debitAccount', 'creditAccount'])
            ->findOrFail(decrypt($id));

        $pdf = PDF::loadView(
            'receive_voucher.pdf',
            compact('receiveVoucher'),
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

        return $pdf->stream('RV-' . ($receiveVoucher->uid ?: $receiveVoucher->id) . '.pdf');
    }
}
