<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShiftTurner extends Model
{
    use HasFactory;
    protected $table = 'shiftturners';
    protected $casts = [
        'last_shifted_at' => 'datetime',
    ];


}
