<?php

namespace App\Http\Controllers;

use App\Models\ChartOfAccount;
use App\Models\OutletAccount;
use App\Models\OutletTransactionConfig;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Calculation\Statistical\Distributions\Beta;
use Yajra\DataTables\Facades\DataTables;

use function Ramsey\Uuid\v1;

class OutletAccountController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $outletAccounts = OutletAccount::with('outlet', 'coa')->latest();
        if (\request()->ajax()) {
            return DataTables::of($outletAccounts)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    return view('outlet_account.action', compact('row'));
                })
                ->addColumn('created_at', function ($row) {
                    return view('common.created_at', compact('row'));
                })
                ->addColumn('status', function ($row) {
                    return showStatus($row->status);
                })
                ->rawColumns(['action','status'])
                ->make(true);
        }
        return view('outlet_account.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $outlets = \App\Models\Outlet::all();
        $chartOfAccounts = \App\Models\ChartOfAccount::where('root_account_type','as')->get();
        return view('outlet_account.create',compact('outlets', 'chartOfAccounts'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'outlet_id' => 'required',
            'status' => 'nullable',
        ]);
        DB::beginTransaction();
        try {
            $coa = \App\Models\ChartOfAccount::where('name', $request->name)->first();
        // if (!$coa){
            $coa =  ChartOfAccount::create([
                'name' => $request->name,
                'status' => 'active',
                'type' => 'ledger',
                'account_type' => 'debit',
                'is_bank_cash' => 'yes',
                'root_account_type' => 'as',
                'parent_id' => $request->coa_id,
            ]);
            $coaId = $coa->id;
        // }
        
        OutletAccount::create([
            'outlet_id' => $request->outlet_id,
            'coa_id' => $coaId,
        ]);
        $outletTransactionConfig = \App\Models\OutletTransactionConfig::where(['type'=> $request->type, 'outlet_id'=> $request->outlet_id])->first();
        if(!$outletTransactionConfig){
            OutletTransactionConfig::create([
                'type' => $request->type,
                'outlet_id' => $request->outlet_id,
                'coa_id' => $coaId,
            ]);
        }
        

        DB::commit();
        } catch (\Exception $error) {
            DB::rollBack();
            Toastr::info('Something went wrong!.', '', ["progressBar" => true]);
            return back();
        }
        
        Toastr::success('Outlet Account has been created successfully.');
        return redirect()->route('outlet-accounts.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $outletAccount = OutletAccount::findOrFail(decrypt($id));
        $outlets = \App\Models\Outlet::all();
        $chartOfAccounts = \App\Models\ChartOfAccount::all();
        return view('outlet_account.create',compact('outletAccount', 'outlets', 'chartOfAccounts'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validated = $request->validate([
            'status' => 'nullable',
        ]);
        DB::beginTransaction();
        try {
            $outletAccount = OutletAccount::findOrFail(decrypt($id));
            $outletAccount->update($validated);
            DB::commit();
        } catch (\Exception $error) {
            DB::rollBack();
            Toastr::info('Something went wrong!.', '', ["progressBar" => true]);
            return back();
        }
        Toastr::success('Fund Transfer Voucher Update Successfully!.', '', ["progressBar" => true]);
        return redirect()->route('outlet-accounts.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $outletAccount = OutletAccount::findOrFail(decrypt($id));
        $outletAccountItem = ChartOfAccount::where('id', $outletAccount->coa_id);
        $outletAccountItem->delete();
        Toastr::success('Outlet Account has been deleted successfully.');
        return redirect()->route('outlet-accounts.index');
    }

    public function changeStatus(Request $request, $id)
    {
        $outletAccount = OutletAccount::find($id);
        $outletAccount->update([
            'status' => $request->status
        ]);
        return response()->successRedirect('Info Updated!', 'outlet-accounts.index');
    }
}
