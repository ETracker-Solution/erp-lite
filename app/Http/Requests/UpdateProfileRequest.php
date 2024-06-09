<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProfileRequest extends FormRequest
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
            // 'mobile' => ['nullable','exclude_if:mobile,null'],
            // 'mobile' => 'required|unique:admins,mobile,'.$this->id,
            // 'email' => 'required',
            'password' => ['nullable', 'exclude_if:password,null'],
            // 'image' => ['nullable','exclude_if:image,null'],
        ];
    }
    public function messages()
    {
        return [
            'mobile.required' => 'Contact No Cannot be Empty',
            'mobile.unique' => 'Contact No Already exist',
        ];
    }
}
