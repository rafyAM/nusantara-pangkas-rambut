<?php

use App\Models\CashMovement;
use App\Models\CashierShift;

test('can create cash movement when shift is open', function () {
    $shift = CashierShift::factory()->create(['status' => 'open']);
    
    $movement = CashMovement::factory()->create([
        'cashier_shift_id' => $shift->id,
        'type' => 'in',
        'amount' => 100000
    ]);
    
    expect($movement->id)->not->toBeNull();
    expect((float) $movement->amount)->toBe(100000.0);
});

test('cannot create cash movement when shift is closed', function () {
    $shift = CashierShift::factory()->create(['status' => 'closed']);
    
    expect(fn () => CashMovement::factory()->create([
        'cashier_shift_id' => $shift->id,
    ]))->toThrow(RuntimeException::class, 'Tidak bisa menambah cash movement: shift tidak aktif.');
});

test('cannot create cash movement when shift does not exist', function () {
    expect(fn () => CashMovement::factory()->create([
        'cashier_shift_id' => 9999,
    ]))->toThrow(RuntimeException::class, 'Tidak bisa menambah cash movement: shift tidak aktif.');
});
