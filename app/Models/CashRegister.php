<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CashRegister extends Model
{
    protected $fillable = [
        'restaurant_id',
        'user_id',
        'user_name',
        'opened_at',
        'closed_at',
        'opening_cash',
        'closing_cash',
        'expected_cash',
        'difference',
        'total_sales',
        'total_cash_sales',
        'total_upi_sales',
        'total_card_sales',
        'total_orders',
        'status',
        'notes'
    ];

    protected $casts = [
        'opened_at' => 'datetime',
        'closed_at' => 'datetime',
        'opening_cash' => 'float',
        'closing_cash' => 'float',
        'expected_cash' => 'float',
        'difference' => 'float',
        'total_sales' => 'float',
        'total_cash_sales' => 'float',
        'total_upi_sales' => 'float',
        'total_card_sales' => 'float',
    ];

    public function transactions()
    {
        return $this->hasMany(CashTransaction::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function restaurant()
    {
        return $this->belongsTo(Restaurant::class);
    }
}
