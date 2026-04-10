<?php

use App\Models\User;
use Spatie\Permission\Models\Role;
use Livewire\Livewire;
use App\Livewire\PosKasir;

beforeEach(function () {
    Role::firstOrCreate(['name' => 'cashier', 'guard_name' => 'web']);
});

test('pos kasir component renders properly for authenticated cashier', function () {
    $user = User::factory()->create();
    $user->assignRole('cashier');

    $this->actingAs($user);

    Livewire::test(PosKasir::class)
            ->assertStatus(200);
});
