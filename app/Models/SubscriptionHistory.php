<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SubscriptionHistory extends Model
{
    protected $fillable = [
        'restaurant_id',
        'previous_plan',
        'new_plan',
        'previous_status',
        'new_status',
        'starts_at',
        'expires_at',
        'billing_amount',
        'payment_status',
        'payment_method',
        'payment_reference',
        'changed_by',
        'reason',
        'metadata',
    ];

    protected $casts = [
        'starts_at' => 'datetime',
        'expires_at' => 'datetime',
        'billing_amount' => 'decimal:2',
        'metadata' => 'array',
    ];

    public function restaurant()
    {
        return $this->belongsTo(Restaurant::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'changed_by');
    }

    public static function record(Restaurant $restaurant, string $newPlan, string $newStatus, array $options = []): self
    {
        return self::create([
            'restaurant_id' => $restaurant->id,
            'previous_plan' => $options['previous_plan'] ?? $restaurant->getOriginal('plan_type'),
            'new_plan' => $newPlan,
            'previous_status' => $options['previous_status'] ?? $restaurant->getOriginal('subscription_status'),
            'new_status' => $newStatus,
            'starts_at' => $options['starts_at'] ?? $restaurant->subscription_starts_at,
            'expires_at' => $options['expires_at'] ?? $restaurant->subscription_expires_at,
            'billing_amount' => $options['billing_amount'] ?? $restaurant->billing_amount ?? 0.00,
            'payment_status' => $options['payment_status'] ?? $restaurant->payment_status ?? 'paid',
            'payment_method' => $options['payment_method'] ?? $restaurant->payment_method,
            'payment_reference' => $options['payment_reference'] ?? $restaurant->payment_reference,
            'changed_by' => $options['changed_by'] ?? auth()->id(),
            'reason' => $options['reason'] ?? 'Super Admin manual action',
            'metadata' => $options['metadata'] ?? null,
        ]);
    }
}
