<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->onDelete('cascade');
            $table->foreignId('category_id')->nullable()->constrained('order_categories')->onDelete('set null');
            $table->text('details');
            $table->decimal('delivery_cost', 10, 2)->default(0);
            $table->integer('order_index')->default(0); // ترتيب الطلب (الطلب الأول، الثاني، إلخ)
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }
};

