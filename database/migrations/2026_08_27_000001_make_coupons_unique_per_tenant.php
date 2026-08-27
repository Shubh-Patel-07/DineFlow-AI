<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Safely drop global unique index if exists, and add per-tenant unique composite
        try {
            Schema::table('coupons', function (Blueprint $table) {
                $table->dropUnique('coupons_code_unique');
            });
        } catch (\Throwable $e) {
            // Index might not exist or already dropped
        }

        try {
            Schema::table('coupons', function (Blueprint $table) {
                $table->unique(['restaurant_id', 'code']);
            });
        } catch (\Throwable $e) {
            // Index might already exist
        }
    }

    public function down(): void
    {
        try {
            Schema::table('coupons', function (Blueprint $table) {
                $table->dropUnique(['restaurant_id', 'code']);
                $table->unique('code');
            });
        } catch (\Throwable $e) {
            // Revert silently if fails
        }
    }
};
