<?php

namespace App\Http\Requests;

use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;

class UpdatePurchaseRequest extends FormRequest
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
            'store_id' => ['required'],
            'supplier_id' => ['required'],
            'date' => ['required'],
            'status' => ['nullable'],
            'products' => ['required', 'array'],
            'remark' => ['nullable'],
            'subtotal' => ['required'],
            'reference_no' => ['nullable'],
            'vat' => ['nullable'],
            'alter_unit_id' => ['nullable'],
            'a_unit_quantity' => ['nullable'],
            'discount' => ['nullable'],
            'net_payable' => ['required'],
            'updated_by' => ['required'],
        ];
    }

    public function prepareForValidation()
    {

        $this->merge([
            'updated_by' => auth()->user()->id,
            'date' => Carbon::parse($this->date)->format('Y-m-d'),
        ]);

    }
}
