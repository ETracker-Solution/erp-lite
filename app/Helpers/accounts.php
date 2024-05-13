<?php


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
            'payee_name' => $doc->payee_name,
            'narration' => $doc->note,
            'reference_no' => $doc->reference_no,
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
            'payee_name' => $doc->payee_name,
            'narration' => $doc->note,
            'reference_no' => $doc->reference_no,
            'chart_of_account_id' => $credit_account_id,
            'doc_id' => $doc->id,
            'doc_type' => $doc_type,
        ]
    ];

    DB::table('account_transactions')->insert($data);
}
