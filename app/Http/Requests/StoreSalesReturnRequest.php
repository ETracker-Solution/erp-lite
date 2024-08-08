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
            'date' => 'required',
            'reference_no' => 'nullable',
            'subtotal' => 'required',
            'remark' => 'nullable',
            'net_payable' => 'nullable',
            'uid' => 'required',
            'created_by' => 'required',
        ];
    }
    public function prepareForValidation()
    {

        $this->merge([
            'uid' => generateUniqueUUID(null, SalesReturn::class, 'uid', false, true),
            'created_by' => auth()->user()->id,
            'date' => Carbon::parse($this->date)->format('Y-m-d'),
        ]);

    }
}
