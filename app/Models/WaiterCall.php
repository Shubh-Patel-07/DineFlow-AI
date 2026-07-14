<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WaiterCall extends Model
{
    protected $table = 'waiter_calls';
    
    protected $fillable = ['restaurant_id', 'table_id', 'status'];
}
