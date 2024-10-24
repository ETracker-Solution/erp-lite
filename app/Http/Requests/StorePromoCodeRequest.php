<?php

namespace App\Http\Requests;

use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;

class StorePromoCodeRequest extends FormRequest
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
            'code'=>['required', 'unique:promo_codes,code'],
            'discount_type'=>['required'],
            'discount_value'=>['required'],
            'minimum_purchase'=>['required'],
            'start_date'=>['required','date_format:Y-m-d','after_or_equal:' . Carbon::tomorrow()->format('Y-m-d')],
            'end_date'=>['required','date_format:Y-m-d','after_or_equal:' . $this->start_date],
            'discount_for'=>['required'],
            'member_type'=>['required_if:discount_for,member'],
            'customers'=>['nullable'],
        ];
    }
}
