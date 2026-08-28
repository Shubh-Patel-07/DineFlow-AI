<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DemoRequest extends Model
{
    use HasFactory;

    protected $table = 'demo_requests';

    protected $fillable = [
        'name',
        'restaurant_name',
        'phone',
        'email',
        'city',
        'preferred_time',
        'message',
        'source',
        'status',
        'notes',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Status Constants
    public const STATUS_NEW = 'new';
    public const STATUS_PENDING = 'pending';
    public const STATUS_CONTACTED = 'contacted';
    public const STATUS_CONVERTED = 'converted';
    public const STATUS_CLOSED = 'closed';

    /**
     * Scope for new/pending leads
     */
    public function scopeNewOrPending($query)
    {
        return $query->whereIn('status', [self::STATUS_NEW, self::STATUS_PENDING]);
    }
}
