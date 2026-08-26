<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Expense extends Model
{
    use HasFactory;

    protected $fillable = [
        'restaurant_id',
        'title',
        'amount',
        'category',
        'payment_method',
        'date',
        'notes',
    ];

    public function restaurant()
    {
        return $this->belongsTo(Restaurant::class);
    }
}
