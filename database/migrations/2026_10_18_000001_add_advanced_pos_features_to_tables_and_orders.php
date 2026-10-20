<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Add notes and status to order_items
        Schema::table('order_items', function (Blueprint $table) {
            if (!Schema::hasColumn('order_items', 'notes')) {
                $table->text('notes')->nullable()->after('price');
            }
            if (!Schema::hasColumn('order_items', 'kot_status')) {
                $table->string('kot_status', 50)->default('pending')->after('notes');
            }
        });

        // 2. Add section and capacity to tables
        Schema::table('tables', function (Blueprint $table) {
            if (!Schema::hasColumn('tables', 'section')) {
                $table->string('section', 100)->default('Ground Floor')->after('table_number');
            }
            if (!Schema::hasColumn('tables', 'capacity')) {
                $table->integer('capacity')->default(4)->after('section');
            }
        });

        // 3. Add split_data and special_notes to orders
        Schema::table('orders', function (Blueprint $table) {
            if (!Schema::hasColumn('orders', 'special_notes')) {
                $table->text('special_notes')->nullable()->after('status');
            }
            if (!Schema::hasColumn('orders', 'split_data')) {
                $table->longText('split_data')->nullable()->after('special_notes');
            }
        });
    }

    public function down(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            if (Schema::hasColumn('order_items', 'notes')) {
                $table->dropColumn('notes');
            }
            if (Schema::hasColumn('order_items', 'kot_status')) {
                $table->dropColumn('kot_status');
            }
        });

        Schema::table('tables', function (Blueprint $table) {
            if (Schema::hasColumn('tables', 'section')) {
                $table->dropColumn('section');
            }
            if (Schema::hasColumn('tables', 'capacity')) {
                $table->dropColumn('capacity');
            }
        });

        Schema::table('orders', function (Blueprint $table) {
            if (Schema::hasColumn('orders', 'special_notes')) {
                $table->dropColumn('special_notes');
            }
            if (Schema::hasColumn('orders', 'split_data')) {
                $table->dropColumn('split_data');
            }
        });
    }
};
