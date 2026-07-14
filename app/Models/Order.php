<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = ['restaurant_id', 'table_id', 'total_amount', 'status', 'payment_status', 'payment_method', 'special_instructions'];

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }
}
