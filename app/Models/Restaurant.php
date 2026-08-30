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
        'suspension_reason',
        'suspension_message',
        'suspension_internal_note',
        'suspended_at',
        'expiry_message',
        'grace_period_message',
        'renewal_message',
        'is_demo',
        'demo_session_id',
        'expires_at'
    ];

    protected $casts = [
        'settings' => 'array',
        'gst_rate' => 'decimal:2',
        'service_charge' => 'decimal:2',
        'onboarding_completed' => 'boolean',
        'is_demo' => 'boolean',
        'expires_at' => 'datetime',
        'plan_limits' => 'array',
        'trial_ends_at' => 'datetime',
        'subscription_starts_at' => 'datetime',
        'subscription_expires_at' => 'datetime',
        'last_payment_date' => 'datetime',
        'next_renewal_date' => 'datetime',
        'billing_amount' => 'decimal:2',
        'grace_period_days' => 'integer',
        'suspended_at' => 'datetime',
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

    public function isExpired(): bool
    {
        return in_array(strtolower(trim($this->subscription_status ?? '')), ['expired', 'cancelled']);
    }

    public function isExpiringSoon(): bool
    {
        return strtolower(trim($this->subscription_status ?? '')) === 'expiring';
    }

    public function getCustomerFacingSuspensionMessage(): string
    {
        if (!empty($this->suspension_message)) {
            return $this->suspension_message;
        }

        return "Your restaurant account has been temporarily suspended. Please contact Tendorabuy support for assistance.";
    }

    public function getCustomerFacingExpiryMessage(): string
    {
        if (!empty($this->expiry_message)) {
            return $this->expiry_message;
        }

        return "Your Tendorabuy subscription has expired. Please renew your plan to continue using the platform.";
    }

    public function getCustomerFacingGracePeriodMessage(): string
    {
        if (!empty($this->grace_period_message)) {
            return $this->grace_period_message;
        }

        return "Your subscription has expired and is currently in the grace period. Please renew your plan before the grace period ends.";
    }
}
