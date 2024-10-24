<?php

namespace App\Http\Requests;

use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;

class StoreRMInventoryAdjustmentRequest extends FormRequest
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
            'products' => 'array',
            'date' => 'required',
            'reference_no' => 'nullable',
            'remark' => 'nullable',
            'status' => 'nullable',
            'created_by' => 'required',
            'transaction_type' => 'required',
            'subtotal' => 'required',
            'type' => 'required',
        ];
    }
    public function prepareForValidation()
    {

        $this->merge([
            'type'=>'RM',
            'status'=>'adjusted',
            'created_by' => auth()->user()->id,
            'date' => Carbon::parse($this->date)->format('Y-m-d'),
        ]);

    }
}
