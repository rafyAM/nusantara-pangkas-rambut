<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class ServiceFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => fake()->unique()->words(3, true),
            'price' => fake()->randomElement([25000, 30000, 35000, 50000, 75000, 100000]),
            'duration_minutes' => fake()->randomElement([15, 20, 30, 45, 60]),
            'description' => fake()->optional()->sentence(),
            'is_active' => true,
        ];
    }
}
