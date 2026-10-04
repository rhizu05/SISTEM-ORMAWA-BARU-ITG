<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $seeders = [
            RolePermissionSeeder::class,
            UserSeeder::class,
            WorkflowSeeder::class,
            KonfigurasiSeeder::class,
            MasterDataSeeder::class,
            PeriodeAnggaranSeeder::class,
        ];

        // ContohLayananDanInformasiSeeder memuat data sampel demo dan hanya dijalankan pada lokal/staging
        if (!app()->isProduction() && env('APP_ENV') !== 'production') {
            $seeders[] = ContohLayananDanInformasiSeeder::class;
        }

        $this->call($seeders);
    }
}
