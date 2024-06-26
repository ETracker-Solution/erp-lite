<?php

namespace App\Http\Requests;

use App\Classes\RequisitionNumber;
use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;

class StoreRequisitionRequest extends FormRequest
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
            'from_store_id' => 'required',
            'to_store_id' => 'required',
            'outlet_id' => 'required',
            'to_factory_id' => 'required',
            'products' => 'array',
            'uid' => 'required',
            'date' => 'required',
            'reference_no' => 'nullable',
            'total_item' => 'required',
            'total_quantity' => 'required',
            'remark' => 'nullable',
            'created_by' => 'required',
        ];
    }

    public function prepareForValidation()
    {

        $this->merge([
            'outlet_id'=>getStoreDocId($this->from_store_id),
            'to_factory_id'=>getStoreDocId($this->to_store_id),
            'uid' => RequisitionNumber::serial_number(),
            'created_by' => auth()->user()->id,
            'date' => Carbon::parse($this->date)->format('Y-m-d'),
        ]);

    }
}
