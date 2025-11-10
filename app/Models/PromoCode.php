<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PromoCode extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function customerPromoCodes()
    {
        return $this->hasMany(CustomerPromoCode::class, 'promo_code_id');
    }

    public function customers()
    {
        return $this->belongsToMany(Customer::class, 'customer_promo_codes', 'promo_code_id', 'customer_id');
    }

    public function smsTemplate()
    {
        return $this->belongsTo(SmsTemplate::class, 'sms_template_id');
    }
}
