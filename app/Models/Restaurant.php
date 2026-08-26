<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Restaurant extends Model
{
    protected $fillable = [
        'name',
        'address',
        'phone',
        'logo',
        'gstin',
        'gst_rate',
        'service_charge',
        'settings',
        'plan_type',
        'subscription_status',
        'onboarding_completed',
        'plan_limits',
        'trial_ends_at'
    ];

    protected $casts = [
        'settings' => 'array',
        'gst_rate' => 'decimal:2',
        'service_charge' => 'decimal:2',
        'onboarding_completed' => 'boolean',
        'plan_limits' => 'array',
        'trial_ends_at' => 'datetime'
    ];

    public function tables()
    {
        return $this->hasMany(Table::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function menuItems()
    {
        return $this->hasMany(MenuItem::class);
    }

    public function users()
    {
        return $this->hasMany(User::class, 'restaurant_id');
    }

    public function coupons()
    {
        return $this->hasMany(Coupon::class, 'restaurant_id');
    }

    public function customers()
    {
        return $this->hasMany(Customer::class, 'restaurant_id');
    }

    public function inventory()
    {
        return $this->hasMany(Inventory::class, 'restaurant_id');
    }

    public function feedbacks()
    {
        return $this->hasMany(Feedback::class, 'restaurant_id');
    }

    public function waiterCalls()
    {
        return $this->hasMany(WaiterCall::class, 'restaurant_id');
    }
}
