<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CustomerDashboardController;
use App\Livewire\PosKasir;
use App\Livewire\TransactionHistoryPage;
use App\Livewire\KasirReservation;
use App\Http\Controllers\PushSubscriptionController;

Route::get('/', function () {
    return view('welcome');
});



// 1. Menampilkan halaman utama untuk memilih jadwal dan kapster
Route::get('/dashboard', [CustomerDashboardController::class, 'index'])
    ->middleware(['auth:customer', 'verified'])
    ->name('dashboard');

// 2. Menampilkan riwayat pemesanan yang sudah selesai atau dibatalkan
Route::get('/history', [CustomerDashboardController::class, 'history'])
    ->middleware(['auth:customer', 'verified'])
    ->name('history');

// 3. Memproses penyimpanan data pembuatan reservasi baru ke database
Route::post('/reservations', [CustomerDashboardController::class, 'store'])
    ->middleware(['auth:customer', 'verified', 'throttle:5,1'])
    ->name('reservations.store');

// 4. [FITUR SKRIPSI] Memproses pembatalan jadwal secara mandiri oleh pelanggan (Memicu Web Push ke Kasir)
Route::patch('/reservations/{reservation}/cancel', [CustomerDashboardController::class, 'cancel'])
    ->middleware(['auth:customer', 'verified'])
    ->name('reservations.cancel');

// 5. [FITUR SKRIPSI] Endpoint VAPID untuk mendaftarkan dan menyimpan kunci Push Notification milik Pelanggan
Route::post('/push/subscribe', [PushSubscriptionController::class, 'subscribe'])
    ->middleware(['auth:customer'])
    ->name('push.subscribe');

// RUTE APLIKASI KASIR (POINT OF SALES)
Route::middleware(['auth', 'role:cashier'])->group(function () {
    // Menampilkan halaman sistem kasir (POS)
    Route::get('/kasir/pos', PosKasir::class)->name('kasir.pos');
    // Menampilkan riwayat transaksi pemasukan uang
    Route::get('/kasir/transaction-history', TransactionHistoryPage::class)->name('kasir.transaction-history');
    // Menampilkan daftar pemesanan dari sisi manajemen kasir
    Route::get('/kasir/reservations', KasirReservation::class)->name('kasir.reservations');
    
    // [FITUR SKRIPSI] Endpoint VAPID untuk mendaftarkan kunci Push Notification milik Kasir
    Route::post('/kasir/push/subscribe', [PushSubscriptionController::class, 'subscribeUser'])->name('kasir.push.subscribe');
});

// RUTE MANAJEMEN AKUN & OTENTIKASI

// Mengatur update Profil (Nama, Email, Password) untuk Customer
Route::middleware('auth:customer')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Memuat rute login/register bawaan dari Laravel Breeze
require __DIR__.'/auth.php';
