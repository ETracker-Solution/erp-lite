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


function getOpeningBalanceOfEquityGLId()
{
    return 47;
}

function getRMInventoryGLId()
{
    return 15;
}

function getAccountsPayableGLId()
{
    return 22;
}

function getAccountsReceiveableGLId()
{
    return 18;
}

function getFGInventoryGLId()
{
    return 16;
}

function getIncomeFromSalesGLId()
{
    return 35;
}

function getCOGSGLId()
{
    return 43;
}

function getCashGLID()
{
    return 13;
}

function getAllLedgers()
{
    return ChartOfAccount::where('type', 'ledger')->select(DB::raw('id,name,CONCAT(id,". ",name, " (",root_account_type,")") as display_name'))->get();
}

function outletTransactionAccount($outlet_id, $account_type = 'cash')
{
    $account = \App\Models\OutletTransactionConfig::where(['type' => $account_type, 'outlet_id' => $outlet_id])->first();
    if ($account) {
        return $account->coa_id;
    }
    return false;
}

function getDiscountGLID()
{
    return 50;
}

function getRewardGLID()
{
    return 51;
}

function getCustomersReceiveableGLId()
{
    return 58;
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
