<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $settings = [
            [
                'key' => 'car_wash_price_small_exterior',
                'value' => '30',
                'display_name' => 'سعر غسيل خارجي (سيارة صغيرة)',
                'group' => 'car_wash_prices',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'car_wash_price_small_interior',
                'value' => '40',
                'display_name' => 'سعر غسيل داخلي (سيارة صغيرة)',
                'group' => 'car_wash_prices',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'car_wash_price_small_interior_exterior',
                'value' => '60',
                'display_name' => 'سعر غسيل داخلي وخارجي (سيارة صغيرة)',
                'group' => 'car_wash_prices',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'car_wash_price_large_exterior',
                'value' => '50',
                'display_name' => 'سعر غسيل خارجي (سيارة كبيرة)',
                'group' => 'car_wash_prices',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'car_wash_price_large_interior',
                'value' => '60',
                'display_name' => 'سعر غسيل داخلي (سيارة كبيرة)',
                'group' => 'car_wash_prices',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'car_wash_price_large_interior_exterior',
                'value' => '100',
                'display_name' => 'سعر غسيل داخلي وخارجي (سيارة كبيرة)',
                'group' => 'car_wash_prices',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        // Insert only if they don't exist
        foreach ($settings as $setting) {
            $exists = DB::table('system_settings')->where('key', $setting['key'])->exists();
            if (!$exists) {
                DB::table('system_settings')->insert($setting);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('system_settings')
            ->whereIn('key', [
                'car_wash_price_small_exterior',
                'car_wash_price_small_interior',
                'car_wash_price_small_interior_exterior',
                'car_wash_price_large_exterior',
                'car_wash_price_large_interior',
                'car_wash_price_large_interior_exterior',
            ])
            ->delete();
    }
};
