<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Recipes & BOM (Bill of Materials) mapping menu items to raw inventory ingredients
        if (!Schema::hasTable('recipes')) {
            Schema::create('recipes', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('restaurant_id')->index();
                $table->unsignedBigInteger('menu_item_id')->index();
                $table->unsignedBigInteger('inventory_id')->index();
                $table->decimal('quantity_required', 10, 4)->comment('Amount of raw ingredient consumed per dish');
                $table->string('unit', 20)->default('pcs')->comment('kg, gm, ltr, ml, pcs');
                $table->timestamps();
            });
        }

        // 2. Wastage & Spoilage tracking
        if (!Schema::hasTable('wastage_logs')) {
            Schema::create('wastage_logs', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('restaurant_id')->index();
                $table->unsignedBigInteger('inventory_id')->index();
                $table->decimal('quantity', 10, 4);
                $table->string('unit', 20);
                $table->string('reason', 255);
                $table->decimal('cost_loss', 10, 2)->default(0.00);
                $table->string('recorded_by', 100)->nullable();
                $table->timestamps();
            });
        }

        // 3. GST B2B billing fields on orders table
        Schema::table('orders', function (Blueprint $table) {
            if (!Schema::hasColumn('orders', 'customer_gstin')) {
                $table->string('customer_gstin', 20)->nullable()->after('customer_phone');
            }
            if (!Schema::hasColumn('orders', 'customer_company')) {
                $table->string('customer_company', 150)->nullable()->after('customer_gstin');
            }
            if (!Schema::hasColumn('orders', 'cgst_amount')) {
                $table->decimal('cgst_amount', 10, 2)->default(0.00)->after('total_amount');
            }
            if (!Schema::hasColumn('orders', 'sgst_amount')) {
                $table->decimal('sgst_amount', 10, 2)->default(0.00)->after('cgst_amount');
            }
        });

        // 4. Restaurant Tax & FSSAI Details
        Schema::table('restaurants', function (Blueprint $table) {
            if (!Schema::hasColumn('restaurants', 'gstin')) {
                $table->string('gstin', 20)->nullable()->after('phone');
            }
            if (!Schema::hasColumn('restaurants', 'fssai_licence')) {
                $table->string('fssai_licence', 30)->nullable()->after('gstin');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('recipes');
        Schema::dropIfExists('wastage_logs');

        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['customer_gstin', 'customer_company', 'cgst_amount', 'sgst_amount']);
        });

        Schema::table('restaurants', function (Blueprint $table) {
            $table->dropColumn(['gstin', 'fssai_licence']);
        });
    }
};
