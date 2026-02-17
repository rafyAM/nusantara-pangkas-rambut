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
            'description' => fake()->optional()->sentence(),
            'is_active' => true,
        ];
    }
}
