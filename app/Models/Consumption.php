<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Consumption extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function items(): HasMany
    {

        return $this->hasMany('App\Models\ConsumptionItem', 'consumption_id');

    }
}
