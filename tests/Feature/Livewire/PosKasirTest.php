<?php

use App\Models\Branch;
use App\Models\CashierShift;
use App\Models\Customer;
use App\Models\Employee;
use App\Models\Service;
use App\Models\Transaction;
use App\Models\User;
use App\Livewire\PosKasir;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;

beforeEach(function () {
    Role::firstOrCreate(['name' => 'cashier', 'guard_name' => 'web']);
});

/**
 * Buat user kasir yang sudah terhubung ke employee dan branch.
 * Dibutuhkan agar processTransaction() bisa resolve branchId dan employeeId.
 */
function setupCashierEnv(): array
{
    $branch   = Branch::factory()->create();
    $user     = User::factory()->create();
    $user->assignRole('cashier');
    $user->branches()->attach($branch->id);
    $employee = Employee::factory()->create([
        'branch_id' => $branch->id,
        'user_id'   => $user->id,
    ]);

    CashierShift::create([
        'user_id' => $user->id,
        'start_at' => now(),
        'opening_cash' => 0,
        'status' => 'open',
    ]);

    return compact('user', 'branch', 'employee');
}

/**
 * Buat cart array satu item layanan untuk dipakai di set('cart', ...).
 */
function makeServiceCart(Service $service): array
{
    return [
        'service_' . $service->id => [
            'id'       => $service->id,
            'type'     => 'service',
            'name'     => $service->name,
            'price'    => (float) $service->price,
            'quantity' => 1,
            'subtotal' => (float) $service->price,
        ],
    ];
}

// --- Render ---

test('pos kasir component renders properly for authenticated cashier', function () {
    $user = User::factory()->create();
    $user->assignRole('cashier');

    $this->actingAs($user);

    Livewire::test(PosKasir::class)
            ->assertStatus(200);
});

// --- Bug B2: Nomor telepon customer baru ---

test('new customer is created with phone from dedicated customerPhone field, not from search query', function () {
    ['user' => $user] = setupCashierEnv();
    $service = Service::factory()->create(['price' => 50000, 'is_active' => true]);

    $this->actingAs($user);

    Livewire::test(PosKasir::class)
        ->set('customerName', 'Pelanggan Baru')
        ->set('customerSearch', 'Pelanggan Baru') // Ini dulu isi phone (bug lama)
        ->set('customerPhone', '081234567890')
        ->set('cart', makeServiceCart($service))
        ->set('paymentMethod', 'cash')
        ->set('paymentAmount', 50000)
        ->call('processTransaction');

    $customer = Customer::where('name', 'Pelanggan Baru')->first();

    expect($customer)->not->toBeNull();
    expect($customer->phone)->toBe('081234567890');
    // Pastikan nama tidak tersimpan sebagai nomor telepon (bug lama)
    expect($customer->phone)->not->toBe('Pelanggan Baru');
});

test('new customer is created with null phone when customerPhone is empty', function () {
    ['user' => $user] = setupCashierEnv();
    $service = Service::factory()->create(['price' => 30000, 'is_active' => true]);

    $this->actingAs($user);

    Livewire::test(PosKasir::class)
        ->set('customerName', 'Tamu Tanpa Nomor')
        ->set('customerPhone', '')
        ->set('cart', makeServiceCart($service))
        ->set('paymentMethod', 'cash')
        ->set('paymentAmount', 30000)
        ->call('processTransaction');

    $customer = Customer::where('name', 'Tamu Tanpa Nomor')->first();

    expect($customer)->not->toBeNull();
    expect($customer->phone)->toBeNull();
});

// --- K1: Diskon tersimpan di kolom terstruktur ---

test('nominal discount is saved to structured columns and notes is null', function () {
    ['user' => $user] = setupCashierEnv();
    $service = Service::factory()->create(['price' => 50000, 'is_active' => true]);

    $this->actingAs($user);

    Livewire::test(PosKasir::class)
        ->set('cart', makeServiceCart($service))
        ->set('discountType', 'nominal')
        ->set('discountValue', 10000)
        ->set('paymentMethod', 'cash')
        ->set('paymentAmount', 40000) // 50000 - 10000
        ->call('processTransaction');

    $transaction = Transaction::withoutGlobalScopes()->latest()->first();

    expect($transaction->discount_type)->toBe('nominal');
    expect((float) $transaction->discount_value)->toBe(10000.00);
    expect((float) $transaction->discount_amount)->toBe(10000.00);
    expect((float) $transaction->total_amount)->toBe(40000.00);
    expect($transaction->notes)->toBeNull();
});

test('percent discount amount is calculated and saved correctly', function () {
    ['user' => $user] = setupCashierEnv();
    $service = Service::factory()->create(['price' => 50000, 'is_active' => true]);

    $this->actingAs($user);

    Livewire::test(PosKasir::class)
        ->set('cart', makeServiceCart($service))
        ->set('discountType', 'percent')
        ->set('discountValue', 10)   // 10% dari 50000 = 5000
        ->set('paymentMethod', 'cash')
        ->set('paymentAmount', 45000) // 50000 - 5000
        ->call('processTransaction');

    $transaction = Transaction::withoutGlobalScopes()->latest()->first();

    expect($transaction->discount_type)->toBe('percent');
    expect((float) $transaction->discount_value)->toBe(10.00);
    expect((float) $transaction->discount_amount)->toBe(5000.00);
    expect((float) $transaction->total_amount)->toBe(45000.00);
});

test('transaction without discount has null discount_type and zero discount columns', function () {
    ['user' => $user] = setupCashierEnv();
    $service = Service::factory()->create(['price' => 50000, 'is_active' => true]);

    $this->actingAs($user);

    Livewire::test(PosKasir::class)
        ->set('cart', makeServiceCart($service))
        ->set('paymentMethod', 'cash')
        ->set('paymentAmount', 50000)
        ->call('processTransaction');

    $transaction = Transaction::withoutGlobalScopes()->latest()->first();

    expect($transaction->discount_type)->toBeNull();
    expect((float) $transaction->discount_value)->toBe(0.00);
    expect((float) $transaction->discount_amount)->toBe(0.00);
    expect((float) $transaction->total_amount)->toBe(50000.00);
});

// --- Reset state setelah transaksi ---

test('discount type resets to nominal after transaction is processed', function () {
    ['user' => $user] = setupCashierEnv();
    $service = Service::factory()->create(['price' => 50000, 'is_active' => true]);

    $this->actingAs($user);

    $component = Livewire::test(PosKasir::class)
        ->set('cart', makeServiceCart($service))
        ->set('discountType', 'percent')
        ->set('discountValue', 20)
        ->set('paymentMethod', 'cash')
        ->set('paymentAmount', 40000)
        ->call('processTransaction');

    $component->assertSet('discountType', 'nominal');
    $component->assertSet('discountValue', 0);
    $component->assertSet('cart', []);
});

// --- Cashier Shift & Cash Movement Feature Tests ---

test('cashier cannot open a new shift if one is already active', function () {
    ['user' => $user] = setupCashierEnv(); // Creates an open shift
    $this->actingAs($user);

    Livewire::test(PosKasir::class)
        ->set('openingCash', 100000)
        ->call('openShift')
        ->assertHasErrors(['openingCash' => 'Anda sudah memiliki shift yang masih aktif.']);
});

test('cashier can close an active shift', function () {
    ['user' => $user] = setupCashierEnv();
    $this->actingAs($user);

    Livewire::test(PosKasir::class)
        ->call('openCloseShiftModal')
        ->assertSet('showCloseShiftModal', true)
        ->set('actualCash', 50000)
        ->set('closingNotes', 'All good')
        ->call('closeShift')
        ->assertSet('showCloseShiftModal', false)
        ->assertRedirect('admin/login');

    $shift = CashierShift::where('user_id', $user->id)->latest()->first();
    expect($shift->status)->toBe('closed');
    expect((float) $shift->actual_cash)->toBe(50000.0);
    expect($shift->notes)->toBe('All good');
});

test('cashier can add cash movement in', function () {
    ['user' => $user] = setupCashierEnv();
    $this->actingAs($user);

    Livewire::test(PosKasir::class)
        ->call('openCashMovementModal')
        ->assertSet('showCashMovementModal', true)
        ->set('cashMovementType', 'in')
        ->set('cashMovementAmount', 150000)
        ->set('cashMovementReason', 'Top up change')
        ->call('saveCashMovement')
        ->assertSet('showCashMovementModal', false);

    $shift = CashierShift::where('user_id', $user->id)->latest()->first();
    $movement = $shift->cashMovements()->first();

    expect($movement)->not->toBeNull();
    expect($movement->type)->toBe('in');
    expect((float) $movement->amount)->toBe(150000.0);
    expect($movement->reason)->toBe('Top up change');
});
