<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Production extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function items(): HasMany
    {

        return $this->hasMany('App\Models\ProductionItem', 'production_id');

    }

    public function stockItems(): HasMany
    {

        return $this->hasMany('App\Models\Stock', 'production_id');

    }
}
