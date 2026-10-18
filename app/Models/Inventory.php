<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Inventory extends Model
{
    // Fix: use 'inventory' table (not 'inventories')
    protected $table = 'inventory';
    
    protected $fillable = [
        'restaurant_id',
        'item_name',
        'category',
        'quantity',
        'unit',
        'low_stock_alert',
        'cost_per_unit',
        'notes',
    ];

    public function restaurant()
    {
        return $this->belongsTo(Restaurant::class, 'restaurant_id');
    }

    public function recipes()
    {
        return $this->hasMany(Recipe::class, 'inventory_id');
    }

    public function wastageLogs()
    {
        return $this->hasMany(WastageLog::class, 'inventory_id');
    }
}
