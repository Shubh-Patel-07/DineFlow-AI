<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('restaurants', function (Blueprint $table) {
            if (!Schema::hasColumn('restaurants', 'plan_type')) {
                $table->string('plan_type')->default('free')->after('settings');
            }
            if (!Schema::hasColumn('restaurants', 'subscription_status')) {
                $table->string('subscription_status')->default('trial')->after('plan_type');
            }
            if (!Schema::hasColumn('restaurants', 'onboarding_completed')) {
                $table->boolean('onboarding_completed')->default(false)->after('subscription_status');
            }
            if (!Schema::hasColumn('restaurants', 'plan_limits')) {
                $table->json('plan_limits')->nullable()->after('onboarding_completed');
            }
            if (!Schema::hasColumn('restaurants', 'trial_ends_at')) {
                $table->timestamp('trial_ends_at')->nullable()->after('plan_limits');
            }
        });

        // Set safe defaults for existing restaurants so existing operations continue seamlessly
        try {
            DB::table('restaurants')->whereNull('plan_limits')->update([
                'plan_type' => 'free',
                'subscription_status' => 'active',
                'onboarding_completed' => true,
                'plan_limits' => json_encode([
                    'tables' => 50,
                    'staff' => 25,
                    'menu_items' => 500,
                    'monthly_orders' => 5000,
                ]),
            ]);
        } catch (\Throwable $e) {
            // Ignore if in fresh testing environment
        }
    }

    public function down(): void
    {
        Schema::table('restaurants', function (Blueprint $table) {
            $columns = [];
            if (Schema::hasColumn('restaurants', 'plan_type')) $columns[] = 'plan_type';
            if (Schema::hasColumn('restaurants', 'subscription_status')) $columns[] = 'subscription_status';
            if (Schema::hasColumn('restaurants', 'onboarding_completed')) $columns[] = 'onboarding_completed';
            if (Schema::hasColumn('restaurants', 'plan_limits')) $columns[] = 'plan_limits';
            if (Schema::hasColumn('restaurants', 'trial_ends_at')) $columns[] = 'trial_ends_at';
            
            if (!empty($columns)) {
                $table->dropColumn($columns);
            }
        });
    }
};
