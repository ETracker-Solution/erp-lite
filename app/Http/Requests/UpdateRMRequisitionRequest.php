<?php

namespace App\Http\Requests;

use App\Classes\RequisitionNumber;
use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;

class UpdateRMRequisitionRequest extends FormRequest
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
            'total_item' => 'required',
            'total_quantity' => 'required',
            'remark' => 'nullable',
            'updated_by' => 'required',
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
