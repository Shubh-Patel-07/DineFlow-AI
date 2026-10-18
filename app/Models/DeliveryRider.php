<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DeliveryRider extends Model
{
    protected $fillable = [
        'restaurant_id',
        'name',
        'phone',
        'vehicle_number',
        'status',
        'active_orders_count',
        'total_deliveries'
    ];

    public function orders()
    {
        return $this->hasMany(Order::class, 'rider_id');
    }

    public function restaurant()
    {
        return $this->belongsTo(Restaurant::class, 'restaurant_id');
    }
}
