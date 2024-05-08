<?php

namespace App\Http\Requests;

use App\Classes\ProductionNumber;
use Illuminate\Foundation\Http\FormRequest;

class StoreProductionRequest extends FormRequest
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
            'date'=>['required'],
            'status'=>['nullable'],
            'products'=>['required', 'array'],
            'remark'=>['nullable'],
            'subtotal'=>['nullable'],
            'reference_no'=>['nullable'],
            'discount'=>['nullable'],
            'grand_total'=>['nullable'],
            'production_no'=>['required'],
        ];
    }
    public function prepareForValidation()
    {
        $this->merge([
            'grand_total' =>  $this->grandtotal,
            'production_no' =>  ProductionNumber::serial_number()
        ]);
    }
}
