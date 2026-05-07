<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Wage extends Model
{
    public static $Wages_type = [
        'highly_skilled' => 'Highly Skilled',
        'skilled' => 'Skilled',
        'semi_skilled' => 'Semi Skilled',
        'un_skilled' => 'Un-Skilled'
    ];
}
