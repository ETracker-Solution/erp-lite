<?php


use Illuminate\Support\Facades\DB;


//accounting integration
function accountsTransaction($doc_type, $doc, $debit_account_id, $credit_account_id)
{
    $data = [
        [
            'date' => $doc->date,
            'type' => 'debit',
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

    DB::table('transactions')->insert($data);
}
