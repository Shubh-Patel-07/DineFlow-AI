<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('restaurants', function (Blueprint $table) {
            if (!Schema::hasColumn('restaurants', 'signature')) {
                $table->string('signature', 500)->nullable()->after('logo');
            }
            if (!Schema::hasColumn('restaurants', 'signature_name')) {
                $table->string('signature_name', 255)->nullable()->after('signature');
            }
        });
    }

    public function down(): void
    {
        Schema::table('restaurants', function (Blueprint $table) {
            if (Schema::hasColumn('restaurants', 'signature_name')) {
                $table->dropColumn('signature_name');
            }
            if (Schema::hasColumn('restaurants', 'signature')) {
                $table->dropColumn('signature');
            }
        });
    }
};
