<?php

namespace App\Http\Requests;

use Carbon\Carbon;
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
            'date' => 'required',
            'products' => 'array',
            'products.*.amount' => 'required',
            'products.*.credit_account_id' => ['required','different:products.*.debit_account_id'],
            'products.*.debit_account_id' => ['required', 'different:products.*.credit_account_id'],
            'products.*.payee_name' => 'required',
            // 'products.*.narration' => 'nullable',
            'products.*.reference_no' => 'nullable',
            'narration' => 'nullable',
        ];
    }
    public function prepareForValidation()
    {

        //

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
