<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PayrollSlip extends Model
{
    protected $fillable = [
        'restaurant_id', 'user_id', 'month', 'year', 'basic_salary', 'total_days',
        'present_days', 'overtime_pay', 'bonus', 'deductions', 'net_salary',
        'payment_status', 'payment_date', 'payment_mode', 'generated_by', 'remarks'
    ];

    protected $casts = [
        'basic_salary' => 'float',
        'present_days' => 'float',
        'overtime_pay' => 'float',
        'bonus' => 'float',
        'deductions' => 'float',
        'net_salary' => 'float',
        'payment_date' => 'date',
        'month' => 'integer',
        'year' => 'integer',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
