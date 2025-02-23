<?php

namespace App\Http\Requests;

use App\Models\Purchase;
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
            'products' => 'array',
            'date' => 'required',
            'reference_no' => 'nullable',
            'subtotal' => 'required',
            'vat' => 'nullable',
            'discount' => 'nullable',
            'alter_unit_id' => 'nullable',
            'a_unit_quantity' => 'nullable',
            'remark' => 'nullable',
            'net_payable' => 'required',
            'uid' => 'required',
            'created_by' => 'required',
        ];
    }

    public function prepareForValidation()
    {

        $this->merge([
            'uid' => generateUniqueCode( Purchase::class, 'uid'),
            'created_by' => auth()->user()->id,
            'date' => Carbon::parse($this->date)->format('Y-m-d'),
        ]);

    }
}
