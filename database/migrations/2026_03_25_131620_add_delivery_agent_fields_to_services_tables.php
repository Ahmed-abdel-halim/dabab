<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->foreignId('delivery_agent_id')->nullable()->after('user_id')->constrained('users')->onDelete('set null');
            $table->string('item_photo')->nullable()->after('status');
            $table->string('invoice_photo')->nullable()->after('item_photo');
            $table->timestamp('delivered_at')->nullable()->after('invoice_photo');
        });

        Schema::table('deliveries', function (Blueprint $table) {
            $table->foreignId('delivery_agent_id')->nullable()->after('user_id')->constrained('users')->onDelete('set null');
            $table->string('item_photo')->nullable()->after('status');
            $table->string('invoice_photo')->nullable()->after('item_photo');
            $table->timestamp('delivered_at')->nullable()->after('invoice_photo');
        });

        Schema::table('car_washes', function (Blueprint $table) {
            $table->foreignId('delivery_agent_id')->nullable()->after('user_id')->constrained('users')->onDelete('set null');
            $table->string('item_photo')->nullable()->after('status');
            $table->string('invoice_photo')->nullable()->after('item_photo');
            $table->timestamp('delivered_at')->nullable()->after('invoice_photo');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeign(['delivery_agent_id']);
            $table->dropColumn(['delivery_agent_id', 'item_photo', 'invoice_photo', 'delivered_at']);
        });

        Schema::table('deliveries', function (Blueprint $table) {
            $table->dropForeign(['delivery_agent_id']);
            $table->dropColumn(['delivery_agent_id', 'item_photo', 'invoice_photo', 'delivered_at']);
        });

        Schema::table('car_washes', function (Blueprint $table) {
            $table->dropForeign(['delivery_agent_id']);
            $table->dropColumn(['delivery_agent_id', 'item_photo', 'invoice_photo', 'delivered_at']);
        });
    }
};
