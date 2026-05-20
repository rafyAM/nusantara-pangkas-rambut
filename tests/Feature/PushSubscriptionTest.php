<?php

use App\Models\Customer;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('it allows an authenticated customer to subscribe to web push notifications', function () {
    // 1. Siapkan data customer yang sudah login
    /** @var \App\Models\Customer|\Illuminate\Contracts\Auth\Authenticatable $customer */
    $customer = Customer::factory()->create();

    // 2. Siapkan payload simulasi dari browser (PWA)
    $payload = [
        'endpoint' => 'https://fcm.googleapis.com/fcm/send/fake-endpoint-12345',
        'keys' => [
            'p256dh' => 'fake-p256dh-key',
            'auth' => 'fake-auth-token',
        ]
    ];

    // 3. Lakukan request POST ke endpoint sebagai customer tersebut
    $response = $this->actingAs($customer, 'customer')
        ->postJson(route('push.subscribe'), $payload);

    // 4. Pastikan response sukses
    $response->assertSuccessful()
             ->assertJson(['success' => true]);

    // 5. Pastikan data tersimpan dengan benar di tabel push_subscriptions
    $this->assertDatabaseHas('push_subscriptions', [
        'subscribable_type' => Customer::class,
        'subscribable_id' => $customer->id,
        'endpoint' => 'https://fcm.googleapis.com/fcm/send/fake-endpoint-12345',
        'public_key' => 'fake-p256dh-key',
        'auth_token' => 'fake-auth-token',
    ]);
});

test('it rejects unauthenticated users from subscribing to web push notifications', function () {
    // 1. Siapkan payload
    $payload = [
        'endpoint' => 'https://fcm.googleapis.com/fcm/send/fake-endpoint-12345',
        'keys' => [
            'p256dh' => 'fake-p256dh-key',
            'auth' => 'fake-auth-token',
        ]
    ];

    // 2. Lakukan request POST TANPA login
    $response = $this->postJson(route('push.subscribe'), $payload);

    // 3. Pastikan server menolak (Unauthorized 401)
    $response->assertStatus(401);

    // 4. Pastikan data TIDAK tersimpan di database untuk menjaga keamanan
    $this->assertDatabaseMissing('push_subscriptions', [
        'endpoint' => 'https://fcm.googleapis.com/fcm/send/fake-endpoint-12345',
    ]);
});
