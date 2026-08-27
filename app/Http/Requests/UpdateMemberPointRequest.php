<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateMemberPointRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'from_amount' => 'nullable',
            'to_amount' => ['nullable', 'gt:from_amount'],
            'per_amount' => ['nullable'],
            'point' => 'required',
            'member_type_id' => 'required',
            'updated_by' => 'required',
        ];
    }

    public function prepareForValidation()
    {
        $this->merge([
            'updated_by' => auth()->user()->id,
        ]);
    }
}
