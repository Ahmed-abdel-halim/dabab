<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('deliveries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('order_number')->unique();
            $table->text('shipment_details');
            $table->string('sender_address');
            $table->decimal('sender_lat', 10, 7);
            $table->decimal('sender_lng', 10, 7);
            $table->string('sender_phone');
            $table->string('recipient_address');
            $table->decimal('recipient_lat', 10, 7);
            $table->decimal('recipient_lng', 10, 7);
            $table->string('recipient_phone');
            $table->enum('status', ['pending', 'in_progress', 'completed', 'cancelled'])->default('pending');
            $table->decimal('delivery_cost', 10, 2)->default(0);
            $table->enum('payment_method', ['cash', 'apple_pay', 'bank_card'])->nullable();
            $table->enum('payment_status', ['pending', 'paid'])->default('pending');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('deliveries');
    }
};

