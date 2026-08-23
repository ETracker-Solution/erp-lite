<?php

namespace App\Http\Controllers;

use App\Models\ChartOfAccount;
use App\Models\Outlet;
use App\Models\OutletAccount;
use App\Models\OutletTransactionConfig;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OuteletConfigController extends Controller
{
    /**
     * Show the form for creating / editing outlet payment configs.
     */
    public function create()
    {
        $paymentTypes = outletAccountTypeOptions();

        $outlets = Outlet::query()
            ->select(['id', 'name', 'status'])
            ->with([
                'transactionConfigs:id,outlet_id,type,coa_id',
                'outletAccounts:id,outlet_id,coa_id,status',
            ])
            ->orderBy('name')
            ->get();

        // One ledger query for the whole page (was N outlets × M types before).
        $ledgers = ChartOfAccount::query()
            ->select(['id', 'name', 'is_bank_cash', 'root_account_type'])
            ->where('type', 'ledger')
            ->where('status', 'active')
            ->orderBy('name')
            ->get()
            ->map(function ($ledger) {
                $ledger->display_name = $ledger->id . '. ' . $ledger->name
                    . ' (' . $ledger->root_account_type . ')';
                return $ledger;
            });

        $bankCashLedgers = $ledgers->where('is_bank_cash', 'yes')->values();

        $rows = $outlets->map(function (Outlet $outlet) use ($paymentTypes) {
            $configs = $outlet->transactionConfigs->pluck('coa_id', 'type');
            $linkedCoaIds = $outlet->outletAccounts
                ->where('status', 'active')
                ->pluck('coa_id')
                ->unique()
                ->values()
                ->all();

            $values = [];
            $missing = 0;
            foreach ($paymentTypes as $type) {
                $coaId = $configs[$type] ?? null;
                $values[$type] = $coaId;
                if (!$coaId) {
                    $missing++;
                }
            }

            return [
                'id' => $outlet->id,
                'name' => $outlet->name,
                'status' => $outlet->status,
                'values' => $values,
                'linked_coa_ids' => $linkedCoaIds,
                'missing' => $missing,
            ];
        });

        return view('outlet_config.create', [
            'paymentTypes' => $paymentTypes,
            'rows' => $rows,
            'ledgers' => $ledgers,
            'bankCashLedgers' => $bankCashLedgers,
        ]);
    }

    /**
     * Upsert OTC rows and keep outlet_accounts in sync for selected ledgers.
     */
    public function store(Request $request)
    {
        $request->validate([
            'settings' => 'required|array',
        ]);

        $allowedTypes = outletAccountTypeOptions();

        DB::beginTransaction();
        try {
            foreach ($request->settings as $outletId => $items) {
                if (!is_array($items)) {
                    continue;
                }

                foreach ($items as $type => $coaId) {
                    if (!in_array($type, $allowedTypes, true)) {
                        continue;
                    }

                    // Empty selection skips / clears nothing — keep existing.
                    if ($coaId === null || $coaId === '') {
                        continue;
                    }

                    OutletTransactionConfig::updateOrCreate(
                        [
                            'outlet_id' => $outletId,
                            'type' => $type,
                        ],
                        [
                            'coa_id' => $coaId,
                        ]
                    );

                    // Align with outlet-accounts: assigned payment COA must be linked.
                    OutletAccount::firstOrCreate(
                        [
                            'outlet_id' => $outletId,
                            'coa_id' => $coaId,
                        ],
                        [
                            'status' => 'active',
                        ]
                    );
                }
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Outlet config store failed: ' . $e->getMessage());
            Toastr::error('Something went wrong while saving configs.', '', ["progressBar" => true]);
            return back();
        }

        Toastr::success('Outlet payment configurations updated successfully!.', '', ["progressBar" => true]);
        return redirect()->route('outlet-configs.create');
    }

    public function index()
    {
        return redirect()->route('outlet-configs.create');
    }
}
