<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Create cash_registers table (Shift Open / Close)
        if (!Schema::hasTable('cash_registers')) {
            Schema::create('cash_registers', function (Blueprint $table) {
                $table->id();
                $table->string('restaurant_id');
                $table->unsignedBigInteger('user_id')->nullable();
                $table->string('user_name')->default('Cashier');
                $table->timestamp('opened_at')->useCurrent();
                $table->timestamp('closed_at')->nullable();
                $table->decimal('opening_cash', 10, 2)->default(0);
                $table->decimal('closing_cash', 10, 2)->nullable();
                $table->decimal('expected_cash', 10, 2)->nullable();
                $table->decimal('difference', 10, 2)->nullable();
                $table->decimal('total_sales', 10, 2)->default(0);
                $table->decimal('total_cash_sales', 10, 2)->default(0);
                $table->decimal('total_upi_sales', 10, 2)->default(0);
                $table->decimal('total_card_sales', 10, 2)->default(0);
                $table->integer('total_orders')->default(0);
                $table->string('status', 30)->default('open'); // open, closed
                $table->text('notes')->nullable();
                $table->timestamps();
            });
        }

        // 2. Create cash_transactions table (Petty Cash Cash-In / Cash-Out)
        if (!Schema::hasTable('cash_transactions')) {
            Schema::create('cash_transactions', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('cash_register_id')->nullable();
                $table->string('restaurant_id');
                $table->unsignedBigInteger('user_id')->nullable();
                $table->string('type', 20); // cash_in, cash_out
                $table->decimal('amount', 10, 2);
                $table->string('category', 100)->default('misc');
                $table->string('reason', 255);
                $table->timestamps();
            });
        }

        // 3. Add manager_pin to restaurants table for authorization
        Schema::table('restaurants', function (Blueprint $table) {
            if (!Schema::hasColumn('restaurants', 'manager_pin')) {
                $table->string('manager_pin', 10)->default('1234')->after('name');
            }
        });

        // 4. Add cancellation audit fields to order_items
        Schema::table('order_items', function (Blueprint $table) {
            if (!Schema::hasColumn('order_items', 'cancelled_reason')) {
                $table->string('cancelled_reason', 255)->nullable()->after('kot_status');
            }
            if (!Schema::hasColumn('order_items', 'cancelled_by')) {
                $table->string('cancelled_by', 100)->nullable()->after('cancelled_reason');
            }
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cash_transactions');
        Schema::dropIfExists('cash_registers');

        Schema::table('restaurants', function (Blueprint $table) {
            if (Schema::hasColumn('restaurants', 'manager_pin')) {
                $table->dropColumn('manager_pin');
            }
        });

        Schema::table('order_items', function (Blueprint $table) {
            if (Schema::hasColumn('order_items', 'cancelled_reason')) {
                $table->dropColumn('cancelled_reason');
            }
            if (Schema::hasColumn('order_items', 'cancelled_by')) {
                $table->dropColumn('cancelled_by');
            }
        });
    }
};
