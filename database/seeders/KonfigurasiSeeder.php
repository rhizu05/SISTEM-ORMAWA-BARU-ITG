<?php

namespace Database\Seeders;

use App\Models\Konfigurasi;
use Illuminate\Database\Seeder;

class KonfigurasiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $konfigurasi = [
            ['nama_konfigurasi' => 'nama_aplikasi', 'nilai_konfigurasi' => 'SI-Keuangan (v2)'],
            ['nama_konfigurasi' => 'logo_sistem', 'nilai_konfigurasi' => null],
            ['nama_konfigurasi' => 'kop_logo', 'nilai_konfigurasi' => null],
            ['nama_konfigurasi' => 'kop_baris1', 'nilai_konfigurasi' => 'KEMENTERIAN PENDIDIKAN, KEBUDAYAAN, RISET, DAN TEKNOLOGI'],
            ['nama_konfigurasi' => 'kop_baris2', 'nilai_konfigurasi' => 'INSTITUT TEKNOLOGI GARUT'],
            ['nama_konfigurasi' => 'kop_baris3', 'nilai_konfigurasi' => 'Jalan Mayor Syamsu No. 1 Jayaraga Garut 44151 Telepon/Fax. (0262) 232773'],
            ['nama_konfigurasi' => 'kop_baris4', 'nilai_konfigurasi' => 'Website : www.itg.ac.id | Email : info@itg.ac.id'],
        ];

        foreach ($konfigurasi as $item) {
            Konfigurasi::updateOrCreate(
                ['nama_konfigurasi' => $item['nama_konfigurasi']],
                ['nilai_konfigurasi' => $item['nilai_konfigurasi']]
            );
        }
    }
}
