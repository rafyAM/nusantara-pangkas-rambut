<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create Roles
        $roleSuperAdmin = Role::firstOrCreate(['name' => 'super-admin']);
        $roleAdmin = Role::firstOrCreate(['name' => 'admin']);
        $roleCashier = Role::firstOrCreate(['name' => 'cashier']);
        $roleCustomer = Role::firstOrCreate(['name' => 'customer']);

        // Define permissions (Example)
        // Permission::create(['name' => 'edit articles']);

        // Assign permissions to roles
        // $roleSuperAdmin->givePermissionTo(Permission::all());
    }
}
