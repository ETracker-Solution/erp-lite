<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChartOfInventory extends Model
{
    use HasFactory;


    public function subChartOfInventories()
    {
        return $this->hasMany(self::class, 'parent_id');
    }
}
