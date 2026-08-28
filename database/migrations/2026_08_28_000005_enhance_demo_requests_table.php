<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('demo_requests', function (Blueprint $table) {
            if (!Schema::hasColumn('demo_requests', 'source')) {
                $table->string('source')->default('website')->after('message');
            }
            if (!Schema::hasColumn('demo_requests', 'notes')) {
                $table->text('notes')->nullable()->after('status');
            }
        });
    }

    public function down(): void
    {
        Schema::table('demo_requests', function (Blueprint $table) {
            if (Schema::hasColumn('demo_requests', 'source')) {
                $table->dropColumn('source');
            }
            if (Schema::hasColumn('demo_requests', 'notes')) {
                $table->dropColumn('notes');
            }
        });
    }
};
