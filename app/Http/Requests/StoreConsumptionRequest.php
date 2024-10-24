<?php

namespace App\Http\Requests;

use App\Classes\ConsumptionNumber;
use Illuminate\Foundation\Http\FormRequest;

class StoreConsumptionRequest extends FormRequest
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
            'date' => ['required'],
            'status' => ['required'],
            'products' => ['required', 'array'],
//            'products.*.coi' => 'required',
//            'products.*.rate' => 'required',
//            'products.*.quantity' => 'required',
            'remark' => ['nullable'],
            'subtotal' => ['nullable'],
            'reference_no' => ['nullable'],
            // 'serial_no' => ['required'],
            'batch_id' => ['required'],
            'store_id' => ['required'],
        ];
    }

    public function prepareForValidation()
    {
        $this->merge([
            'status' =>'completed',
            'created_by' => auth()->user()->id
        ]);
    }

    public function messages()
    {
        return [
            'products.*.quantity.required' => 'Quantity of the Raw Material is required'
        ];

    }
}
