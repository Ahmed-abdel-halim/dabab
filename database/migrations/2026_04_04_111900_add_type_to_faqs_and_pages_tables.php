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
        Schema::table('faqs', function (Blueprint $table) {
            $table->string('type')->default('customer')->after('sort_order');
        });

        Schema::table('pages', function (Blueprint $table) {
            $table->string('type')->default('customer')->after('slug');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('faqs', function (Blueprint $table) {
            $table->dropColumn('type');
        });

        Schema::table('pages', function (Blueprint $table) {
            $table->dropColumn('type');
        });
    }
};
