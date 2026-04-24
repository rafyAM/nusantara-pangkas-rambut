<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\CashierShift>
 */
class CashierShiftFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'start_at' => now(),
            'opening_cash' => 0,
            'expected_cash' => null,
            'actual_cash' => null,
            'difference' => null,
            'status' => 'open',
            'notes' => null,
        ];
    }
}
