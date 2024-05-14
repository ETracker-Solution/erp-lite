<?php

namespace App\Http\Requests;

use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;

class StoreSupplierVoucherRequest extends FormRequest
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
            'sv_no' => 'required',
            'date' => 'required',
            'amount' => 'required',
            'supplier_id' => 'required',
            'debit_account_id' => ['required','different:credit_account_id'],
            'credit_account_id' => ['required', 'different:debit_account_id'],
            'payee_name' => 'required',
            'narration' => 'nullable',
            'reference_no' => 'nullable',
        ];
    }
    public function prepareForValidation()
    {

        $this->merge([
            'date' => Carbon::parse($this->date)->format('Y-m-d'),
        ]);

    }
}
