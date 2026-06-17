<?php

namespace Database\Seeders;

use App\Models\Service;
use Illuminate\Database\Seeder;

class ServiceSeeder extends Seeder
{
    public function run(): void
    {
        $services = [
            [
                'name' => 'Pangkas Rambut',
                'price' => 40000,
                'commission_owner_pct' => 52.50,
                'commission_kapster_pct' => 47.50,
                'description' => 'Potong rambut standar dengan hasil rapi',
            ],
            [
                'name' => 'Gundul Licin',
                'price' => 45000,
                'commission_owner_pct' => 55.60,
                'commission_kapster_pct' => 44.40,
                'description' => 'Cukur gundul licin',
            ],
            [
                'name' => 'Jenggot',
                'price' => 10000,
                'commission_owner_pct' => 0,
                'commission_kapster_pct' => 100,
                'description' => 'Rapikan dan cukur jenggot',
            ],
            [
                'name' => 'Creambath',
                'price' => 50000,
                'commission_owner_pct' => 62,
                'commission_kapster_pct' => 38,
                'description' => 'Perawatan creambath untuk kesehatan rambut',
            ],
            [
                'name' => 'Cat Rambut',
                'price' => 50000,
                'commission_owner_pct' => 62,
                'commission_kapster_pct' => 38,
                'description' => 'Pewarnaan rambut profesional (bahan ditanggung owner)',
            ],
        ];

        foreach ($services as $service) {
            Service::updateOrCreate(
                ['name' => $service['name']],
                $service
            );
        }
    }
}
