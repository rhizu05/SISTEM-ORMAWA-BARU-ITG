<?php

namespace Tests\Feature;

use App\Models\Notifikasi;
use App\Models\SuratPeringatan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class SuratPeringatanNotifikasiTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Artisan::call('db:seed', ['--class' => 'RolePermissionSeeder', '--force' => true]);
        Artisan::call('db:seed', ['--class' => 'WorkflowSeeder', '--force' => true]);
        Artisan::call('db:seed', ['--class' => 'KonfigurasiSeeder', '--force' => true]);
    }

    private function makeUserWithRole(string $roleName, ?string $name = null): User
    {
        $role = Role::firstOrCreate(['name' => $roleName]);
        $user = User::factory()->create([
            'name' => $name ?? ('User ' . $roleName),
        ]);
        $user->assignRole($role);
        return $user;
    }

    public function test_bkhm_issuing_sp_creates_notification_and_ormawa_can_view_it()
    {
        $bkhm = $this->makeUserWithRole('bkhm', 'BKHM');
        $wr3 = $this->makeUserWithRole('wr3', 'Dr. Ayu Latifah, S.T., M.T.');
        $himaif = $this->makeUserWithRole('ormawa', 'HIMAIF ITG');
        $otherOrmawa = $this->makeUserWithRole('ormawa', 'HIMATI ITG');

        // 1. BKHM membuat draf SP ke HIMAIF
        $this->actingAs($bkhm);
        $spPayload = [
            'target_user_id' => $himaif->id,
            'nomor_surat'    => '001/SP/BKHM/IX/2026',
            'tingkat'        => 'SP-1',
            'perihal'        => 'Keterlambatan Pengumpulan LPJ Kegiatan Informatics Day',
            'alasan_singkat' => 'Batas waktu 14 hari kerja telah terlampaui',
            'deskripsi'      => 'Panitia belum mengunggah dokumen pertanggungjawaban dana termin 1.',
            'sanksi'         => 'Penangguhan permohonan dana kegiatan baru hingga LPJ diunggah.',
            'tanggal_surat'  => '2026-09-26',
            'pejabat_nama'   => 'Dr. Ayu Latifah, S.T., M.T.',
            'pejabat_nidn'   => '0421099301',
            'pejabat_jabatan'=> 'Wakil Rektor III Bidang Kemahasiswaan dan Kerjasama',
        ];

        $response = $this->post(route('bkhm.sp.store'), $spPayload);
        $response->assertRedirect(route('bkhm.arsip.index'));

        // Pastikan SP tersimpan di database dengan status menunggu_validasi
        $this->assertDatabaseHas('surat_peringatans', [
            'nomor_surat' => '001/SP/BKHM/IX/2026',
            'target_user_id' => $himaif->id,
            'status' => 'menunggu_validasi',
        ]);

        $sp = SuratPeringatan::where('nomor_surat', '001/SP/BKHM/IX/2026')->first();

        // Target ormawa BELUM menerima notifikasi sebelum disetujui WR3
        $this->assertDatabaseMissing('notifikasi', [
            'user_id' => $himaif->id,
        ]);

        // WR3 menerima notifikasi pengajuan draf SP dari BKHM
        $this->assertDatabaseHas('notifikasi', [
            'user_id' => $wr3->id,
        ]);

        // HIMAIF belum bisa melihat SP yang masih berstatus menunggu_validasi
        $this->actingAs($himaif);
        $blockedShow = $this->get(route('sp.saya.show', $sp));
        $blockedShow->assertStatus(403);

        // 2. WR3 meninjau dan menyetujui SP
        $this->actingAs($wr3);
        $wr3Index = $this->get(route('wr3.sp.index'));
        $wr3Index->assertStatus(200);
        $wr3Index->assertSee('001/SP/BKHM/IX/2026');

        $approveResponse = $this->post(route('wr3.sp.approve', $sp), [
            'catatan_wr3' => 'Disetujui untuk diterbitkan secara resmi.',
        ]);
        $approveResponse->assertRedirect(route('wr3.sp.index'));

        $sp->refresh();
        $this->assertEquals('disetujui', $sp->status);
        $this->assertEquals($wr3->id, $sp->validated_by);
        $this->assertNotNull($sp->validated_at);

        // 3. Setelah disetujui WR3, notifikasi otomatis terkirim ke HIMAIF
        $this->assertDatabaseHas('notifikasi', [
            'user_id' => $himaif->id,
            'status_baca' => 'belum',
        ]);
        $notif = Notifikasi::where('user_id', $himaif->id)->first();
        $this->assertStringContainsString('Surat Peringatan SP-1', $notif->pesan);
        $this->assertStringContainsString('Wakil Rektor III', $notif->pesan);

        // 4. Login sebagai HIMAIF -> periksa dashboard (banner peringatan aktif)
        $this->actingAs($himaif);
        $dashResponse = $this->get(route('dashboard'));
        $dashResponse->assertStatus(200);
        $dashResponse->assertSee('Surat Peringatan (SP) Resmi');
        $dashResponse->assertSee('001/SP/BKHM/IX/2026');

        // 5. HIMAIF membuka halaman daftar SP Saya
        $indexResponse = $this->get(route('sp.saya.index'));
        $indexResponse->assertStatus(200);
        $indexResponse->assertSee('001/SP/BKHM/IX/2026');
        $indexResponse->assertSee('Keterlambatan Pengumpulan LPJ');

        // 6. HIMAIF membuka rincian dokumen SP yang sudah disetujui
        $showResponse = $this->get(route('sp.saya.show', $sp));
        $showResponse->assertStatus(200);
        $showResponse->assertSee('SURAT PERINGATAN');
        $showResponse->assertSee('Penangguhan permohonan dana kegiatan baru');

        // 7. Ormawa lain (HIMATI) TIDAK boleh bisa melihat SP milik HIMAIF (403 Forbidden)
        $this->actingAs($otherOrmawa);
        $forbiddenResponse = $this->get(route('sp.saya.show', $sp));
        $forbiddenResponse->assertStatus(403);
    }

    public function test_wr3_can_reject_draft_sp_with_notes_and_notify_bkhm()
    {
        $bkhm = $this->makeUserWithRole('bkhm', 'BKHM');
        $wr3 = $this->makeUserWithRole('wr3', 'Dr. Ayu Latifah, S.T., M.T.');
        $himaif = $this->makeUserWithRole('ormawa', 'HIMAIF ITG');

        $this->actingAs($bkhm);
        $spPayload = [
            'target_user_id' => $himaif->id,
            'nomor_surat'    => '003/SP/BKHM/IX/2026',
            'tingkat'        => 'SP-1',
            'perihal'        => 'Uji Coba Penolakan SP',
            'alasan_singkat' => 'Alasan awal draf',
            'deskripsi'      => 'Deskripsi awal yang perlu direvisi.',
            'sanksi'         => 'Sanksi awal.',
            'tanggal_surat'  => '2026-09-26',
            'pejabat_nama'   => 'Dr. Ayu Latifah, S.T., M.T.',
            'pejabat_nidn'   => '0421099301',
            'pejabat_jabatan'=> 'Wakil Rektor III Bidang Kemahasiswaan dan Kerjasama',
        ];

        $this->post(route('bkhm.sp.store'), $spPayload);
        $sp = SuratPeringatan::where('nomor_surat', '003/SP/BKHM/IX/2026')->first();

        // WR3 menolak draf SP
        $this->actingAs($wr3);
        $rejectRes = $this->post(route('wr3.sp.reject', $sp), [
            'catatan_wr3' => 'Mohon revisi rumusan sanksi sesuai dengan pedoman disiplin ormawa ITG.',
        ]);
        $rejectRes->assertRedirect(route('wr3.sp.index'));

        $sp->refresh();
        $this->assertEquals('ditolak', $sp->status);
        $this->assertEquals('Mohon revisi rumusan sanksi sesuai dengan pedoman disiplin ormawa ITG.', $sp->catatan_wr3);

        // BKHM menerima notifikasi penolakan & catatan revisi
        $notifBkhm = Notifikasi::where('user_id', $bkhm->id)->latest()->first();
        $this->assertNotNull($notifBkhm);
        $this->assertStringContainsString('dikembalikan oleh Wakil Rektor III', $notifBkhm->pesan);

        // HIMAIF TIDAK melihat dokumen ini di /sp/saya
        $this->actingAs($himaif);
        $spSayaRes = $this->get(route('sp.saya.index'));
        $spSayaRes->assertDontSee('003/SP/BKHM/IX/2026');
    }

    public function test_bpm_reguler_sp_is_routed_to_bkhm_review()
    {
        $bpm = $this->makeUserWithRole('bpm', 'Ketua BPM ITG');
        $bkhm = $this->makeUserWithRole('bkhm', 'Staf BKHM ITG');
        $himaif = $this->makeUserWithRole('ormawa', 'HIMAIF ITG');

        $this->actingAs($bpm);
        $response = $this->post(route('bpm.sp.store'), [
            'tipe_sasaran'   => 'ormawa',
            'target_user_id' => $himaif->id,
            'nomor_surat'    => '002/SP/BPM/IX/2026',
            'tingkat'        => 'SP-2',
            'perihal'        => 'Ketidaksesuaian Alokasi Dana Anggaran',
            'alasan_singkat' => 'Pengeluaran tanpa konfirmasi anggaran',
            'deskripsi'      => 'Terdapat pengeluaran di luar proposal yang disetujui.',
            'sanksi'         => 'Pengembalian selisih dana ke kas bendahara ormawa.',
            'tanggal_surat'  => '2026-09-26',
        ]);
        $response->assertRedirect(route('bpm.dashboard'));

        $sp = SuratPeringatan::where('nomor_surat', '002/SP/BPM/IX/2026')->first();
        $this->assertEquals('menunggu_bkhm', $sp->status);

        // BKHM diberi tahu untuk meninjau; target belum menerima notifikasi.
        $this->assertDatabaseHas('notifikasi', ['user_id' => $bkhm->id]);
        $this->assertDatabaseMissing('notifikasi', ['user_id' => $himaif->id]);

        // Target belum dapat melihat draf yang belum divalidasi.
        $this->actingAs($himaif);
        $this->get(route('sp.saya.show', $sp))->assertStatus(403);
    }

    public function test_bpm_internal_sp_published_directly_and_notifies_target()
    {
        $bpm = $this->makeUserWithRole('bpm', 'Ketua BPM ITG');
        $anggota = $this->makeUserWithRole('bpm', 'Anggota BPM ITG');

        $this->actingAs($bpm);
        $response = $this->post(route('bpm.sp.store'), [
            'tipe_sasaran'   => 'ormawa',
            'target_user_id' => $anggota->id,
            'nomor_surat'    => '003/SP/BPM/IX/2026',
            'tingkat'        => 'SP-1',
            'perihal'        => 'Pelanggaran Internal BPM',
            'alasan_singkat' => 'Tidak menghadiri rapat pleno',
            'deskripsi'      => 'Tidak menghadiri rapat pleno tanpa keterangan.',
            'sanksi'         => 'Teguran lisan dan tertulis.',
            'tanggal_surat'  => '2026-09-26',
            'anggota_bpm'    => 1,
            'penandatangan'  => 'Presidium BPM ITG',
        ]);
        $response->assertRedirect(route('bpm.dashboard'));

        $sp = SuratPeringatan::where('nomor_surat', '003/SP/BPM/IX/2026')->first();
        $this->assertEquals('disetujui', $sp->status);
        $this->assertTrue($sp->is_internal_bpm);

        $this->assertDatabaseHas('tanda_tangan_digitals', [
            'signable_type' => SuratPeringatan::class,
            'signable_id'   => $sp->id,
            'role'          => 'bpm',
        ]);
        $this->assertDatabaseHas('notifikasi', ['user_id' => $anggota->id]);

        // Target internal dapat mengunduh PDF SP yang langsung terbit.
        $this->actingAs($anggota);
        $pdfResponse = $this->get(route('sp.saya.pdf', $sp));
        $pdfResponse->assertStatus(200);
        $this->assertEquals('application/pdf', $pdfResponse->headers->get('content-type'));
    }

    public function test_bkhm_can_create_sp_for_individual_student_and_generate_official_itg_pdf()
    {
        $bkhm = $this->makeUserWithRole('bkhm', 'BKHM ITG');
        $this->actingAs($bkhm);

        $spPayload = [
            'tipe_sasaran'    => 'mahasiswa',
            'target_mahasiswas' => [
                ['nim' => '2306085', 'nama' => 'Andi Muhamad Ramdani', 'prodi' => 'S1 Teknik Informatika', 'kontak' => 'andi@itg.ac.id'],
                ['nim' => '2306090', 'nama' => 'Budi Santoso', 'prodi' => 'S1 Sistem Informasi', 'kontak' => 'budi@itg.ac.id'],
            ],
            'nomor_surat'     => '501/ITG/E.8/B/VI/2026',
            'tingkat'         => 'SP-1',
            'perihal'         => 'Pelanggaran Ketertiban Umum di Lingkungan Kampus',
            'alasan_singkat'  => 'Melanggar jam malam dan ketertiban fasilitas kampus',
            'deskripsi'       => 'Ditemukan beraktivitas di luar jam operasional tanpa izin resmi BKHM.',
            'sanksi'          => 'Peringatan tertulis pertama dan wajib konseling di BKHM.',
            'tanggal_surat'   => '2026-09-26',
            'pejabat_nama'    => 'Dr. Ayu Latifah, S.T., M.T.',
            'pejabat_nidn'    => '0421099301',
            'pejabat_jabatan' => 'Wakil Rektor III Bidang Kemahasiswaan dan Kerjasama',
        ];

        $response = $this->post(route('bkhm.sp.store'), $spPayload);
        $response->assertRedirect(route('bkhm.arsip.index'));

        // Pastikan tersimpan dengan identitas mahasiswa dan pejabat
        $this->assertDatabaseHas('surat_peringatans', [
            'tipe_sasaran'    => 'mahasiswa',
            'target_nim'      => '2306085',
            'target_nama'     => 'Andi Muhamad Ramdani',
            'target_prodi'    => 'S1 Teknik Informatika',
            'pejabat_nama'    => 'Dr. Ayu Latifah, S.T., M.T.',
            'nomor_surat'     => '501/ITG/E.8/B/VI/2026',
        ]);

        $sp = SuratPeringatan::where('nomor_surat', '501/ITG/E.8/B/VI/2026')->first();

        // Daftar penerima tersimpan lengkap dan tanpa tautan akun
        $this->assertCount(2, $sp->penerima_mahasiswa);
        $this->assertNull($sp->target_user_id);

        // Cek halaman pratinjau BKHM memuat kedua mahasiswa
        $showResponse = $this->get(route('bkhm.sp.show', $sp));
        $showResponse->assertStatus(200);
        $showResponse->assertSee('INSTITUT');
        $showResponse->assertSee('TEKNOLOGI');
        $showResponse->assertSee('GARUT');
        $showResponse->assertSee('Dr. Ayu Latifah, S.T., M.T.');
        $showResponse->assertSee('0421099301');
        $showResponse->assertSee('Andi Muhamad Ramdani');
        $showResponse->assertSee('2306085');
        $showResponse->assertSee('Budi Santoso');
        $showResponse->assertSee('2306090');
        $showResponse->assertSee('S1 Teknik Informatika');

        // Cek unduh PDF resmi
        $pdfResponse = $this->get(route('bkhm.sp.pdf', $sp));
        $pdfResponse->assertStatus(200);
        $this->assertEquals('application/pdf', $pdfResponse->headers->get('content-type'));
    }

    public function test_admin_can_update_pejabat_configuration_and_it_reflects_in_sp_creation()
    {
        $admin = $this->makeUserWithRole('admin', 'Admin Kampus');
        $bkhm = $this->makeUserWithRole('bkhm', 'BKHM');
        $ormawa = $this->makeUserWithRole('ormawa', 'HIMAIF ITG');

        // 1. Admin mengubah identitas pejabat di Pengaturan Sistem
        $this->actingAs($admin);
        $configPayload = [
            'nama_aplikasi' => 'SKIN - Sistem Ormawa ITG',
            'wr3_nama'      => 'Prof. Dr. Ir. Pejabat Baru, M.T.',
            'wr3_nidn'      => '0499999999',
            'wr3_jabatan'   => 'Wakil Rektor III Bidang Kemahasiswaan dan Kerjasama',
            'bkhm_nama'     => 'Encep Jianul Hayat, S.T., M.T.',
            'bkhm_nidn'     => '0401019004',
            'bkhm_jabatan'  => 'Kepala Biro Kemahasiswaan dan Hubungan Masyarakat (BKHM)',
        ];

        $updateConfigResponse = $this->put(route('admin.konfigurasi.update'), $configPayload);
        $updateConfigResponse->assertRedirect();

        $this->assertDatabaseHas('konfigurasi', [
            'nama_konfigurasi'  => 'wr3_nama',
            'nilai_konfigurasi' => 'Prof. Dr. Ir. Pejabat Baru, M.T.',
        ]);

        // 2. BKHM membuka form buat SP -> periksa nilai default memuat nama pejabat baru
        $this->actingAs($bkhm);
        $createResponse = $this->get(route('bkhm.sp.create'));
        $createResponse->assertStatus(200);
        $createResponse->assertSee('Prof. Dr. Ir. Pejabat Baru, M.T.');
        $createResponse->assertSee('0499999999');

        // 3. BKHM menerbitkan SP tanpa mengetik pejabat_nama manual -> otomatis menggunakan nilai dinamis
        $spPayload = [
            'tipe_sasaran'   => 'ormawa',
            'target_user_id' => $ormawa->id,
            'nomor_surat'    => '999/SP/BKHM/IX/2026',
            'tingkat'        => 'SP-1',
            'perihal'        => 'Uji Coba Pejabat Dinamis',
            'alasan_singkat' => 'Pengujian konfigurasi database',
            'deskripsi'      => 'Deskripsi pengujian sistem.',
            'sanksi'         => 'Sanksi pengujian.',
            'tanggal_surat'  => '2026-09-26',
        ];

        $storeResponse = $this->post(route('bkhm.sp.store'), $spPayload);
        $storeResponse->assertRedirect(route('bkhm.arsip.index'));

        $this->assertDatabaseHas('surat_peringatans', [
            'nomor_surat'  => '999/SP/BKHM/IX/2026',
            'pejabat_nama' => 'Prof. Dr. Ir. Pejabat Baru, M.T.',
            'pejabat_nidn' => '0499999999',
        ]);
    }
}
