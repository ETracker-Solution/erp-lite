<?php

namespace App\Http\Requests;

use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;

class StoreJournalVoucherRequest extends FormRequest
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
            'uid' => 'required',
            'date' => 'required',
            'amount' => 'required',
            'debit_account_id' => ['required','different:credit_account_id'],
            'credit_account_id' => ['required', 'different:debit_account_id'],
            'narration' => 'nullable',
            'reference_no' => 'nullable',
        ];
    }
    public function prepareForValidation()
    {

        //

    }
    public function messages()
    {
        return [
            'debit_account_id.different' => 'Debit Account must be Different Credit Account',
            'debit_account_id.required' => 'Debit Account Can not be Null',
            'credit_account_id.different' => 'Credit Account must be Different Debit Account',
            'credit_account_id.different' => 'Credit Account Can not be Null',
            'amount.required' => 'Amount Can not be Null',
            'date.required' => 'Date Can not be Null',
        ];
    }
}
