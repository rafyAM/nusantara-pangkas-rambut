<?php

use App\Models\Transaction;
use App\Models\TransactionItem;
use App\Models\Service;
use App\Models\Product;
use Illuminate\Support\Str;

test('it generates correct invoice number', function () {
    $transaction = Transaction::factory()->create();

    expect($transaction->invoice_number)->toStartWith('INV-'.now()->format('Ymd'));
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
