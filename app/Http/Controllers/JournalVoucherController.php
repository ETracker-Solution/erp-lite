<?php

namespace App\Http\Controllers;

use App\Models\AccountTransaction;
use App\Models\JournalVoucher;
use App\Http\Requests\StoreJournalVoucherRequest;
use App\Http\Requests\UpdateJournalVoucherRequest;
use App\Models\ChartOfAccount;
use App\Models\Transaction;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;
use niklasravnsborg\LaravelPdf\Facades\Pdf;

class JournalVoucherController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (\request()->ajax()) {
            $journalVouchers = JournalVoucher::with('debitAccount', 'creditAccount')->latest();
            return DataTables::of($journalVouchers)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    return view('journal_voucher.action-button', compact('row'));
                })
                ->addColumn('created_at', function ($row) {
                    return view('common.created_at', compact('row'));
                })
                ->rawColumns(['action'])
                ->make(true);
        }
        return view('journal_voucher.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {

        $chartOfAccounts = ChartOfAccount::where(['type' => 'ledger', 'status' => 'active'])->get();
        $lastValue = JournalVoucher::latest()->pluck('jv_no')->first();
        if ($lastValue !== null) {
            $JVno = (int)$lastValue + 1;
        } else {
            $JVno = 1; // Set the default value to 1
        }
        return view('journal_voucher.create', compact('chartOfAccounts','JVno'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \App\Http\Requests\StoreJournalVoucherRequest $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreJournalVoucherRequest $request)
    {
        $validated = $request->validated();
        DB::beginTransaction();
        try {
            $voucher = JournalVoucher::create($validated);
            // Accounts Effect
            addAccountsTransaction('JV', $voucher, $voucher->debit_account_id, $voucher->credit_account_id);
            DB::commit();
        } catch (\Exception $error) {
            DB::rollBack();
            Toastr::info('Something went wrong!.', '', ["progressBar" => true]);
            return back();
        }
        Toastr::success('Journal Voucher Created Successfully!.', '', ["progressBar" => true]);
        return redirect()->route('journal-vouchers.index');
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Models\JournalVoucher $journalVoucher
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $journalVoucher = JournalVoucher::findOrFail(decrypt($id));
        return view('journal_voucher.show', compact('journalVoucher'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Models\JournalVoucher $journalVoucher
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \App\Http\Requests\UpdateJournalVoucherRequest $request
     * @param \App\Models\JournalVoucher $journalVoucher
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Models\JournalVoucher $journalVoucher
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            JournalVoucher::findOrFail(decrypt($id))->delete();
            AccountTransaction::where('doc_type','JV')->where('doc_id',decrypt($id))->delete();
            DB::commit();
        } catch (\Exception $error) {
            DB::rollBack();
            Toastr::info('Something went wrong!.', '', ["progressBar" => true]);
            return back();
        }
        Toastr::success('Journal Voucher Deleted Successfully!.', '', ["progressBar" => true]);
        return redirect()->route('journal-vouchers.index');
    }

    public function journalVoucherPdf($id)
    {
        $journalVoucher = JournalVoucher::findOrFail(decrypt($id));

            $data = [
                'journalVoucher' => $journalVoucher,
            ];

        $pdf = PDF::loadView(
            'journal_voucher.pdf',
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
