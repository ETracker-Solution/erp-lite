<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateMemberPointRequest extends FormRequest
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
            'from_amount' => 'nullable',
            'to_amount' => ['nullable','gt:from_amount'],
            'per_amount' => ['nullable'],
            'point' => 'required',
            'member_type_id' => 'required',
            'updated_at' => 'required'
        ];
    }
    public function prepareForValidation()
    {

        $this->merge([
            'updated_at' => auth()->user()->id,
        ]);

    }
}
