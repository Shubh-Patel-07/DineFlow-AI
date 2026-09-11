<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('restaurants', function (Blueprint $table) {
            if (!Schema::hasColumn('restaurants', 'upi_qr')) {
                $table->string('upi_qr', 500)->nullable()->after('signature_name');
            }
            if (!Schema::hasColumn('restaurants', 'upi_vpa')) {
                $table->string('upi_vpa', 100)->nullable()->after('upi_qr');
            }
        });
    }

    public function down(): void
    {
        Schema::table('restaurants', function (Blueprint $table) {
            if (Schema::hasColumn('restaurants', 'upi_qr')) {
                $table->dropColumn('upi_qr');
            }
            if (Schema::hasColumn('restaurants', 'upi_vpa')) {
                $table->dropColumn('upi_vpa');
            }
        });
    }
};
