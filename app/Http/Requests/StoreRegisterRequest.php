<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreRegisterRequest extends FormRequest
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
            'email' => 'required',
            'phone' => 'required',
            'address' => 'nullable',
            'no_of_employee' => 'nullable',
            'type' => 'required',
            // 'password' => 'required',
            // 'password_confirmation' => 'required_with:password|same:password',
        ];
    }
}
