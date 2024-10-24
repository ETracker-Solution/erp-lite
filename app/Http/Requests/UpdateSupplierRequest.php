<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSupplierRequest extends FormRequest
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
            'name' => 'required',
            'mobile' => 'required',
            'address' => 'required',
            'email' => 'nullable',
            'supplier_group_id' => 'required',
            'website' => 'nullable',
            'status' => ['in:active,inactive'],
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
