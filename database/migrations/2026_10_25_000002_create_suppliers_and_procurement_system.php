<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Suppliers Table
        if (!Schema::hasTable('suppliers')) {
            Schema::create('suppliers', function (Blueprint $table) {
                $table->id();
                $table->string('restaurant_id')->index();
                $table->string('name');
                $table->string('company_name')->nullable();
                $table->string('contact_person')->nullable();
                $table->string('phone')->nullable();
                $table->string('email')->nullable();
                $table->string('gstin')->nullable();
                $table->text('address')->nullable();
                $table->string('city')->nullable();
                $table->string('state')->nullable();
                $table->string('payment_terms')->default('Net 30'); // Cash, Net 15, Net 30, Advance
                $table->string('bank_details')->nullable();
                $table->string('status')->default('ACTIVE'); // ACTIVE, INACTIVE
                $table->timestamps();
            });
        }

        // 2. Purchase Orders (PO)
        if (!Schema::hasTable('purchase_orders')) {
            Schema::create('purchase_orders', function (Blueprint $table) {
                $table->id();
                $table->string('restaurant_id')->index();
                $table->foreignId('supplier_id')->constrained('suppliers')->onDelete('cascade');
                $table->string('po_number')->unique();
                $table->date('order_date');
                $table->date('expected_date')->nullable();
                $table->decimal('subtotal', 10, 2)->default(0.00);
                $table->decimal('tax_amount', 10, 2)->default(0.00);
                $table->decimal('shipping_charge', 10, 2)->default(0.00);
                $table->decimal('total_amount', 10, 2)->default(0.00);
                $table->string('status')->default('DRAFT'); // DRAFT, SENT, PARTIAL, COMPLETED, CANCELLED
                $table->string('payment_status')->default('UNPAID'); // UNPAID, PARTIAL, PAID
                $table->text('notes')->nullable();
                $table->string('created_by')->nullable();
                $table->timestamps();
            });
        }

        // 3. Purchase Order Items
        if (!Schema::hasTable('purchase_order_items')) {
            Schema::create('purchase_order_items', function (Blueprint $table) {
                $table->id();
                $table->foreignId('purchase_order_id')->constrained('purchase_orders')->onDelete('cascade');
                $table->string('ingredient_name');
                $table->decimal('quantity_ordered', 10, 2);
                $table->decimal('quantity_received', 10, 2)->default(0.00);
                $table->string('unit')->default('kg');
                $table->decimal('unit_price', 10, 2)->default(0.00);
                $table->decimal('tax_rate', 5, 2)->default(0.00);
                $table->decimal('total_price', 10, 2)->default(0.00);
                $table->timestamps();
            });
        }

        // 4. Goods Received Notes (GRN - Inward Stock Receiving)
        if (!Schema::hasTable('goods_received_notes')) {
            Schema::create('goods_received_notes', function (Blueprint $table) {
                $table->id();
                $table->string('restaurant_id')->index();
                $table->foreignId('purchase_order_id')->constrained('purchase_orders')->onDelete('cascade');
                $table->string('grn_number')->unique();
                $table->date('received_date');
                $table->string('supplier_invoice_number')->nullable();
                $table->date('supplier_invoice_date')->nullable();
                $table->decimal('billed_amount', 10, 2)->default(0.00);
                $table->string('received_by')->nullable();
                $table->text('remarks')->nullable();
                $table->timestamps();
            });
        }

        // 5. Supplier Payments
        if (!Schema::hasTable('supplier_payments')) {
            Schema::create('supplier_payments', function (Blueprint $table) {
                $table->id();
                $table->string('restaurant_id')->index();
                $table->foreignId('supplier_id')->constrained('suppliers')->onDelete('cascade');
                $table->foreignId('purchase_order_id')->nullable()->constrained('purchase_orders')->onDelete('set null');
                $table->string('payment_number')->unique();
                $table->decimal('amount', 10, 2);
                $table->string('payment_mode')->default('BANK_TRANSFER'); // CASH, UPI, BANK_TRANSFER, CHEQUE
                $table->string('transaction_reference')->nullable();
                $table->date('payment_date');
                $table->text('notes')->nullable();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('supplier_payments');
        Schema::dropIfExists('goods_received_notes');
        Schema::dropIfExists('purchase_order_items');
        Schema::dropIfExists('purchase_orders');
        Schema::dropIfExists('suppliers');
    }
};
