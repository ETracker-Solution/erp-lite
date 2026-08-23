<?php


use App\Models\AccountTransaction;
use App\Models\ChartOfAccount;
use App\Models\DeliveryCashTransfer;
use App\Models\OthersOutletSale;
use App\Models\OutletAccount;
use App\Models\Sale;
use Illuminate\Support\Facades\DB;


//accounting integration
function addAccountsTransaction($doc_type, $doc, $debit_account_id, $credit_account_id): void
{

    $data = [
        [
            'date' => $doc->date,
            'type' => 'debit',
            'transaction_type' => 1,
            'amount' => $doc->amount,
            'transaction_id' => $doc->transaction_id ?? '000',
            'payee_name' => $doc->payee_name ?? null,
            'narration' => $doc->narration ?? null,
            'reference_no' => $doc->reference_no ?? null,
            'chart_of_account_id' => $debit_account_id,
            'doc_id' => $doc->id,
            'doc_type' => $doc_type,
        ],
        [
            'date' => $doc->date,
            'type' => 'credit',
            'transaction_type' => -1,
            'amount' => $doc->amount,
            'transaction_id' => $doc->transaction_id ?? '000',
            'payee_name' => $doc->payee_name ?? null,
            'narration' => $doc->narration ?? null,
            'reference_no' => $doc->reference_no ?? null,
            'chart_of_account_id' => $credit_account_id,
            'doc_id' => $doc->id,
            'doc_type' => $doc_type,
        ]
    ];

    DB::table('account_transactions')->insert($data);
}


function resolveGLId(?string $settingKey, int $fallback): int
{
    $id = $settingKey ? getSettingValue($settingKey) : null;
    $id = ($id !== null && $id !== '') ? (int) $id : $fallback;

    static $ledgerIds = null;
    if ($ledgerIds === null) {
        try {
            $ledgerIds = array_flip(
                ChartOfAccount::where('type', 'ledger')->pluck('id')->map(fn ($v) => (int) $v)->all()
            );
        } catch (\Throwable $e) {
            $ledgerIds = [];
        }
    }

    if ($ledgerIds && !isset($ledgerIds[$id])) {
        return isset($ledgerIds[$fallback]) ? $fallback : $id;
    }

    return $id;
}

function getOpeningBalanceOfEquityGLId()
{
    return resolveGLId('retained_earning', 47);
}

function getRMInventoryGLId()
{
    return resolveGLId('goods_purchase_bill_debit_account', 15);
}

function getAccountsPayableGLId()
{
    return resolveGLId('goods_purchase_bill_credit_account', 22);
}

function getAccountsReceiveableGLId()
{
    return resolveGLId('sales_account_receivable_account', 18);
}

function getFGInventoryGLId()
{
    return resolveGLId('sales_fg_inventory_account', 16);
}

function getIncomeFromSalesGLId()
{
    return resolveGLId('income_from_sales_account', 35);
}

function getCOGSGLId()
{
    return resolveGLId('cogs_account', 43);
}

function getCashGLID()
{
    return resolveGLId('sales_cash_account', 13);
}

function getInventoryAdjustmentGLId()
{
    return resolveGLId('inventory_adjustment_account', 52);
}

function getWIPGLId()
{
    return resolveGLId('fg_production_credit_account', 17);
}

function getAllLedgers()
{
    return ChartOfAccount::where('type', 'ledger')->select(DB::raw('id,name,CONCAT(id,". ",name, " (",root_account_type,")") as display_name'))->get();
}

function outletTransactionAccount($outlet_id, $account_type = 'Cash')
{
    static $cache = [];
    $key = $outlet_id . ':' . $account_type;
    if (array_key_exists($key, $cache)) {
        return $cache[$key];
    }

    if (!array_key_exists($outlet_id . ':__loaded', $cache)) {
        $rows = \App\Models\OutletTransactionConfig::where('outlet_id', $outlet_id)
            ->pluck('coa_id', 'type');
        foreach ($rows as $type => $coaId) {
            $cache[$outlet_id . ':' . $type] = $coaId;
        }
        $cache[$outlet_id . ':__loaded'] = true;
    }

    $cache[$key] = $cache[$key] ?? false;
    return $cache[$key];
}

function getDiscountGLID()
{
    return resolveGLId('discount_account', 50);
}

function getRewardGLID()
{
    return resolveGLId('reward_account', 51);
}

function getCustomersReceiveableGLId()
{
    return resolveGLId('customers_receivable_account', 58);
}

function salePaymentMethodOptions(): array
{
    return [
        ['value' => 'cash', 'label' => 'Cash'],
        ['value' => 'bkash', 'label' => 'Bkash'],
        ['value' => 'nagad', 'label' => 'Nagad'],
        ['value' => 'DBBL', 'label' => 'DBBL'],
        ['value' => 'UCB', 'label' => 'UCB'],
        ['value' => 'rocket', 'label' => 'Rocket'],
        ['value' => 'upay', 'label' => 'Upay'],
        ['value' => 'nexus', 'label' => 'Nexus'],
        ['value' => 'pbl', 'label' => 'PBL POS'],
        ['value' => 'PBLQR', 'label' => 'PBL QR'],
        ['value' => 'FOODIE', 'label' => 'FOODIE'],
        ['value' => 'due', 'label' => 'Due Sale'],
        ['value' => 'FoodPanda', 'label' => 'Food Panda'],
        ['value' => 'CityBank', 'label' => 'City Bank'],
        ['value' => 'point', 'label' => 'Redeem Point'],
    ];
}

function salePaymentDebitAccount(string $method, $outlet_id)
{
    $types = [
        'FOODIE' => 'FOODIE',
        'PBLQR' => 'PBLQR',
        'nexus' => 'Nexus',
        'pbl' => 'PBL',
        'due' => 'Due',
        'FoodPanda' => 'FoodPanda',
        'CityBank' => 'CityBank',
        'upay' => 'Upay',
        'rocket' => 'Rocket',
        'DBBL' => 'DBBL',
        'UCB' => 'UCB',
        'nagad' => 'Nagad',
        'bkash' => 'Bkash',
        'cash' => 'Cash',
    ];

    if (!isset($types[$method])) {
        return false;
    }

    $type = $types[$method];
    return $type === 'Cash'
        ? outletTransactionAccount($outlet_id)
        : outletTransactionAccount($outlet_id, $type);
}

function postSalePaymentTransaction($sale, string $method, $outlet_id, $customer_id = null): void
{
    if ($method === 'exchange' || (float) $sale->amount <= 0) {
        return;
    }

    if ($method === 'point') {
        redeemPoint($sale->id, $customer_id, $sale->amount);
        addAccountsTransaction('POS', $sale, getRewardGLID(), getAccountsReceiveableGLId());
        return;
    }

    $debitAccount = salePaymentDebitAccount($method, $outlet_id);
    if ($debitAccount) {
        addAccountsTransaction('POS', $sale, $debitAccount, getAccountsReceiveableGLId());
    }
}

function addCustomerTransaction($item, $transaction_type = 1)
{
    \App\Models\CustomerTransaction::query()->create([
        'customer_id' => $item->customer_id,
        'doc_type' => 'POS',
        'doc_id' => $item->id,
        'amount' => $item->amount,
        'date' => $item->date,
        'transaction_type' => $transaction_type,
        'chart_of_account_id' => getAccountsReceiveableGLId(),
        'description' => 'Product Sales',
    ]);
}


function accountBalanceForOtherOutletSales($chart_of_account_id){
    $balances = accountBalancesForOtherOutletSales([$chart_of_account_id]);
    return $balances[$chart_of_account_id] ?? 0;
}

/**
 * Batch version to avoid N+1 queries when calculating balances for multiple COAs.
 * COAs belonging to the same outlet share the expensive sale-lookup work.
 */
function accountBalancesForOtherOutletSales(array $chart_of_account_ids): array
{
    $chart_of_account_ids = array_values(array_unique(array_filter($chart_of_account_ids)));
    $balances = array_fill_keys($chart_of_account_ids, 0);

    if (empty($chart_of_account_ids)) {
        return $balances;
    }

    $outletAccounts = OutletAccount::whereIn('coa_id', $chart_of_account_ids)
        ->get(['coa_id', 'outlet_id'])
        ->groupBy('outlet_id');

    foreach ($outletAccounts as $outletId => $accounts) {
        $coaIds = $accounts->pluck('coa_id')->unique()->values()->all();

        $alreadyTransferred = DeliveryCashTransfer::where('from_outlet', $outletId)
            ->pluck('other_outlet_sale_id')
            ->toArray();

        $otherOutletSalesQuery = OthersOutletSale::where('outlet_id', '!=', $outletId)
            ->where('delivery_point_id', $outletId)
            ->where('payment_status', 'paid');

        if (!empty($alreadyTransferred)) {
            $otherOutletSalesQuery->whereNotIn('id', $alreadyTransferred);
        }

        $invoiceNumbers = $otherOutletSalesQuery->pluck('invoice_number')->toArray();
        if (empty($invoiceNumbers)) {
            continue;
        }

        $originalSaleIds = Sale::whereIn('invoice_number', $invoiceNumbers)->pluck('id')->toArray();
        if (empty($originalSaleIds)) {
            continue;
        }

        $sums = AccountTransaction::whereIn('chart_of_account_id', $coaIds)
            ->where('doc_type', 'POS')
            ->whereIn('doc_id', $originalSaleIds)
            ->select('chart_of_account_id', DB::raw('SUM(amount * transaction_type) as balance'))
            ->groupBy('chart_of_account_id')
            ->pluck('balance', 'chart_of_account_id');

        foreach ($coaIds as $coaId) {
            $balances[$coaId] = $sums[$coaId] ?? 0;
        }
    }

    return $balances;
}


/**
 * Payment method types for outlet COA + OTC provisioning.
 * Driven by Chart of Accounts groups tagged default_type = payment_method.
 * Falls back to legacy defaults if none are tagged yet.
 */
function outletAccountTypeOptions(): array
{
    $fromDb = ChartOfAccount::query()
        ->where('type', 'group')
        ->where('status', 'active')
        ->where('default_type', 'payment_method')
        ->orderBy('name')
        ->pluck('name')
        ->filter()
        ->values()
        ->all();

    if (count($fromDb) === 0) {
        $fromDb = [
            'Cash', 'Bkash', 'Nagad', 'Bank', 'Rocket', 'Upay',
            'DBBL', 'UCB', 'Nexus', 'PBL', 'Due', 'FoodPanda',
            'CityBank', 'PBLQR', 'FOODIE',
        ];
    }

    return array_values(array_unique($fromDb));
}

/**
 * Default methods pre-selected when creating an outlet.
 */
function defaultOutletAccountTypes(): array
{
    $preferred = ['Cash', 'Bkash', 'Nagad', 'Bank', 'Rocket', 'Upay'];
    $available = outletAccountTypeOptions();
    $selected = array_values(array_intersect($preferred, $available));

    return count($selected) ? $selected : array_slice($available, 0, 6);
}

/**
 * Create/link ledgers + OTC/OA for every outlet (and optional HO office ledger)
 * for one payment-method group (e.g. "Shaj").
 */
function provisionPaymentMethodEverywhere(string $method, array $options = []): array
{
    $method = trim($method);
    $withOffice = array_key_exists('office', $options) ? (bool) $options['office'] : true;
    $withOutlets = array_key_exists('outlets', $options) ? (bool) $options['outlets'] : true;

    $result = [
        'outlets' => 0,
        'ledgers' => 0,
        'outlet_accounts' => 0,
        'otc' => 0,
        'office_ledger' => 0,
    ];

    if ($method === '') {
        return $result;
    }

    if ($withOutlets) {
        $outlets = \App\Models\Outlet::query()
            ->where('status', 'active')
            ->orderBy('id')
            ->get(['id', 'name', 'status']);

        foreach ($outlets as $outlet) {
            $created = provisionOutletAccounts($outlet, [
                'types' => [$method],
                'petty_cash' => false,
                'fg_store' => false,
            ]);
            $result['outlets']++;
            $result['ledgers'] += $created['ledgers'] ?? 0;
            $result['outlet_accounts'] += $created['outlet_accounts'] ?? 0;
            $result['otc'] += $created['otc'] ?? 0;
        }
    }

    if ($withOffice) {
        $group = ChartOfAccount::query()
            ->where('name', $method)
            ->where('type', 'group')
            ->first();

        if ($group) {
            $officeName = $method . ' Office';
            $office = ChartOfAccount::query()
                ->where('type', 'ledger')
                ->where(function ($q) use ($officeName, $method) {
                    $q->where('name', $officeName)
                        ->orWhere('name', $method . ' Head Office')
                        ->orWhere('name', 'Office ' . $method);
                })
                ->orderBy('id')
                ->first();

            if (!$office) {
                $group->subChartOfAccounts()->create([
                    'name' => $officeName,
                    'type' => 'ledger',
                    'account_type' => $group->account_type ?: 'debit',
                    'root_account_type' => $group->root_account_type ?: 'as',
                    'is_bank_cash' => 'yes',
                    'status' => 'active',
                    'default_type' => 'office_account',
                    'created_by' => auth()->id(),
                ]);
                $result['office_ledger'] = 1;
                $result['ledgers']++;
            } else {
                $office->fill([
                    'is_bank_cash' => 'yes',
                    'parent_id' => $office->parent_id ?: $group->id,
                    'status' => $office->status ?: 'active',
                    'default_type' => $office->default_type ?: 'office_account',
                ])->save();
            }
        }
    }

    return $result;
}

function officeAccountQuery()
{
    return ChartOfAccount::query()
        ->where('type', 'ledger')
        ->where('status', 'active')
        ->where('is_bank_cash', 'yes')
        ->where('default_type', 'office_account');
}

function transferableOutletCoaConstraint($query)
{
    return $query->where(function ($q) {
        $q->whereNull('default_type')
            ->orWhere('default_type', '')
            ->orWhereNotIn('default_type', ['office_account', 'petty_cash']);
    });
}

/**
 * Canonical ledger name for an outlet payment method.
 */
function outletPaymentLedgerName(string $method, string $outletName): string
{
    return trim($method) . ' ' . trim($outletName);
}

/**
 * Historical / alternate ledger names used in older data.
 */
function outletPaymentLedgerNameVariants(string $method, string $outletName): array
{
    $method = trim($method);
    $outletName = trim($outletName);

    return array_values(array_unique(array_filter([
        $method . ' ' . $outletName,          // Upay CT-9 (canonical)
        $outletName . '-' . $method,          // CT-9-Upay
        $outletName . '- ' . $method,         // CT-9- Upay
        $outletName . ' - ' . $method,        // CT-9 - Upay
        $outletName . ' ' . $method,          // CT-9 Upay
        $method . '-' . $outletName,          // Upay-CT-9
        $method . ' - ' . $outletName,        // Upay - CT-9
    ])));
}

/**
 * Reuse an existing payment ledger for this outlet+method when possible.
 * Prefer OTC mapping, then linked outlet accounts, then name variants.
 */
function findExistingOutletPaymentLedger(int $outletId, string $method, string $outletName): ?\App\Models\ChartOfAccount
{
    $method = trim($method);

    // 1) Already configured in OTC (case-insensitive type)
    $otc = \App\Models\OutletTransactionConfig::query()
        ->where('outlet_id', $outletId)
        ->whereRaw('LOWER(type) = ?', [strtolower($method)])
        ->first();

    if ($otc && $otc->coa_id) {
        $coa = ChartOfAccount::query()
            ->where('id', $otc->coa_id)
            ->where('type', 'ledger')
            ->first();
        if ($coa) {
            return $coa;
        }
    }

    $variants = outletPaymentLedgerNameVariants($method, $outletName);
    $linkedIds = \App\Models\OutletAccount::query()
        ->where('outlet_id', $outletId)
        ->pluck('coa_id');

    // 2) Already linked to this outlet under a known name / method pattern
    if ($linkedIds->isNotEmpty()) {
        $ledger = ChartOfAccount::query()
            ->where('type', 'ledger')
            ->whereIn('id', $linkedIds)
            ->where(function ($q) use ($variants, $method) {
                $q->whereIn('name', $variants)
                    ->orWhere('name', 'like', $method . ' %')
                    ->orWhere('name', 'like', '%-' . $method)
                    ->orWhere('name', 'like', '% ' . $method);
            })
            ->orderBy('id')
            ->first();
        if ($ledger) {
            return $ledger;
        }
    }

    // 3) Orphan ledger with a known variant name (link it instead of recreating)
    return ChartOfAccount::query()
        ->where('type', 'ledger')
        ->whereIn('name', $variants)
        ->orderBy('id')
        ->first();
}


/**
 * Remove orphan outlet_account rows that duplicate a payment method ledger
 * already mapped via OTC / canonical account (blank-type sync leftovers).
 */
function cleanupDuplicateOutletPaymentAccounts(\App\Models\Outlet $outlet, array $types): int
{
    $removed = 0;
    $otcCoaIds = \App\Models\OutletTransactionConfig::query()
        ->where('outlet_id', $outlet->id)
        ->pluck('coa_id')
        ->filter()
        ->map(fn ($id) => (int) $id)
        ->all();

    foreach ($types as $method) {
        $method = trim((string) $method);
        if ($method === '') {
            continue;
        }

        $keeper = findExistingOutletPaymentLedger($outlet->id, $method, $outlet->name);
        if (!$keeper) {
            continue;
        }

        $variants = outletPaymentLedgerNameVariants($method, $outlet->name);
        $duplicateOas = \App\Models\OutletAccount::query()
            ->where('outlet_id', $outlet->id)
            ->where('coa_id', '!=', $keeper->id)
            ->whereHas('coa', function ($q) use ($variants, $method) {
                $q->where('type', 'ledger')
                    ->where(function ($q2) use ($variants, $method) {
                        $q2->whereIn('name', $variants)
                            ->orWhere('name', 'like', $method . ' %')
                            ->orWhere('name', 'like', '%-' . $method)
                            ->orWhere('name', 'like', '% ' . $method);
                    });
            })
            ->get();

        foreach ($duplicateOas as $oa) {
            // Never detach a COA still used by OTC for this outlet.
            if (in_array((int) $oa->coa_id, $otcCoaIds, true)) {
                continue;
            }
            $oa->delete();
            $removed++;
        }
    }

    return $removed;
}

/**
 * Ensure group + ledger COAs, outlet_accounts, OTC (and optional petty cash / FG store)
 * exist for an outlet. Idempotent — safe to re-run.
 */
function provisionOutletAccounts(\App\Models\Outlet $outlet, array $options = []): array
{
    $types = $options['types'] ?? defaultOutletAccountTypes();
    $withPettyCash = array_key_exists('petty_cash', $options)
        ? (bool) $options['petty_cash']
        : true;
    $withFgStore = array_key_exists('fg_store', $options)
        ? (bool) $options['fg_store']
        : true;

    $created = ['ledgers' => 0, 'outlet_accounts' => 0, 'otc' => 0];

    $assetsRootId = ChartOfAccount::query()
        ->whereNull('parent_id')
        ->where('root_account_type', 'as')
        ->where('type', 'group')
        ->value('id');

    $currentAssetsId = ChartOfAccount::query()
        ->where('name', 'Current Assets')
        ->where('type', 'group')
        ->value('id') ?: $assetsRootId;

    foreach ($types as $method) {
        $method = trim((string) $method);
        if ($method === '') {
            continue;
        }

        $group = ChartOfAccount::query()
            ->where('name', $method)
            ->where('type', 'group')
            ->first();

        if (!$group) {
            $group = ChartOfAccount::create([
                'name' => $method,
                'type' => 'group',
                'account_type' => 'debit',
                'root_account_type' => 'as',
                'is_bank_cash' => 'yes',
                'status' => 'active',
                'parent_id' => $currentAssetsId,
                'default_type' => null,
            ]);
        } elseif ($group->is_bank_cash !== 'yes') {
            $group->update(['is_bank_cash' => 'yes']);
        }

        $ledger = findExistingOutletPaymentLedger($outlet->id, $method, $outlet->name);

        if (!$ledger) {
            $ledger = $group->subChartOfAccounts()->create([
                'name' => outletPaymentLedgerName($method, $outlet->name),
                'type' => 'ledger',
                'account_type' => 'debit',
                'root_account_type' => 'as',
                'is_bank_cash' => 'yes',
                'status' => 'active',
                'default_type' => null,
                'created_by' => auth()->id(),
            ]);
            $created['ledgers']++;
        } else {
            $ledger->fill([
                'is_bank_cash' => 'yes',
                'parent_id' => $ledger->parent_id ?: $group->id,
                'status' => $ledger->status ?: 'active',
            ])->save();
        }

        $oa = \App\Models\OutletAccount::firstOrCreate(
            [
                'outlet_id' => $outlet->id,
                'coa_id' => $ledger->id,
            ],
            ['status' => 'active']
        );
        if ($oa->wasRecentlyCreated) {
            $created['outlet_accounts']++;
        }

        // Case-insensitive OTC lookup — never steal mapping from an existing typed account.
        $otc = \App\Models\OutletTransactionConfig::query()
            ->where('outlet_id', $outlet->id)
            ->whereRaw('LOWER(type) = ?', [strtolower($method)])
            ->first();

        if (!$otc) {
            \App\Models\OutletTransactionConfig::create([
                'outlet_id' => $outlet->id,
                'type' => $method,
                'coa_id' => $ledger->id,
            ]);
            $created['otc']++;
        } else {
            $updates = [];
            if ($otc->type !== $method) {
                $updates['type'] = $method; // normalize casing only
            }
            if (!$otc->coa_id) {
                $updates['coa_id'] = $ledger->id;
            }
            if (!empty($updates)) {
                $otc->update($updates);
            }
        }
    }

    // Drop duplicate outlet_account links created by earlier sync name mismatches
    // (e.g. "Upay CT-9" kept + orphan "CT-9-Upay" with blank type).
    $created['duplicates_removed'] = cleanupDuplicateOutletPaymentAccounts($outlet, $types);

    if ($withPettyCash) {
        $cashGroup = ChartOfAccount::query()
            ->where('name', 'Cash')
            ->where('type', 'group')
            ->first();

        if (!$cashGroup) {
            $cashGroup = ChartOfAccount::create([
                'name' => 'Cash',
                'type' => 'group',
                'account_type' => 'debit',
                'root_account_type' => 'as',
                'is_bank_cash' => 'yes',
                'status' => 'active',
                'parent_id' => $currentAssetsId,
            ]);
        }

        $pettyVariants = [
            $outlet->name . '- Petty Cash',
            $outlet->name . '-Petty Cash',
            $outlet->name . ' Petty Cash',
            'Petty Cash ' . $outlet->name,
        ];

        $petty = ChartOfAccount::query()
            ->where('type', 'ledger')
            ->where(function ($q) use ($pettyVariants, $outlet) {
                $q->whereIn('name', $pettyVariants)
                    ->orWhere(function ($q2) use ($outlet) {
                        $q2->where('default_type', 'petty_cash')
                            ->whereIn('id', function ($sub) use ($outlet) {
                                $sub->select('coa_id')
                                    ->from('outlet_accounts')
                                    ->where('outlet_id', $outlet->id);
                            });
                    });
            })
            ->orderBy('id')
            ->first();

        if (!$petty) {
            $petty = $cashGroup->subChartOfAccounts()->create([
                'name' => $outlet->name . '- Petty Cash',
                'type' => 'ledger',
                'account_type' => 'debit',
                'root_account_type' => 'as',
                'is_bank_cash' => 'yes',
                'status' => 'active',
                'default_type' => 'petty_cash',
                'created_by' => auth()->id(),
            ]);
            $created['ledgers']++;
        } else {
            $petty->update([
                'default_type' => 'petty_cash',
                'is_bank_cash' => 'yes',
            ]);
        }

        $oa = \App\Models\OutletAccount::firstOrCreate(
            [
                'outlet_id' => $outlet->id,
                'coa_id' => $petty->id,
            ],
            ['status' => 'active']
        );
        if ($oa->wasRecentlyCreated) {
            $created['outlet_accounts']++;
        }
    }

    if ($withFgStore) {
        \App\Models\Store::firstOrCreate(
            [
                'doc_type' => 'outlet',
                'doc_id' => $outlet->id,
                'type' => 'FG',
            ],
            [
                'name' => 'Store FG ' . $outlet->name,
                'status' => 'active',
                'created_by' => auth()->id(),
            ]
        );
    }

    return $created;
}
