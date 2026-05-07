<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pension extends Model
{
    protected $fillable = [
        'employee_id',
        'title',
        'amount',
        'created_by',
    ];

    public function employee()
    {
        return $this->hasOne(Employee::class, 'id', 'employee_id')->first();
    }
}
