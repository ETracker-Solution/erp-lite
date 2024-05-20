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
}
