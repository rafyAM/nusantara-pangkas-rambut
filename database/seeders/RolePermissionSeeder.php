<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cache sebelum seeding
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // ── Buat Role ──
        $roleSuperAdmin = Role::firstOrCreate(['name' => 'super_admin',  'guard_name' => 'web']);
        $roleAdmin      = Role::firstOrCreate(['name' => 'admin',        'guard_name' => 'web']);
        $roleCashier    = Role::firstOrCreate(['name' => 'cashier',      'guard_name' => 'web']);

        // ── Daftar permission per resource ──
        $resources = [
            'branch', 'employee', 'service', 'product',
            'transaction', 'customer', 'reservation',
        ];

        $actions = [
            'view', 'view_any', 'create', 'update',
            'delete', 'delete_any', 'restore', 'restore_any',
            'force_delete', 'force_delete_any',
        ];

        // Buat semua permission jika belum ada
        foreach ($resources as $resource) {
            foreach ($actions as $action) {
                Permission::firstOrCreate([
                    'name'       => "{$action}_{$resource}",
                    'guard_name' => 'web',
                ]);
            }
        }

        // Permission tambahan untuk Shield (role management)
        $shieldPermissions = [
            'view_role', 'view_any_role', 'create_role',
            'update_role', 'delete_role', 'delete_any_role',
        ];
        foreach ($shieldPermissions as $perm) {
            Permission::firstOrCreate(['name' => $perm, 'guard_name' => 'web']);
        }

        // ── Super Admin: semua permission ──
        $roleSuperAdmin->syncPermissions(Permission::all());

        // ── Admin Cabang: kelola operasional cabang sendiri, tidak bisa kelola branch & role ──
        $adminPermissions = [
            'view_employee', 'view_any_employee', 'create_employee',
            'update_employee', 'delete_employee',

            'view_transaction', 'view_any_transaction', 'create_transaction',
            'update_transaction', 'delete_transaction', 'restore_transaction',

            'view_service', 'view_any_service',

            'view_product', 'view_any_product', 'create_product',
            'update_product', 'delete_product',

            'view_customer', 'view_any_customer', 'create_customer',
            'update_customer',

            'view_reservation', 'view_any_reservation', 'create_reservation',
            'update_reservation', 'delete_reservation', 'restore_reservation',
        ];
        $roleAdmin->syncPermissions($adminPermissions);

        $cashierPermissions = [
            'view_transaction', 'view_any_transaction', 'create_transaction',

            'view_service',     'view_any_service',
            'view_product',     'view_any_product',
            'view_employee',    'view_any_employee',
            'view_customer',    'view_any_customer', 'create_customer',

            'view_reservation', 'view_any_reservation', 'create_reservation',
            'update_reservation', 'delete_reservation', 'restore_reservation',
        ];
        $roleCashier->syncPermissions($cashierPermissions);

        $this->command->info(' RolePermissionSeeder selesai.');
        $this->command->info("   super_admin : {$roleSuperAdmin->permissions()->count()} permissions");
        $this->command->info("   admin       : {$roleAdmin->permissions()->count()} permissions");
        $this->command->info("   cashier     : {$roleCashier->permissions()->count()} permissions");
    }
}
