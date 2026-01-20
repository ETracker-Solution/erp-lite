<?php

namespace App\Http\Requests;

use App\Models\Employee;
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
            'employee_id' => ['nullable', 'unique:' . app(Employee::class)->getTable() . ',employee_id'],
            'name' => 'required',
            'image' => 'nullable',
            'father_name' => 'nullable',
            'mother_name' => 'nullable',
            'nominee_name' => 'nullable',
            'nominee_relation' => 'nullable',
            'blood_group' => 'nullable',
            'nid' => ['nullable', 'unique:' . app(Employee::class)->getTable() . ',nid'],
            'dob' => 'nullable',
            'bank_account' => 'nullable',
            'present_address' => 'nullable',
            'permanent_address' => 'nullable',
            'email' => 'required|email|unique:employees,email',
            'personal_email' => 'nullable',
            'phone' => 'required',
            'alternative_phone' => 'nullable',
            'department_id' => 'nullable',
            'designation_id' => 'nullable',
            'outlet_id' => 'nullable',
            'salary' => 'nullable',
            'joining_date' => 'nullable',
            'confirm_date' => 'nullable',
            'status' => 'required',
            'user_of'=>'required',
            'factory_id' => 'nullable',
        ];
    }
}
