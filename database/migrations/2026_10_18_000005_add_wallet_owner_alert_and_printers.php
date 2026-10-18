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
        // 1. Customer Wallet Balance, Birthday & Anniversary
        Schema::table('customers', function (Blueprint $table) {
            if (!Schema::hasColumn('customers', 'wallet_balance')) {
                $table->decimal('wallet_balance', 10, 2)->default(0.00)->after('loyalty_points');
            }
            if (!Schema::hasColumn('customers', 'date_of_birth')) {
                $table->date('date_of_birth')->nullable()->after('wallet_balance');
            }
            if (!Schema::hasColumn('customers', 'anniversary_date')) {
                $table->date('anniversary_date')->nullable()->after('date_of_birth');
            }
        });

        // 2. Restaurant Owner Phone for WhatsApp Day-End Z-Report & Multi-Printer Kitchen Config
        Schema::table('restaurants', function (Blueprint $table) {
            if (!Schema::hasColumn('restaurants', 'owner_phone')) {
                $table->string('owner_phone', 20)->nullable()->after('phone');
            }
            if (!Schema::hasColumn('restaurants', 'kitchen_printers_config')) {
                $table->text('kitchen_printers_config')->nullable()->after('fssai_licence');
            }
        });

        // 3. Orders table wallet discount & cashback tracking
        Schema::table('orders', function (Blueprint $table) {
            if (!Schema::hasColumn('orders', 'wallet_discount')) {
                $table->decimal('wallet_discount', 10, 2)->default(0.00)->after('total_amount');
            }
            if (!Schema::hasColumn('orders', 'cashback_earned')) {
                $table->decimal('cashback_earned', 10, 2)->default(0.00)->after('wallet_discount');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->dropColumn(['wallet_balance', 'date_of_birth', 'anniversary_date']);
        });

        Schema::table('restaurants', function (Blueprint $table) {
            $table->dropColumn(['owner_phone', 'kitchen_printers_config']);
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['wallet_discount', 'cashback_earned']);
        });
    }
};
