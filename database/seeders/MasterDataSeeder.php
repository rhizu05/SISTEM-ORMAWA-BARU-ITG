<?php

namespace Database\Seeders;

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
            MasterRuangan::create($item);
        }

        $barang = [
            ['nama_barang' => 'Sound System (Set)', 'stok_tersedia' => 2, 'status_aktif' => true],
            ['nama_barang' => 'Proyektor EPSON', 'stok_tersedia' => 5, 'status_aktif' => true],
            ['nama_barang' => 'Layar Proyektor (Screen)', 'stok_tersedia' => 5, 'status_aktif' => true],
            ['nama_barang' => 'Microphone Wireless', 'stok_tersedia' => 10, 'status_aktif' => true],
            ['nama_barang' => 'Kursi Lipat', 'stok_tersedia' => 200, 'status_aktif' => true],
            ['nama_barang' => 'Tenda Terop', 'stok_tersedia' => 4, 'status_aktif' => true],
        ];

        foreach ($barang as $item) {
            MasterBarang::create($item);
        }
    }
}
