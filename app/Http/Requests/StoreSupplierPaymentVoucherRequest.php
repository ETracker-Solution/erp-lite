<?php

namespace App\Http\Requests;

use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;

class StoreSupplierPaymentVoucherRequest extends FormRequest
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
            'products.*.supplier_id' => 'required',
            'products.*.credit_account_id' => ['required'],
            'products.*.payee_name' => 'required',
            'products.*.reference_no' => 'nullable',
            'narration' => 'nullable',
        ];
    }
    public function prepareForValidation()
    {
        //
    }
}
