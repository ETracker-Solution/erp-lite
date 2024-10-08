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
            'products' => 'array',
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
        ];
    }
    public function prepareForValidation()
    {

        $this->merge([
            'created_by' => auth()->user()->id,
            'date' => Carbon::parse($this->date)->format('Y-m-d'),
        ]);

    }
}
