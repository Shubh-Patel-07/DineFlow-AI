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
}
