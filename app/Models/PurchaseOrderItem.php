<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PurchaseOrderItem extends Model
{
    protected $fillable = [
        'purchase_order_id', 'ingredient_name', 'quantity_ordered', 'quantity_received',
        'unit', 'unit_price', 'tax_rate', 'total_price'
    ];

    protected $casts = [
        'quantity_ordered' => 'float',
        'quantity_received' => 'float',
        'unit_price' => 'float',
        'tax_rate' => 'float',
        'total_price' => 'float',
    ];

    public function purchaseOrder()
    {
        return $this->belongsTo(PurchaseOrder::class);
    }
}
