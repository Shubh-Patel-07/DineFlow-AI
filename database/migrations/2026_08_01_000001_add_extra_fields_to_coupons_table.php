<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('coupons', function (Blueprint $table) {
            if (!Schema::hasColumn('coupons', 'is_auto_apply')) {
                $table->boolean('is_auto_apply')->default(false)->after('is_active');
            }
            if (!Schema::hasColumn('coupons', 'first_time_only')) {
                $table->boolean('first_time_only')->default(false)->after('is_auto_apply');
            }
            if (!Schema::hasColumn('coupons', 'category')) {
                $table->string('category')->default('all')->after('first_time_only');
            }
            if (!Schema::hasColumn('coupons', 'order_type')) {
                $table->string('order_type')->default('all')->after('category');
            }
        });
    }

    public function down(): void
    {
        Schema::table('coupons', function (Blueprint $table) {
            $table->dropColumn(['is_auto_apply', 'first_time_only', 'category', 'order_type']);
        });
    }
};
