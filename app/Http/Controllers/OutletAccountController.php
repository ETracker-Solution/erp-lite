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
        if (\request()->ajax()) {
            $outletAccounts = OutletAccount::with('outlet', 'coa');
            $outletAccounts = $this->filter($outletAccounts);

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
                ->rawColumns(['action', 'status'])
                ->make(true);
        }

        $outlets = \App\Models\Outlet::all();
        return view('outlet_account.index', compact('outlets'));
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
                $data->whereBetween('created_at', [$dateRange[0] . ' 00:00:00', $dateRange[1] . ' 23:59:59']);
            } elseif (isset($dateRange[0])) {
                $data->whereDate('created_at', $dateRange[0]);
            }
        }

        if (request()->filled('outlet_id')) {
            $data->where('outlet_id', request('outlet_id'));
        }

        if (request()->filled('coa_name')) {
            $data->whereHas('coa', function ($query) {
                $query->where('name', 'like', '%' . request('coa_name') . '%');
            });
        }

        if (request()->filled('status')) {
            $data->where('status', request('status'));
        }

        return $data->latest();
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $outlets = \App\Models\Outlet::all();
        $chartOfAccounts = \App\Models\ChartOfAccount::where(['root_account_type'=>'as','type'=>'group'])->get();
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
