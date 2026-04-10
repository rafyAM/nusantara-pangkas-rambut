<?php

use App\Models\Customer;

test('portal login page renders initially', function () {
    $this->get('/login')->assertStatus(200);
});

test('customer can authenticate into portal', function () {
    $customer = Customer::factory()->create();

    $this->actingAs($customer, 'customer')
         ->get('/')
         ->assertStatus(200);
});
