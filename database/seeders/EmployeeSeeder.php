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
        $branchPusat = Branch::where('slug', 'nusantara-pangkas-pusat')->first();
        $branchSelatan = Branch::where('slug', 'nusantara-pangkas-selatan')->first();

        if (!$branchPusat && $branchNgaliyan)
            $branchPusat = $branchNgaliyan;
        if (!$branchSelatan && $branchFatmawati)
            $branchSelatan = $branchFatmawati;


        if ($branchPusat && $branchPusat->employees()->count() === 0) {
            Employee::factory()->count(3)->create(['branch_id' => $branchPusat->id]);
        }

        if ($branchSelatan && $branchSelatan->employees()->count() === 0) {
            Employee::factory()->count(2)->create(['branch_id' => $branchSelatan->id]);
        }

        if ($branchNgaliyan && $branchNgaliyan->employees()->count() === 0) {
            Employee::factory()->count(2)->create(['branch_id' => $branchNgaliyan->id]);
        }
    }
}
