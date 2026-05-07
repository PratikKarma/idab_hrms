<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Peark extends Model
{
    protected $fillable = [
        'employee_id',
        'title',
        'amount',
        'peark_coupon',
        'created_by',
    ];

    public function employee()
    {
        return $this->hasOne(Employee::class, 'id', 'employee_id')->first();
    }
}
