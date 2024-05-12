<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePaymentVoucherRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'pv_no' => 'required',
            'date' => 'required',
            'amount' => 'required',
            'credit_account_id' => ['required','different:debit_account_id'],
            'debit_account_id' => ['required', 'different:credit_account_id'],
            'payee_name' => 'required',
            'narration' => 'nullable',
            'reference_no' => 'nullable',
        ];
    }
    public function messages()
    {
        return [
            'cash_bank_account_id.different' => 'Cash Bank Account must be Different Debit Account',
            'cash_bank_account_id.required' => 'Cash Bank Account Can not be Null',
            'debit_account_id.different' => 'Debit Account must be Different Cash Bank Account',
            'debit_account_id.different' => 'Debit Account Can not be Null',
            'amount.required' => 'Amount Can not be Null',
            'payee_name.required' => 'Receive Name Can not be Null',
            'date.required' => 'Date Can not be Null',
        ];
    }
}
