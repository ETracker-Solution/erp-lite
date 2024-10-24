<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Membership extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function memberType()
    {
        return $this->belongsTo(MemberType::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
}
