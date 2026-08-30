<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('restaurants', function (Blueprint $table) {
            if (!Schema::hasColumn('restaurants', 'expiry_message')) {
                $table->text('expiry_message')->nullable()->after('suspension_message');
            }
            if (!Schema::hasColumn('restaurants', 'grace_period_message')) {
                $table->text('grace_period_message')->nullable()->after('expiry_message');
            }
            if (!Schema::hasColumn('restaurants', 'renewal_message')) {
                $table->text('renewal_message')->nullable()->after('grace_period_message');
            }
        });
    }

    public function down(): void
    {
        Schema::table('restaurants', function (Blueprint $table) {
            $cols = ['expiry_message', 'grace_period_message', 'renewal_message'];
            foreach ($cols as $col) {
                if (Schema::hasColumn('restaurants', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
