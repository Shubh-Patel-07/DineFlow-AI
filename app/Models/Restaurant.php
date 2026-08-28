<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

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
        'trial_ends_at',
        'subscription_starts_at',
        'subscription_expires_at',
        'grace_period_days',
        'last_payment_date',
        'next_renewal_date',
        'payment_status',
        'payment_method',
        'payment_reference',
        'billing_amount',
        'currency',
        'suspension_reason'
    ];

    protected $casts = [
        'settings' => 'array',
        'gst_rate' => 'decimal:2',
        'service_charge' => 'decimal:2',
        'onboarding_completed' => 'boolean',
        'plan_limits' => 'array',
        'trial_ends_at' => 'datetime',
        'subscription_starts_at' => 'datetime',
        'subscription_expires_at' => 'datetime',
        'last_payment_date' => 'datetime',
        'next_renewal_date' => 'datetime',
        'billing_amount' => 'decimal:2',
        'grace_period_days' => 'integer'
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

    public function subscriptionHistories()
    {
        return $this->hasMany(SubscriptionHistory::class, 'restaurant_id')->orderBy('created_at', 'desc');
    }

    public function isOperational(): bool
    {
        $status = strtolower(trim($this->subscription_status ?? 'active'));
        if (in_array($status, ['suspended', 'expired', 'cancelled'])) {
            return false;
        }
        return true;
    }

    public function isSuspended(): bool
    {
        return strtolower(trim($this->subscription_status ?? '')) === 'suspended';
    }

    public function isInGracePeriod(): bool
    {
        return strtolower(trim($this->subscription_status ?? '')) === 'grace_period';
    }
}
