<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class VatScopedModel extends Model
{
    protected static function booted()
    {
        static::addGlobalScope('is_vat_scope', function (Builder $builder) {
            $instance = new static;

            // Only apply where('is_vat', true) if logged in via VAT mode
            if ($instance->hasColumn('is_vat') && session('is_vat') === true) {
                $builder->where('is_vat', true);
            }
        });

        static::creating(function ($model) {
            if (session()->has('is_vat') && $model->hasColumn('is_vat')) {
                $model->is_vat = session('is_vat');
            }
        });
    }

    public function hasColumn($column)
    {
        try {
            return \Schema::hasColumn($this->getTable(), $column);
        } catch (\Exception $e) {
            return false;
        }
    }
}
