<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreMembershipRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'membership_number' => 'nullable',
            'customer_id' => ['required', 'unique:memberships,customer_id'],
            'member_type_id' => 'required',
            'point' => 'required',
        ];
    }
}
