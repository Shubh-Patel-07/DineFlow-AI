<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ModifierGroup extends Model
{
    protected $fillable = [
        'restaurant_id', 'name', 'selection_type', 'min_selection', 'max_selection', 'is_required', 'sort_order'
    ];

    protected $casts = [
        'is_required' => 'boolean',
        'min_selection' => 'integer',
        'max_selection' => 'integer',
        'sort_order' => 'integer',
    ];

    public function options()
    {
        return $this->hasMany(ModifierOption::class)->orderBy('is_default', 'desc');
    }

    public function menuItems()
    {
        return $this->belongsToMany(MenuItem::class, 'menu_item_modifiers');
    }
}
