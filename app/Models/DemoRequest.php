<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DemoRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'restaurant_name',
        'phone',
        'email',
        'city',
        'preferred_time',
        'message',
        'status',
    ];
}
