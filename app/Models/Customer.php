<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Customer extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = ['id'];

    public function points(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(EarnPoint::class, 'customer_id');
    }

    public function point()
    {
        return $this->points->sum('point');
    }

    public function sales(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Sale::class, 'customer_id');
    }

    public function membership()
    {
        return $this->hasOne(Membership::class, 'customer_id');
    }

    public function membershipPointHistories()
    {
        return $this->hasMany(MembershipPointHistory::class, 'customer_id');
    }

    public function currentReedemablePoint()
    {
        return $this->membershipPointHistories()->whereYear('created_at', date('Y'))->sum('point');
    }
}
