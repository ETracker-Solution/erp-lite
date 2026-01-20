<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdjustmentEditHistory extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'inventory_adjustment_id',
        'old_data',
        'new_data',
        'remarks',
        'edited_by',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'edited_by');
    }
}
