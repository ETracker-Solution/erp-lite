<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateJournalVoucherRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'date' => 'required',
            'amount' => 'required',
            'debit_account_id' => ['required','different:credit_account_id'],
            'credit_account_id' => ['required', 'different:debit_account_id'],
            'narration' => 'nullable',
            'reference_no' => 'nullable',
        ];
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
