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
        if (request()->ajax()) {
            $journalVouchers = SupplierPaymentVoucher::query()
                ->select([
                    DB::raw('MAX(id) as id'),
                    'uid',
                    DB::raw('MAX(date) as date'),
                    DB::raw('SUM(amount) as amount'),
                    DB::raw('SUM(settle_discount) as settle_discount'),
                    DB::raw('MAX(payee_name) as payee_name'),
                    DB::raw('MAX(created_at) as created_at')
                ])->groupBy('uid');
            $journalVouchers = $this->filter($journalVouchers, request());
            return DataTables::of($journalVouchers->latest())
                ->addIndexColumn()
                ->addColumn('debit_account.name', function ($row) {
                    return \App\Models\SupplierPaymentVoucher::where('uid', $row->uid)
                        ->join('chart_of_accounts', 'chart_of_accounts.id', '=', 'supplier_payment_vouchers.debit_account_id')
                        ->pluck('chart_of_accounts.name')->unique()->implode(', ');
                })
                ->addColumn('credit_account.name', function ($row) {
                    return \App\Models\SupplierPaymentVoucher::where('uid', $row->uid)
                        ->join('chart_of_accounts', 'chart_of_accounts.id', '=', 'supplier_payment_vouchers.credit_account_id')
                        ->pluck('chart_of_accounts.name')->unique()->implode(', ');
                })
                ->addColumn('supplier.name', function ($row) {
                    return \App\Models\SupplierPaymentVoucher::where('uid', $row->uid)
                        ->join('suppliers', 'suppliers.id', '=', 'supplier_payment_vouchers.supplier_id')
                        ->pluck('suppliers.name')->unique()->implode(', ');
                })
                ->addColumn('action', fn($row) => view('supplier_payment_voucher.action-button', compact('row')))
                ->addColumn('created_at', fn($row) => view('common.created_at', compact('row')))
                ->rawColumns(['action'])
                ->make(true);
        }

        $suppliers = Supplier::select('id', 'name')->where('status', 'active')->get();
        $creditAccounts = ChartOfAccount::select('id', 'name')
            ->where(['is_bank_cash' => 'yes', 'type' => 'ledger', 'status' => 'active'])
            ->get();
        $debitAccounts = ChartOfAccount::select('id', 'name')
            ->where(['type' => 'ledger', 'status' => 'active'])
            ->get();

        return view('supplier_payment_voucher.index', compact('suppliers', 'creditAccounts', 'debitAccounts'));
    }

    private function filter($query, $request)
    {
        return $query
            ->when($request->supplier_id, fn($q) => $q->where('supplier_id', $request->supplier_id))
            ->when($request->date_range, function ($q) use ($request) {
                searchColumnByDateRange($q, 'date', $request->date_range);
            })
            ->when($request->uid, fn($q) => $q->where('uid', 'like', "%{$request->uid}%"))
            ->when($request->debit_account_id, fn($q) => $q->where('debit_account_id', $request->debit_account_id))
            ->when($request->credit_account_id, fn($q) => $q->where('credit_account_id', $request->credit_account_id))
            ->when($request->created_at_range, function ($q) use ($request) {
                searchColumnByDateRange($q, 'created_at', $request->created_at_range);
            });
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $supplier_groups = SupplierGroup::where('status','active')->get();
        // Credit (Bank/Cash)
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
            if (!isset($validated['products']) || count($validated['products']) < 1) {
                Toastr::info('At Least One Item Required.', '', ["progressBar" => true]);
                return back();
            }

            $uid = generateUniqueCode(SupplierPaymentVoucher::class, 'uid');
            foreach ($validated['products'] as $product) {
                $product['date'] = $validated['date'];
                $product['narration'] = $validated['narration'];
                $product['uid'] = $uid;
                $product['debit_account_id'] = 22; // Hardcoded Accounts Payable
                $product['settle_discount'] = $product['settle_discount'] ?? 0;

                $voucher = SupplierPaymentVoucher::create($product);

                //Accounts Effect
                addAccountsTransaction('SPV', $voucher, $voucher->debit_account_id, $voucher->credit_account_id);
                // Supplier Transaction Effect
                SupplierTransaction::query()->create([
                    'supplier_id' => $voucher->supplier_id,
                    'doc_type' => 'SPV',
                    'doc_id' => $voucher->id,
                    'amount' => $voucher->amount + $voucher->settle_discount,
                    'date' => $voucher->date,
                    'transaction_type' => -1, // Payment reduces balance
                    'chart_of_account_id' => $voucher->credit_account_id, // Paid from Bank
                    'description' => 'Payment For Purchase of Goods',
                ]);

                // Discount Entry
                if ($voucher->settle_discount > 0) {
                    addAccountsTransaction(
                        'SPV',
                        (object)[
                            'date' => $voucher->date,
                            'amount' => $voucher->settle_discount,
                            'narration' => 'Supplier Discount',
                            'reference_no' => $voucher->uid,
                            'id' => $voucher->id
                        ],
                        $voucher->debit_account_id,
                        getDiscountGLID()
                    );
                }
            }
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
        $voucher = SupplierPaymentVoucher::findOrFail(decrypt($id));
        $supplierVouchers = SupplierPaymentVoucher::where('uid', $voucher->uid)->get();
        return view('supplier_payment_voucher.show', compact('supplierVouchers', 'voucher'));
    }

    public function edit($id)
    {
        $voucher = SupplierPaymentVoucher::findOrFail(decrypt($id));
        $supplierVouchers = SupplierPaymentVoucher::with('supplier', 'creditAccount')->where('uid', $voucher->uid)->get();
        $supplier_groups = SupplierGroup::where('status','active')->get();
        $paymentAccounts = ChartOfAccount::where(['is_bank_cash' => 'yes', 'type' => 'ledger', 'status' => 'active'])->get();
        $uid = $voucher->uid;
        $date = $voucher->date;
        $narration = $voucher->narration;
        return view('supplier_payment_voucher.edit', compact('supplierVouchers', 'voucher', 'supplier_groups', 'paymentAccounts', 'uid', 'date', 'narration'));
    }

    public function update(StoreSupplierPaymentVoucherRequest $request, $id)
    {
        $validated = $request->validated();
        DB::beginTransaction();
        try {
            if (!isset($validated['products']) || count($validated['products']) < 1) {
                Toastr::info('At Least One Item Required.', '', ["progressBar" => true]);
                return back();
            }

            $voucher = SupplierPaymentVoucher::findOrFail($id);
            $uid = $voucher->uid;

            $oldVouchers = SupplierPaymentVoucher::where('uid', $uid)->get();
            foreach ($oldVouchers as $oldVoucher) {
                AccountTransaction::where('doc_type', 'SPV')->where('doc_id', $oldVoucher->id)->delete();
                SupplierTransaction::where('doc_type', 'SPV')->where('doc_id', $oldVoucher->id)->delete();
                $oldVoucher->delete();
            }

            foreach ($validated['products'] as $product) {
                $product['date'] = $validated['date'];
                $product['narration'] = $validated['narration'];
                $product['uid'] = $uid;
                $product['debit_account_id'] = 22;
                $product['settle_discount'] = $product['settle_discount'] ?? 0;

                $newVoucher = SupplierPaymentVoucher::create($product);
                //Accounts Effect
                addAccountsTransaction('SPV', $newVoucher, $newVoucher->debit_account_id, $newVoucher->credit_account_id);
                // Supplier Transaction Effect
                SupplierTransaction::query()->create([
                    'supplier_id' => $newVoucher->supplier_id,
                    'doc_type' => 'SPV',
                    'doc_id' => $newVoucher->id,
                    'amount' => $newVoucher->amount + $newVoucher->settle_discount,
                    'date' => $newVoucher->date,
                    'transaction_type' => -1,
                    'chart_of_account_id' => $newVoucher->credit_account_id,
                    'description' => 'Payment For Purchase of Goods',
                ]);

                // Discount Entry
                if ($newVoucher->settle_discount > 0) {
                    addAccountsTransaction(
                        'SPV',
                        (object)[
                            'date' => $newVoucher->date,
                            'amount' => $newVoucher->settle_discount,
                            'narration' => 'Supplier Discount',
                            'reference_no' => $newVoucher->uid,
                            'id' => $newVoucher->id
                        ],
                        $newVoucher->debit_account_id,
                        getDiscountGLID()
                    );
                }
            }
            DB::commit();
        } catch (\Exception $error) {
            DB::rollBack();
            Toastr::info('Something went wrong!.', '', ["progressBar" => true]);
            return back();
        }
        Toastr::success('Supplier Payment Voucher Updated Successfully!.', '', ["progressBar" => true]);
        return redirect()->route('supplier-vouchers.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $voucher = SupplierPaymentVoucher::findOrFail(decrypt($id));
            $oldVouchers = SupplierPaymentVoucher::where('uid', $voucher->uid)->get();
            foreach ($oldVouchers as $oldVoucher) {
                AccountTransaction::where('doc_type', 'SPV')->where('doc_id', $oldVoucher->id)->delete();
                SupplierTransaction::where('doc_type', 'SPV')->where('doc_id', $oldVoucher->id)->delete();
                $oldVoucher->delete();
            }

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
        $voucher = SupplierPaymentVoucher::findOrFail(decrypt($id));
        $supplierVouchers = SupplierPaymentVoucher::where('uid', $voucher->uid)->get();
        $data = [
            'voucher' => $voucher,
            'supplierVouchers' => $supplierVouchers,
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
