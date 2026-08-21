<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reservation extends Model
{
    use HasFactory;

    protected $fillable = [
        'restaurant_id',
        'customer_name',
        'customer_phone',
        'pax_count',
        'reservation_date',
        'time_slot',
        'status',
        'special_requests'
    ];

    public function restaurant()
    {
        return $this->belongsTo(Restaurant::class, 'restaurant_id');
    }
}