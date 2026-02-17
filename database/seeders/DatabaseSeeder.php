<?php

namespace Database\Seeders;

use App\Models\Branch;
use App\Models\Customer;
use App\Models\Employee;
use App\Models\Service;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        // Seed Branches
        $branches = [
            ['name' => 'Nusantara Pangkas Pusat', 'slug' => 'nusantara-pangkas-pusat', 'address' => 'Jl. Sudirman No. 1, Jakarta Pusat'],
            ['name' => 'Nusantara Pangkas Selatan', 'slug' => 'nusantara-pangkas-selatan', 'address' => 'Jl. Fatmawati No. 10, Jakarta Selatan'],
        ];

        foreach ($branches as $branch) {
            Branch::firstOrCreate(['slug' => $branch['slug']], $branch);
        }

        // Seed Services
        $services = [
            ['name' => 'Potong Rambut Reguler', 'price' => 35000, 'duration_minutes' => 30, 'description' => 'Potong rambut standar dengan hasil rapi'],
            ['name' => 'Potong Rambut Premium', 'price' => 50000, 'duration_minutes' => 45, 'description' => 'Potong rambut premium dengan konsultasi gaya'],
            ['name' => 'Cukur Jenggot', 'price' => 20000, 'duration_minutes' => 15, 'description' => 'Rapikan dan cukur jenggot'],
            ['name' => 'Creambath', 'price' => 75000, 'duration_minutes' => 60, 'description' => 'Perawatan creambath untuk kesehatan rambut'],
            ['name' => 'Hair Coloring', 'price' => 150000, 'duration_minutes' => 90, 'description' => 'Pewarnaan rambut profesional'],
            ['name' => 'Hair Wash', 'price' => 25000, 'duration_minutes' => 20, 'description' => 'Cuci rambut dengan shampoo premium'],
            ['name' => 'Pijat Kepala', 'price' => 30000, 'duration_minutes' => 20, 'description' => 'Pijat kepala relaksasi'],
            ['name' => 'Styling Rambut', 'price' => 40000, 'duration_minutes' => 30, 'description' => 'Penataan dan styling rambut'],
        ];

        foreach ($services as $service) {
            Service::firstOrCreate(['name' => $service['name']], $service);
        }

        // Seed Employees
        $branchPusat = Branch::where('slug', 'nusantara-pangkas-pusat')->first();
        $branchSelatan = Branch::where('slug', 'nusantara-pangkas-selatan')->first();

        if ($branchPusat && $branchPusat->employees()->count() === 0) {
            Employee::factory()->count(3)->create(['branch_id' => $branchPusat->id]);
        }

        if ($branchSelatan && $branchSelatan->employees()->count() === 0) {
            Employee::factory()->count(2)->create(['branch_id' => $branchSelatan->id]);
        }

        // Seed Customers
        if (Customer::count() === 0) {
            Customer::factory()->count(20)->create();
        }
    }
}
