<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('orders', function (Blueprint $table) {
            // التحقق من وجود الأعمدة قبل حذفها
            if (Schema::hasColumn('orders', 'category_id')) {
                $table->dropForeign(['category_id']);
                $table->dropColumn('category_id');
            }
            if (Schema::hasColumn('orders', 'category_name')) {
                $table->dropColumn('category_name');
            }
            if (Schema::hasColumn('orders', 'details')) {
                $table->dropColumn('details');
            }
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            if (!Schema::hasColumn('orders', 'category_id')) {
                $table->foreignId('category_id')->nullable()->constrained('order_categories')->onDelete('set null');
            }
            if (!Schema::hasColumn('orders', 'category_name')) {
                $table->string('category_name')->nullable();
            }
            if (!Schema::hasColumn('orders', 'details')) {
                $table->text('details')->nullable();
            }
        });
    }
};

