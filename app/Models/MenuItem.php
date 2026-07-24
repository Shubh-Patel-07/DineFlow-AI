<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MenuItem extends Model
{
    protected $fillable = ['restaurant_id', 'name', 'description', 'price', 'category', 'image', 'is_available', 'is_veg'];

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class, 'menu_item_id');
    }
}
