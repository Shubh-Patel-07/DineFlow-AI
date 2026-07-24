<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Table extends Model
{
    protected $fillable = ['restaurant_id', 'table_number', 'qr_code_url', 'status'];

    public function orders()
    {
        return $this->hasMany(Order::class);
    }
}
