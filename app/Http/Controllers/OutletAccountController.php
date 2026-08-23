<?php

namespace App\Http\Controllers;

use App\Models\ChartOfAccount;
use App\Models\Outlet;
use App\Models\OutletAccount;
use App\Models\OutletTransactionConfig;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class OutletAccountController extends Controller
{
    public function index()
    {
        if (request()->ajax()) {
            $query = OutletAccount::query()
                ->with([
                    'outlet:id,name',
                    'coa:id,name,is_bank_cash,default_type',
                ])
                ->latest('id');

            if (request()->filled('outlet_id')) {
                $query->where('outlet_id', request('outlet_id'));
            }

            return DataTables::of($query)
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
                ->addColumn('type', function ($row) {
                    return OutletTransactionConfig::query()
                        ->where('outlet_id', $row->outlet_id)
                        ->where('coa_id', $row->coa_id)
                        ->value('type') ?: '—';
                })
                ->rawColumns(['action', 'status'])
                ->make(true);
        }

        $outlets = Outlet::query()->select(['id', 'name'])->orderBy('name')->get();
        return view('outlet_account.index', compact('outlets'));
    }

    public function create()
    {
        $outlets = Outlet::query()->select(['id', 'name'])->orderBy('name')->get();
        $chartOfAccounts = ChartOfAccount::query()
            ->select(['id', 'name'])
            ->where(['root_account_type' => 'as', 'type' => 'group'])
            ->orderBy('name')
            ->get();
        $existingLedgers = ChartOfAccount::query()
            ->select(['id', 'name'])
            ->where(['type' => 'ledger', 'is_bank_cash' => 'yes', 'status' => 'active'])
            ->orderBy('name')
            ->get();
        $accountTypes = outletAccountTypeOptions();

        return view('outlet_account.create', compact(
            'outlets',
            'chartOfAccounts',
            'existingLedgers',
            'accountTypes'
        ));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'outlet_id' => 'required|exists:outlets,id',
            'mode' => 'nullable|in:create,link',
            'name' => 'required_if:mode,create|nullable|string|max:191',
            'coa_id' => 'required_if:mode,create|nullable|exists:chart_of_accounts,id',
            'existing_coa_id' => 'required_if:mode,link|nullable|exists:chart_of_accounts,id',
            'type' => 'required|string|max:50',
            'status' => 'nullable|in:active,inactive',
        ]);

        DB::beginTransaction();
        try {
            $mode = $validated['mode'] ?? 'create';

            if ($mode === 'link' && !empty($validated['existing_coa_id'])) {
                $coaId = (int) $validated['existing_coa_id'];
            } else {
                $parent = ChartOfAccount::query()->findOrFail($validated['coa_id']);
                $coa = ChartOfAccount::create([
                    'name' => $validated['name'],
                    'status' => 'active',
                    'type' => 'ledger',
                    'account_type' => $parent->account_type ?: 'debit',
                    'is_bank_cash' => 'yes',
                    'root_account_type' => $parent->root_account_type ?: 'as',
                    'parent_id' => $parent->id,
                    'created_by' => auth()->id(),
                ]);
                $coaId = $coa->id;
            }

            $exists = OutletAccount::query()
                ->where('outlet_id', $validated['outlet_id'])
                ->where('coa_id', $coaId)
                ->exists();
            if ($exists) {
                DB::rollBack();
                Toastr::warning('This account is already assigned to the outlet.');
                return back()->withInput();
            }

            OutletAccount::create([
                'outlet_id' => $validated['outlet_id'],
                'coa_id' => $coaId,
                'status' => $validated['status'] ?? 'active',
            ]);

            OutletTransactionConfig::firstOrCreate(
                [
                    'type' => $validated['type'],
                    'outlet_id' => $validated['outlet_id'],
                ],
                ['coa_id' => $coaId]
            );

            DB::commit();
        } catch (\Exception $error) {
            DB::rollBack();
            \Log::error('Outlet account store failed: ' . $error->getMessage());
            Toastr::info('Something went wrong!.', '', ["progressBar" => true]);
            return back()->withInput();
        }

        Toastr::success('Outlet Account has been created successfully.');
        return redirect()->route('outlet-accounts.index');
    }

    public function edit(string $id)
    {
        $outletAccount = OutletAccount::with(['coa', 'outlet'])->findOrFail(decrypt($id));
        $outlets = Outlet::query()->select(['id', 'name'])->orderBy('name')->get();
        $chartOfAccounts = ChartOfAccount::query()
            ->select(['id', 'name'])
            ->where(['root_account_type' => 'as', 'type' => 'group'])
            ->orderBy('name')
            ->get();
        $accountTypes = outletAccountTypeOptions();
        $currentType = OutletTransactionConfig::query()
            ->where('outlet_id', $outletAccount->outlet_id)
            ->where('coa_id', $outletAccount->coa_id)
            ->value('type');

        return view('outlet_account.create', compact(
            'outletAccount',
            'outlets',
            'chartOfAccounts',
            'accountTypes',
            'currentType'
        ));
    }

    public function update(Request $request, string $id)
    {
        $validated = $request->validate([
            'status' => 'required|in:active,inactive',
            'name' => 'nullable|string|max:191',
            'type' => 'nullable|string|max:50',
        ]);

        DB::beginTransaction();
        try {
            $outletAccount = OutletAccount::findOrFail(decrypt($id));
            $outletAccount->update(['status' => $validated['status']]);

            if (!empty($validated['name']) && $outletAccount->coa) {
                $outletAccount->coa->update(['name' => $validated['name']]);
            }

            if (!empty($validated['type'])) {
                $otc = OutletTransactionConfig::query()
                    ->where('outlet_id', $outletAccount->outlet_id)
                    ->where('coa_id', $outletAccount->coa_id)
                    ->first();
                if ($otc) {
                    $otc->update(['type' => $validated['type']]);
                } else {
                    OutletTransactionConfig::create([
                        'outlet_id' => $outletAccount->outlet_id,
                        'coa_id' => $outletAccount->coa_id,
                        'type' => $validated['type'],
                    ]);
                }
            }

            DB::commit();
        } catch (\Exception $error) {
            DB::rollBack();
            Toastr::info('Something went wrong!.', '', ["progressBar" => true]);
            return back();
        }
        Toastr::success('Outlet Account updated successfully!.', '', ["progressBar" => true]);
        return redirect()->route('outlet-accounts.index');
    }

    public function destroy(string $id)
    {
        DB::beginTransaction();
        try {
            $outletAccount = OutletAccount::findOrFail(decrypt($id));
            OutletTransactionConfig::query()
                ->where('outlet_id', $outletAccount->outlet_id)
                ->where('coa_id', $outletAccount->coa_id)
                ->delete();
            $outletAccount->delete();
            DB::commit();
        } catch (\Exception $error) {
            DB::rollBack();
            Toastr::info('Something went wrong!.', '', ["progressBar" => true]);
            return back();
        }
        Toastr::success('Outlet Account has been removed successfully.');
        return redirect()->route('outlet-accounts.index');
    }

    public function changeStatus(Request $request, $id)
    {
        $outletAccount = OutletAccount::findOrFail($id);
        $outletAccount->update([
            'status' => $request->status,
        ]);
        return response()->successRedirect('Info Updated!', 'outlet-accounts.index');
    }

    /**
     * Sync missing default payment accounts for an outlet (AJAX / button).
     */
    public function sync(Request $request)
    {
        $validated = $request->validate([
            'outlet_id' => 'required|exists:outlets,id',
        ]);

        $outlet = Outlet::findOrFail($validated['outlet_id']);
        $result = provisionOutletAccounts($outlet);

        $msg = sprintf(
            'Synced: %d ledger(s), %d link(s), %d payment method(s)',
            $result['ledgers'] ?? 0,
            $result['outlet_accounts'] ?? 0,
            $result['otc'] ?? 0
        );
        if (!empty($result['duplicates_removed'])) {
            $msg .= sprintf(', removed %d duplicate link(s)', $result['duplicates_removed']);
        }
        Toastr::success($msg . '.');
        return back();
    }
}
