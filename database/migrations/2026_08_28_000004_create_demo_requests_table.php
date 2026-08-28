<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('demo_requests')) {
            Schema::create('demo_requests', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('restaurant_name');
                $table->string('phone');
                $table->string('email')->nullable();
                $table->string('city')->nullable();
                $table->string('preferred_time')->nullable();
                $table->text('message')->nullable();
                $table->string('status')->default('pending'); // pending, contacted, completed
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('demo_requests');
    }
};
