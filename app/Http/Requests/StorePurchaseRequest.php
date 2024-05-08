<?php

namespace App\Http\Requests;

use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;

class StorePurchaseRequest extends FormRequest
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
            'store_id' => 'required',
            'supplier_id' => 'required',
            'serial_no' => 'required',
            'products' => 'array',
            'date' => 'required',
            'subtotal' => 'required',
            'vat' => 'required',
            'store_id' => 'nullable',
            'remark' => 'nullable',
            'net_payable' => 'required',
        ];
    }

    public function prepareForValidation()
    {

        $this->merge([
            'created_by' => auth()->user()->id,
            'date' => Carbon::parse($this->date)->format('Y-m-d'),
        ]);

    }
}
