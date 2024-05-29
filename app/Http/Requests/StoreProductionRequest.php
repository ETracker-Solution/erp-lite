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
            'store_id' => ['required'],
            'factory_id' => ['required'],
            'batch_id' => ['required'],
            'date' => ['required'],
            'status' => ['nullable'],
            'products' => ['required', 'array'],
            'remark' => ['nullable'],
            'reference_no' => ['nullable'],
            'subtotal' => ['nullable'],
            'total_quantity' => ['nullable'],
            'created_by' => ['nullable'],
        ];
    }

    public function prepareForValidation()
    {
        $this->merge([
            'status' =>'completed',
            'created_by' => auth()->user()->id,
        ]);
    }
}
