<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\PosKasir;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware(['auth', 'role:cashier'])->group(function () {
    Route::get('/kasir/pos', PosKasir::class)->name('kasir.pos');
});
