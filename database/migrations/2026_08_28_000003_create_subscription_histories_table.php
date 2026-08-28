<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('subscription_histories')) {
            Schema::create('subscription_histories', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('restaurant_id');
                $table->string('previous_plan', 50)->nullable();
                $table->string('new_plan', 50);
                $table->string('previous_status', 50)->nullable();
                $table->string('new_status', 50);
                $table->timestamp('starts_at')->nullable();
                $table->timestamp('expires_at')->nullable();
                $table->decimal('billing_amount', 10, 2)->default(0.00);
                $table->string('payment_status', 30)->default('paid');
                $table->string('payment_method', 50)->nullable();
                $table->string('payment_reference', 100)->nullable();
                $table->unsignedBigInteger('changed_by')->nullable(); // user_id of superadmin or system
                $table->string('reason')->nullable();
                $table->json('metadata')->nullable();
                $table->timestamps();

                $table->index('restaurant_id');
                $table->index('changed_by');
                $table->index(['restaurant_id', 'created_at']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('subscription_histories');
    }
};
