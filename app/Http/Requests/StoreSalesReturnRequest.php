<?php

namespace App\Http\Requests;

use App\Models\Purchase;
use App\Models\SalesReturn;
use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;

class StoreSalesReturnRequest extends FormRequest
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
            'products' => 'required|array',
            'sale_id' => 'required',
            'store_id' => 'required',
            'date' => 'required',
            'reference_no' => 'nullable',
            'subtotal' => 'required',
            'discount' => 'required',
            'grand_total' => 'required',
            'remark' => 'nullable',
            'net_payable' => 'nullable',
            'created_by' => 'required',
            'status' => 'nullable',
        ];
    }
    public function prepareForValidation()
    {
        $date = $this->date ?: now()->format('Y-m-d');
        try {
            $date = \Carbon\Carbon::parse($date)->format('Y-m-d');
        } catch (\Exception $e) {
            $date = now()->format('Y-m-d');
        }

        $this->merge([
            'created_by' => auth()->user()->id,
            'date' => $date,
            'status' => $this->status ?: 'final',
        ]);
    }
}
