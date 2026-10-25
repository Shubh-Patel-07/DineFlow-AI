<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PurchaseOrder extends Model
{
    protected $fillable = [
        'restaurant_id', 'supplier_id', 'po_number', 'order_date', 'expected_date',
        'subtotal', 'tax_amount', 'shipping_charge', 'total_amount', 'status',
        'payment_status', 'notes', 'created_by'
    ];

    protected $casts = [
        'subtotal' => 'float',
        'tax_amount' => 'float',
        'shipping_charge' => 'float',
        'total_amount' => 'float',
        'order_date' => 'date',
        'expected_date' => 'date',
    ];

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function items()
    {
        return $this->hasMany(PurchaseOrderItem::class);
    }

    public function grns()
    {
        return $this->hasMany(GoodsReceivedNote::class);
    }

    public function payments()
    {
        return $this->hasMany(SupplierPayment::class);
    }
}
