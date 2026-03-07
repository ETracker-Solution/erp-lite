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
        if (request()->ajax()) {
            $journalVouchers = JournalVoucher::query()
                ->with([
                    'debitAccount:id,name',
                    'creditAccount:id,name'
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
            $journalVouchers = $this->filter($journalVouchers, request());
            return DataTables::of($journalVouchers->latest())
                ->addIndexColumn()
                ->addColumn('action', fn($row) => view('journal_voucher.action-button', compact('row')))
                ->addColumn('created_at', fn($row) => view('common.created_at', compact('row')))
                ->rawColumns(['action'])
                ->make(true);
        }

        $chartOfAccounts = ChartOfAccount::select('id', 'name')
            ->where(['type' => 'ledger', 'status' => 'active'])
            ->get();

        return view('journal_voucher.index', compact('chartOfAccounts'));
    }

    private function filter($query, $request)
    {
        return $query
            ->when($request->date_range, function ($q) use ($request) {
                searchColumnByDateRange($q, 'date', $request->date_range);
            })
            ->when($request->uid, fn($q) => $q->where('uid', 'like', "%{$request->uid}%"))
            ->when($request->debit_account_id, fn($q) => $q->where('debit_account_id', $request->debit_account_id))
            ->when($request->credit_account_id, fn($q) => $q->where('credit_account_id', $request->credit_account_id));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {

        $chartOfAccounts = ChartOfAccount::where(['type' => 'ledger', 'status' => 'active'])->get();
        $JVno = generateUniqueCode(JournalVoucher::class,'uid');
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
        $chartOfAccounts = ChartOfAccount::where(['type' => 'ledger', 'status' => 'active'])->get();
        $journalVoucher = JournalVoucher::findOrFail(decrypt($id));
        return view('journal_voucher.edit', compact('journalVoucher','chartOfAccounts'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \App\Http\Requests\UpdateJournalVoucherRequest $request
     * @param \App\Models\JournalVoucher $journalVoucher
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateJournalVoucherRequest $request, JournalVoucher $journalVoucher)
    {
        $validated = $request->validated();
        DB::beginTransaction();
        try {
            $journalVoucher->update($validated);
            // Accounts Effect
            AccountTransaction::where('doc_type','JV')->where('doc_id',$journalVoucher->id)->delete();
            addAccountsTransaction('JV', $journalVoucher, $validated['debit_account_id'], $validated['credit_account_id']);
            DB::commit();
        } catch (\Exception $error) {
            DB::rollBack();
            Toastr::info('Something went wrong!.', '', ["progressBar" => true]);
            return back();
        }
        Toastr::success('Journal Voucher Update Successfully!.', '', ["progressBar" => true]);
        return redirect()->route('journal-vouchers.index');
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

    public function Pdf($id)
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
