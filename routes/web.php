<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

use App\Http\Controllers\CustomerDashboardController;

Route::get('/dashboard', [CustomerDashboardController::class, 'index'])
    ->middleware(['auth:customer', 'verified'])
    ->name('dashboard');

Route::post('/reservations', [CustomerDashboardController::class, 'store'])
    ->middleware(['auth:customer', 'verified'])
    ->name('reservations.store');

Route::middleware('auth:customer')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
