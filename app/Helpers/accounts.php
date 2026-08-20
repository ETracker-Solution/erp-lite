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
