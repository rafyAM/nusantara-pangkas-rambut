<?php

namespace Database\Seeders;

use App\Models\Branch;
use Illuminate\Database\Seeder;

class BranchSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $branches = [
            ['name' => 'Nusantara Pangkas Ngaliyan', 'slug' => 'nusantara-pangkas-ngaliyan', 'address' => 'Bringin, Kec. Ngaliyan, Kota Semarang, Jawa Tengah'],
            ['name' => 'Nusantara Pangkas Fatmawati', 'slug' => 'nusantara-pangkas-fatmawati', 'address' => 'Jl. Fatmawati No.139, Kedungmundu, Kec. Tembalang, Kota Semarang, Jawa Tengah 50273'],
        ];

        foreach ($branches as $branch) {
            Branch::firstOrCreate(['slug' => $branch['slug']], $branch);
        }
    }
}
