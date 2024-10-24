<?php

namespace App\Http\Controllers;

use App\Models\ChartOfAccount;
use App\Models\CustomerReceiveVoucher;
use App\Http\Requests\StoreCustomerReceiveVoucherRequest;
use App\Http\Requests\UpdateCustomerReceiveVoucherRequest;
use App\Models\SupplierGroup;

class CustomerReceiveVoucherController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $supplier_groups = SupplierGroup::where('status','active')->get();
        $paymentAccounts = ChartOfAccount::where(['is_bank_cash' => 'yes', 'type' => 'ledger', 'status' => 'active'])->get();

        $serial_count =CustomerReceiveVoucher::latest()->first() ? CustomerReceiveVoucher::latest()->first()->id : 0;
        $uid = $serial_count + 1;
        return view('customer_receive_voucher.create', compact('paymentAccounts', 'uid', 'supplier_groups'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCustomerReceiveVoucherRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(CustomerReceiveVoucher $customerReceiveVoucher)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(CustomerReceiveVoucher $customerReceiveVoucher)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCustomerReceiveVoucherRequest $request, CustomerReceiveVoucher $customerReceiveVoucher)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(CustomerReceiveVoucher $customerReceiveVoucher)
    {
        //
    }
}
