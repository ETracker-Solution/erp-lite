<?php


use App\Models\AccountTransaction;
use App\Models\ChartOfAccount;
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
    $other_outlet_sales_balance = 0;
    $account_outlet = OutletAccount::where('coa_id', $chart_of_account_id)->first();
    if ($account_outlet) {
        $otherOutletSales = OthersOutletSale::where('outlet_id', '!=', $account_outlet->outlet_id)
            ->where('delivery_point_id', '=', $account_outlet->outlet_id)
            ->where('payment_status', 'paid')
            ->pluck('invoice_number')->toArray();

        $originalSales = Sale::whereIn('invoice_number', $otherOutletSales)->pluck('id')->toArray();
        $other_outlet_sales_balance = AccountTransaction::where('chart_of_account_id', $chart_of_account_id)->where('doc_type', 'POS')->whereIn('doc_id', $originalSales)->sum(\DB::raw('amount * transaction_type'));
    }
    return $other_outlet_sales_balance;
}
