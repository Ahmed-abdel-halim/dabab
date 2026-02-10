<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\UserLocation>
 */
class UserLocationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'type' => fake()->randomElement(['Home', 'Work', 'Friend', 'Other']),
            'address' => fake()->address(),
            'lat' => fake()->latitude(),
            'lng' => fake()->longitude(),
            'is_default' => fake()->boolean(20), // 20% chance of being default
        ];
    }
}
