<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProductionRequest extends FormRequest
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
            'factory_id' => ['required'],
            'store_id' => ['required'],
            'batch_id' => ['required'],
            'date' => ['required'],
            'status' => ['nullable'],
            'products' => ['required', 'array'],
            'remark' => ['nullable'],
            'reference_no' => ['nullable'],
            'subtotal' => ['nullable'],
            'total_quantity' => ['nullable'],
            'updated_by' => ['nullable'],
        ];
    }

    public function prepareForValidation()
    {
        $this->merge([
            'updated_by' => auth()->user()->id,
        ]);
    }
}
