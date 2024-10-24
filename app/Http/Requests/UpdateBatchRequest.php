<?php

namespace App\Http\Requests;

use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;

class UpdateBatchRequest extends FormRequest
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
            'batch_no' => 'required',
            'date' => 'required',
            'p_manager' => 'required',
            'description' => 'nullable',
        ];
    }
    public function prepareForValidation()
    {

        $this->merge([
            'date' => Carbon::parse($this->date)->format('Y-m-d'),
        ]);

    }
}
