<?php

use App\Models\Customer;
use App\Models\Employee;
use App\Models\Branch;
use App\Models\Reservation;
use App\Models\Service;
use Carbon\Carbon;
use Illuminate\Support\Facades\Schema;

test('it has the correct database table schema', function () {
    expect(Schema::hasTable('reservations'))->toBeTrue();
    expect(Schema::hasColumns('reservations', [
        'id', 'customer_id', 'branch_id', 'employee_id', 'reservation_time', 'status', 'created_at', 'updated_at'
    ]))->toBeTrue();
});

test('it successfully creates a pending reservation', function () {
    $customer = Customer::factory()->create();
    $branch = Branch::factory()->create();
    $employee = Employee::factory()->create(['branch_id' => $branch->id]);

    $reservation = Reservation::create([
        'customer_id' => $customer->id,
        'branch_id' => $branch->id,
        'employee_id' => $employee->id,
        'reservation_time' => now()->addDay()->format('Y-m-d 10:00:00'),
        'status' => 'pending'
    ]);

    expect($reservation->status)->toBe('pending');
    expect($reservation->customer_id)->toBe($customer->id);
    expect($reservation->branch_id)->toBe($branch->id);
});

test('it correctly relates to multiple services', function () {
    $reservation = Reservation::factory()->create();
    $service1 = Service::factory()->create();
    $service2 = Service::factory()->create();

    $reservation->services()->attach([$service1->id, $service2->id]);

    expect($reservation->services)->toHaveCount(2);
});

test('it validates time correctly to prevent conflict (kapster collision)', function () {
    $branch = Branch::factory()->create();
    $employee = Employee::factory()->create(['branch_id' => $branch->id]);
    $bookingTime = now()->addDays(2)->format('Y-m-d 14:00:00');

    // Reservasi Pertama
    Reservation::factory()->create([
        'branch_id' => $branch->id,
        'employee_id' => $employee->id,
        'reservation_time' => $bookingTime,
        'status' => 'pending'
    ]);

    // Uji Logika: Cek apakah kapster sudah di-booking di jam tersebut
    $isConflict = Reservation::where('employee_id', $employee->id)
        ->where('reservation_time', $bookingTime)
        ->whereIn('status', ['pending', 'arrived'])
        ->exists();
    
    expect($isConflict)->toBeTrue();
});

test('it simulates auto cancel when late threshold passed', function () {
    $reservation = Reservation::factory()->create([
        'reservation_time' => now()->subMinutes(15), // Telat 15 menit
        'status' => 'pending'
    ]);   

    $threshold = now()->subMinutes(10); // Toleransi 10 menit

    if ($reservation->reservation_time <= $threshold && $reservation->status === 'pending') {
        $reservation->update(['status' => 'cancelled']);
    }

    expect($reservation->fresh()->status)->toBe('cancelled');
});
