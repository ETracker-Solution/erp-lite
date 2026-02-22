<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCustomerReceiveVoucherRequest extends FormRequest
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
            'products' => 'array',
            'products.*.amount' => 'required',
            'products.*.settle_discount' => 'nullable',
            'products.*.customer_id' => 'required',
            'products.*.sale_id' => 'required', // Invoice ID
            'products.*.debit_account_id' => ['required'], // Receipt Account (Bank/Cash)
            // Credit Account would be Accounts Receivable or Customer?
            // In Accounting: Debit Cash/Bank, Credit Receivables (Customer)
            // So we need debit_account_id (received to)
            'narration' => 'nullable',
        ];
    }

    public function prepareForValidation()
    {
        //
    }
}
