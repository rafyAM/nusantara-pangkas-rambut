<?php

use App\Models\Service;
use App\Models\Product;

test('it filters active services', function () {
    Service::factory()->create(['is_active' => true]);
    Service::factory()->create(['is_active' => true]);
    Service::factory()->create(['is_active' => false]);

    $activeServices = Service::where('is_active', true)->get();

    expect($activeServices)->toHaveCount(2);
});

test('it filters active products', function () {
    $branch = \App\Models\Branch::factory()->create();
    Product::create(['branch_id' => $branch->id, 'name' => 'Pomade', 'price' => 50000, 'is_active' => true]);
    Product::create(['branch_id' => $branch->id, 'name' => 'Hair Tonic', 'price' => 30000, 'is_active' => false]);

    $activeProducts = Product::where('is_active', true)->get();

    expect($activeProducts)->toHaveCount(1);
});

test('it calculates service price and duration', function () {
    $service = Service::factory()->create([
        'price' => 50000,
        'duration' => 30
    ]);

    expect((float)$service->price)->toBe(50000.0);
    expect((int)$service->duration)->toBe(30);
});
