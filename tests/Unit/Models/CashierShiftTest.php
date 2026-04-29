<?php

use App\Models\CashierShift;
use App\Models\CashMovement;
use App\Models\Payment;
use App\Models\User;

test('open scope returns only open shifts', function () {
    CashierShift::factory()->create(['status' => 'open']);
    CashierShift::factory()->create(['status' => 'closed']);
    
    expect(CashierShift::open()->count())->toBeGreaterThanOrEqual(1);
    
    $openShifts = CashierShift::open()->get();
    foreach ($openShifts as $shift) {
        expect($shift->status)->toBe('open');
    }
});

test('byUser scope returns only shifts for specific user', function () {
    $user1 = User::factory()->create();
    $user2 = User::factory()->create();
    
    CashierShift::factory()->create(['user_id' => $user1->id]);
    CashierShift::factory()->create(['user_id' => $user2->id]);
    
    $user1Shifts = CashierShift::byUser($user1->id)->get();
    
    expect($user1Shifts->count())->toBeGreaterThanOrEqual(1);
    foreach ($user1Shifts as $shift) {
        expect($shift->user_id)->toBe($user1->id);
    }
});

test('calculateExpectedCash returns correct amount', function () {
    $shift = CashierShift::factory()->create(['opening_cash' => 100000]);
    $branch = \App\Models\Branch::factory()->create();
    
    // Uang masuk (cash in) = 50.000
    CashMovement::factory()->create([
        'cashier_shift_id' => $shift->id,
        'type' => 'in',
        'amount' => 50000
    ]);
    
    // Uang keluar (cash out) = 20.000
    CashMovement::factory()->create([
        'cashier_shift_id' => $shift->id,
        'type' => 'out',
        'amount' => 20000
    ]);
    
    // Pembayaran tunai = 150.000
    Payment::factory()->create([
        'cashier_shift_id' => $shift->id,
        'method' => 'cash',
        'amount' => 150000,
        'transaction_id' => \App\Models\Transaction::factory()->create(['branch_id' => $branch->id])->id
    ]);
    
    // Pembayaran non-tunai (tidak boleh dihitung) = 300.000
    Payment::factory()->create([
        'cashier_shift_id' => $shift->id,
        'method' => 'qris',
        'amount' => 300000,
        'transaction_id' => \App\Models\Transaction::factory()->create(['branch_id' => $branch->id])->id
    ]);
    
    // Expected: 100k (open) + 50k (in) - 20k (out) + 150k (payment cash) = 280.000
    expect($shift->calculateExpectedCash())->toBe(280000.0);
});

test('close method sets expected, actual, difference and status correctly', function () {
    $shift = CashierShift::factory()->create(['opening_cash' => 100000]);
    
    // Pembayaran tunai = 150.000, expected cash = 250.000
    Payment::factory()->create([
        'cashier_shift_id' => $shift->id,
        'method' => 'cash',
        'amount' => 150000
    ]);
    
    // Actual cash = 240.000 (selisih -10.000)
    $shift->close(240000, 'Kurang 10 ribu');
    
    $shift->refresh();
    
    expect($shift->status)->toBe('closed');
    expect((float) $shift->expected_cash)->toBe(250000.0);
    expect((float) $shift->actual_cash)->toBe(240000.0);
    expect((float) $shift->difference)->toBe(-10000.0);
    expect($shift->notes)->toBe('Kurang 10 ribu');
    expect($shift->end_at)->not->toBeNull();
});

test('paymentSummary groups and sums payments correctly', function () {
    $shift = CashierShift::factory()->create();
    $branch = \App\Models\Branch::factory()->create();
    
    // Cash: 2 transaksi (100k + 50k = 150k)
    Payment::factory()->create(['cashier_shift_id' => $shift->id, 'method' => 'cash', 'amount' => 100000, 'transaction_id' => \App\Models\Transaction::factory()->create(['branch_id' => $branch->id])->id]);
    Payment::factory()->create(['cashier_shift_id' => $shift->id, 'method' => 'cash', 'amount' => 50000, 'transaction_id' => \App\Models\Transaction::factory()->create(['branch_id' => $branch->id])->id]);
    
    // QRIS: 1 transaksi (200k)
    Payment::factory()->create(['cashier_shift_id' => $shift->id, 'method' => 'qris', 'amount' => 200000, 'transaction_id' => \App\Models\Transaction::factory()->create(['branch_id' => $branch->id])->id]);
    
    $summary = $shift->paymentSummary();
    
    expect($summary)->toHaveKey('cash');
    expect($summary)->toHaveKey('qris');
    
    expect((float) $summary['cash']['total'])->toBe(150000.0);
    expect((int) $summary['cash']['count'])->toBe(2);
    
    expect((float) $summary['qris']['total'])->toBe(200000.0);
    expect((int) $summary['qris']['count'])->toBe(1);
});
