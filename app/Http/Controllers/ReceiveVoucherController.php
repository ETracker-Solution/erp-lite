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
        if (request()->ajax()) {
            $receiveVouchers = ReceiveVoucher::query()
                ->with([
                    'debitAccount:id,name',
                    'creditAccount:id,name'
                ])
                ->select([
                    DB::raw('MAX(id) as id'),
                    'uid',
                    DB::raw('MAX(date) as date'),
                    DB::raw('MAX(debit_account_id) as debit_account_id'),
                    DB::raw('MAX(credit_account_id) as credit_account_id'),
                    DB::raw('SUM(amount) as amount'),
                    DB::raw('MAX(payee_name) as payee_name'),
                    DB::raw('MAX(created_at) as created_at')
                ])->groupBy('uid');
            $receiveVouchers = $this->filter($receiveVouchers, request());
            return DataTables::of($receiveVouchers->latest())
                ->addIndexColumn()
                ->addColumn('action', fn($row) => view('receive_voucher.action-button', compact('row')))
                ->addColumn('created_at', fn($row) => view('common.created_at', compact('row')))
                ->rawColumns(['action'])
                ->make(true);
        }

        $creditAccounts = ChartOfAccount::select('id', 'name')
            ->where(['is_bank_cash' => 'no', 'type' => 'ledger', 'status' => 'active'])
            ->get();
        $debitAccounts = ChartOfAccount::select('id', 'name')
            ->where(['is_bank_cash' => 'yes', 'type' => 'ledger', 'status' => 'active'])
            ->get();

        return view('receive_voucher.index', compact('creditAccounts', 'debitAccounts'));
    }

    private function filter($query, $request)
    {
        return $query
            ->when($request->date_range, function ($q) use ($request) {
                searchColumnByDateRange($q, 'date', $request->date_range);
            })
            ->when($request->uid, fn($q) => $q->where('uid', 'like', "%{$request->uid}%"))
            ->when($request->credit_account_id, fn($q) => $q->where('credit_account_id', $request->credit_account_id))
            ->when($request->debit_account_id, fn($q) => $q->where('debit_account_id', $request->debit_account_id))
            ->when($request->payee_name, fn($q) => $q->where('payee_name', 'like', "%{$request->payee_name}%"));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        // Credit (Source/Income) - Not Bank/Cash
        $chartOfAccounts = ChartOfAccount::where(['is_bank_cash' => 'no', 'type' => 'ledger', 'status' => 'active'])->get();
        // Debit (Destination/Bank/Cash) - Is Bank/Cash
        $toChartOfAccounts = ChartOfAccount::where(['is_bank_cash' => 'yes', 'type' => 'ledger', 'status' => 'active'])->get();
        $RVno = generateUniqueCode(ReceiveVoucher::class,'uid');
        return view('receive_voucher.create', compact('toChartOfAccounts', 'chartOfAccounts', 'RVno'));
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
            if (!isset($validated['products']) || count($validated['products']) < 1) {
                Toastr::info('At Least One Item Required.', '', ["progressBar" => true]);
                return back();
            }

            $uid = generateUniqueCode(ReceiveVoucher::class, 'uid');
            foreach ($validated['products'] as $product) {
                $product['date'] = $validated['date'];
                $product['narration'] = $validated['narration'];
                $product['uid'] = $uid;

                $voucher = ReceiveVoucher::create($product);
                // Accounts Effect
                addAccountsTransaction('RV', $voucher, $voucher->debit_account_id, $voucher->credit_account_id);
            }
            DB::commit();
        } catch (\Exception $error) {
            DB::rollBack();
            Toastr::error('Something went wrong!.', '', ["progressBar" => true]);
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
        $voucher = ReceiveVoucher::findOrFail(decrypt($id));
        $receiveVouchers = ReceiveVoucher::where('uid', $voucher->uid)->get();
        return view('receive_voucher.show', compact('receiveVouchers', 'voucher'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Models\ReceiveVoucher $receiveVoucher
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $voucher = ReceiveVoucher::findOrFail(decrypt($id));
        $receiveVouchers = ReceiveVoucher::with('debitAccount', 'creditAccount')->where('uid', $voucher->uid)->get();
        $toChartOfAccounts = ChartOfAccount::where(['is_bank_cash' => 'yes', 'type' => 'ledger', 'status' => 'active'])->get();
        $chartOfAccounts = ChartOfAccount::where(['is_bank_cash' => 'no', 'type' => 'ledger', 'status' => 'active'])->get();
        $RVno = $voucher->uid;
        $date = $voucher->date;
        $narration = $voucher->narration;
        return view('receive_voucher.edit', compact('receiveVouchers', 'voucher', 'toChartOfAccounts', 'chartOfAccounts', 'RVno', 'date', 'narration'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \App\Http\Requests\UpdateReceiveVoucherRequest $request
     * @param \App\Models\ReceiveVoucher $receiveVoucher
     * @return \Illuminate\Http\Response
     */
    public function update(StoreReceiveVoucherRequest $request, $id)
    {
        $validated = $request->validated();
        DB::beginTransaction();
        try {
            if (!isset($validated['products']) || count($validated['products']) < 1) {
                Toastr::info('At Least One Item Required.', '', ["progressBar" => true]);
                return back();
            }

            $voucher = ReceiveVoucher::findOrFail($id);
            $uid = $voucher->uid;

            $oldVouchers = ReceiveVoucher::where('uid', $uid)->get();
            foreach ($oldVouchers as $oldVoucher) {
                AccountTransaction::where('doc_type', 'RV')->where('doc_id', $oldVoucher->id)->delete();
                $oldVoucher->delete();
            }

            foreach ($validated['products'] as $product) {
                $product['date'] = $validated['date'];
                $product['narration'] = $validated['narration'];
                $product['uid'] = $uid;

                $newVoucher = ReceiveVoucher::create($product);
                // Accounts Effect
                addAccountsTransaction('RV', $newVoucher, $newVoucher->debit_account_id, $newVoucher->credit_account_id);
            }
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
            $voucher = ReceiveVoucher::findOrFail(decrypt($id));
            $oldVouchers = ReceiveVoucher::where('uid', $voucher->uid)->get();
            foreach ($oldVouchers as $oldVoucher) {
                AccountTransaction::where('doc_type', 'RV')->where('doc_id', $oldVoucher->id)->delete();
                $oldVoucher->delete();
            }
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
        $voucher = ReceiveVoucher::findOrFail(decrypt($id));
        $receiveVouchers = ReceiveVoucher::where('uid', $voucher->uid)->get();
        $data = [
            'voucher' => $voucher,
            'receiveVouchers' => $receiveVouchers,
        ];

        $pdf = PDF::loadView(
            'receive_voucher.pdf',
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
