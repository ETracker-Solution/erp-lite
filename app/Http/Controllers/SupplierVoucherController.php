<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSupplierVoucherRequest;
use App\Http\Requests\UpdateSupplierVoucherRequest;
use App\Models\AccountTransaction;
use App\Models\ChartOfAccount;
use App\Models\Supplier;
use App\Models\SupplierVoucher;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class SupplierVoucherController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (\request()->ajax()) {
            $journalVouchers = SupplierVoucher::with('debitAccount', 'creditAccount','supplier')->latest();
            return DataTables::of($journalVouchers)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    return view('supplier_voucher.action-button', compact('row'));
                })
                ->addColumn('created_at', function ($row) {
                    return view('common.created_at', compact('row'));
                })
                ->rawColumns(['action'])
                ->make(true);
        }
        return view('supplier_voucher.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $suppliers = Supplier::all();
        $debitAccounts = ChartOfAccount::where(['is_bank_cash' => 'yes', 'type' => 'ledger', 'status' => 'active'])->get();
        $creditAccounts = ChartOfAccount::where(['is_bank_cash' => 'no', 'type' => 'ledger', 'status' => 'active'])->get();
        $lastValue = SupplierVoucher::latest()->pluck('sv_no')->first();
        if ($lastValue !== null) {
            $SVno = (int)$lastValue + 1;
        } else {
            $SVno = 1; // Set the default value to 1
        }
        return view('supplier_voucher.create', compact('debitAccounts', 'creditAccounts','SVno','suppliers'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreSupplierVoucherRequest $request)
    {
        $validated = $request->validated();
//        DB::beginTransaction();
//        try {
        $voucher = SupplierVoucher::create($validated);
        // transaction('rv', $voucher);
//            DB::commit();
//        } catch (\Exception $error) {
//            DB::rollBack();
//            Toastr::info('Something went wrong!.', '', ["progressBar" => true]);
//            return back();
//        }
        Toastr::success('Supplier Voucher Created Successfully!.', '', ["progressBar" => true]);
        return redirect()->route('supplier-vouchers.index');
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $supplierVoucher = SupplierVoucher::findOrFail(decrypt($id));
        return view('supplier_voucher.show', compact('supplierVoucher'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(SupplierVoucher $supplierVoucher)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateSupplierVoucherRequest $request, SupplierVoucher $supplierVoucher)
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
            SupplierVoucher::findOrFail(decrypt($id))->delete();
            AccountTransaction::where('doc_type','sv')->where('doc_id',decrypt($id))->delete();

            DB::commit();
        } catch (\Exception $error) {
            DB::rollBack();
            Toastr::info('Something went wrong!.', '', ["progressBar" => true]);
            return back();
        }
        Toastr::success('Supplier Voucher Deleted Successfully!.', '', ["progressBar" => true]);
        return redirect()->route('supplier-vouchers.index');
    }
}
