<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Inventory extends Model
{
    protected $table = 'inventories';
    
    protected $fillable = ['restaurant_id', 'name', 'quantity', 'unit', 'threshold'];
}
