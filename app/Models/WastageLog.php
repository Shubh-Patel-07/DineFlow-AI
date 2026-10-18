<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WastageLog extends Model
{
    protected $fillable = [
        'restaurant_id',
        'inventory_id',
        'quantity',
        'unit',
        'reason',
        'cost_loss',
        'recorded_by',
    ];

    public function inventory()
    {
        return $this->belongsTo(Inventory::class, 'inventory_id');
    }

    public function restaurant()
    {
        return $this->belongsTo(Restaurant::class, 'restaurant_id');
    }
}
