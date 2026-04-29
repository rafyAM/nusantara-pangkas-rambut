<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;
use App\Models\CashierShift;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\CashMovement>
 */
class CashMovementFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'cashier_shift_id' => CashierShift::factory(),
            'user_id' => User::factory(),
            'type' => 'in',
            'amount' => 50000,
            'reason' => 'Restock uang kembalian',
        ];
    }
}
