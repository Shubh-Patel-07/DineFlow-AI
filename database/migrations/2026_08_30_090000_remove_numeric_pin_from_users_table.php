<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Safely drop numeric_pin from users table to completely remove Quick PIN authentication.
     */
    public function up(): void
    {
        if (Schema::hasColumn('users', 'numeric_pin')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('numeric_pin');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (!Schema::hasColumn('users', 'numeric_pin')) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('numeric_pin', 255)->nullable()->after('password');
            });
        }
    }
};
