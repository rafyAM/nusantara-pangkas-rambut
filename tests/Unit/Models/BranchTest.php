<?php

use App\Models\Branch;
use App\Models\User;
use App\Models\Employee;

test('it can create a branch', function () {
    $branch = Branch::factory()->create([
        'name' => 'Test Branch',
    ]);

    expect($branch)->toBeInstanceOf(Branch::class);
    expect($branch->name)->toBe('Test Branch');
    $this->assertDatabaseHas('branches', ['name' => 'Test Branch']);
});

test('branch can have associated users', function () {
    $branch = Branch::factory()->create();
    $user = User::factory()->create();

    $branch->users()->attach($user);

    expect($branch->users->contains($user))->toBeTrue();
});

test('branch has employees', function () {
    $branch = Branch::factory()->create();
    $employee = Employee::factory()->create(['branch_id' => $branch->id]);

    expect($branch->employees->contains($employee))->toBeTrue();
});
