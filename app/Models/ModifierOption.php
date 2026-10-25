<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ModifierOption extends Model
{
    protected $fillable = [
        'modifier_group_id', 'name', 'price', 'is_veg', 'is_default', 'is_available'
    ];

    protected $casts = [
        'price' => 'float',
        'is_veg' => 'boolean',
        'is_default' => 'boolean',
        'is_available' => 'boolean',
    ];

    public function group()
    {
        return $this->belongsTo(ModifierGroup::class, 'modifier_group_id');
    }
}
