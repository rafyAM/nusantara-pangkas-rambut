<?php

use App\Models\User;
use Spatie\Permission\Models\Role;

beforeEach(function () {
    Role::firstOrCreate(['name' => 'cashier', 'guard_name' => 'web']);
    Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
});

test('cashier cannot access branch resource explicitly', function () {
    $user = User::factory()->create();
    $user->assignRole('cashier');

    $this->actingAs($user)
         ->get('/admin/branches')
         ->assertStatus(403);
});
