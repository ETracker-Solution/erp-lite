<?php


use App\Models\ChartOfAccount;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;


//accounting integration
function accountsTransaction($doc_type, $doc_id,$amount, $debit_account_id, $credit_account_id,$payee_name=null,$narration=null,$reference_no=null)
{
    $date=date('Y-m-d');
    $data = [
        [
            'date' => $date,
            'type' => 'debit',
            'amount' => $amount,
            'transaction_id' => $transaction_id ?? '000',
            'payee_name' => $payee_name??'',
            'narration' => $narration,
            'reference_no' => $reference_no,
            'chart_of_account_id' => $debit_account_id,
            'doc_id' => $doc_id,
            'doc_type' => $doc_type,
        ],
        [
            'date' => $date,
            'type' => 'credit',
            'amount' => $amount,
            'transaction_id' => $object->transaction_id ?? '000',
            'payee_name' => $payee_name,
            'narration' => $narration,
            'reference_no' => $reference_no,
            'chart_of_account_id' => $credit_account_id,
            'doc_id' => $doc_id,
            'doc_type' => $doc_type,
        ]

    ];

    DB::table('transactions')->insert($data);
}
