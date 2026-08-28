<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('restaurants', function (Blueprint $table) {
            if (!Schema::hasColumn('restaurants', 'is_demo')) {
                $table->boolean('is_demo')->default(false)->after('onboarding_completed')->index();
            }
            if (!Schema::hasColumn('restaurants', 'demo_session_id')) {
                $table->string('demo_session_id')->nullable()->after('is_demo')->unique();
            }
            if (!Schema::hasColumn('restaurants', 'expires_at')) {
                $table->timestamp('expires_at')->nullable()->after('demo_session_id')->index();
            }
        });
    }

    public function down(): void
    {
        Schema::table('restaurants', function (Blueprint $table) {
            $table->dropColumn(['is_demo', 'demo_session_id', 'expires_at']);
        });
    }
};
