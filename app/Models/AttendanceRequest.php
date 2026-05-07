<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AttendanceRequest extends Model
{
    protected $fillable = [
        'employee_id',
        'type',
        'clock_out',
        'reason',
        'status',
        'requested_at',
        'approved_at',
        'approved_by',
        'total_rest',
        'breaks',
        'total_break',
        'created_by',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
}
