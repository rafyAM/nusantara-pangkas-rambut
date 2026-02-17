<?php

namespace Database\Seeders;

use App\Models\Service;
use Illuminate\Database\Seeder;

class ServiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $services = [
            ['name' => 'Potong Rambut', 'price' => 35000, 'description' => 'Potong rambut standar dengan hasil rapi'],
            ['name' => 'Cukur Jenggot', 'price' => 10000, 'description' => 'Rapikan dan cukur jenggot'],
            ['name' => 'Gundul Licin', 'price' => 40000, 'description' => 'Penataan dan styling rambut'],
            ['name' => 'Creambath', 'price' => 40000, 'description' => 'Perawatan creambath untuk kesehatan rambut'],
            ['name' => 'Hair Coloring', 'price' => 40000, 'description' => 'Pewarnaan rambut profesional'],
        ];

        foreach ($services as $service) {
            Service::firstOrCreate(['name' => $service['name']], $service);
        }
    }
}
