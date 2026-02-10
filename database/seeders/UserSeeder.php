<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = \App\Models\User::firstOrCreate(
            ['phone' => '01000000000'],
            [
                'name' => 'Ahmed Abelhalim',
                'email' => 'admin@example.com',
            ]
        );

        // Add locations to the specific user if not present
        if ($user->wasRecentlyCreated) {
             $user->locations()->saveMany(\App\Models\UserLocation::factory(3)->make());
        }

        \App\Models\User::factory(10)->create()->each(function ($user) {
            $user->locations()->saveMany(\App\Models\UserLocation::factory(3)->make());
        });
    }
}
