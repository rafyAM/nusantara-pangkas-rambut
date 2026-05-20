<?php

use App\Models\Customer;
use App\Models\Reservation;
use App\Models\Branch;
use App\Models\Employee;
use App\Notifications\ReservationReminder;
use App\Notifications\ReservationCancelled;
use NotificationChannels\WebPush\WebPushChannel;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Carbon\Carbon;

uses(RefreshDatabase::class);

test('ReservationReminder builds correct web push message payload', function () {
    // 1. Siapkan data dummy
    /** @var \App\Models\Customer|\Illuminate\Contracts\Auth\Authenticatable $customer */
    $customer = Customer::factory()->create(['name' => 'Budi Tabuti']);
    $branch = Branch::factory()->create();
    $employee = Employee::factory()->create(['branch_id' => $branch->id]);
    
    $reservationTime = Carbon::now()->addHour()->setMinute(30)->setSecond(0);
    
    $reservation = Reservation::create([
        'customer_id' => $customer->id,
        'branch_id' => $branch->id,
        'employee_id' => $employee->id,
        'reservation_time' => $reservationTime,
        'status' => 'pending'
    ]);

    // 2. Inisiasi kelas Notifikasi
    $notification = new ReservationReminder($reservation);

    // 3. Pastikan channel yang digunakan adalah WebPushChannel
    expect($notification->via($customer))->toContain(WebPushChannel::class);

    // 4. Proses method toWebPush dan ambil hasilnya dalam bentuk array
    $message = $notification->toWebPush($customer, null);
    $payload = $message->toArray();

    // 5. Lakukan verifikasi (Assertion) bahwa payload berisi data yang benar
    expect($payload['title'])->toBe('Waktunya Cukur!');
    expect($payload['icon'])->toBe('/favicon.ico');
    expect($payload['body'])->toContain('Halo Budi Tabuti');
    expect($payload['body'])->toContain($reservationTime->format('H:i'));
    expect($payload['actions'][0]['title'])->toBe('Cek Jadwal');
});

test('ReservationCancelled builds correct web push message payload', function () {
    // 1. Siapkan data dummy
    /** @var \App\Models\Customer|\Illuminate\Contracts\Auth\Authenticatable $customer */
    $customer = Customer::factory()->create(['name' => 'Siti Aisyah']);
    $branch = Branch::factory()->create();
    $employee = Employee::factory()->create(['branch_id' => $branch->id]);
    
    $reservationTime = Carbon::now()->addHour()->setMinute(15)->setSecond(0);
    
    $reservation = Reservation::create([
        'customer_id' => $customer->id,
        'branch_id' => $branch->id,
        'employee_id' => $employee->id,
        'reservation_time' => $reservationTime,
        'status' => 'pending'
    ]);

    // 2. Inisiasi kelas Notifikasi
    $notification = new ReservationCancelled($reservation);

    // 3. Pastikan channel yang digunakan adalah WebPushChannel
    expect($notification->via($customer))->toContain(WebPushChannel::class);

    // 4. Proses method toWebPush dan ambil hasilnya dalam bentuk array
    $message = $notification->toWebPush($customer, null);
    $payload = $message->toArray();

    // 5. Lakukan verifikasi (Assertion) bahwa payload berisi data yang benar
    expect($payload['title'])->toBe('Reservasi Hangus/Dibatalkan!');
    expect($payload['icon'])->toBe('/favicon.ico');
    expect($payload['body'])->toContain('Mohon maaf Siti Aisyah');
    expect($payload['body'])->toContain($reservationTime->format('H:i'));
    expect($payload['actions'][0]['title'])->toBe('Buat Jadwal Baru');
});
