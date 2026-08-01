<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Coupon extends Model
{
    protected $table = 'coupons';

    protected $fillable = [
        'restaurant_id',
        'code',
        'name',
        'discount_type',
        'discount_value',
        'min_order_amount',
        'max_discount',
        'usage_limit',
        'used_count',
        'is_active',
        'is_auto_apply',
        'first_time_only',
        'category',
        'order_type',
        'expires_at',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_auto_apply' => 'boolean',
        'first_time_only' => 'boolean',
        'expires_at' => 'datetime',
        'discount_value' => 'float',
        'min_order_amount' => 'float',
        'max_discount' => 'float',
    ];
}
