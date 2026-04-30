<?php

use App\Models\Transaction;
use App\Models\TransactionItem;
use App\Models\Service;
use App\Models\Product;
use Illuminate\Support\Str;

test('it generates correct invoice number', function () {
    $transaction = Transaction::factory()->create();

    expect($transaction->invoice_number)->toStartWith('INV-');
    expect($transaction->invoice_number)->toContain(now()->format('Ymd'));
});

test('it calculates subtotal correctly', function () {
    $transaction = Transaction::factory()->create([
        'total_amount' => 0
    ]); 

    $service1 = Service::factory()->create(['price' => 50000]);
    $service2 = Service::factory()->create(['price' => 20000]);

    TransactionItem::create([
        'transaction_id' => $transaction->id,
        'service_id' => $service1->id,
        'quantity' => 1,
        'price' => 50000,
        'subtotal' => 50000
    ]);

    TransactionItem::create([
        'transaction_id' => $transaction->id,
        'service_id' => $service2->id,
        'quantity' => 2,
        'price' => 20000,
        'subtotal' => 40000
    ]);

    $calculatedTotal = $transaction->items()->sum('subtotal');
    $transaction->update(['total_amount' => $calculatedTotal]);

    expect((float)$transaction->total_amount)->toBe(90000.00);
});

// --- Invoice Number Uniqueness (B1) ---

test('invoice numbers are unique when multiple transactions are created sequentially', function () {
    $branch = \App\Models\Branch::factory()->create();
    $transactions = Transaction::factory()->count(3)->create(['branch_id' => $branch->id]);
    $invoiceNumbers = $transactions->pluck('invoice_number');

    expect($invoiceNumbers->unique()->count())->toBe(3);
});

test('invoice number sequence increments correctly within the same day', function () {
    $branch = \App\Models\Branch::factory()->create();
    $first  = Transaction::factory()->create(['branch_id' => $branch->id]);
    $second = Transaction::factory()->create(['branch_id' => $branch->id]);

    preg_match('/(\d{4})$/', $first->invoice_number, $m1);
    preg_match('/(\d{4})$/', $second->invoice_number, $m2);

    expect((int) $m2[1])->toBe((int) $m1[1] + 1);
});

// --- Discount Structured Columns (K1) ---

test('nominal discount is stored in dedicated columns', function () {
    $transaction = Transaction::factory()->create([
        'discount_type'   => 'nominal',
        'discount_value'  => 15000,
        'discount_amount' => 15000,
    ]);

    expect($transaction->discount_type)->toBe('nominal');
    expect((float) $transaction->discount_value)->toBe(15000.00);
    expect((float) $transaction->discount_amount)->toBe(15000.00);
});

test('percent discount is stored in dedicated columns', function () {
    $transaction = Transaction::factory()->create([
        'discount_type'   => 'percent',
        'discount_value'  => 10,
        'discount_amount' => 5000,
    ]);

    expect($transaction->discount_type)->toBe('percent');
    expect((float) $transaction->discount_value)->toBe(10.00);
    expect((float) $transaction->discount_amount)->toBe(5000.00);
});

test('discount columns default to null and zero when no discount is given', function () {
    $transaction = Transaction::factory()->create();

    expect($transaction->discount_type)->toBeNull();
    expect((float) $transaction->discount_value)->toBe(0.00);
    expect((float) $transaction->discount_amount)->toBe(0.00);
});
