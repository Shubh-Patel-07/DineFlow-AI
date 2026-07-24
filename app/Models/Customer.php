<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    protected $table = 'customers';

    protected $fillable = [
        'restaurant_id',
        'name',
        'phone',
        'email',
        'total_visits',
        'total_spent',
        'loyalty_points',
        'last_visit',
        'notes',
    ];

    protected $casts = [
        'total_spent' => 'float',
        'last_visit' => 'datetime',
    ];
}
