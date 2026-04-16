<?php

use App\Models\User;
use Spatie\Permission\Models\Role;

beforeEach(function () {
    Role::firstOrCreate(['name' => 'super_admin', 'guard_name' => 'web']);
    Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
    Role::firstOrCreate(['name' => 'cashier', 'guard_name' => 'web']);
});

test('super admin can login and access filament dashboard', function () {
    $user = User::factory()->create();
    $user->assignRole('super_admin');

    $this->actingAs($user)
         ->get('/admin')
         ->assertSuccessful();
});

test('admin can login and access filament dashboard', function () {
    $user = User::factory()->create();
    $user->assignRole('admin');

    $this->actingAs($user)
         ->get('/admin')
         ->assertSuccessful();
});

test('cashier can login and access pos', function () {
    $user = User::factory()->create();
    $user->assignRole('cashier');

    $this->actingAs($user)
         ->get('/admin')
         ->assertSuccessful();
});
