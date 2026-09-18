<?php

namespace Database\Seeders;

use App\Models\Pengumuman;
use App\Models\Regulasi;
use App\Models\TiketLayanan;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class ContohLayananDanInformasiSeeder extends Seeder
{
    /**
     * Seed sample data untuk Pusat Informasi & Berita (Saran A) dan Tiket Layanan Publik.
     */
    public function run(): void
    {
        $bkhm = User::where('email', 'bkhm@test.com')->first();
        $bem = User::where('email', 'bem@test.com')->first();
        $bpm = User::where('email', 'bpm@test.com')->first();
        $himaif = User::where('email', 'himaif@test.com')->first();

        // 1. Seed Pengumuman & Berita Kemahasiswaan (FR-021 - Saran A)
        if ($bkhm) {
            Pengumuman::firstOrCreate(
                ['judul' => 'Pengumuman Resmi: Pembukaan Pendaftaran Program Kreativitas Mahasiswa (PKM) 2026'],
                [
                    'user_id' => $bkhm->id,
                    'isi' => 'Biro Kemahasiswaan (BKHM) ITG mengumumkan pembukaan pendanaan proposal PKM tahun 2026. Seluruh mahasiswa aktif ITG diharapkan mengajukan ide inovatif melalui SKIN sebelum batas waktu akhir.',
                    'kategori' => 'Pengumuman',
                    'status' => 'published',
                    'tanggal_kegiatan' => Carbon::now()->addWeeks(2)->toDateString(),
                ]
            );
        }

        if ($bem) {
            Pengumuman::firstOrCreate(
                ['judul' => 'Open Recruitment: Kepanitiaan Pekan Olahraga & Seni Mahasiswa (PORSENI) ITG 2026'],
                [
                    'user_id' => $bem->id,
                    'isi' => 'Badan Eksekutif Mahasiswa (BEM) ITG membuka kesempatan bagi seluruh mahasiswa ITG angkatan 2024 dan 2025 untuk bergabung menjadi panitia PORSENI 2026.',
                    'kategori' => 'Kegiatan',
                    'status' => 'published',
                    'tanggal_kegiatan' => Carbon::now()->addDays(5)->toDateString(),
                ]
            );
        }

        if ($himaif) {
            // Berita HIMA yang sudah disetujui BEM
            Pengumuman::firstOrCreate(
                ['judul' => 'Tech Expo & Seminar Nasional AI 2026 oleh HIMA Informatika'],
                [
                    'user_id' => $himaif->id,
                    'isi' => 'HIMA Informatika menyelenggarakan Seminar Nasional Kecerdasan Buatan dan Pameran Tugas Akhir Mahasiswa di Aula Gedung Rektorat ITG.',
                    'kategori' => 'Kegiatan',
                    'status' => 'published',
                    'disetujui_oleh_id' => $bem?->id,
                    'catatan_kurasi' => 'Artikel memenuhi kaidah publikasi kemahasiswaan. Disetujui tayang.',
                    'tanggal_kegiatan' => Carbon::now()->addWeeks(1)->toDateString(),
                ]
            );

            // Berita HIMA yang masih pending kurasi BEM (untuk menguji antrean kurasi BEM)
            Pengumuman::firstOrCreate(
                ['judul' => 'Draf Usulan: Pelatihan Cyber Security Fundamental untuk Pemula'],
                [
                    'user_id' => $himaif->id,
                    'isi' => 'Draf usulan publikasi berita kegiatan pelatihan keamanan siber yang diajukan oleh HIMA Informatika menunggu peninjauan dan persetujuan kurasi BEM.',
                    'kategori' => 'Kegiatan',
                    'status' => 'pending_kurasi',
                    'tanggal_kegiatan' => Carbon::now()->addDays(10)->toDateString(),
                ]
            );
        }

        // 2. Seed Regulasi BPM ITG
        if ($bpm) {
            Regulasi::firstOrCreate(
                ['judul' => 'Pedoman Tata Tertib Pemilihan Umum Raya (PEMIRA) Mahasiswa ITG'],
                [
                    'user_id' => $bpm->id,
                    'kategori' => 'Peraturan Mahasiswa',
                    'deskripsi' => 'Regulasi resmi Badan Perwakilan Mahasiswa (BPM) yang mengatur tata cara dan kode etik pemilihan Ketua BEM dan Himpunan Mahasiswa.',
                    'tanggal_terbit' => Carbon::now()->subMonths(1)->toDateString(),
                ]
            );
        }

        // 3. Seed Tiket Layanan Publik (Aspirasi, Konseling, Prestasi)
        $tahun = date('Y');

        // Aspirasi: Status pending BPM
        TiketLayanan::firstOrCreate(
            ['kode_tiket' => "SKIN-TKT-{$tahun}-0001"],
            [
                'kategori' => 'aspirasi',
                'nim' => '2206001',
                'nama_mahasiswa' => 'Ahmad Fauzi',
                'email' => 'ahmad.fauzi@mahasiswa.itg.ac.id',
                'no_hp' => '081234567890',
                'prodi' => 'Teknik Informatika',
                'judul' => 'Peningkatan Kecepatan Wi-Fi di Gedung Perpustakaan',
                'isi' => 'Mohon akses koneksi internet Wi-Fi di lantai 2 perpustakaan ditingkatkan karena sering mengalami disconnect saat jam praktikum dan belajar mandiri.',
                'status' => 'pending_bpm',
            ]
        );

        // Aspirasi: Status diteruskan ke BKHM
        TiketLayanan::firstOrCreate(
            ['kode_tiket' => "SKIN-TKT-{$tahun}-0002"],
            [
                'kategori' => 'aspirasi',
                'nim' => '2206015',
                'nama_mahasiswa' => 'Siti Nurhaliza',
                'email' => 'siti.nurhaliza@mahasiswa.itg.ac.id',
                'no_hp' => '082198765432',
                'prodi' => 'Teknik Sipil',
                'judul' => 'Penambahan Lampu Penerangan di Area Parkir Belakang',
                'isi' => 'Area parkir mahasiswa di belakang gedung lab teknik sangat gelap di malam hari setelah jam kuliah malam, mohon dipasang penerangan tambahan.',
                'catatan_bpm' => 'Aspirasi sangat mendesak terkait keselamatan mahasiswa. Diteruskan ke BKHM dan Sarpras untuk pengadaan lampu.',
                'diteruskan_ke_bkhm_at' => Carbon::now()->subDays(1),
                'status' => 'diteruskan_ke_bkhm',
            ]
        );

        // Konseling: Status jadwal ditentukan & dikonfirmasi hadir
        TiketLayanan::firstOrCreate(
            ['kode_tiket' => "SKIN-TKT-{$tahun}-0003"],
            [
                'kategori' => 'konseling',
                'nim' => '2306042',
                'nama_mahasiswa' => 'Budi Santoso',
                'email' => 'budi.santoso@mahasiswa.itg.ac.id',
                'no_hp' => '085712345678',
                'prodi' => 'Sistem Informasi',
                'topik_konseling' => 'Manajemen Waktu & Tekanan Akademik',
                'metode_konseling' => 'tatap_muka',
                'deskripsi_masalah' => 'Saya merasa kewalahan menyeimbangkan tugas kuliah semester 5 dengan kepengurusan organisasi, sehingga nilai UTS menurun.',
                'tanggapan_bkhm' => 'Halo Budi, terima kasih telah mempercayai layanan konseling BKHM. Kami siap membantu berdiskusi dan mencari solusi terbaik.',
                'jadwal_temu' => Carbon::now()->addDays(2)->setTime(10, 0),
                'lokasi_atau_link' => 'Ruang Konseling BKHM, Gedung Rektorat Lt. 2',
                'konfirmasi_mahasiswa' => 'hadir',
                'catatan_konfirmasi_mahasiswa' => 'Baik Pak/Bu, saya bersedia hadir tepat waktu.',
                'konfirmasi_at' => Carbon::now()->subHours(2),
                'status' => 'jadwal_ditentukan',
            ]
        );

        // Prestasi: Juara Hackathon Nasional (Tampil ke publik)
        TiketLayanan::firstOrCreate(
            ['kode_tiket' => "SKIN-TKT-{$tahun}-0004"],
            [
                'kategori' => 'prestasi',
                'sub_kategori' => 'lapor_prestasi',
                'nim' => '2206088',
                'nama_mahasiswa' => 'Rian Pratama',
                'email' => 'rian.pratama@mahasiswa.itg.ac.id',
                'no_hp' => '087812345678',
                'prodi' => 'Teknik Informatika',
                'nama_kegiatan' => 'National Hackathon Competition Kominfo 2026',
                'penyelenggara' => 'Kementerian Komunikasi dan Informatika RI',
                'tingkat' => 'Nasional',
                'capaian' => 'Juara 1 Kategori Smart Campus Solution',
                'tanggal_kegiatan' => Carbon::now()->subWeeks(2)->toDateString(),
                'tampil_ke_publik' => true,
                'status' => 'disetujui',
            ]
        );
    }
}