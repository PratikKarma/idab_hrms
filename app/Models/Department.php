<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    protected $fillable = [
        'branch_id',
        'name',
        'slug',
        'created_by',
    ];

    public function branch(){
        return $this->hasOne(Branch::class,'id','branch_id');
    }
}
