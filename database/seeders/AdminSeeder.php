<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\Admin;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // First Admin
        Admin::updateOrCreate(
            ['email' => 'admin@admin.com'],
            [
                'name' => 'Super Admin',
                'password' => Hash::make('password'),
            ]
        );

        // Second Test Admin (if you want more than one)
        Admin::updateOrCreate(
            ['email' => 'test@admin.com'],
            [
                'name' => 'Test Admin',
                'password' => Hash::make('12345678'),
            ]
        );
    }
}
