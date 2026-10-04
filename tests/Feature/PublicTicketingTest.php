<?php

namespace Tests\Feature;

use App\Models\Peminjaman;
use App\Models\Pengajuan;
use App\Models\TiketLayanan;
use App\Models\User;
use App\Models\WorkflowState;
use App\Models\WorkflowTransition;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PublicTicketingTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Artisan::call('db:seed', ['--class' => 'RolePermissionSeeder', '--force' => true]);
        Artisan::call('db:seed', ['--class' => 'WorkflowSeeder', '--force' => true]);
        Storage::fake('local');
    }

    public function test_public_can_access_layanan_portal_and_showcase(): void
    {
        $response = $this->get(route('layanan.index'));
        $response->assertStatus(200);
        $response->assertSee('Portal Layanan Mahasiswa');
        $response->assertSee('images/logo-skin-torch.png');
        $response->assertDontSee('Login Pengurus');
        $response->assertDontSee(route('login'));

        $showcase = $this->get(route('prestasi.showcase'));
        $showcase->assertStatus(200);
        $showcase->assertSee('Showcase Prestasi');
        $showcase->assertSee('images/logo-skin-torch.png');
        $showcase->assertDontSee('Login Pengurus');

        $user = User::factory()->create();
        $authResponse = $this->actingAs($user)->get(route('layanan.index'));
        $authResponse->assertStatus(200);
        $authResponse->assertSee('Dashboard');
        $authResponse->assertDontSee('Login Pengurus');
    }

    public function test_public_can_submit_aspirasi_and_generates_uniform_ticket_code(): void
    {
        $payload = [
            'nim' => '2106001',
            'nama_mahasiswa' => 'Budi Santoso',
            'email' => 'budi@itg.ac.id',
            'no_hp' => '081234567890',
            'prodi' => 'Teknik Informatika',
            'judul' => 'Perbaikan Fasilitas Lab Komputer',
            'isi' => 'Beberapa PC di Lab 3 mengalami kendala koneksi LAN.',
            'lampiran' => UploadedFile::fake()->create('bukti.pdf', 500, 'application/pdf'),
        ];

        $response = $this->post(route('layanan.aspirasi.store'), $payload);

        $tiket = TiketLayanan::where('email', 'budi@itg.ac.id')->first();
        $this->assertNotNull($tiket);
        $this->assertMatchesRegularExpression('/^SKIN-TKT-\d{4}-[A-Z0-9]{6}$/', $tiket->kode_tiket);
        $this->assertEquals('aspirasi', $tiket->kategori);
        $this->assertEquals('pending', $tiket->status);

        $response->assertRedirect(route('layanan.cek-status', [
            'kode' => $tiket->kode_tiket,
        ]));
    }

    public function test_public_can_submit_konseling_confidential_to_bkhm(): void
    {
        $payload = [
            'nim' => '2106002',
            'nama_mahasiswa' => 'Siti Nurhaliza',
            'email' => 'siti@itg.ac.id',
            'no_hp' => '089876543210',
            'prodi' => 'Sistem Informasi',
            'topik_konseling' => 'Kendala Akademik / IPK',
            'metode_konseling' => 'Tatap Muka (Ruang Konseling BKHM)',
            'deskripsi_masalah' => 'Saya merasa cemas dan kesulitan membagi waktu antara kuliah dan kerja paruh waktu.',
        ];

        $response = $this->post(route('layanan.konseling.store'), $payload);

        $tiket = TiketLayanan::where('email', 'siti@itg.ac.id')->first();
        $this->assertNotNull($tiket);
        $this->assertEquals('konseling', $tiket->kategori);
        $this->assertMatchesRegularExpression('/^SKIN-TKT-\d{4}-[A-Z0-9]{6}$/', $tiket->kode_tiket);

        $response->assertRedirect(route('layanan.cek-status', [
            'kode' => $tiket->kode_tiket,
        ]));
    }

    public function test_public_can_submit_prestasi_and_delegasi(): void
    {
        $payloadLapor = [
            'nim' => '2106003',
            'nama_mahasiswa' => 'Ahmad Dahlan',
            'email' => 'ahmad@itg.ac.id',
            'no_hp' => '085211223344',
            'prodi' => 'Teknik Sipil',
            'sub_kategori' => 'lapor_prestasi',
            'nama_kegiatan' => 'Lomba Desain Jembatan Nasional 2026',
            'penyelenggara' => 'Institut Teknologi Bandung',
            'tingkat' => 'Nasional',
            'capaian' => 'Juara 1',
            'lampiran_bukti' => UploadedFile::fake()->create('sertifikat.pdf', 300, 'application/pdf'),
        ];

        $responseLapor = $this->post(route('layanan.prestasi.store'), $payloadLapor);
        $responseLapor->assertRedirect();
        $this->assertDatabaseHas('tiket_layanans', [
            'nim' => '2106003',
            'kategori' => 'prestasi',
            'sub_kategori' => 'lapor_prestasi',
            'capaian' => 'Juara 1',
            'tampil_ke_publik' => false,
        ]);

        $payloadDelegasi = [
            'nim' => '2106004',
            'nama_mahasiswa' => 'Rina Gunawan',
            'email' => 'rina@itg.ac.id',
            'no_hp' => '085299887766',
            'prodi' => 'Teknik Industri',
            'sub_kategori' => 'pengajuan_dana_delegasi',
            'nama_kegiatan' => 'Kompetisi Lean Manufacturing 2026',
            'penyelenggara' => 'UGM Yogyakarta',
            'tingkat' => 'Nasional',
            'estimasi_biaya' => 2500000,
            'lampiran_bukti' => UploadedFile::fake()->create('proposal.pdf', 800, 'application/pdf'),
        ];

        $responseDelegasi = $this->post(route('layanan.prestasi.store'), $payloadDelegasi);
        $responseDelegasi->assertRedirect();
        $this->assertDatabaseHas('tiket_layanans', [
            'nim' => '2106004',
            'sub_kategori' => 'pengajuan_dana_delegasi',
            'estimasi_biaya' => 2500000,
        ]);
    }

    public function test_ticket_tracking_requires_correct_code_and_email(): void
    {
        $tiket = TiketLayanan::create([
            'kode_tiket' => 'SKIN-TKT-2026-9999',
            'kategori' => 'aspirasi',
            'nim' => '2106099',
            'nama_mahasiswa' => 'Bambang',
            'email' => 'bambang@itg.ac.id',
            'judul' => 'Uji Cek Status',
            'isi' => 'Konten pengujian',
            'status' => 'pending',
        ]);

        // Gagal jika email salah
        $resWrong = $this->get(route('layanan.tracking', [
            'kode' => 'SKIN-TKT-2026-9999',
            'email' => 'wrong@domain.com',
        ]));
        $resWrong->assertStatus(200);
        $resWrong->assertSee('tidak ditemukan');

        // Berhasil jika kode + email cocok
        $resCorrect = $this->get(route('layanan.tracking', [
            'kode' => 'SKIN-TKT-2026-9999',
            'email' => 'bambang@itg.ac.id',
        ]));
        $resCorrect->assertStatus(200);
        $resCorrect->assertSee('SKIN-TKT-2026-9999');
        $resCorrect->assertSee('Uji Cek Status');
    }

    public function test_bkhm_can_manage_konseling_and_update_schedule(): void
    {
        $bkhm = User::factory()->create();
        $bkhm->assignRole('bkhm');

        $tiket = TiketLayanan::create([
            'kode_tiket' => 'SKIN-TKT-2026-7777',
            'kategori' => 'konseling',
            'nim' => '2106077',
            'nama_mahasiswa' => 'Rahasia Mahasiswa',
            'email' => 'rahasia@itg.ac.id',
            'topik_konseling' => 'Stres Perkuliahan',
            'metode_konseling' => 'Tatap Muka',
            'deskripsi_masalah' => 'Perlu sesi konseling.',
            'status' => 'pending',
        ]);

        // BKHM buka index & detail
        $resIndex = $this->actingAs($bkhm)->get(route('bkhm.konseling.index'));
        $resIndex->assertStatus(200);
        $resIndex->assertSee('SKIN-TKT-2026-7777');

        $resShow = $this->actingAs($bkhm)->get(route('bkhm.konseling.show', $tiket));
        $resShow->assertStatus(200);
        $resShow->assertSee('Rahasia Mahasiswa');

        // BKHM update jadwal temu
        $resUpdate = $this->actingAs($bkhm)->post(route('bkhm.konseling.update', $tiket), [
            'status' => 'jadwal_ditentukan',
            'jadwal_temu' => now()->addDays(2)->format('Y-m-d H:i:s'),
            'lokasi_atau_link' => 'Ruang BKHM Gedung Rektorat Lt. 2',
            'tanggapan_bkhm' => 'Silakan hadir menemui konselor Ibu Nur.',
        ]);
        $resUpdate->assertRedirect();

        $freshTiket = $tiket->fresh();
        $this->assertEquals('jadwal_ditentukan', $freshTiket->status);
        $this->assertNotNull($freshTiket->jadwal_temu);
        $this->assertEquals('Ruang BKHM Gedung Rektorat Lt. 2', $freshTiket->lokasi_atau_link);
    }

    public function test_bpm_can_escalate_aspirasi_to_bkhm_and_bkhm_can_update(): void
    {
        $bpm = User::factory()->create();
        $bpm->assignRole('bpm');

        $bkhm = User::factory()->create();
        $bkhm->assignRole('bkhm');

        $tiket = TiketLayanan::create([
            'kode_tiket' => 'SKIN-TKT-2026-6666',
            'kategori' => 'aspirasi',
            'nim' => '2106066',
            'nama_mahasiswa' => 'Dewi Sartika',
            'email' => 'dewi@itg.ac.id',
            'judul' => 'Kebijakan Parkir Kampus',
            'isi' => 'Kapasitas parkir motor kurang memadai saat jam kuliah siang.',
            'status' => 'pending',
        ]);

        // BPM eskalasikan ke BKHM
        $resTeruskan = $this->actingAs($bpm)->post(route('bpm.aspirasi.teruskan', $tiket), [
            'catatan_bpm' => 'Rekomendasi BPM: mohon berkoordinasi dengan Sarpras untuk perluasan area parkir.',
        ]);
        $resTeruskan->assertRedirect();

        $freshTiket = $tiket->fresh();
        $this->assertEquals('diteruskan_ke_bkhm', $freshTiket->status);
        $this->assertNotNull($freshTiket->diteruskan_ke_bkhm_at);

        // BKHM melihat di antrean eskalasi
        $resBkhmIndex = $this->actingAs($bkhm)->get(route('bkhm.tiket-aspirasi.index'));
        $resBkhmIndex->assertStatus(200);
        $resBkhmIndex->assertSee('SKIN-TKT-2026-6666');

        // BKHM perbarui tindak lanjut
        $resBkhmUpdate = $this->actingAs($bkhm)->post(route('bkhm.tiket-aspirasi.update', $tiket), [
            'status' => 'ditindaklanjuti',
            'catatan_bkhm' => 'Telah dikoordinasikan dengan Sarpras untuk pembukaan kantong parkir darurat di lapangan barat.',
        ]);
        $resBkhmUpdate->assertRedirect();

        $this->assertEquals('ditindaklanjuti', $tiket->fresh()->status);
    }

    public function test_bkhm_can_approve_prestasi_and_toggle_public_showcase(): void
    {
        $bkhm = User::factory()->create();
        $bkhm->assignRole('bkhm');

        $tiket = TiketLayanan::create([
            'kode_tiket' => 'SKIN-TKT-2026-5555',
            'kategori' => 'prestasi',
            'sub_kategori' => 'lapor_prestasi',
            'nim' => '2106055',
            'nama_mahasiswa' => 'Juara Kampus',
            'email' => 'juara@itg.ac.id',
            'nama_kegiatan' => 'Olimpiade Pemrograman Nasional 2026',
            'penyelenggara' => 'Kemendikbud',
            'tingkat' => 'Nasional',
            'capaian' => 'Medali Emas',
            'status' => 'pending',
            'tampil_ke_publik' => false,
        ]);

        $resUpdate = $this->actingAs($bkhm)->post(route('bkhm.tiket-prestasi.update', $tiket), [
            'status' => 'disetujui',
            'catatan_bkhm' => 'Selamat atas capaian membanggakan!',
            'tampil_ke_publik' => true,
        ]);
        $resUpdate->assertRedirect();

        $freshTiket = $tiket->fresh();
        $this->assertEquals('disetujui', $freshTiket->status);
        $this->assertTrue($freshTiket->tampil_ke_publik);

        // Harus muncul di Showcase Publik
        $resShowcase = $this->get(route('prestasi.showcase'));
        $resShowcase->assertStatus(200);
        $resShowcase->assertSee('Olimpiade Pemrograman Nasional 2026');
        $resShowcase->assertSee('Medali Emas');
    }

    public function test_public_can_submit_prestasi_with_bimbingan_fields_and_view_in_showcase(): void
    {
        Storage::fake('public');
        Storage::fake('local');

        $bkhm = User::factory()->create();
        $bkhm->assignRole('bkhm');

        $payload = [
            'nim'               => '2306085',
            'nama_mahasiswa'    => 'Andi Muhamad Ramdani',
            'email'             => 'andi@itg.ac.id',
            'no_hp'             => '08123456789',
            'prodi'             => 'Teknik Informatika',
            'sub_kategori'      => 'lapor_prestasi',
            'nama_kegiatan'     => 'Gemastik XVII 2026 Divisi UX Design',
            'penyelenggara'     => 'Pusat Prestasi Nasional Kemdikbud',
            'url_penyelenggara' => 'https://pusatprestasinasional.kemdikbud.go.id/gemastik2026',
            'tingkat'           => 'Nasional',
            'capaian'           => 'Juara 1 (Medali Emas)',
            'tanggal_mulai'     => '2026-08-10',
            'tanggal_selesai'   => '2026-08-15',
            'lampiran_bukti'    => UploadedFile::fake()->create('sertifikat_juara.pdf', 500, 'application/pdf'),
            'foto_penyerahan'   => UploadedFile::fake()->image('penyerahan_medali.jpg'),
        ];

        $resStore = $this->post(route('layanan.prestasi.store'), $payload);
        $resStore->assertRedirect();

        // 1. Verifikasi data tersimpan di tabel tiket_layanans
        $this->assertDatabaseHas('tiket_layanans', [
            'nim'               => '2306085',
            'sub_kategori'      => 'lapor_prestasi',
            'url_penyelenggara' => 'https://pusatprestasinasional.kemdikbud.go.id/gemastik2026',
        ]);

        $tiket = TiketLayanan::where('nim', '2306085')->latest()->first();
        $this->assertEquals('2026-08-10', $tiket->tanggal_mulai->format('Y-m-d'));
        $this->assertEquals('2026-08-15', $tiket->tanggal_selesai->format('Y-m-d'));
        $this->assertNotNull($tiket->foto_penyerahan);
        Storage::disk('public')->assertExists($tiket->foto_penyerahan);
        Storage::disk('local')->assertExists($tiket->lampiran_bukti);

        // 2. BKHM membuka index verifikasi tiket prestasi
        $resBkhm = $this->actingAs($bkhm)->get(route('bkhm.tiket-prestasi.index'));
        $resBkhm->assertStatus(200);
        $resBkhm->assertSee('Gemastik XVII 2026 Divisi UX Design');
        $resBkhm->assertSee('https://pusatprestasinasional.kemdikbud.go.id/gemastik2026');
        $resBkhm->assertSee('Scan Sertifikat');
        $resBkhm->assertSee('Foto Penyerahan');

        // 3. BKHM menyetujui dan mempublikasikan ke Showcase
        $resApprove = $this->actingAs($bkhm)->post(route('bkhm.tiket-prestasi.update', $tiket), [
            'status'            => 'disetujui',
            'catatan_bkhm'      => 'Prestasi luar biasa! Selamat kepada Andi.',
            'tampil_ke_publik'  => true,
        ]);
        $resApprove->assertRedirect();

        // 4. Publik membuka Showcase Prestasi
        $resShowcase = $this->get(route('prestasi.showcase'));
        $resShowcase->assertStatus(200);
        $resShowcase->assertSee('Gemastik XVII 2026 Divisi UX Design');
        $resShowcase->assertSee('Juara 1 (Medali Emas)');
        $resShowcase->assertSee('Andi Muhamad Ramdani');
        $resShowcase->assertSee('https://pusatprestasinasional.kemdikbud.go.id/gemastik2026');
        $resShowcase->assertSee($tiket->rentang_tanggal);
        $resShowcase->assertSee($tiket->foto_penyerahan);
    }

    public function test_revisi_proposal_resets_to_initial_stage(): void
    {
        $ormawa = User::factory()->create();
        $ormawa->assignRole('ormawa');

        $draftState = WorkflowState::where('name', WorkflowState::DRAFT)->firstOrFail();
        $submittedState = WorkflowState::where('name', WorkflowState::SUBMITTED)->firstOrFail();
        $bpmApproved = WorkflowState::where('name', WorkflowState::BPM_APPROVED)->firstOrFail();

        $pengajuan = Pengajuan::create([
            'user_id' => $ormawa->id,
            'nama_kegiatan' => 'Proposal Telah Direvisi',
            'dana_diajukan' => 2000000,
            'tanggal_pengajuan' => now()->toDateString(),
            'workflow_state_id' => $draftState->id,
            'rejected_from_state_id' => $bpmApproved->id,
        ]);

        $response = $this->actingAs($ormawa)->post(route('pengajuan.ajukan', $pengajuan));
        $response->assertRedirect();

        $fresh = $pengajuan->fresh();
        $this->assertEquals($submittedState->id, $fresh->workflow_state_id);
        $this->assertNull($fresh->rejected_from_state_id);
    }

    public function test_peminjaman_with_surat_prodi_bypasses_bkhm_to_sarpras(): void
    {
        $ormawa = User::factory()->create();
        $ormawa->assignRole('ormawa');

        $ruangan = \App\Models\MasterRuangan::create([
            'nama_ruangan' => 'Aula Utama ITG',
            'status_aktif' => true,
        ]);

        $payload = [
            'ruangan_id' => $ruangan->id,
            'nama_kegiatan' => 'Seminar Teknologi Informasi',
            'tgl_mulai' => now()->addDays(5)->format('Y-m-d'),
            'tgl_selesai' => now()->addDays(5)->format('Y-m-d'),
            'jam_mulai' => '08:00',
            'jam_selesai' => '16:00',
            'file_persetujuan_prodi' => UploadedFile::fake()->create('surat_prodi.pdf', 300, 'application/pdf'),
        ];

        $response = $this->actingAs($ormawa)->post(route('peminjaman.tempat.store'), $payload);
        $response->assertRedirect(route('peminjaman.tempat.index'));

        $peminjaman = \App\Models\PeminjamanTempat::where('user_id', $ormawa->id)->latest()->first();
        $this->assertNotNull($peminjaman);
        $this->assertEquals('disetujui', $peminjaman->status_bkhm);
        $this->assertEquals('pending', $peminjaman->status_sarpras);
        $this->assertEquals('Proses Sarpras', $peminjaman->status_akhir);
    }

    public function test_bendahara_and_bkhm_export_excel_and_pdf(): void
    {
        $bendahara = User::factory()->create();
        $bendahara->assignRole('bendahara');

        $bkhm = User::factory()->create();
        $bkhm->assignRole('bkhm');

        // Bendahara Excel & PDF
        $resBendaharaExcel = $this->actingAs($bendahara)->get(route('bendahara.export.excel'));
        $resBendaharaExcel->assertStatus(200);
        $this->assertTrue(str_contains($resBendaharaExcel->headers->get('content-type'), 'spreadsheetml'));

        $resBendaharaPdf = $this->actingAs($bendahara)->get(route('bendahara.export.pdf'));
        $resBendaharaPdf->assertStatus(200);
        $this->assertTrue(str_contains($resBendaharaPdf->headers->get('content-type'), 'pdf'));

        // BKHM Excel & PDF
        $resBkhmExcel = $this->actingAs($bkhm)->get(route('bkhm.export.excel'));
        $resBkhmExcel->assertStatus(200);

        $resBkhmPdf = $this->actingAs($bkhm)->get(route('bkhm.export.pdf'));
        $resBkhmPdf->assertStatus(200);
    }

    public function test_submitting_ticket_requires_prodi_and_no_hp(): void
    {
        // 1. Test Aspirasi missing prodi & no_hp
        $resAspirasi = $this->post(route('layanan.aspirasi.store'), [
            'nim' => '2106099',
            'nama_mahasiswa' => 'Test User',
            'email' => 'test@itg.ac.id',
            'judul' => 'Judul Test',
            'isi' => 'Konten Test',
        ]);
        $resAspirasi->assertSessionHasErrors(['prodi', 'no_hp']);

        // 2. Test Konseling missing prodi & no_hp
        $resKonseling = $this->post(route('layanan.konseling.store'), [
            'nim' => '2106099',
            'nama_mahasiswa' => 'Test User',
            'email' => 'test@itg.ac.id',
            'topik_konseling' => 'Kendala Akademik / IPK',
            'metode_konseling' => 'Tatap Muka (Ruang Konseling BKHM)',
            'deskripsi_masalah' => 'Deskripsi masalah',
        ]);
        $resKonseling->assertSessionHasErrors(['prodi', 'no_hp']);

        // 3. Test Prestasi missing prodi & no_hp
        $resPrestasi = $this->post(route('layanan.prestasi.store'), [
            'nim' => '2106099',
            'nama_mahasiswa' => 'Test User',
            'email' => 'test@itg.ac.id',
            'sub_kategori' => 'lapor_prestasi',
            'nama_kegiatan' => 'Lomba Test',
            'penyelenggara' => 'Institut Test',
            'tingkat' => 'Nasional',
        ]);
        $resPrestasi->assertSessionHasErrors(['prodi', 'no_hp']);
    }
}
