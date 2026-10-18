<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CashTransaction extends Model
{
    protected $fillable = [
        'cash_register_id',
        'restaurant_id',
        'user_id',
        'type',
        'amount',
        'category',
        'reason'
    ];

    protected $casts = [
        'amount' => 'float',
    ];

    public function register()
    {
        return $this->belongsTo(CashRegister::class, 'cash_register_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
