<?php

namespace Database\Seeders;

use App\Models\Branch;
use App\Models\Customer;
use App\Models\Employee;
use App\Models\Service;
use App\Models\Transaction;
use App\Models\TransactionItem;
use Illuminate\Database\Seeder;

class TransactionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $branches = Branch::all();
        $customers = Customer::all();
        $services = Service::all();

        if ($branches->isEmpty() || $customers->isEmpty() || $services->isEmpty()) {
            return;
        }

        for ($i = 0; $i < 20; $i++) {
            $branch = $branches->random();
            $employee = $branch->employees()->inRandomOrder()->first();

            if (!$employee)
                continue;

            $transaction = Transaction::factory()->create([
                'branch_id' => $branch->id,
                'employee_id' => $employee->id,
                'customer_id' => $customers->random()->id,
            ]);

            $numItems = rand(1, 3);
            for ($j = 0; $j < $numItems; $j++) {
                $service = $services->random();
                TransactionItem::create([
                    'transaction_id' => $transaction->id,
                    'service_id' => $service->id,
                    'quantity' => 1,
                    'price' => $service->price,
                    'subtotal' => $service->price,
                ]);
            }

            $transaction->recalculateTotal();
        }
    }
}
