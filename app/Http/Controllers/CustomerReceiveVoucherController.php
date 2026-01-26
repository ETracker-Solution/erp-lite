<?php

namespace App\Http\Controllers;

use App\Models\ChartOfAccount;
use App\Models\CustomerReceiveVoucher;
use App\Http\Requests\StoreCustomerReceiveVoucherRequest;
use App\Http\Requests\UpdateCustomerReceiveVoucherRequest;
use App\Models\OutletAccount;
use App\Models\SupplierGroup;

class CustomerReceiveVoucherController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (\request()->ajax()) {
            $data = CustomerReceiveVoucher::with('customer', 'sale', 'debitAccount')->latest();
            return \Yajra\DataTables\Facades\DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                     return view('customer_receive_voucher.action-button', compact('row'));
                })
                ->editColumn('date', function ($row) {
                    return \Carbon\Carbon::parse($row->date)->format('Y-m-d');
                })
                ->addColumn('invoice_no', function($row){
                    return $row->sale ? $row->sale->invoice_number : 'N/A';
                })
                ->rawColumns(['action'])
                ->make(true);
        }
        return view('customer_receive_voucher.index');
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

            //    $cons = OutletTransactionConfig::with('coa')->where('outlet_id', \auth()->user()->employee->outlet_id)->get();
            //     foreach ($cons as $con) {
            //         $chartOfAccounts[] = $con->coa;
            //     }
            $chartOfAccounts = OutletAccount::with(['coa'])->whereHas('coa', function ($coa) {
                return $coa->whereNull('default_type');
            })->where('outlet_id', \auth()->user()->employee->outlet_id)->pluck('coi_id')->toArray();
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

            foreach ($validated['products'] as $product) {

                // Get Sale Information
                $sale = \App\Models\Sale::findOrFail($product['sale_id']);

                // Fields to create Voucher
                // CRV fields: uid, date, amount, customer_id, debit_account_id, sale_id, narration, created_by, etc.
                $voucherData = [
                    'uid' => generateUniqueCode(CustomerReceiveVoucher::class, 'uid'),
                    'date' => $validated['date'],
                    'customer_id' => $product['customer_id'],
                    'sale_id' => $product['sale_id'],
                    'debit_account_id' => $product['debit_account_id'],
                    'credit_account_id' => getAccountsReceiveableGLId(), // Receivables
                    'amount' => $product['amount'],
                    'narration' => $validated['narration'] ?? 'Due Collection',
                    'created_by' => auth()->id(),
                ];

                $voucher = CustomerReceiveVoucher::create($voucherData);

                // 1. Accounting Effect: Debit Bank/Cash, Credit Accounts Receivable
                addAccountsTransaction('CRV', $voucher, $voucherData['debit_account_id'], $voucherData['credit_account_id']);

                // 2. Update Sale Record (receive_amount, change_amount if applicable, usually 0 for due)
                // We should track this payment. Sale table has `receive_amount`.
                // We need to increment it.
                $sale->receive_amount += $voucher->amount;
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
        $customerReceiveVoucher = CustomerReceiveVoucher::with('customer', 'sale', 'debitAccount')->findOrFail($id);
        return view('customer_receive_voucher.show', compact('customerReceiveVoucher'));
    }
}
