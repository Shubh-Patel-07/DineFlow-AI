<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AuditLog extends Model
{
    protected $fillable = [
        'user_id',
        'action',
        'target_type',
        'target_id',
        'restaurant_id',
        'metadata',
        'ip_address',
        'user_agent'
    ];

    protected $casts = [
        'metadata' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function restaurant()
    {
        return $this->belongsTo(Restaurant::class);
    }

    public static function record(string $action, ?int $restaurantId = null, ?string $targetType = null, ?int $targetId = null, ?array $metadata = null): self
    {
        $user = auth()->user();
        return self::create([
            'user_id' => $user?->id,
            'action' => $action,
            'target_type' => $targetType,
            'target_id' => $targetId,
            'restaurant_id' => $restaurantId,
            'metadata' => $metadata,
            'ip_address' => request()->ip(),
            'user_agent' => substr(request()->userAgent() ?? '', 0, 500)
        ]);
    }
}
