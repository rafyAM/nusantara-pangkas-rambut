<?php

use App\Models\Customer;
use App\Models\Reservation;
use Illuminate\Support\Facades\Schema;

// 1. Memastikan tabel customers di database memiliki kolom yang dibutuhkan sistem
test('it has the correct database table schema', function () {
    expect(Schema::hasTable('customers'))->toBeTrue();
    expect(Schema::hasColumns('customers', [
        'id', 'name', 'phone', 'loyalty_points', 'created_at', 'updated_at'
    ]))->toBeTrue();
});

// 2. Memastikan model Customer bisa dibuat dan disimpan dengan data valid
test('it can create a customer successfully', function () {
    $customer = Customer::factory()->create([
        'name' => 'Budi Santoso',
        'phone' => '08123456789',
        'loyalty_points' => 0
    ]);

    expect($customer->name)->toBe('Budi Santoso');
    expect($customer->phone)->toBe('08123456789');
    expect($customer->loyalty_points)->toBe(0);
});

// 3. Memastikan satu Customer bisa memiliki banyak riwayat Reservasi (Relasi One-to-Many)
test('it has many reservations', function () {
    $customer = Customer::factory()->create();
    $reservation = Reservation::factory()->create(['customer_id' => $customer->id]);

    expect($customer->reservations)->toHaveCount(1);
    expect($customer->reservations->first()->id)->toBe($reservation->id);
});

// 4. [PENTING UNTUK SKRIPSI] Memastikan model Customer mendukung penyimpanan endpoint VAPID untuk Notifikasi Web Push
test('it can save push subscriptions for VAPID', function () {
    $customer = Customer::factory()->create();

    // Mensimulasikan pemanggilan method updatePushSubscription bawaan library webpush
    $customer->updatePushSubscription(
        'https://fcm.googleapis.com/fcm/send/test-endpoint-123',
        'test-p256dh-key',
        'test-auth-key'
    );

    expect($customer->pushSubscriptions()->count())->toBe(1);

    $subscription = $customer->pushSubscriptions()->first();
    expect($subscription->endpoint)->toBe('https://fcm.googleapis.com/fcm/send/test-endpoint-123');
});
