<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SystemSettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $settings = [
            // Car Wash Prices
            ['key' => 'car_wash_price_small_exterior', 'value' => '30', 'display_name' => 'Car Wash - Small - Exterior', 'group' => 'prices'],
            ['key' => 'car_wash_price_small_interior', 'value' => '40', 'display_name' => 'Car Wash - Small - Interior', 'group' => 'prices'],
            ['key' => 'car_wash_price_small_interior_exterior', 'value' => '60', 'display_name' => 'Car Wash - Small - Interior & Exterior', 'group' => 'prices'],
            ['key' => 'car_wash_price_large_exterior', 'value' => '50', 'display_name' => 'Car Wash - Large - Exterior', 'group' => 'prices'],
            ['key' => 'car_wash_price_large_interior', 'value' => '60', 'display_name' => 'Car Wash - Large - Interior', 'group' => 'prices'],
            ['key' => 'car_wash_price_large_interior_exterior', 'value' => '100', 'display_name' => 'Car Wash - Large - Interior & Exterior', 'group' => 'prices'],
            
            // Rental Prices
            ['key' => 'rental_price_scooter_only', 'value' => '150', 'display_name' => 'Rental - Scooter Only (Per Day)', 'group' => 'prices'],
            ['key' => 'rental_price_scooter_with_driver', 'value' => '250', 'display_name' => 'Rental - Scooter with Driver (Per Day)', 'group' => 'prices'],
        ];

        foreach ($settings as $setting) {
            \App\Models\SystemSetting::updateOrCreate(['key' => $setting['key']], $setting);
        }
    }
}
