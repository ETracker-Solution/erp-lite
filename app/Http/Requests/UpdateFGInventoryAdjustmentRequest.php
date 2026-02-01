<?php

namespace App\Http\Requests;

use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;

class UpdateFGInventoryAdjustmentRequest extends FormRequest
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
            'store_id' => 'required_if:status,adjusted',
            'products' => 'array',
            'date' => 'required_if:status,adjusted',
            'reference_no' => 'nullable',
            'remark' => 'nullable', // Original remark
            'edit_remark' => 'required_if:status,adjusted', // Reason for edit
            'updated_by' => 'required_if:status,adjusted',
            'transaction_type' => 'required_if:status,adjusted',
            'subtotal' => 'required_if:status,adjusted',
            'status' => 'required',
        ];
    }

    public function prepareForValidation()
    {
        $this->merge([
//            'status' => 'adjusted',
            'updated_by' => auth()->user()->id,
            'date' => Carbon::parse($this->date)->format('Y-m-d'),
        ]);
    }
}
