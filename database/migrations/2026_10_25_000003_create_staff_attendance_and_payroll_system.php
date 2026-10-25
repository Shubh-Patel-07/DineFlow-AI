<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Staff Attendances (Daily Shift Clock-in & Clock-out)
        if (!Schema::hasTable('staff_attendances')) {
            Schema::create('staff_attendances', function (Blueprint $table) {
                $table->id();
                $table->string('restaurant_id')->index();
                $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
                $table->date('date')->index();
                $table->dateTime('clock_in')->nullable();
                $table->dateTime('clock_out')->nullable();
                $table->decimal('total_hours', 5, 2)->default(0.00);
                $table->decimal('overtime_hours', 5, 2)->default(0.00);
                $table->string('status')->default('PRESENT'); // PRESENT, LATE, HALF_DAY, ABSENT, ON_LEAVE
                $table->string('shift')->default('REGULAR'); // MORNING, EVENING, NIGHT, REGULAR
                $table->string('clock_in_ip')->nullable();
                $table->text('notes')->nullable();
                $table->timestamps();

                $table->unique(['restaurant_id', 'user_id', 'date']);
            });
        }

        // 2. Staff Payroll & Salary Slips
        if (!Schema::hasTable('payroll_slips')) {
            Schema::create('payroll_slips', function (Blueprint $table) {
                $table->id();
                $table->string('restaurant_id')->index();
                $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
                $table->integer('month'); // 1 to 12
                $table->integer('year');  // e.g. 2026
                $table->decimal('basic_salary', 10, 2)->default(0.00);
                $table->integer('total_days')->default(30);
                $table->decimal('present_days', 4, 1)->default(0.0);
                $table->decimal('overtime_pay', 10, 2)->default(0.00);
                $table->decimal('bonus', 10, 2)->default(0.00);
                $table->decimal('deductions', 10, 2)->default(0.00);
                $table->decimal('net_salary', 10, 2)->default(0.00);
                $table->string('payment_status')->default('PENDING'); // PENDING, PAID
                $table->date('payment_date')->nullable();
                $table->string('payment_mode')->nullable(); // CASH, BANK_TRANSFER, UPI
                $table->string('generated_by')->nullable();
                $table->text('remarks')->nullable();
                $table->timestamps();

                $table->unique(['restaurant_id', 'user_id', 'month', 'year']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('payroll_slips');
        Schema::dropIfExists('staff_attendances');
    }
};
