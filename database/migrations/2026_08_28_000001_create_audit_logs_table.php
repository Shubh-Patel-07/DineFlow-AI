<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('audit_logs')) {
            Schema::create('audit_logs', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id')->nullable();
                $table->string('action'); // e.g. restaurant_created, restaurant_suspended, plan_changed
                $table->string('target_type')->nullable(); // e.g. restaurant, plan, user
                $table->unsignedBigInteger('target_id')->nullable();
                $table->unsignedBigInteger('restaurant_id')->nullable();
                $table->json('metadata')->nullable();
                $table->string('ip_address', 45)->nullable();
                $table->text('user_agent')->nullable();
                $table->timestamps();

                $table->index(['action', 'created_at']);
                $table->index('restaurant_id');
                $table->index('user_id');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
    }
};
