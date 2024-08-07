<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Factory extends Model
{
    use HasFactory;
    protected $guarded = ['id'];

    public function stores(){
        return $this->hasMany(Store::class,'doc_id')->where('doc_type','factory');
    }
}
