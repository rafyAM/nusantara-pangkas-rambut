<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\BranchController;
use App\Http\Controllers\Api\ReservationController;
use App\Http\Controllers\Api\ReviewController;
use Illuminate\Support\Facades\Route;

// ─── Public endpoints ────────────────────────────────────────────────────────
Route::post('/auth/login', [AuthController::class, 'login']);

Route::get('/branches', [BranchController::class, 'index']);
Route::get('/branches/{branch}/services', [BranchController::class, 'services']);
Route::get('/branches/{branch}/barbers', [BranchController::class, 'barbers']);
Route::get('/reservations/availability', [ReservationController::class, 'availability']);

// ─── Protected endpoints (Sanctum token) ─────────────────────────────────────
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/auth/logout', [AuthController::class, 'logout']);
    Route::get('/auth/me', [AuthController::class, 'me']);

    Route::get('/reservations', [ReservationController::class, 'index']);
    Route::post('/reservations', [ReservationController::class, 'store'])
        ->middleware('throttle:5,1');
    Route::get('/reservations/{id}', [ReservationController::class, 'show']);
    Route::patch('/reservations/{id}/cancel', [ReservationController::class, 'cancel']);

    Route::post('/reviews', [ReviewController::class, 'store']);
});
