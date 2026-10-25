<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    protected $fillable = [
        'order_id', 'menu_item_id', 'name', 'quantity', 'price',
        'selected_modifiers', 'combo_selections',
        'notes', 'kot_status', 'cancelled_reason', 'cancelled_by'
    ];

    protected $casts = [
        'selected_modifiers' => 'array',
        'combo_selections' => 'array',
        'price' => 'float',
        'quantity' => 'integer',
    ];

    public function menuItem()
    {
        return $this->belongsTo(MenuItem::class, 'menu_item_id');
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
