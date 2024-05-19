<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreEmployeeRequest extends FormRequest
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
            'employee_id' => 'required',
            'name' => 'required',
            'image' => 'nullable',
            'father_name' => 'nullable',
            'mother_name' => 'nullable',
            'nominee_name' => 'nullable',
            'nominee_relation' => 'nullable',
            'blood_group' => 'required',
            'nid' => 'required',
            'dob' => 'required',
            'bank_account' => 'required',
            'present_address' => 'required',
            'permanent_address' => 'nullable',
            'email' => 'required',
            'personal_email' => 'nullable',
            'phone' => 'required',
            'alternative_phone' => 'nullable',
            'department_id' => 'required',
            'designation_id' => 'required',
            'outlet_id' => 'required',
            'sallery' => 'required',
            'joining_date' => 'nullable',
            'confirm_date' => 'nullable',
            'status' => 'required',
        ];
    }
}
