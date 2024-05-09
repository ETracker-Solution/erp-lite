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
            'status' => ['nullable'],
            'materials' => ['required', 'array'],
            'materials.*.quantity' => 'required',
            'remark' => ['nullable'],
            'subtotal' => ['nullable'],
            'reference_no' => ['nullable'],
            'discount' => ['nullable'],
            'grand_total' => ['nullable'],
            'consumption_no' => ['required'],
            'production_id' => ['required'],
        ];
    }

    public function prepareForValidation()
    {
        $this->merge([
            'grand_total' => $this->grandtotal,
            'consumption_no' => ConsumptionNumber::serial_number()
        ]);
    }

    public function messages()
    {
        return [
            'materials.*.quantity.required' => 'Quantity of the Raw Material is required'
        ];

}
}
