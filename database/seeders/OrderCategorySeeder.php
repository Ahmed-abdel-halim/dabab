<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\OrderCategory;

class OrderCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'name_ar' => 'طلبات الصيدلية',
                'name_en' => 'Pharmacy Orders',
                'icon' => 'pharmacy',
                'fixed_price' => 5.00,
                'is_active' => true,
            ],
            [
                'name_ar' => 'طلبات السوبر ماركت',
                'name_en' => 'Supermarket Orders',
                'icon' => 'supermarket',
                'fixed_price' => 5.00,
                'is_active' => true,
            ],
            [
                'name_ar' => 'طلبات المطاعم',
                'name_en' => 'Restaurant Orders',
                'icon' => 'restaurant',
                'fixed_price' => 7.00,
                'is_active' => true,
            ],
            [
                'name_ar' => 'طلبات البقالة',
                'name_en' => 'Grocery Orders',
                'icon' => 'grocery',
                'fixed_price' => 5.00,
                'is_active' => true,
            ],
            [
                'name_ar' => 'طلبات المقاهي',
                'name_en' => 'Cafe Orders',
                'icon' => 'cafe',
                'fixed_price' => 6.00,
                'is_active' => true,
            ],
            [
                'name_ar' => 'طلبات الحلويات',
                'name_en' => 'Dessert Orders',
                'icon' => 'dessert',
                'fixed_price' => 6.00,
                'is_active' => true,
            ],
            [
                'name_ar' => 'طلبات الفواكه والخضار',
                'name_en' => 'Fruits & Vegetables Orders',
                'icon' => 'fruits',
                'fixed_price' => 5.00,
                'is_active' => true,
            ],
            [
                'name_ar' => 'طلبات اللحوم والدواجن',
                'name_en' => 'Meat & Poultry Orders',
                'icon' => 'meat',
                'fixed_price' => 8.00,
                'is_active' => true,
            ],
            [
                'name_ar' => 'طلبات أخرى',
                'name_en' => 'Other Orders',
                'icon' => 'other',
                'fixed_price' => 5.00,
                'is_active' => true,
            ],
        ];

        // استخدام updateOrCreate لتجنب التكرار
        foreach ($categories as $category) {
            OrderCategory::updateOrCreate(
                ['name_ar' => $category['name_ar']],
                $category
            );
        }
    }
}

