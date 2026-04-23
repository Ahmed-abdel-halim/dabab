<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('car_wash_periods', function (Blueprint $table) {
            $table->id();
            $table->string('period_key')->unique();
            $table->string('time_range');
            $table->string('start_time');
            $table->string('end_time');
            $table->string('period_type');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        $date = now();
        DB::table('car_wash_periods')->insert([
            ['period_key' => 'before_lunch', 'time_range' => '11:00 - 13:00', 'start_time' => '11:00', 'end_time' => '13:00', 'period_type' => 'afternoon', 'created_at' => $date, 'updated_at' => $date],
            ['period_key' => 'early_evening', 'time_range' => '17:30 - 19:30', 'start_time' => '17:30', 'end_time' => '19:30', 'period_type' => 'evening', 'created_at' => $date, 'updated_at' => $date],
            ['period_key' => 'dinner_time', 'time_range' => '19:30 - 21:30', 'start_time' => '19:30', 'end_time' => '21:30', 'period_type' => 'evening', 'created_at' => $date, 'updated_at' => $date],
            ['period_key' => 'late_night', 'time_range' => '21:30 - 00:30', 'start_time' => '21:30', 'end_time' => '00:30', 'period_type' => 'evening', 'created_at' => $date, 'updated_at' => $date],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('car_wash_periods');
    }
};
