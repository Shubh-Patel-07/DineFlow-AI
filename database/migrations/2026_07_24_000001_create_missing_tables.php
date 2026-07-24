<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Fix 1: Create inventory table (was 'inventories' but frontend uses 'inventory')
        if (!Schema::hasTable('inventory')) {
            Schema::create('inventory', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('restaurant_id')->default(1);
                $table->string('item_name');
                $table->string('category')->default('General');
                $table->decimal('quantity', 10, 2)->default(0);
                $table->string('unit')->default('units');
                $table->decimal('low_stock_alert', 10, 2)->default(10);
                $table->decimal('cost_per_unit', 10, 2)->default(0);
                $table->text('notes')->nullable();
                $table->timestamps();
            });
        }

        // Fix 2: Create coupons table
        if (!Schema::hasTable('coupons')) {
            Schema::create('coupons', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('restaurant_id')->default(1);
                $table->string('code')->unique();
                $table->string('name');
                $table->enum('discount_type', ['percentage', 'fixed'])->default('percentage');
                $table->decimal('discount_value', 8, 2);
                $table->decimal('min_order_amount', 8, 2)->default(0);
                $table->decimal('max_discount', 8, 2)->nullable();
                $table->integer('usage_limit')->nullable();
                $table->integer('used_count')->default(0);
                $table->boolean('is_active')->default(true);
                $table->timestamp('expires_at')->nullable();
                $table->timestamps();
            });
        }

        // Fix 3: Create customers table
        if (!Schema::hasTable('customers')) {
            Schema::create('customers', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('restaurant_id')->default(1);
                $table->string('name');
                $table->string('phone')->nullable();
                $table->string('email')->nullable();
                $table->integer('total_visits')->default(0);
                $table->decimal('total_spent', 10, 2)->default(0);
                $table->integer('loyalty_points')->default(0);
                $table->timestamp('last_visit')->nullable();
                $table->text('notes')->nullable();
                $table->timestamps();
            });
        }

        // Fix 4: Create feedbacks alias (some code uses 'feedbacks', some uses 'feedback')
        // We already have 'feedback' table - create a view or just ensure both names work
        // The actual table is 'feedback' - we'll update the model
    }

    public function down(): void
    {
        Schema::dropIfExists('inventory');
        Schema::dropIfExists('coupons');
        Schema::dropIfExists('customers');
    }
};
