<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WaiterCall extends Model
{
    protected $table = 'waiter_calls';
    
    protected $fillable = ['restaurant_id', 'table_id', 'status', 'reason'];

    public function restaurant()
    {
        return $this->belongsTo(Restaurant::class, 'restaurant_id');
    }

    public function table()
    {
        return $this->belongsTo(Table::class, 'table_id');
    }
}
