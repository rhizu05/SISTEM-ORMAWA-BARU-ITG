<?php

namespace Database\Seeders;

use App\Models\JadwalKuliah;
use App\Models\MasterBarang;
use App\Models\MasterRuangan;
use Illuminate\Database\Seeder;

class MasterDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $ruangan = [
            ['nama_ruangan' => 'Aula Gedung Rektorat', 'kapasitas' => 200, 'status_aktif' => true],
            ['nama_ruangan' => 'Ruang Sidang BEM', 'kapasitas' => 50, 'status_aktif' => true],
            ['nama_ruangan' => 'Lapangan Olahraga', 'kapasitas' => 500, 'status_aktif' => true],
            ['nama_ruangan' => 'Ruang Kelas A101', 'kapasitas' => 40, 'status_aktif' => true],
        ];

        foreach ($ruangan as $item) {
            MasterRuangan::firstOrCreate(
                ['nama_ruangan' => $item['nama_ruangan']],
                $item
            );
        }

        $barang = [
            ['nama_barang' => 'Sound System (Set)', 'stok_tersedia' => 2, 'status_aktif' => true, 'boleh_dibawa_keluar' => false],
            ['nama_barang' => 'Proyektor EPSON', 'stok_tersedia' => 5, 'status_aktif' => true, 'boleh_dibawa_keluar' => true],
            ['nama_barang' => 'Layar Proyektor (Screen)', 'stok_tersedia' => 5, 'status_aktif' => true, 'boleh_dibawa_keluar' => true],
            ['nama_barang' => 'Microphone Wireless', 'stok_tersedia' => 10, 'status_aktif' => true, 'boleh_dibawa_keluar' => true],
            ['nama_barang' => 'Kursi Lipat', 'stok_tersedia' => 200, 'status_aktif' => true, 'boleh_dibawa_keluar' => false],
            ['nama_barang' => 'Tenda Terop', 'stok_tersedia' => 4, 'status_aktif' => true, 'boleh_dibawa_keluar' => true],
        ];

        foreach ($barang as $item) {
            MasterBarang::firstOrCreate(
                ['nama_barang' => $item['nama_barang']],
                $item
            );
        }

        // Seed Sample Jadwal Kuliah untuk Ruang Kelas A101 (Q-SAR-02)
        $kelasA101 = MasterRuangan::where('nama_ruangan', 'Ruang Kelas A101')->first();
        if ($kelasA101) {
            $jadwal = [
                [
                    'ruangan_id' => $kelasA101->id,
                    'hari' => 1, // Senin
                    'jam_mulai' => '08:00:00',
                    'jam_selesai' => '10:30:00',
                    'mata_kuliah' => 'Algoritma & Pemrograman (TIF-101)',
                    'semester' => 'Ganjil 2026/2027',
                    'aktif' => true,
                ],
                [
                    'ruangan_id' => $kelasA101->id,
                    'hari' => 3, // Rabu
                    'jam_mulai' => '13:00:00',
                    'jam_selesai' => '15:30:00',
                    'mata_kuliah' => 'Basis Data Lanjut (TIF-204)',
                    'semester' => 'Ganjil 2026/2027',
                    'aktif' => true,
                ],
            ];

            foreach ($jadwal as $j) {
                JadwalKuliah::firstOrCreate(
                    [
                        'ruangan_id' => $j['ruangan_id'],
                        'hari' => $j['hari'],
                        'jam_mulai' => $j['jam_mulai'],
                        'mata_kuliah' => $j['mata_kuliah'],
                    ],
                    $j
                );
            }
        }
    }
}
