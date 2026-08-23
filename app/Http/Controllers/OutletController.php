<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreOutletRequest;
use App\Http\Requests\UpdateOutletRequest;
use App\Models\Outlet;
use App\Models\OutletTransactionConfig;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class OutletController extends Controller
{
    public function index()
    {
        if (\request()->ajax()) {
            return DataTables::of(Outlet::query())
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
                ->rawColumns(['action', 'status'])
                ->make(true);
        }
        return view('outlet.index');
    }

    public function create()
    {
        $serial_count = Outlet::latest()->first() ? Outlet::latest()->first()->id : 0;
        $serial_no = $serial_count + 1;
        $accountTypes = outletAccountTypeOptions();
        $defaultAccountTypes = defaultOutletAccountTypes();
        return view('outlet.create', compact('serial_no', 'accountTypes', 'defaultAccountTypes'));
    }

    public function store(StoreOutletRequest $request)
    {
        $validated = $request->validated();

        $types = $request->input('account_types', defaultOutletAccountTypes());
        if (!is_array($types) || count($types) === 0) {
            $types = defaultOutletAccountTypes();
        }
        $types = array_values(array_intersect(outletAccountTypeOptions(), $types));
        if (count($types) === 0) {
            $types = defaultOutletAccountTypes();
        }

        DB::beginTransaction();
        try {
            $outlet = Outlet::create($validated);

            provisionOutletAccounts($outlet, [
                'types' => $types,
                'petty_cash' => $request->boolean('petty_cash', true),
                'fg_store' => true,
            ]);

            DB::commit();
        } catch (\Exception $error) {
            DB::rollBack();
            \Log::error('Outlet create failed: ' . $error->getMessage());
            Toastr::info('Something went wrong!.', '', ["progressBar" => true]);
            return back()->withInput();
        }
        Toastr::success('Outlet Created Successfully!.', '', ["progressBar" => true]);
        return redirect()->route('outlets.index');
    }

    public function show(Outlet $outlet)
    {
        //
    }

    public function edit($id)
    {
        $outlet = Outlet::findOrFail(decrypt($id));
        $accountTypes = outletAccountTypeOptions();
        $defaultAccountTypes = defaultOutletAccountTypes();
        $assignedTypes = OutletTransactionConfig::query()
            ->where('outlet_id', $outlet->id)
            ->pluck('type')
            ->toArray();
        return view('outlet.create', compact('outlet', 'accountTypes', 'defaultAccountTypes', 'assignedTypes'));
    }

    public function update(UpdateOutletRequest $request, $id)
    {
        $validated = $request->validated();
        DB::beginTransaction();
        try {
            $outlet = Outlet::findOrFail($id);
            $outlet->update($validated);

            // Backfill any missing payment accounts without wiping existing ones.
            $types = $request->input('account_types', defaultOutletAccountTypes());
            if (!is_array($types) || count($types) === 0) {
                $types = defaultOutletAccountTypes();
            }
            $types = array_values(array_intersect(outletAccountTypeOptions(), $types));

            provisionOutletAccounts($outlet, [
                'types' => $types,
                'petty_cash' => $request->boolean('petty_cash', true),
                'fg_store' => true,
            ]);

            DB::commit();
        } catch (\Exception $error) {
            DB::rollBack();
            \Log::error('Outlet update failed: ' . $error->getMessage());
            Toastr::info('Something went wrong!.', '', ["progressBar" => true]);
            return back()->withInput();
        }
        Toastr::success('Outlet Updated Successfully!.', '', ["progressBar" => true]);
        return redirect()->route('outlets.index');
    }

    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $outlet = Outlet::findOrFail(decrypt($id));
            OutletTransactionConfig::where('outlet_id', $outlet->id)->delete();
            $outlet->delete();
            DB::commit();
        } catch (\Exception $error) {
            DB::rollBack();
            Toastr::info('Something went wrong!.', '', ["progressBar" => true]);
            return back();
        }
        Toastr::success('Outlet Deleted Successfully!.', '', ["progressBar" => true]);
        return redirect()->route('outlets.index');
    }
}
