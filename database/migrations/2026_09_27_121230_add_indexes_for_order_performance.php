<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->index(['restaurant_id', 'created_at'], 'idx_orders_rest_created');
            $table->index(['restaurant_id', 'status'], 'idx_orders_rest_status');
            $table->index(['restaurant_id', 'payment_status'], 'idx_orders_rest_payment');
            $table->index(['restaurant_id', 'table_id', 'payment_status'], 'idx_orders_rest_table_pay');
            $table->index('status', 'idx_orders_status');
        });

        Schema::table('order_items', function (Blueprint $table) {
            $table->index('order_id', 'idx_order_items_order_id');
            $table->index('menu_item_id', 'idx_order_items_menu_item_id');
        });

        Schema::table('menu_items', function (Blueprint $table) {
            $table->index(['restaurant_id', 'is_available'], 'idx_menu_rest_available');
            $table->index(['restaurant_id', 'category'], 'idx_menu_rest_category');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropIndex('idx_orders_rest_created');
            $table->dropIndex('idx_orders_rest_status');
            $table->dropIndex('idx_orders_rest_payment');
            $table->dropIndex('idx_orders_rest_table_pay');
            $table->dropIndex('idx_orders_status');
        });

        Schema::table('order_items', function (Blueprint $table) {
            $table->dropIndex('idx_order_items_order_id');
            $table->dropIndex('idx_order_items_menu_item_id');
        });

        Schema::table('menu_items', function (Blueprint $table) {
            $table->dropIndex('idx_menu_rest_available');
            $table->dropIndex('idx_menu_rest_category');
        });
    }
};
