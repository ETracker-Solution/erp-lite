<?php

namespace App\Http\Controllers;

use App\Models\ChartOfAccount;
use App\Models\CustomerReceiveVoucher;
use App\Http\Requests\StoreCustomerReceiveVoucherRequest;
use App\Http\Requests\UpdateCustomerReceiveVoucherRequest;
use App\Models\OutletAccount;
use App\Models\Sale;
use App\Models\SupplierGroup;

class CustomerReceiveVoucherController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (\request()->ajax()) {
            $data = CustomerReceiveVoucher::query()
                ->select([
                    \DB::raw('MAX(id) as id'),
                    'uid',
                    \DB::raw('MAX(date) as date'),
                    \DB::raw('SUM(amount) as amount'),
                    \DB::raw('SUM(settle_discount) as settle_discount'),
                    \DB::raw('MAX(narration) as narration'),
                    \DB::raw('MAX(created_at) as created_at')
                ])->groupBy('uid');
            $data = $this->filter($data);

            return \Yajra\DataTables\Facades\DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('debit_account.name', function ($row) {
                    return \App\Models\CustomerReceiveVoucher::where('uid', $row->uid)
                        ->join('chart_of_accounts', 'chart_of_accounts.id', '=', 'customer_receive_vouchers.debit_account_id')
                        ->pluck('chart_of_accounts.name')->unique()->implode(', ');
                })
                ->addColumn('customer.name', function ($row) {
                    return \App\Models\CustomerReceiveVoucher::where('uid', $row->uid)
                        ->join('customers', 'customers.id', '=', 'customer_receive_vouchers.customer_id')
                        ->pluck('customers.name')->unique()->implode(', ');
                })
                ->addColumn('action', function ($row) {
                     return view('customer_receive_voucher.action-button', compact('row'));
                })
                ->editColumn('date', function ($row) {
                    return \Carbon\Carbon::parse($row->date)->format('Y-m-d');
                })
                ->addColumn('invoice_no', function($row){
                    return \App\Models\CustomerReceiveVoucher::where('uid', $row->uid)
                        ->join('sales', 'sales.id', '=', 'customer_receive_vouchers.sale_id')
                        ->pluck('sales.invoice_number')->unique()->implode(', ');
                })
                ->filterColumn('invoice_no', function($query, $keyword) {
                    $query->whereHas('sale', function($q) use ($keyword) {
                        $q->where('invoice_number', 'like', "%{$keyword}%");
                    });
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        if (\auth()->user() && \auth()->user()->employee && \auth()->user()->employee->outlet_id) {
            $chartOfAccounts = OutletAccount::where('outlet_id', \auth()->user()->employee->outlet_id)->pluck('coa_id')->toArray();
            $paymentAccounts = ChartOfAccount::whereIn('id', $chartOfAccounts)->get();
        } else {
            $paymentAccounts = ChartOfAccount::where(['is_bank_cash' => 'yes', 'type' => 'ledger', 'status' => 'active'])->get();
        }

        return view('customer_receive_voucher.index', compact('paymentAccounts'));
    }

    protected function filter($data)
    {
        if (request('date_range')) {
            $dateRange = [];
            if (str_contains(request('date_range'), ' to ')) {
                $dateRange = explode(' to ', request('date_range'));
            } elseif (str_contains(request('date_range'), ' - ')) {
                $dateRange = explode(' - ', request('date_range'));
            } else {
                $dateRange = [request('date_range'), request('date_range')];
            }

            if (isset($dateRange[0]) && isset($dateRange[1])) {
                $data->whereBetween('date', [$dateRange[0], $dateRange[1]]);
            } elseif (isset($dateRange[0])) {
                $data->where('date', $dateRange[0]);
            }
        }

        if (request()->filled('crv_no')) {
            $data->where('uid', 'like', '%' . request('crv_no') . '%');
        }

        if (request()->filled('invoice_no')) {
            $data->whereHas('sale', function ($query) {
                $query->where('invoice_number', 'like', '%' . request('invoice_no') . '%');
            });
        }

        if (request()->filled('received_to')) {
            $data->where('debit_account_id', request('received_to'));
        }

        return $data->latest();
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Only active customers who have at least one invoice with due amount
        $customers = \App\Models\Customer::where('status', 'active')
            ->whereHas('sales', function ($q) {
                // Assuming 'sales' relationship exists in Customer model
                $q->whereRaw('grand_total > receive_amount');
            })->get();

        if (\auth()->user() && \auth()->user()->employee && \auth()->user()->employee->outlet_id) {
            $chartOfAccounts = OutletAccount::where('outlet_id', \auth()->user()->employee->outlet_id)->pluck('coa_id')->toArray();
            $paymentAccounts = ChartOfAccount::whereIn('id', $chartOfAccounts)->get();
        }else{
            // Debit (Bank/Cash) - Receiving Money INTO
            $paymentAccounts = ChartOfAccount::where(['is_bank_cash' => 'yes', 'type' => 'ledger', 'status' => 'active'])->get();
        }

        $serial_count = CustomerReceiveVoucher::latest()->first() ? CustomerReceiveVoucher::latest()->first()->id : 0;
        $uid = $serial_count + 1;
        return view('customer_receive_voucher.create', compact('paymentAccounts', 'uid', 'customers'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCustomerReceiveVoucherRequest $request)
    {
        $validated = $request->validated();
        \DB::beginTransaction();
        try {
            if (!isset($validated['products']) || count($validated['products']) < 1) {
                \Brian2694\Toastr\Facades\Toastr::info('At Least One Invoice Payment Required.', '', ["progressBar" => true]);
                return back();
            }

            $uid = generateUniqueCode(CustomerReceiveVoucher::class, 'uid');
            foreach ($validated['products'] as $product) {

                // Get Sale Information
                $sale = \App\Models\Sale::findOrFail($product['sale_id']);

                // Fields to create Voucher
                // CRV fields: uid, date, amount, customer_id, debit_account_id, sale_id, narration, created_by, etc.
                $voucherData = [
                    'uid' => $uid,
                    'date' => $validated['date'],
                    'customer_id' => $product['customer_id'],
                    'sale_id' => $product['sale_id'],
                    'debit_account_id' => $product['debit_account_id'],
                    'credit_account_id' => getAccountsReceiveableGLId(), // Receivables
                    'amount' => $product['amount'],
                    'settle_discount' => $product['settle_discount'] ?? 0,
                    'narration' => $validated['narration'] ?? 'Due Collection',
                    'created_by' => auth()->id(),
                ];

                $voucher = CustomerReceiveVoucher::create($voucherData);

                $findSale = Sale::where('customer_id',$product['customer_id'])->find($product['sale_id']);
                $findSale->update([
                    'discount' => $findSale->discount + $product['settle_discount']
                ]);

                // 1. Accounting Effect: Debit Bank/Cash, Credit Accounts Receivable
                addAccountsTransaction('CRV', $voucher, $voucherData['debit_account_id'], $voucherData['credit_account_id']);

                // Discount Entry
                if ($voucher->settle_discount > 0) {
                    addAccountsTransaction(
                        'CRV',
                        (object)[
                            'date' => $voucher->date,
                            'amount' => $voucher->settle_discount,
                            'narration' => 'Settle Discount',
                            'reference_no' => $voucher->uid,
                            'id' => $voucher->id
                        ],
                        getDiscountGLID(),
                        $voucher->credit_account_id
                    );
                }

                // 2. Update Sale Record (receive_amount, change_amount if applicable, usually 0 for due)
                // We should track this payment. Sale table has `receive_amount`.
                // We need to increment it.
                $sale->receive_amount += $voucher->amount + $voucher->settle_discount;
                // Update Sale Status if Paid
                // Check if internal method available? No property on model.
                // Simple logic:
                if ($sale->receive_amount >= $sale->grand_total) {
                     // Maybe status 'final' is already there, but if we have payment status?
                     // Sale doesn't seem to have payment_status column in Controller logic, just 'receive_amount'.
                }
                $sale->save();

                // 3. Create Payment Record (for Sale History and Reports)
                \App\Models\Payment::create([
                    'sale_id' => $sale->id,
                    'customer_id' => $sale->customer_id,
                    'payment_method' => 'Due Collection', // or 'cash'/'bank' but we can map from account?
                    // Payment table asks for 'payment_method' enum strings?
                    // In SaleController: 'cash', 'card', etc.
                    // Here we can put 'cash' or 'bank' depending on debit_account type?
                    // Or just 'due_collection' if supported.
                    // Let's assume 'due_collection' or similar string is fine as it's likely a string column.
                    'amount' => $voucher->amount,
                ]);

                // 4. Customer Transaction Effect
                \App\Models\CustomerTransaction::query()->create([
                    'customer_id' => $voucher->customer_id,
                    'doc_type' => 'CRV',
                    'doc_id' => $voucher->id,
                    'amount' => $voucher->amount + $voucher->settle_discount,
                    'date' => $voucher->date,
                    'transaction_type' => -1, // Credit (Payment reduces balance)
                    'chart_of_account_id' => $voucher->debit_account_id, // Payment to Bank/Cash
                    'description' => 'Due Collection',
                ]);
            }
            \DB::commit();
        } catch (\Exception $error) {
            \DB::rollBack();
            \Brian2694\Toastr\Facades\Toastr::info('Something went wrong! ' . $error->getMessage(), '', ["progressBar" => true]);
            return back();
        }
        \Brian2694\Toastr\Facades\Toastr::success('Customer Due Receive Voucher Created Successfully!.', '', ["progressBar" => true]);
        return redirect()->route('customer-receive-vouchers.index'); // Check route name
    }

    public function fetchDueInvoice($customer_id)
    {
        // Find Sales with Due for this customer
        // Due = grand_total - receive_amount > 0
        // Also status should be valid (e.g. 'final') if applicable.
        // Assuming all active sales.
        $sales = \App\Models\Sale::where('customer_id', $customer_id)
            ->whereRaw('grand_total > receive_amount')
            ->select('id', 'invoice_number', 'date', 'grand_total', 'receive_amount')
            ->get();

        $invoices = $sales->map(function($sale) {
            return [
                'id' => $sale->id,
                'invoice_number' => $sale->invoice_number,
                'date' => $sale->date,
                'grand_total' => $sale->grand_total,
                'receive_amount' => $sale->receive_amount,
                'due_amount' => $sale->grand_total - $sale->receive_amount
            ];
        });

        return response()->json([
            'invoices' => $invoices
        ]);
    }
    public function show($id)
    {
        try {
            $id = decrypt($id);
        } catch (\Exception $e) {
            abort(404);
        }
        $voucher = CustomerReceiveVoucher::with('customer', 'sale', 'debitAccount')->findOrFail($id);
        $customerReceiveVouchers = CustomerReceiveVoucher::where('uid', $voucher->uid)->get();
        return view('customer_receive_voucher.show', compact('customerReceiveVouchers', 'voucher'));
    }

    public function edit($id)
    {
        $voucher = CustomerReceiveVoucher::findOrFail(decrypt($id));
        $customerReceiveVouchers = CustomerReceiveVoucher::with('customer', 'sale', 'debitAccount')->where('uid', $voucher->uid)->get();

        $customers = \App\Models\Customer::where('status', 'active')->get();

        if (\auth()->user() && \auth()->user()->employee && \auth()->user()->employee->outlet_id) {
            $chartOfAccounts = OutletAccount::where('outlet_id', \auth()->user()->employee->outlet_id)->pluck('coa_id')->toArray();
            $paymentAccounts = ChartOfAccount::whereIn('id', $chartOfAccounts)->get();
        }else{
            $paymentAccounts = ChartOfAccount::where(['is_bank_cash' => 'yes', 'type' => 'ledger', 'status' => 'active'])->get();
        }

        $uid = $voucher->uid;
        $date = $voucher->date;
        $narration = $voucher->narration;
        return view('customer_receive_voucher.edit', compact('customerReceiveVouchers', 'voucher', 'paymentAccounts', 'uid', 'date', 'narration', 'customers'));
    }

    public function update(StoreCustomerReceiveVoucherRequest $request, $id)
    {
        $validated = $request->validated();
        \DB::beginTransaction();
        try {
            if (!isset($validated['products']) || count($validated['products']) < 1) {
                \Brian2694\Toastr\Facades\Toastr::info('At Least One Invoice Payment Required.', '', ["progressBar" => true]);
                return back();
            }

            $voucher = CustomerReceiveVoucher::findOrFail($id);
            $uid = $voucher->uid;

            $oldVouchers = CustomerReceiveVoucher::where('uid', $uid)->get();
            foreach ($oldVouchers as $oldVoucher) {
                \App\Models\AccountTransaction::where('doc_type', 'CRV')->where('doc_id', $oldVoucher->id)->delete();
                \App\Models\CustomerTransaction::where('doc_type', 'CRV')->where('doc_id', $oldVoucher->id)->delete();
                $sale = \App\Models\Sale::find($oldVoucher->sale_id);
                if ($sale) {
                    $sale->receive_amount -= $oldVoucher->amount;
                    $sale->discount -= $oldVoucher->settle_discount;
                    $sale->save();
                    \App\Models\Payment::where('sale_id', $sale->id)->where('amount', $oldVoucher->amount)->where('payment_method', 'Due Collection')->first()?->delete();
                }
                $oldVoucher->delete();
            }

            foreach ($validated['products'] as $product) {
                $sale = \App\Models\Sale::findOrFail($product['sale_id']);
                $voucherData = [
                    'uid' => $uid,
                    'date' => $validated['date'],
                    'customer_id' => $product['customer_id'],
                    'sale_id' => $product['sale_id'],
                    'debit_account_id' => $product['debit_account_id'],
                    'credit_account_id' => getAccountsReceiveableGLId(),
                    'amount' => $product['amount'],
                    'settle_discount' => $product['settle_discount'] ?? 0,
                    'narration' => $validated['narration'] ?? 'Due Collection',
                    'created_by' => auth()->id(),
                ];

                $newVoucher = CustomerReceiveVoucher::create($voucherData);

                $findSale = Sale::where('customer_id',$product['customer_id'])->find($product['sale_id']);
                $findSale->update([
                    'discount' => $findSale->discount + $product['settle_discount']
                ]);

                addAccountsTransaction('CRV', $newVoucher, $voucherData['debit_account_id'], $voucherData['credit_account_id']);

                // Discount Entry
                if ($newVoucher->settle_discount > 0) {
                    addAccountsTransaction(
                        'CRV',
                        (object)[
                            'date' => $newVoucher->date,
                            'amount' => $newVoucher->settle_discount,
                            'narration' => 'Settle Discount',
                            'reference_no' => $newVoucher->uid,
                            'id' => $newVoucher->id
                        ],
                        getDiscountGLID(),
                        $newVoucher->credit_account_id
                    );
                }

                $sale->receive_amount += $newVoucher->amount;
                $sale->save();

                \App\Models\Payment::create([
                    'sale_id' => $sale->id,
                    'customer_id' => $sale->customer_id,
                    'payment_method' => 'Due Collection',
                    'amount' => $newVoucher->amount,
                ]);

                // 4. Customer Transaction Effect
                \App\Models\CustomerTransaction::query()->create([
                    'customer_id' => $newVoucher->customer_id,
                    'doc_type' => 'CRV',
                    'doc_id' => $newVoucher->id,
                    'amount' => $newVoucher->amount + $newVoucher->settle_discount,
                    'date' => $newVoucher->date,
                    'transaction_type' => -1, // Credit (Payment reduces balance)
                    'chart_of_account_id' => $newVoucher->debit_account_id, // Payment to Bank/Cash
                    'description' => 'Due Collection',
                ]);
            }
            \DB::commit();
        } catch (\Exception $error) {
            \DB::rollBack();
            \Brian2694\Toastr\Facades\Toastr::info('Something went wrong! ' . $error->getMessage(), '', ["progressBar" => true]);
            return back();
        }
        \Brian2694\Toastr\Facades\Toastr::success('Customer Due Receive Voucher Updated Successfully!.', '', ["progressBar" => true]);
        return redirect()->route('customer-receive-vouchers.index');
    }

    public function destroy($id)
    {
        \DB::beginTransaction();
        try {
            $voucher = CustomerReceiveVoucher::findOrFail(decrypt($id));
            $oldVouchers = CustomerReceiveVoucher::where('uid', $voucher->uid)->get();
            foreach ($oldVouchers as $oldVoucher) {
                \App\Models\AccountTransaction::where('doc_type', 'CRV')->where('doc_id', $oldVoucher->id)->delete();
                \App\Models\CustomerTransaction::where('doc_type', 'CRV')->where('doc_id', $oldVoucher->id)->delete();
                $sale = \App\Models\Sale::find($oldVoucher->sale_id);
                if ($sale) {
                    $sale->receive_amount -= $oldVoucher->amount;
                    $sale->discount -= $oldVoucher->settle_discount;
                    $sale->save();
                    \App\Models\Payment::where('sale_id', $sale->id)->where('amount', $oldVoucher->amount)->where('payment_method', 'Due Collection')->first()?->delete();
                }
                $oldVoucher->delete();
            }
            \DB::commit();
        } catch (\Exception $error) {
            \DB::rollBack();
            \Brian2694\Toastr\Facades\Toastr::info('Something went wrong!.', '', ["progressBar" => true]);
            return back();
        }
        \Brian2694\Toastr\Facades\Toastr::success('Customer Receive Voucher Deleted Successfully!.', '', ["progressBar" => true]);
        return redirect()->route('customer-receive-vouchers.index');
    }

    public function Pdf($id)
    {
        $voucher = CustomerReceiveVoucher::findOrFail(decrypt($id));
        $customerReceiveVouchers = CustomerReceiveVoucher::where('uid', $voucher->uid)->get();
        $data = [
            'voucher' => $voucher,
            'customerReceiveVouchers' => $customerReceiveVouchers,
        ];

        $pdf = \niklasravnsborg\LaravelPdf\Facades\Pdf::loadView(
            'customer_receive_voucher.pdf',
            $data,
            [],
            [
                'format' => 'A4-P',
                'orientation' => 'P',
                'margin-left' => 1,
                '', '', 0, '', 1, 1, 1, 1, 1, 1, 'L',
            ]
        );
        $name = \Carbon\Carbon::now()->format('d-m-Y');

        return $pdf->stream($name . '.pdf');
    }
}
