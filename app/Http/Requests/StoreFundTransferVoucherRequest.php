<?php

namespace App\Http\Requests;

use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class StoreFundTransferVoucherRequest extends FormRequest
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
//            'amount' => 'required',
            'created_by' => 'required',
//            'credit_account_id' => ['required', 'different:debit_account_id'],
//            'debit_account_id' => ['required', 'different:credit_account_id'],
            'narration' => 'nullable',

        ];
    }

    public function prepareForValidation()
    {

        $this->merge([
            'created_by' => auth()->user()->id,
        ]);

    }

    public function messages()
    {
        return [
//            'credit_account_id.different' => 'Transfer From Account must be Different Transfer To Account',
//            'credit_account_id.required' => 'Transfer From Account Can not be Null',
//            'debit_account_id.different' => 'Transfer To Account must be Different Transfer From Account',
//            'debit_account_id.different' => 'Transfer To Account Can not be Null',
//            'amount.required' => 'Amount Can not be Null',
            'date.required' => 'Date Can not be Null',
        ];
    }
}
