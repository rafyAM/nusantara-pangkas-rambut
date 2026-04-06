<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use App\Models\Reservation;
use Carbon\Carbon;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Auto cancel reservasi jika telat >30 menit (dari jam booking belum di-approve kasir) 
Schedule::call(function () {
    $threshold = Carbon::now()->subMinutes(5); // memberi kelonggaran 5 menit

    $expiredReservations = Reservation::with('customer')
        ->where('status', 'pending')
        ->where('reservation_time', '<=', $threshold)
        ->get();

    $count = 0;
    foreach ($expiredReservations as $res) {
        $res->update(['status' => 'cancelled']);
        if ($res->customer) {
            $res->customer->notify(new \App\Notifications\ReservationCancelled($res));
            $count++;
        }
    }

    if ($count > 0) {
        info("Auto-cancelled {$count} reservations due to 30 mins late.");
    }
})->everyMinute();

// Pengingat lewat Web Push Notification (PWA) 20 menit sebelum jadwal tiba
Schedule::call(function () {
    $targetTimeStart = Carbon::now()->addMinutes(20)->startOfMinute();
    $targetTimeEnd = Carbon::now()->addMinutes(20)->endOfMinute();

    $upcoming = Reservation::with('customer')
        ->where('status', 'pending')
        ->whereBetween('reservation_time', [$targetTimeStart, $targetTimeEnd])
        ->get();

    foreach ($upcoming as $res) {
        if ($res->customer) {
            $res->customer->notify(new \App\Notifications\ReservationReminder($res));
            info("Sent 20-min reminder for reservation ID {$res->id}");
        }
    }
})->everyMinute();
