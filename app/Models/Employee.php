<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    use HasFactory;
    protected $guarded = ['id'];

    public function user()
    {
        return $this->hasOne(User::class, 'employee_id');
    }

    public function outlet()
    {
        return $this->belongsTo(Outlet::class);
    }

    public function factory()
    {
        return $this->belongsTo(Factory::class);
    }
}
