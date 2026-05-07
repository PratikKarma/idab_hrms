<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubDepartment extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'department',
        'created_by',
    ];

    public function department()
    {
        return $this->hasOne(Department::class, 'id', 'department_id');
    }
}
