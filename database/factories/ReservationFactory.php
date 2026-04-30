<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Reservation;
use App\Models\Customer;
use App\Models\Branch;
use App\Models\Employee;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Reservation>
 */
class ReservationFactory extends Factory
{
    protected $model = Reservation::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'customer_id' => Customer::factory(),
            'branch_id' => Branch::factory(),
            'employee_id' => Employee::factory(),
            'reservation_time' => now()->addDay()->format('Y-m-d H:i:00'),
            'status' => 'pending',
            'notes' => $this->faker->sentence(),
        ];
    }
}
