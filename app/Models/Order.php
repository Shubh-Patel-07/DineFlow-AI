<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'restaurant_id', 'table_id', 'total_amount', 'status', 'payment_status', 
        'payment_method', 'special_instructions', 'kot_language', 'split_data',
        'order_type', 'rider_id', 'customer_name', 'customer_phone', 'customer_address', 
        'delivery_fee', 'delivery_status', 'aggregator_order_id', 'prep_time_minutes',
        'customer_gstin', 'customer_company', 'cgst_amount', 'sgst_amount'
    ];

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function table()
    {
        return $this->belongsTo(Table::class);
    }

    public function restaurant()
    {
        return $this->belongsTo(Restaurant::class);
    }

    public function rider()
    {
        return $this->belongsTo(DeliveryRider::class, 'rider_id');
    }
}
