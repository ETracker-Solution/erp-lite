<?php

namespace App\Http\Requests;

use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;

class StoreFGRequisitionDeliveryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'from_store_id' => 'required',
            'to_store_id' => 'required',
            'requisition_id' => 'required',
            'products' => 'required|array',
            'status' => 'required',
            'date' => 'required',
            'type' => 'required',
            'reference_no' => 'nullable',
            'total_item' => 'required',
            'total_quantity' => 'required',
            'remark' => 'nullable',
            'created_by' => 'required',
            'delivery_status' => 'nullable|in:full,partial,close',
        ];
    }

    public function prepareForValidation()
    {
        $date = $this->date;
        if ($date) {
            try {
                $date = Carbon::parse($date)->format('Y-m-d');
            } catch (\Exception $e) {
                // keep raw; validation will fail if invalid
            }
        }

        $this->merge([
            'status' => 'completed',
            'type' => 'FG',
            'created_by' => auth()->user()->id,
            'date' => $date,
        ]);
    }
}
