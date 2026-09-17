<?php

namespace Database\Seeders;

use App\Models\PeriodeAnggaran;
use Illuminate\Database\Seeder;

class PeriodeAnggaranSeeder extends Seeder
{
    /**
     * Seed periode anggaran berjalan (Q-BKHM-02).
     */
    public function run(): void
    {
        $tahun = (int) date('Y');

        PeriodeAnggaran::updateOrCreate(
            ['nama' => 'Anggaran ' . $tahun],
            [
                'tanggal_mulai' => $tahun . '-01-01',
                'tanggal_selesai' => $tahun . '-12-31',
                'aktif' => true,
            ]
        );

        PeriodeAnggaran::where('nama', '!=', 'Anggaran ' . $tahun)->update(['aktif' => false]);
    }
}
