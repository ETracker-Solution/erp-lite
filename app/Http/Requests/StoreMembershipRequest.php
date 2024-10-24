<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreMembershipRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'membership_number' => 'nullable',
            'customer_id' => ['required', 'unique:memberships,customer_id'],
            'member_type_id' => 'required',
            'point' => 'required',
            'created_by' => 'required'
        ];
    }

    public function prepareForValidation()
    {

        $this->merge([
            'created_by' => auth()->user()->id,
        ]);

    }
}
