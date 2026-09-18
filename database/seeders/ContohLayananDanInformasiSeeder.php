<?php

namespace Database\Seeders;

use App\Models\MasterBarang;
use App\Models\MasterRuangan;
use App\Models\PeminjamanBarang;
use App\Models\PeminjamanTempat;
use App\Models\Pengajuan;
use App\Models\Pengumuman;
use App\Models\Regulasi;
use App\Models\TiketLayanan;
use App\Models\User;
use App\Models\WorkflowState;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class ContohLayananDanInformasiSeeder extends Seeder
{
    /**
     * Seed sample data untuk Pusat Informasi, Tiket Layanan Publik, Pengajuan Ormawa, dan Sarpras.
     */
    public function run(): void
    {
        $bkhm = User::where('email', 'bkhm@test.com')->first();
        $bem = User::where('email', 'bem@test.com')->first();
        $bpm = User::where('email', 'bpm@test.com')->first();
        $himaif = User::where('email', 'himaif@test.com')->first();
        $ukm = User::where('email', 'ukm.olahraga@test.com')->first();

        // 0. Siapkan direktori dan fungsi pembuat gambar poster sampel
        Storage::disk('public')->makeDirectory('pengumuman/sampul');

        $buatGambar = function (string $filename, string $title, int $r, int $g, int $b) {
            $path = "pengumuman/sampul/{$filename}";
            $fullPath = storage_path("app/public/{$path}");

            if (function_exists('imagecreatetruecolor')) {
                $w = 1200;
                $h = 675;
                $img = imagecreatetruecolor($w, $h);
                $bg = imagecolorallocate($img, $r, $g, $b);
                imagefill($img, 0, 0, $bg);

                $cardBg = imagecolorallocate($img, max(0, $r - 25), max(0, $g - 25), max(0, $b - 25));
                imagefilledrectangle($img, 50, 50, $w - 50, $h - 50, $cardBg);

                $textColor = imagecolorallocate($img, 255, 255, 255);
                $accentColor = imagecolorallocate($img, 251, 191, 36);

                imagestring($img, 5, 80, 100, 'INSTITUT TEKNOLOGI GARUT - SKIN ITG', $accentColor);
                imagestring($img, 5, 80, 150, strtoupper($title), $textColor);
                imagestring($img, 4, 80, 210, 'Portal Resmi Kemahasiswaan & Publikasi Ormawa', $textColor);

                imagepng($img, $fullPath);
                imagedestroy($img);
            }

            return $path;
        };

        $imgPkm = $buatGambar('poster_pkm_2026.png', 'Program Kreativitas Mahasiswa (PKM) 2026', 30, 58, 138);
        $imgPorseni = $buatGambar('poster_porseni_2026.png', 'Open Recruitment Panitia PORSENI 2026', 67, 56, 202);
        $imgTechExpo = $buatGambar('poster_tech_expo_2026.png', 'Tech Expo & Seminar Nasional AI 2026', 16, 149, 106);
        $imgCyberSec = $buatGambar('poster_cyber_sec_2026.png', 'Pelatihan Cyber Security Fundamental 2026', 51, 65, 85);

        // 1. Seed Pengumuman & Berita Kemahasiswaan (FR-021 - Saran A)
        if ($bkhm) {
            Pengumuman::firstOrCreate(
                ['judul' => 'Pengumuman Resmi: Pembukaan Pendaftaran Program Kreativitas Mahasiswa (PKM) 2026'],
                [
                    'user_id' => $bkhm->id,
                    'isi' => "Biro Kemahasiswaan (BKHM) Institut Teknologi Garut resmi membuka pendanaan proposal PKM tahun 2026.\n\nSkema yang dibuka meliputi:\n1. PKM-Riset Eksakta (PKM-RE)\n2. PKM-Kewirausahaan (PKM-K)\n3. PKM-Pengabdian Masyarakat (PKM-PM)\n4. PKM-Penerapan Iptek (PKM-PI)\n5. PKM-Karsa Cipta (PKM-KC)\n\nSeluruh mahasiswa aktif ITG diharapkan segera menyusun kelompok dan proposal ide inovatif melalui portal kemahasiswaan.",
                    'kategori' => 'resmi_kampus',
                    'status' => 'published',
                    'gambar_sampul' => $imgPkm,
                    'tanggal_kegiatan' => Carbon::now()->addWeeks(3)->toDateString(),
                ]
            );
        }

        if ($bem) {
            Pengumuman::firstOrCreate(
                ['judul' => 'Open Recruitment: Kepanitiaan Pekan Olahraga & Seni Mahasiswa (PORSENI) ITG 2026'],
                [
                    'user_id' => $bem->id,
                    'isi' => "Badan Eksekutif Mahasiswa (BEM) ITG membuka kesempatan bagi seluruh mahasiswa ITG angkatan 2024 dan 2025 untuk bergabung menjadi panitia pelaksana PORSENI 2026.\n\nDivisi yang dibutuhkan:\n- Acara & Pertandingan\n- Humas & Kemitraan\n- Logistik & Perlengkapan\n- Desain, Dokumentasi & Media\n\nMari berkontribusi membangun sportivitas dan kreativitas kampus!",
                    'kategori' => 'kegiatan_kemahasiswaan',
                    'status' => 'published',
                    'gambar_sampul' => $imgPorseni,
                    'tanggal_kegiatan' => Carbon::now()->addDays(7)->toDateString(),
                ]
            );
        }

        if ($himaif) {
            // Berita HIMA yang sudah disetujui BEM
            Pengumuman::firstOrCreate(
                ['judul' => 'Tech Expo & Seminar Nasional AI 2026 oleh HIMA Informatika'],
                [
                    'user_id' => $himaif->id,
                    'isi' => "Himpunan Mahasiswa Informatika (HIMA IF) ITG mempersembahkan Tech Expo 2026 dengan tema 'Building Intelligent Future with Machine Learning & Autonomous Agents'.\n\nAcara meliputi pameran inovasi tugas akhir mahasiswa dan seminar menghadirkan narasumber praktisi industri teknologi nasional.",
                    'kategori' => 'kegiatan_kemahasiswaan',
                    'status' => 'published',
                    'disetujui_oleh_id' => $bem?->id,
                    'catatan_kurasi' => 'Artikel memenuhi standar etika publikasi kemahasiswaan. Disetujui tayang.',
                    'gambar_sampul' => $imgTechExpo,
                    'tanggal_kegiatan' => Carbon::now()->addWeeks(2)->toDateString(),
                ]
            );

            // Berita HIMA yang masih pending kurasi BEM (untuk menguji antrean kurasi BEM)
            Pengumuman::firstOrCreate(
                ['judul' => 'Draf Usulan: Pelatihan Cyber Security Fundamental untuk Pemula'],
                [
                    'user_id' => $himaif->id,
                    'isi' => "Draf usulan publikasi berita kegiatan pelatihan keamanan siber (Ethical Hacking & Network Defense) yang diajukan oleh HIMA Informatika, menunggu peninjauan dan persetujuan kurasi BEM.",
                    'kategori' => 'kegiatan_kemahasiswaan',
                    'status' => 'pending_kurasi',
                    'gambar_sampul' => $imgCyberSec,
                    'tanggal_kegiatan' => Carbon::now()->addDays(12)->toDateString(),
                ]
            );
        }

        // 2. Seed Regulasi BPM ITG
        if ($bpm) {
            Regulasi::firstOrCreate(
                ['judul' => 'Pedoman Tata Tertib Pemilihan Umum Raya (PEMIRA) Mahasiswa ITG'],
                [
                    'user_id' => $bpm->id,
                    'kategori' => 'Undang-Undang',
                    'deskripsi' => 'Regulasi resmi Badan Perwakilan Mahasiswa (BPM) yang mengatur tata cara, kode etik, dan alur pendaftaran calon Ketua BEM dan Himpunan Mahasiswa.',
                    'tanggal_terbit' => Carbon::now()->subMonths(1)->toDateString(),
                ]
            );

            Regulasi::firstOrCreate(
                ['judul' => 'Pedoman Pelaksanaan Program Kerja & Pengelolaan Dana Ormawa'],
                [
                    'user_id' => $bpm->id,
                    'kategori' => 'Pedoman',
                    'deskripsi' => 'Petunjuk teknis penyusunan proposal kegiatan, LPJ, serta batasan pengalokasian anggaran organisasi mahasiswa di lingkungan ITG.',
                    'tanggal_terbit' => Carbon::now()->subMonths(2)->toDateString(),
                ]
            );
        }

        // 3. Seed Tiket Layanan Publik (Aspirasi, Konseling, Prestasi)
        $tahun = date('Y');

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

        // 4. Seed Pengajuan Anggaran Ormawa
        $stateSubmitted = WorkflowState::where('name', 'submitted')->first();
        $stateFundsDisbursed = WorkflowState::where('name', 'funds_disbursed')->first();

        if ($himaif && $stateSubmitted) {
            Pengajuan::firstOrCreate(
                ['nama_kegiatan' => 'Workshop Pengembangan Web Fullstack Modern ITG 2026'],
                [
                    'user_id' => $himaif->id,
                    'dana_diajukan' => 3500000,
                    'tanggal_pengajuan' => Carbon::now()->subDays(3)->toDateString(),
                    'workflow_state_id' => $stateSubmitted->id,
                    'nomor_surat' => '012/HIMA-IF/PROP/ITG/2026',
                    'unique_code' => 'PROP-IF-2026-001',
                ]
            );
        }

        if ($ukm && $stateFundsDisbursed) {
            Pengajuan::firstOrCreate(
                ['nama_kegiatan' => 'Turnamen Futsal Antar Angkatan ITG Cup 2026'],
                [
                    'user_id' => $ukm->id,
                    'dana_diajukan' => 2000000,
                    'tanggal_pengajuan' => Carbon::now()->subWeeks(2)->toDateString(),
                    'workflow_state_id' => $stateFundsDisbursed->id,
                    'nomor_surat' => '005/UKM-OR/PROP/ITG/2026',
                    'unique_code' => 'PROP-UKM-2026-002',
                ]
            );
        }

        // 5. Seed Peminjaman Fasilitas Sarpras
        $aula = MasterRuangan::where('nama_ruangan', 'Aula Gedung Rektorat')->first();
        if ($himaif && $aula) {
            PeminjamanTempat::firstOrCreate(
                ['nama_kegiatan' => 'Tech Expo & Pameran Karya Mahasiswa ITG 2026'],
                [
                    'user_id' => $himaif->id,
                    'ruangan_id' => $aula->id,
                    'tgl_mulai' => Carbon::now()->addDays(5)->toDateString(),
                    'tgl_selesai' => Carbon::now()->addDays(6)->toDateString(),
                    'jam_mulai' => '08:00:00',
                    'jam_selesai' => '17:00:00',
                    'deskripsi_kegiatan' => 'Pameran karya inovasi teknologi dan seminar nasional mahasiswa informatika.',
                    'status_bkhm' => 'Disetujui',
                    'status_sarpras' => 'Disetujui',
                    'status_akhir' => 'Selesai / Disetujui',
                ]
            );
        }

        if ($ukm) {
            $sound = MasterBarang::where('nama_barang', 'Sound System (Set)')->first();
            $mic = MasterBarang::where('nama_barang', 'Microphone Wireless')->first();

            PeminjamanBarang::firstOrCreate(
                ['nama_kegiatan' => 'Opening Ceremony Turnamen Futsal ITG Cup'],
                [
                    'user_id' => $ukm->id,
                    'tgl_mulai' => Carbon::now()->addDays(8)->toDateString(),
                    'tgl_selesai' => Carbon::now()->addDays(9)->toDateString(),
                    'kebutuhan_barang' => [
                        ['barang_id' => $sound?->id ?? 1, 'nama_barang' => 'Sound System (Set)', 'jumlah' => 1],
                        ['barang_id' => $mic?->id ?? 4, 'nama_barang' => 'Microphone Wireless', 'jumlah' => 2],
                    ],
                    'status_bkhm' => 'Disetujui',
                    'status_sarpras' => 'Disetujui',
                    'status_akhir' => 'Selesai / Disetujui',
                ]
            );
        }
    }
}