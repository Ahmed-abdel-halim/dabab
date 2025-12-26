<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('car_washes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->enum('car_size', ['small', 'large']);
            $table->enum('wash_type', ['interior_exterior', 'exterior', 'interior']);
            $table->date('scheduled_date');
            $table->time('scheduled_time');
            $table->enum('time_period', ['before_lunch', 'early_evening', 'dinner_time', 'late_night']);
            $table->foreignId('location_id')->nullable()->constrained('user_locations')->onDelete('set null');
            $table->enum('status', ['pending', 'confirmed', 'completed', 'cancelled'])->default('pending');
            $table->decimal('cost', 10, 2)->default(0);
            $table->enum('payment_method', ['cash', 'apple_pay', 'bank_card'])->nullable();
            $table->enum('payment_status', ['pending', 'paid'])->default('pending');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('car_washes');
    }
};

