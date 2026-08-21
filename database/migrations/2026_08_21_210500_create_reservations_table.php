<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reservations', function (Blueprint $table) {
            $table->id();
            $table->string('restaurant_id')->default('1');
            $table->string('customer_name');
            $table->string('customer_phone');
            $table->integer('pax_count')->default(2);
            $table->date('reservation_date');
            $table->string('time_slot')->default('07:30 PM');
            $table->string('status')->default('pending');
            $table->text('special_requests')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reservations');
    }
};
