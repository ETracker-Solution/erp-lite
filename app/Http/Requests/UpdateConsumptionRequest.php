<?php

namespace App\Http\Requests;

use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;

class UpdateConsumptionRequest extends FormRequest
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
            'batch_id' => ['required'],
            'date' => ['required'],
            'status' => ['nullable'],
            'products' => ['required', 'array'],
            'remark' => ['nullable'],
            'subtotal' => ['required'],
            'reference_no' => ['nullable'],
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
