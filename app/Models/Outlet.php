<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Outlet extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function stores(){
        return $this->hasMany(Store::class,'doc_id')->where('doc_type','outlet');
    }

    public function requisitions()
    {
        return $this->hasMany(Requisition::class,'outlet_id');
    }
}
