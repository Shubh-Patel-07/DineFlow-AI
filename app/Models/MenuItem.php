<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MenuItem extends Model
{
    protected $fillable = ['restaurant_id', 'name', 'description', 'price', 'category', 'image', 'is_available', 'is_veg'];

    protected $casts = [
        'is_available' => 'boolean',
        'is_veg'       => 'boolean',
        'price'        => 'float',
    ];

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class, 'menu_item_id');
    }

    public function restaurant()
    {
        return $this->belongsTo(Restaurant::class, 'restaurant_id');
    }
}
