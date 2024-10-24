<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RedeemPoint extends Model
{
    use HasFactory;

    public function customer(){

        return $this->belongsTo(Customer::class);
    }
    public function sale(){
        
        return $this->belongsTo(Sale::class);
    }
}
