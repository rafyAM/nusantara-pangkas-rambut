<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use App\Models\Reservation;
use Carbon\Carbon;
use App\Notifications\ReservationCancelled;
use App\Notifications\ReservationReminder;


Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Auto cancel reservasi jika telat >30 detik (dari jam booking belum di-approve kasir) 
Schedule::call(function () {
    $threshold = Carbon::now()->subSeconds(30); // memberi kelonggaran 30 detik

    $expiredReservations = Reservation::with('customer')
        ->where('status', 'pending')
        ->where('reservation_time', '<=', $threshold)
        ->get();

    $count = 0;
    foreach ($expiredReservations as $res) {
        $res->update(['status' => 'cancelled']);
        if ($res->customer) {
            try {
                $res->customer->notify(new \App\Notifications\ReservationCancelled($res));
                $count++;
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error('Auto-cancel notify failed: ' . $e->getMessage());
            }
        }
    }

    if ($count > 0) {
        info("Auto-cancelled {$count} reservations due to 30 seconds late.");
    }
})->everySecond();

// Pengingat lewat Web Push Notification (PWA) 30 detik sebelum jadwal tiba
Schedule::call(function () {
    $targetTimeStart = Carbon::now()->addSeconds(30)->startOfSecond();
    $targetTimeEnd = Carbon::now()->addSeconds(30)->endOfSecond();

    $upcoming = Reservation::with('customer')
        ->where('status', 'pending')
        ->whereBetween('reservation_time', [$targetTimeStart, $targetTimeEnd])
        ->get();

    foreach ($upcoming as $res) {
        if ($res->customer) {
            try {
                $res->customer->notify(new \App\Notifications\ReservationReminder($res));
                info("Sent 30-sec reminder for reservation ID {$res->id}");
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error('Reminder notify failed: ' . $e->getMessage());
            }
        }
    }
})->everySecond();
