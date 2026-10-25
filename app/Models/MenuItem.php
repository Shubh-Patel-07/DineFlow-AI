<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MenuItem extends Model
{
    protected $fillable = ['restaurant_id', 'name', 'description', 'price', 'category', 'image', 'is_available', 'is_veg', 'has_modifiers', 'is_combo'];

    protected $casts = [
        'is_available'  => 'boolean',
        'is_veg'        => 'boolean',
        'has_modifiers' => 'boolean',
        'is_combo'      => 'boolean',
        'price'         => 'float',
    ];

    public function modifierGroups()
    {
        return $this->belongsToMany(ModifierGroup::class, 'menu_item_modifiers')->with('options');
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class, 'menu_item_id');
    }

    public function restaurant()
    {
        return $this->belongsTo(Restaurant::class, 'restaurant_id');
    }

    public function recipes()
    {
        return $this->hasMany(Recipe::class, 'menu_item_id');
    }
}
