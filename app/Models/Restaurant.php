<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Restaurant extends Model
{
    protected $fillable = ['name', 'address', 'phone', 'logo'];

    public function tables()
    {
        return $this->hasMany(Table::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function menuItems()
    {
        return $this->hasMany(MenuItem::class);
    }
}
