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

function getAdvanceCollectionGLId()
{
    $account = ChartOfAccount::firstOrCreate(
        ['name' => 'Advance Collection'],
        [
            'type' => 'ledger',
            'root_account_type' => 'li',
            'account_type' => 'credit',
            'status' => 'active',
            'is_bank_cash' => 'no', // Assuming it's not a bank/cash account itself
            'parent_id'=> 20
        ]
    );
    return $account->id;
}

function addPreOrderCustomerTransaction($item, $transaction_type = -1) // -1 for credit (advance received from customer reduces AR or creates liability)
{
    // Actually, if we are receiving advance, it is a liability (Advance Collection).
    // In Customer Transaction (Customer Ledger), we credit the customer (we owe them goods).
    // Usually:
    // Sale: Debit Customer (AR), Credit Sales.
    // Payment: Debit Cash, Credit Customer (AR).
    // Advance: Debit Cash, Credit Advance Collection (Liability).
    //          AND we should record this in Customer Ledger?
    // User Requirement: "Update CustomerTransaction as advance received"
    // So yes, we should add an entry. Credit Customer to show they paid.

    // Check if we need a specific ChartOfAccountId for this.
    // If we use getAccountsReceiveableGLId(), it means we are reducing AR.
    // If it's pure advance (no sale yet), it might result in negative AR (Credit Balance) which is fine.

    \App\Models\CustomerTransaction::query()->create([
        'customer_id' => $item->customer_id,
        'doc_type' => 'PRE_ORDER',
        'doc_id' => $item->id,
        'amount' => $item->amount,
        'date' => $item->date,
        'transaction_type' => $transaction_type, // -1 means Credit (Payment Received)
        'chart_of_account_id' => getAdvanceCollectionGLId(), // Linking to Advance Collection
        'description' => 'Pre-Order Advance',
    ]);
}


function accountBalanceForOtherOutletSales($chart_of_account_id){
    $other_outlet_sales_balance = 0;
    $account_outlet = OutletAccount::where('coa_id', $chart_of_account_id)->first();
    if ($account_outlet) {
        $alreadyTransferred = DeliveryCashTransfer::where('from_outlet', $account_outlet->outlet_id)->pluck('other_outlet_sale_id')->toArray();

        $otherOutletSales = OthersOutletSale::where('outlet_id', '!=', $account_outlet->outlet_id)
            ->where('delivery_point_id', '=', $account_outlet->outlet_id)
            ->where('payment_status', 'paid')
            ->whereNotIn('id', $alreadyTransferred)
            ->pluck('invoice_number')->toArray();

        $originalSales = Sale::whereIn('invoice_number', $otherOutletSales)->pluck('id')->toArray();

        $other_outlet_sales_balance = AccountTransaction::where('chart_of_account_id', $chart_of_account_id)->where('doc_type', 'POS')->whereIn('doc_id', $originalSales)->sum(\DB::raw('amount * transaction_type'));
    }
    return $other_outlet_sales_balance;
}
