<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSupplierPaymentVoucherRequest;
use App\Http\Requests\UpdateSupplierPaymentVoucherRequest;
use App\Models\AccountTransaction;
use App\Models\ChartOfAccount;
use App\Models\Supplier;
use App\Models\SupplierGroup;
use App\Models\SupplierPaymentVoucher;
use App\Models\SupplierTransaction;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\DB;
use niklasravnsborg\LaravelPdf\Facades\Pdf;
use Yajra\DataTables\Facades\DataTables;

class SupplierPaymentVoucherController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (\request()->ajax()) {
            $journalVouchers = SupplierPaymentVoucher::with('debitAccount', 'creditAccount', 'supplier')->latest();
            return DataTables::of($journalVouchers)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    return view('supplier_payment_voucher.action-button', compact('row'));
                })
                ->addColumn('created_at', function ($row) {
                    return view('common.created_at', compact('row'));
                })
                ->rawColumns(['action'])
                ->make(true);
        }
        return view('supplier_payment_voucher.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $supplier_groups = SupplierGroup::where('status','active')->get();
        $paymentAccounts = ChartOfAccount::where(['is_bank_cash' => 'yes', 'type' => 'ledger', 'status' => 'active'])->get();

        $serial_count = SupplierPaymentVoucher::latest()->first() ? SupplierPaymentVoucher::latest()->first()->id : 0;
        $uid = $serial_count + 1;
        return view('supplier_payment_voucher.create', compact('paymentAccounts', 'uid', 'supplier_groups'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreSupplierPaymentVoucherRequest $request)
    {
        $validated = $request->validated();
        DB::beginTransaction();
        try {
            $voucher = SupplierPaymentVoucher::create($validated);
            //Accounts Effect
            addAccountsTransaction('SPV', $voucher, $voucher->debit_account_id, $voucher->credit_account_id);
            // Supplier Transaction Effect
            SupplierTransaction::query()->create([
                'supplier_id' => $voucher->supplier_id,
                'doc_type' => 'SPV',
                'doc_id' => $voucher->id,
                'amount' => $voucher->amount,
                'date' => $voucher->date,
                'transaction_type' => -1,
                'chart_of_account_id' => $voucher->credit_account_id,
                'description' => 'Payment For Purchase of Goods',
            ]);
            DB::commit();
        } catch (\Exception $error) {
            DB::rollBack();
            Toastr::info('Something went wrong!.', '', ["progressBar" => true]);
            return back();
        }
        Toastr::success('Supplier Payment Voucher Created Successfully!.', '', ["progressBar" => true]);
        return redirect()->route('supplier-vouchers.index');
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $supplierVoucher = SupplierPaymentVoucher::findOrFail(decrypt($id));
        return view('supplier_payment_voucher.show', compact('supplierVoucher'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(SupplierPaymentVoucher $supplierVoucher)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateSupplierPaymentVoucherRequest $request, SupplierPaymentVoucher $supplierVoucher)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            SupplierPaymentVoucher::findOrFail(decrypt($id))->delete();
            AccountTransaction::where('doc_type', 'SPV')->where('doc_id', decrypt($id))->delete();
            SupplierTransaction::where('doc_type', 'SPV')->where('doc_id', decrypt($id))->delete();

            DB::commit();
        } catch (\Exception $error) {
            DB::rollBack();
            Toastr::info('Something went wrong!.', '', ["progressBar" => true]);
            return back();
        }
        Toastr::success('Supplier Voucher Deleted Successfully!.', '', ["progressBar" => true]);
        return redirect()->route('supplier-vouchers.index');
    }
    public function Pdf($id)
    {
        $supplierVoucher = SupplierPaymentVoucher::findOrFail(decrypt($id));
        $data = [
            'supplierVoucher' => $supplierVoucher,
        ];

        $pdf = Pdf::loadView(
            'supplier_payment_voucher.pdf',
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
