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
        Schema::table('delivery_agent_profiles', function (Blueprint $table) {
            $table->enum('working_service', ['dabab_tawseel', 'car_wash'])->nullable()->after('birth_date');
            $table->enum('service_category', ['tawseel', 'car_wash'])->nullable()->after('working_service');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('delivery_agent_profiles', function (Blueprint $table) {
            $table->dropColumn(['working_service', 'service_category']);
        });
    }
};
