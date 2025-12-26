<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\OrderCategory;

class OrderCategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            [
                'name_ar' => 'طلبات الصيدلية',
                'name_en' => 'Pharmacy Orders',
                'icon' => 'pharmacy',
                'fixed_price' => 5,
                'is_active' => true,
            ],
            [
                'name_ar' => 'طلبات السوبر ماركت',
                'name_en' => 'Supermarket Orders',
                'icon' => 'supermarket',
                'fixed_price' => 5,
                'is_active' => true,
            ],
            [
                'name_ar' => 'طلبات المطاعم',
                'name_en' => 'Restaurant Orders',
                'icon' => 'restaurant',
                'fixed_price' => 7,
                'is_active' => true,
            ],
            [
                'name_ar' => 'طلبات أخرى',
                'name_en' => 'Other Orders',
                'icon' => 'other',
                'fixed_price' => 5,
                'is_active' => true,
            ],
        ];

        foreach ($categories as $category) {
            OrderCategory::create($category);
        }
    }
}

