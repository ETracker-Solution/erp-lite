<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateStoreRequest extends FormRequest
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

            'name' => 'required',
            'type' => 'required',
            'doc_type' => 'required',
            'doc_id' => 'nullable',
            'updated_by' => 'required',
            'status' => ['in:active,inactive']
        ];
    }
    public function prepareForValidation(): void
    {

        $this->merge([
            'updated_by' => auth()->user()->id,
        ]);

    }
}
