<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GoodsReceivedNote extends Model
{
    protected $fillable = [
        'restaurant_id', 'purchase_order_id', 'grn_number', 'received_date',
        'supplier_invoice_number', 'supplier_invoice_date', 'billed_amount',
        'received_by', 'remarks'
    ];

    protected $casts = [
        'received_date' => 'date',
        'supplier_invoice_date' => 'date',
        'billed_amount' => 'float',
    ];

    public function purchaseOrder()
    {
        return $this->belongsTo(PurchaseOrder::class);
    }
}
