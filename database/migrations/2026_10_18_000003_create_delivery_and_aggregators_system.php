<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Create delivery_riders table
        if (!Schema::hasTable('delivery_riders')) {
            Schema::create('delivery_riders', function (Blueprint $table) {
                $table->id();
                $table->string('restaurant_id');
                $table->string('name', 100);
                $table->string('phone', 20);
                $table->string('vehicle_number', 50)->nullable();
                $table->string('status', 30)->default('available'); // available, on_delivery, offline
                $table->integer('active_orders_count')->default(0);
                $table->integer('total_deliveries')->default(0);
                $table->timestamps();
            });
        }

        // 2. Add delivery and aggregator fields to orders table
        Schema::table('orders', function (Blueprint $table) {
            if (!Schema::hasColumn('orders', 'order_type')) {
                $table->string('order_type', 30)->default('dine_in')->after('table_id'); // dine_in, takeaway, delivery, zomato, swiggy
            }
            if (!Schema::hasColumn('orders', 'rider_id')) {
                $table->unsignedBigInteger('rider_id')->nullable()->after('order_type');
            }
            if (!Schema::hasColumn('orders', 'customer_name')) {
                $table->string('customer_name', 100)->nullable()->after('rider_id');
            }
            if (!Schema::hasColumn('orders', 'customer_phone')) {
                $table->string('customer_phone', 20)->nullable()->after('customer_name');
            }
            if (!Schema::hasColumn('orders', 'customer_address')) {
                $table->text('customer_address')->nullable()->after('customer_phone');
            }
            if (!Schema::hasColumn('orders', 'delivery_fee')) {
                $table->decimal('delivery_fee', 8, 2)->default(0)->after('total_amount');
            }
            if (!Schema::hasColumn('orders', 'delivery_status')) {
                $table->string('delivery_status', 30)->default('pending')->after('status'); // pending, assigned, out_for_delivery, delivered, failed
            }
            if (!Schema::hasColumn('orders', 'aggregator_order_id')) {
                $table->string('aggregator_order_id', 50)->nullable()->after('delivery_status');
            }
            if (!Schema::hasColumn('orders', 'prep_time_minutes')) {
                $table->integer('prep_time_minutes')->default(15)->after('aggregator_order_id');
            }
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('delivery_riders');

        Schema::table('orders', function (Blueprint $table) {
            $cols = ['order_type', 'rider_id', 'customer_name', 'customer_phone', 'customer_address', 'delivery_fee', 'delivery_status', 'aggregator_order_id', 'prep_time_minutes'];
            foreach ($cols as $col) {
                if (Schema::hasColumn('orders', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
