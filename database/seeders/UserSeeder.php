<?php

namespace Database\Seeders;

use App\Models\Branch;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $branchNgaliyan   = Branch::where('slug', 'nusantara-pangkas-ngaliyan')->first();
        $branchFatmawati  = Branch::where('slug', 'nusantara-pangkas-fatmawati')->first();

        // ── Super Admin ──
        $superAdmin = User::firstOrCreate(
            ['email' => 'superadmin@example.com'],
            [
                'name'              => 'Super Admin',
                'password'          => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );
        $superAdmin->syncRoles(['super_admin']);

        // ── Admin Cabang Ngaliyan ──
        $adminNgaliyan = User::firstOrCreate(
            ['email' => 'admin.ngaliyan@example.com'],
            [
                'name'              => 'Admin Ngaliyan',
                'password'          => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );
        $adminNgaliyan->syncRoles(['admin']);
        if ($branchNgaliyan) {
            $adminNgaliyan->branches()->syncWithoutDetaching([$branchNgaliyan->id]);
        }

        // ── Admin Cabang Fatmawati ──
        $adminFatmawati = User::firstOrCreate(
            ['email' => 'admin.fatmawati@example.com'],
            [
                'name'              => 'Admin Fatmawati',
                'password'          => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );
        $adminFatmawati->syncRoles(['admin']);
        if ($branchFatmawati) {
            $adminFatmawati->branches()->syncWithoutDetaching([$branchFatmawati->id]);
        }

        // ── Kasir Cabang Ngaliyan ──
        $cashierNgaliyan = User::firstOrCreate(
            ['email' => 'kasir.ngaliyan@example.com'],
            [
                'name'              => 'Kasir Ngaliyan',
                'password'          => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );
        $cashierNgaliyan->syncRoles(['cashier']);
        if ($branchNgaliyan) {
            $cashierNgaliyan->branches()->syncWithoutDetaching([$branchNgaliyan->id]);
        }

        // ── Kasir Cabang Fatmawati ──
        $cashierFatmawati = User::firstOrCreate(
            ['email' => 'kasir.fatmawati@example.com'],
            [
                'name'              => 'Kasir Fatmawati',
                'password'          => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );
        $cashierFatmawati->syncRoles(['cashier']);
        if ($branchFatmawati) {
            $cashierFatmawati->branches()->syncWithoutDetaching([$branchFatmawati->id]);
        }

        $this->command->info('✅ UserSeeder selesai.');
        $this->command->info('   superadmin@example.com      → super_admin');
        $this->command->info('   admin.ngaliyan@example.com  → admin (Ngaliyan)');
        $this->command->info('   admin.fatmawati@example.com → admin (Fatmawati)');
        $this->command->info('   kasir.ngaliyan@example.com  → cashier (Ngaliyan)');
        $this->command->info('   kasir.fatmawati@example.com → cashier (Fatmawati)');
        $this->command->info('   Semua password: password');
    }
}
