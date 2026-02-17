<?php

namespace Database\Factories;

use App\Models\Service;
use App\Models\Transaction;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\TransactionItem>
 */
class TransactionItemFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $service = Service::inRandomOrder()->first() ?? Service::factory()->create();

        return [
            'transaction_id' => Transaction::factory(),
            'service_id' => $service->id,
            'quantity' => 1,
            'price' => $service->price,
            'subtotal' => $service->price, // Default for quantity 1
        ];
    }
}
