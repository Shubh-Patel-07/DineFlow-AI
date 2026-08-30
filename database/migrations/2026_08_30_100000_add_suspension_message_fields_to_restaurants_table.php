<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('restaurants', function (Blueprint $table) {
            if (!Schema::hasColumn('restaurants', 'suspension_message')) {
                $table->text('suspension_message')->nullable()->after('suspension_reason');
            }
            if (!Schema::hasColumn('restaurants', 'suspension_internal_note')) {
                $table->text('suspension_internal_note')->nullable()->after('suspension_message');
            }
            if (!Schema::hasColumn('restaurants', 'suspended_at')) {
                $table->timestamp('suspended_at')->nullable()->after('suspension_internal_note');
            }
        });
    }

    public function down(): void
    {
        Schema::table('restaurants', function (Blueprint $table) {
            $cols = ['suspension_message', 'suspension_internal_note', 'suspended_at'];
            foreach ($cols as $col) {
                if (Schema::hasColumn('restaurants', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
