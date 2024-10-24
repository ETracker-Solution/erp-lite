<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreDeliveryCashTransferRequest extends FormRequest
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
            'credit_account_id' => ['required', 'different:debit_account_id'],
            'debit_account_id' => ['required', 'different:credit_account_id'],
            'narration' => 'nullable',
            'reference_no' => 'nullable',
        ];
    }
}
