<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Shift extends Model
{
    protected $fillable = [
        'name',
        'company_start_time',
        'company_end_time',
        'created_by'
    ];
}
