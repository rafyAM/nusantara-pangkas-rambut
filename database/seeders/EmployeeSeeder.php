<?php

namespace Database\Seeders;

use App\Models\Branch;
use App\Models\Employee;
use Illuminate\Database\Seeder;

class EmployeeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $branchNgaliyan = Branch::where('slug', 'nusantara-pangkas-ngaliyan')->first();
        $branchFatmawati = Branch::where('slug', 'nusantara-pangkas-fatmawati')->first();

        if (!$branchNgaliyan && $branchFatmawati)
            $branchNgaliyan = $branchFatmawati;


        if ($branchNgaliyan && $branchNgaliyan->employees()->count() === 0) {
            Employee::factory()->count(3)->create(['branch_id' => $branchNgaliyan->id]);
        }

        if ($branchFatmawati && $branchFatmawati->employees()->count() === 0) {
            Employee::factory()->count(2)->create(['branch_id' => $branchFatmawati->id]);
        }

        if ($branchNgaliyan && $branchNgaliyan->employees()->count() === 0) {
            Employee::factory()->count(2)->create(['branch_id' => $branchNgaliyan->id]);
        }
    }
}
