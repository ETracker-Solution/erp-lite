<?php

namespace App\Http\Requests;

use App\Classes\RequisitionNumber;
use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;

class StoreFGDeliveryReceiveRequest extends FormRequest
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
            'requisition_delivery_id' => 'required',
            'products' => 'array',
            'status' => 'required',
            'date' => 'required',
            'type' => 'required',
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
            'status' => 'received',
            'type' => 'FG',
            'created_by' => auth()->user()->id,
            'date' => Carbon::parse($this->date)->format('Y-m-d'),
        ]);

    }
}
