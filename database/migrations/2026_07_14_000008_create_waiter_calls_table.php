<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('waiter_calls', function (Blueprint $table) {
            $table->id();
            $table->string('restaurant_id');
            $table->string('table_id');
            $table->string('status')->default('pending'); // pending, resolved
            $table->string('reason')->nullable()->default('General Assistance');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('waiter_calls');
    }
};
