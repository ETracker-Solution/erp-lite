<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreOutletRequest;
use App\Http\Requests\UpdateOutletRequest;
use App\Models\ChartOfAccount;
use App\Models\Outlet;
use App\Models\OutletTransactionConfig;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class OutletController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $outlets = Outlet::all();
        if (\request()->ajax()) {
            return DataTables::of($outlets)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    return view('outlet.action', compact('row'));
                })
                ->editColumn('type', function ($row) {
                    return strtoupper($row->type);
                })
                ->editColumn('status', function ($row) {
                    return showStatus($row->status);
                })
                ->addColumn('created_at', function ($row) {
                    return view('common.created_at', compact('row'));
                })
                ->rawColumns(['action','status'])
                ->make(true);
        }
        return view('outlet.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $serial_count = Outlet::latest()->first() ? Outlet::latest()->first()->id : 0;
        $serial_no = $serial_count + 1;
        return view('outlet.create',compact('serial_no'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreOutletRequest $request)
    {
        $validated = $request->validated();

        DB::beginTransaction();
        try {
            $outlet = Outlet::create($validated);

            $methods = ['Cash', 'Bkash'];
                foreach ($methods as $method) {
                    $exists = ChartOfAccount::where('name', $method)->first();
                    if (!$exists) {
                        $exists = ChartOfAccount::create([
                            'name' => $method,
                            'type' => 'group',
                            'account_type' => 'debit',
                            'root_account_type' => 'as',
                            'parent_id' => $exists->id
                        ]);
                    }
                    $account = $exists->subChartOfAccounts()->create([
                        'name' => $method . ' ' . $outlet->name,
                        'type' => 'ledger',
                        'account_type' => 'debit',
                        'root_account_type' => 'as',
                    ]);

                    OutletTransactionConfig::create([
                        'outlet_id' => $outlet->id,
                        'coa_id' => $account->id,
                        'type' => $method
                    ]);
                }
            DB::commit();
        } catch (\Exception $error) {
            DB::rollBack();
            Toastr::info('Something went wrong!.', '', ["progressBar" => true]);
            return back();
        }
        Toastr::success('Store Created Successfully!.', '', ["progressBar" => true]);
        return redirect()->route('outlets.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(Outlet $outlet)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $outlet = Outlet::findOrFail(decrypt($id));
        return view('outlet.create', compact('outlet'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateOutletRequest $request, $id)
    {
        $validated = $request->validated();
        DB::beginTransaction();
        try {
            Outlet::findOrFail($id)->update($validated);
            DB::commit();
        } catch (\Exception $error) {
            DB::rollBack();
            Toastr::info('Something went wrong!.', '', ["progressBar" => true]);
            return back();
        }
        Toastr::success('Outlet Updated Successfully!.', '', ["progressBar" => true]);
        return redirect()->route('outlets.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $outletTrans = OutletTransactionConfig::where('outlet_id',decrypt($id))->get();
            foreach($outletTrans as $outletTran) {
                $outletTran->coa->delete();
                $outletTran->delete();
            }
            $outlet = Outlet::findOrFail(decrypt($id));
            $outlet->delete();
            DB::commit();
        } catch (\Exception $error) {
            DB::rollBack();
            // return $error;
            Toastr::info('Something went wrong!.', '', ["progressBar" => true]);
            return back();
        }
        Toastr::success('Outlet Deleted Successfully!.', '', ["progressBar" => true]);
        return redirect()->route('outlets.index');
    }
}
