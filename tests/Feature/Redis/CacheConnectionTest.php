<?php

use Illuminate\Support\Facades\Cache;

test('redis system caching successfully stores data', function () {
    Cache::store('redis')->put('barbershop_service_status', 'operational', 10);
    
    expect(Cache::store('redis')->get('barbershop_service_status'))->toBe('operational');
    
    Cache::store('redis')->forget('barbershop_service_status');
    expect(Cache::store('redis')->get('barbershop_service_status'))->toBeNull();
})->skip(fn() => config('cache.stores.redis') === null, 'Redis cache store not configured on environment');
