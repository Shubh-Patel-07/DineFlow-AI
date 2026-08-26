<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('restaurants', function (Blueprint $table) {
            $table->string('gstin')->nullable()->after('phone');
            $table->decimal('gst_rate', 5, 2)->default(5.00)->after('gstin');
            $table->decimal('service_charge', 5, 2)->default(5.00)->after('gst_rate');
            $table->json('settings')->nullable()->after('service_charge');
        });
    }

    public function down(): void
    {
        Schema::table('restaurants', function (Blueprint $table) {
            $table->dropColumn(['gstin', 'gst_rate', 'service_charge', 'settings']);
        });
    }
};
