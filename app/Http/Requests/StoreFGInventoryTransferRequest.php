<?php

namespace App\Http\Requests;

use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;

class StoreFGInventoryTransferRequest extends FormRequest
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
            'to_store_id' => ['required', 'different:from_store_id'],
            'from_store_id' => ['required', 'different:to_store_id'],
            'products' => 'array',
            'date' => 'required',
            'type' => 'required',
            'reference_no' => 'nullable',
            'remark' => 'nullable',
            'created_by' => 'required',
        ];
    }

    public function prepareForValidation()
    {

        $this->merge([
            'type' => 'FG',
            'created_by' => auth()->user()->id,
            'date' => Carbon::parse($this->date)->format('Y-m-d'),
        ]);

    }

    public function messages()
    {
        return [
            'from_store_id.different' => 'Transfer must be Different Store',
            'from_store_id.different' => 'Transfer Can not be Null',
        ];
    }
}
