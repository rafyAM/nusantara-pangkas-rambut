<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CustomerDashboardController;
use App\Livewire\PosKasir;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', [CustomerDashboardController::class, 'index'])
    ->middleware(['auth:customer', 'verified'])
    ->name('dashboard');

Route::post('/reservations', [CustomerDashboardController::class, 'store'])
    ->middleware(['auth:customer', 'verified'])
    ->name('reservations.store');

Route::post('/push/subscribe', [\App\Http\Controllers\PushSubscriptionController::class, 'subscribe'])
    ->middleware(['auth:customer'])
    ->name('push.subscribe');

Route::middleware(['auth', 'role:cashier'])->group(function () {
    Route::get('/kasir/pos', PosKasir::class)->name('kasir.pos');
});

Route::middleware('auth:customer')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
