<?php

namespace App\Http\Requests;

use App\Classes\PreOrderNumber;
use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;

class StorePreOrderRequest extends FormRequest
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
            //
        ];
    }

    public function prepareForValidation()
    {

        $this->merge([
            'advance_amount' => $this->advance_amount ?? 0,
            'created_by' => auth()->user()->id,
            'delivery_date' => Carbon::parse($this->date)->format('Y-m-d'),
            'order_date' => Carbon::now()->format('Y-m-d'),
            'order_number' => PreOrderNumber::serial_number(),
        ]);

    }
}
