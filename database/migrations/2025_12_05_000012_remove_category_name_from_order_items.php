<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('order_items', function (Blueprint $table) {
            // التحقق من وجود العمود قبل حذفه
            if (Schema::hasColumn('order_items', 'category_name')) {
                $table->dropColumn('category_name');
            }
        });
    }

    public function down(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            if (!Schema::hasColumn('order_items', 'category_name')) {
                $table->string('category_name')->nullable();
            }
        });
    }
};
