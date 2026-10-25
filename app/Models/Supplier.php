<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    protected $fillable = [
        'restaurant_id', 'name', 'company_name', 'contact_person', 'phone', 'email',
        'gstin', 'address', 'city', 'state', 'payment_terms', 'bank_details', 'status'
    ];

    public function purchaseOrders()
    {
        return $this->hasMany(PurchaseOrder::class)->orderBy('order_date', 'desc');
    }

    public function payments()
    {
        return $this->hasMany(SupplierPayment::class)->orderBy('payment_date', 'desc');
    }
}
