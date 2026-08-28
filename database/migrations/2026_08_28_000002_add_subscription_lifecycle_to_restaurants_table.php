<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('restaurants', function (Blueprint $table) {
            if (!Schema::hasColumn('restaurants', 'subscription_starts_at')) {
                $table->timestamp('subscription_starts_at')->nullable()->after('trial_ends_at');
            }
            if (!Schema::hasColumn('restaurants', 'subscription_expires_at')) {
                $table->timestamp('subscription_expires_at')->nullable()->after('subscription_starts_at');
            }
            if (!Schema::hasColumn('restaurants', 'grace_period_days')) {
                $table->integer('grace_period_days')->default(7)->after('subscription_expires_at');
            }
            if (!Schema::hasColumn('restaurants', 'last_payment_date')) {
                $table->timestamp('last_payment_date')->nullable()->after('grace_period_days');
            }
            if (!Schema::hasColumn('restaurants', 'next_renewal_date')) {
                $table->timestamp('next_renewal_date')->nullable()->after('last_payment_date');
            }
            if (!Schema::hasColumn('restaurants', 'payment_status')) {
                $table->string('payment_status', 30)->default('paid')->after('next_renewal_date');
            }
            if (!Schema::hasColumn('restaurants', 'payment_method')) {
                $table->string('payment_method', 50)->nullable()->after('payment_status');
            }
            if (!Schema::hasColumn('restaurants', 'payment_reference')) {
                $table->string('payment_reference', 100)->nullable()->after('payment_method');
            }
            if (!Schema::hasColumn('restaurants', 'billing_amount')) {
                $table->decimal('billing_amount', 10, 2)->default(0.00)->after('payment_reference');
            }
            if (!Schema::hasColumn('restaurants', 'currency')) {
                $table->string('currency', 10)->default('INR')->after('billing_amount');
            }
            if (!Schema::hasColumn('restaurants', 'suspension_reason')) {
                $table->text('suspension_reason')->nullable()->after('currency');
            }
        });
    }

    public function down(): void
    {
        Schema::table('restaurants', function (Blueprint $table) {
            $cols = [
                'subscription_starts_at',
                'subscription_expires_at',
                'grace_period_days',
                'last_payment_date',
                'next_renewal_date',
                'payment_status',
                'payment_method',
                'payment_reference',
                'billing_amount',
                'currency',
                'suspension_reason',
            ];
            $toDrop = array_filter($cols, fn($c) => Schema::hasColumn('restaurants', $c));
            if (!empty($toDrop)) {
                $table->dropColumn($toDrop);
            }
        });
    }
};
