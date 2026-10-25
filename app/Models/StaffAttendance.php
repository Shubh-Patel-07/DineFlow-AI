<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StaffAttendance extends Model
{
    protected $fillable = [
        'restaurant_id', 'user_id', 'date', 'clock_in', 'clock_out',
        'total_hours', 'overtime_hours', 'status', 'shift', 'clock_in_ip', 'notes'
    ];

    protected $casts = [
        'date' => 'date',
        'clock_in' => 'datetime',
        'clock_out' => 'datetime',
        'total_hours' => 'float',
        'overtime_hours' => 'float',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
