<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'username',
        'email',
        'phone',
        'password',
        'numeric_pin',
        'role',
        'restaurant_id',
        'is_active',
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'numeric_pin',
    ];

    protected $appends = [
        'has_pin',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }

    public function restaurant()
    {
        return $this->belongsTo(Restaurant::class, 'restaurant_id');
    }

    /**
     * Determine if PIN is configured without exposing plaintext.
     */
    public function getHasPinAttribute(): bool
    {
        return !empty($this->numeric_pin);
    }

    /**
     * Securely verify Quick PIN with auto-migration to bcrypt hash.
     */
    public function verifyPin(string $pin): bool
    {
        $pin = trim($pin);
        if (empty($this->numeric_pin) && empty($this->password)) {
            return false;
        }

        $isValid = false;

        // 1. Check against hashed numeric_pin
        if (!empty($this->numeric_pin) && str_starts_with($this->numeric_pin, '$2y$')) {
            $isValid = Hash::check($pin, $this->numeric_pin);
        }
        // 2. Check against hashed password
        elseif (!empty($this->password) && Hash::check($pin, $this->password)) {
            $isValid = true;
        }
        // 3. Fallback for legacy plain numeric_pin
        elseif (!empty($this->numeric_pin) && hash_equals((string)$this->numeric_pin, (string)$pin)) {
            $isValid = true;
        }

        // Auto-upgrade legacy plain PIN to secure hash
        if ($isValid && !empty($this->numeric_pin) && !str_starts_with($this->numeric_pin, '$2y$')) {
            $this->numeric_pin = Hash::make($pin);
            $this->saveQuietly();
        }

        return $isValid;
    }

    /**
     * Store hashed PIN securely.
     */
    public function setPin(?string $pin): void
    {
        if (empty($pin)) {
            $this->numeric_pin = null;
        } else {
            $this->numeric_pin = Hash::make(trim($pin));
        }
    }
}
