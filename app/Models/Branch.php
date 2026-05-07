<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class Branch extends Model
{
    protected $table = 'branches';
    protected $fillable = [
        'name',
        'created_by'
    ];
}
