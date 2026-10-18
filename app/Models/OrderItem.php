<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    protected $fillable = ['order_id', 'menu_item_id', 'name', 'quantity', 'price', 'notes', 'kot_status', 'cancelled_reason', 'cancelled_by'];

    public function menuItem()
    {
        return $this->belongsTo(MenuItem::class, 'menu_item_id');
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
