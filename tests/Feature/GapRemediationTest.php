<?php

namespace Tests\Feature;

use App\Mail\NotifikasiMail;
use App\Models\Dana;
use App\Models\MasterBarang;
use App\Models\MasterRuangan;
use App\Models\PeminjamanBarang;
use App\Models\PeminjamanTempat;
use App\Models\Pengajuan;
use App\Models\Pengumuman;
use App\Models\ProgramKerja;
use App\Models\User;
use App\Models\WorkflowState;
use App\Models\WorkflowTransition;
use App\Services\NotifikasiService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

/**
 * Regresi untuk perbaikan gap PRD: BR-04, BR-10, BR-11, SEC-01 (lampiran), FR-025.
 */
class GapRemediationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Artisan::call('db:seed', ['--class' => 'RolePermissionSeeder', '--force' => true]);
        Artisan::call('db:seed', ['--class' => 'WorkflowSeeder', '--force' => true]);
    }

    private function pengaju(string $role = 'ormawa', float $saldo = 5000000): User
    {
        $user = User::factory()->create(['saldo' => $saldo, 'saldo_awal' => $saldo]);
        $user->assignRole($role);

        return $user;
    }

    private function pdf(string $name = 'dokumen.pdf'): UploadedFile
    {
        return UploadedFile::fake()->create($name, 100, 'application/pdf');
    }

    public function test_br04_pengajuan_melebihi_sisa_saldo_ditolak(): void
    {
        $user = $this->pengaju('ormawa', 1000000);

        $response = $this->actingAs($user)->post(route('pengajuan.store'), [
            'nama_kegiatan' => 'Melebihi Saldo',
            'dana_diajukan' => 2000000,
            'tanggal_pengajuan' => now()->toDateString(),
            'file_proposal' => $this->pdf(),
        ]);

        $response->assertSessionHasErrors('dana_diajukan');
        $this->assertDatabaseCount('pengajuan', 0);
    }

    public function test_br04_pengajuan_dalam_batas_saldo_diterima(): void
    {
        $user = $this->pengaju('ormawa', 5000000);

        $response = $this->actingAs($user)->post(route('pengajuan.store'), [
            'nama_kegiatan' => 'Dalam Saldo',
            'dana_diajukan' => 1000000,
            'tanggal_pengajuan' => now()->toDateString(),
            'file_proposal' => $this->pdf(),
        ]);

        $response->assertRedirect(route('pengajuan.index'));
        $this->assertDatabaseCount('pengajuan', 1);
    }

    public function test_br10_barang_tidak_boleh_dibawa_keluar_ditolak(): void
    {
        $user = $this->pengaju();
        $barang = MasterBarang::create([
            'nama_barang' => 'Server Rack',
            'stok_tersedia' => 2,
            'status_aktif' => true,
            'boleh_dibawa_keluar' => false,
        ]);

        $response = $this->actingAs($user)->post(route('peminjaman.barang.store'), [
            'nama_kegiatan' => 'Uji BR-10',
            'tgl_mulai' => now()->addDays(3)->toDateString(),
            'tgl_selesai' => now()->addDays(3)->toDateString(),
            'barang_id' => [$barang->id],
            'qty' => [1],
        ]);

        $response->assertSessionHas('error');
        $this->assertDatabaseCount('peminjaman_barang', 0);
    }

    public function test_br10_barang_boleh_dibawa_keluar_diterima(): void
    {
        $user = $this->pengaju();
        $barang = MasterBarang::create([
            'nama_barang' => 'Proyektor',
            'stok_tersedia' => 2,
            'status_aktif' => true,
            'boleh_dibawa_keluar' => true,
        ]);

        $response = $this->actingAs($user)->post(route('peminjaman.barang.store'), [
            'nama_kegiatan' => 'Uji BR-10 lolos',
            'tgl_mulai' => now()->addDays(3)->toDateString(),
            'tgl_selesai' => now()->addDays(3)->toDateString(),
            'barang_id' => [$barang->id],
            'qty' => [1],
        ]);

        $response->assertRedirect(route('peminjaman.barang.index'));
        $this->assertDatabaseCount('peminjaman_barang', 1);
    }

    public function test_fr019_validasi_qty_barang_melebihi_stok_tersedia_ditolak(): void
    {
        $user = $this->pengaju();
        $barang = MasterBarang::create([
            'nama_barang' => 'Sound System Portable',
            'stok_tersedia' => 3,
            'status_aktif' => true,
            'boleh_dibawa_keluar' => true,
        ]);

        // Ajukan pinjam 10 padahal stok hanya 3
        $response = $this->actingAs($user)->post(route('peminjaman.barang.store'), [
            'nama_kegiatan' => 'Konser Musik',
            'tgl_mulai' => now()->addDays(2)->toDateString(),
            'tgl_selesai' => now()->addDays(2)->toDateString(),
            'barang_id' => [$barang->id],
            'qty' => [10],
        ]);

        $response->assertSessionHas('error', 'Stok untuk barang Sound System Portable tidak mencukupi.');
        $this->assertDatabaseCount('peminjaman_barang', 0);
    }

    public function test_fr019_validasi_qty_nol_atau_kosong_ditolak(): void
    {
        $user = $this->pengaju();
        $barang = MasterBarang::create([
            'nama_barang' => 'Tripod Kamera',
            'stok_tersedia' => 5,
            'status_aktif' => true,
            'boleh_dibawa_keluar' => true,
        ]);

        // Ajukan pinjam qty = 0
        $response = $this->actingAs($user)->post(route('peminjaman.barang.store'), [
            'nama_kegiatan' => 'Dokumentasi',
            'tgl_mulai' => now()->addDays(1)->toDateString(),
            'tgl_selesai' => now()->addDays(1)->toDateString(),
            'barang_id' => [$barang->id],
            'qty' => [0],
        ]);

        $response->assertSessionHas('error', 'Harap pilih minimal 1 barang dengan quantity > 0.');
        $this->assertDatabaseCount('peminjaman_barang', 0);
    }

    public function test_fr019_pengembalian_barang_memulihkan_stok_tersedia(): void
    {
        $sarpras = $this->pengaju('sarpras', 0);
        $user = $this->pengaju('ormawa', 0);

        $barang = MasterBarang::create([
            'nama_barang' => 'Microphone Wireless',
            'stok_tersedia' => 5, // Stok saat ini (setelah dipinjam 2 berkurang menjadi 5)
            'status_aktif' => true,
            'boleh_dibawa_keluar' => true,
        ]);

        $peminjaman = PeminjamanBarang::create([
            'user_id' => $user->id,
            'nama_kegiatan' => 'Seminar Nasional',
            'tgl_mulai' => now()->toDateString(),
            'tgl_selesai' => now()->addDays(2)->toDateString(),
            'kebutuhan_barang' => [
                ['id_barang' => $barang->id, 'qty' => 2]
            ],
            'status_bkhm' => 'disetujui',
            'status_sarpras' => 'disetujui',
            'status_akhir' => 'Sedang Digunakan',
        ]);

        $response = $this->actingAs($sarpras)->post(route('peminjaman.barang.kembali', $peminjaman));

        $response->assertRedirect();
        $this->assertEquals('Dikembalikan', $peminjaman->fresh()->status_akhir);
        // Stok harus dipulihkan dari 5 menjadi 7 (+2)
        $this->assertEquals(7, $barang->fresh()->stok_tersedia);
    }

    public function test_fr019_pengembalian_barang_bukan_status_sedang_digunakan_ditolak(): void
    {
        $sarpras = $this->pengaju('sarpras', 0);
        $user = $this->pengaju('ormawa', 0);

        $barang = MasterBarang::create([
            'nama_barang' => 'Kabel HDMI',
            'stok_tersedia' => 10,
            'status_aktif' => true,
            'boleh_dibawa_keluar' => true,
        ]);

        $peminjaman = PeminjamanBarang::create([
            'user_id' => $user->id,
            'nama_kegiatan' => 'Rapat Koordinasi',
            'tgl_mulai' => now()->toDateString(),
            'tgl_selesai' => now()->addDays(1)->toDateString(),
            'kebutuhan_barang' => [
                ['id_barang' => $barang->id, 'qty' => 2]
            ],
            'status_bkhm' => 'pending',
            'status_sarpras' => 'pending',
            'status_akhir' => 'Menunggu Verifikasi',
        ]);

        $response = $this->actingAs($sarpras)->post(route('peminjaman.barang.kembali', $peminjaman));

        $response->assertSessionHas('error');
        $this->assertEquals('Menunggu Verifikasi', $peminjaman->fresh()->status_akhir);
        // Stok tidak boleh bertambah
        $this->assertEquals(10, $barang->fresh()->stok_tersedia);
    }

    private function pengajuanSiapTerminDua(): Pengajuan
    {
        $user = $this->pengaju();
        $state = WorkflowState::where('name', 'to_treasurer')->firstOrFail();

        $pengajuan = Pengajuan::create([
            'user_id' => $user->id,
            'nama_kegiatan' => 'Kegiatan Multi Termin',
            'dana_diajukan' => 2000000,
            'tanggal_pengajuan' => now()->toDateString(),
            'workflow_state_id' => $state->id,
            'file_proposal' => 'proposals/x.pdf',
            'file_lpj' => 'lpj/x.pdf',
        ]);

        Dana::create([
            'pengajuan_id' => $pengajuan->id,
            'termin_ke' => 1,
            'nominal_cair' => 1000000,
            'tanggal_cair' => now()->toDateString(),
        ]);

        return $pengajuan;
    }

    public function test_br11_termin_berikutnya_butuh_evaluasi_termin_sebelumnya(): void
    {
        $pengajuan = $this->pengajuanSiapTerminDua();
        $bendahara = $this->pengaju('bendahara', 0);

        $response = $this->actingAs($bendahara)->post(route('bendahara.proses', $pengajuan), [
            'nominal_cair' => 1000000,
            'tanggal_cair' => now()->toDateString(),
        ]);

        $response->assertSessionHas('error');
        $this->assertDatabaseMissing('dana', ['pengajuan_id' => $pengajuan->id, 'termin_ke' => 2]);
    }

    public function test_br11_termin_berikutnya_ditolak_jika_lpj_termin_1_belum_ada(): void
    {
        $user = $this->pengaju();
        $state = WorkflowState::where('name', 'to_treasurer')->firstOrFail();

        // Pengajuan termin 1 cair, tetapi belum ada file_lpj
        $pengajuan = Pengajuan::create([
            'user_id' => $user->id,
            'nama_kegiatan' => 'Kegiatan Tanpa LPJ',
            'dana_diajukan' => 2000000,
            'tanggal_pengajuan' => now()->toDateString(),
            'workflow_state_id' => $state->id,
            'file_proposal' => 'proposals/x.pdf',
            'file_lpj' => null,
            'evaluasi_termin_ok' => true, // evaluasi true tapi lpj belum ada
        ]);

        Dana::create([
            'pengajuan_id' => $pengajuan->id,
            'termin_ke' => 1,
            'nominal_cair' => 1000000,
            'tanggal_cair' => now()->toDateString(),
        ]);

        $bendahara = $this->pengaju('bendahara', 0);

        $response = $this->actingAs($bendahara)->post(route('bendahara.proses', $pengajuan), [
            'nominal_cair' => 500000,
            'tanggal_cair' => now()->toDateString(),
        ]);

        $response->assertSessionHas('error');
        $this->assertDatabaseMissing('dana', ['pengajuan_id' => $pengajuan->id, 'termin_ke' => 2]);
    }

    public function test_br11_termin_berikutnya_lolos_setelah_evaluasi_ditandai(): void
    {
        $pengajuan = $this->pengajuanSiapTerminDuaDenganEvaluasi();
        $bendahara = $this->pengaju('bendahara', 0);

        $response = $this->actingAs($bendahara)->post(route('bendahara.proses', $pengajuan), [
            'nominal_cair' => 500000,
            'tanggal_cair' => now()->toDateString(),
        ]);

        $response->assertRedirect(route('verifikasi.index'));
        $this->assertDatabaseHas('dana', ['pengajuan_id' => $pengajuan->id, 'termin_ke' => 2]);

        // Pastikan flag evaluasi direset kembali ke false untuk termin berikutnya
        $this->assertFalse((bool) $pengajuan->fresh()->evaluasi_termin_ok);
    }

    private function pengajuanSiapTerminDuaDenganEvaluasi(): Pengajuan
    {
        $pengajuan = $this->pengajuanSiapTerminDua();
        $pengajuan->update(['evaluasi_termin_ok' => true]);

        return $pengajuan;
    }

    public function test_fr013_pencairan_ditolak_jika_pengajuan_belum_mencapai_tahap_bendahara(): void
    {
        $user = $this->pengaju();
        // State masih di verifikasi awal (bem_approved), belum disetujui sampai to_treasurer
        $state = WorkflowState::where('name', 'bem_approved')->firstOrFail();

        $pengajuan = Pengajuan::create([
            'user_id' => $user->id,
            'nama_kegiatan' => 'Kegiatan Belum Siap Cair',
            'dana_diajukan' => 1500000,
            'tanggal_pengajuan' => now()->toDateString(),
            'workflow_state_id' => $state->id,
            'file_proposal' => 'proposals/x.pdf',
        ]);

        $bendahara = $this->pengaju('bendahara', 0);

        $response = $this->actingAs($bendahara)->post(route('bendahara.proses', $pengajuan), [
            'nominal_cair' => 1500000,
            'tanggal_cair' => now()->toDateString(),
        ]);

        $response->assertSessionHas('error', 'Pengajuan ini belum disetujui untuk dicairkan.');
        $this->assertDatabaseCount('dana', 0);
        $this->assertEquals(5000000, (float) $user->fresh()->saldo);
    }

    public function test_fr013_pencairan_gagal_jika_nominal_tidak_valid(): void
    {
        $user = $this->pengaju();
        $state = WorkflowState::where('name', 'to_treasurer')->firstOrFail();

        $pengajuan = Pengajuan::create([
            'user_id' => $user->id,
            'nama_kegiatan' => 'Kegiatan Nominal Negatif',
            'dana_diajukan' => 1000000,
            'tanggal_pengajuan' => now()->toDateString(),
            'workflow_state_id' => $state->id,
            'file_proposal' => 'proposals/x.pdf',
        ]);

        $bendahara = $this->pengaju('bendahara', 0);

        // Nominal cair negatif
        $response = $this->actingAs($bendahara)->post(route('bendahara.proses', $pengajuan), [
            'nominal_cair' => -50000,
            'tanggal_cair' => now()->toDateString(),
        ]);

        $response->assertSessionHasErrors('nominal_cair');
        $this->assertDatabaseCount('dana', 0);
    }

    public function test_security_lampiran_pengumuman_disimpan_privat(): void
    {
        Storage::fake('local');
        $bem = User::factory()->create();
        $bem->assignRole('bem');

        $this->actingAs($bem)->post(route('informasi.pengumuman.store'), [
            'judul' => 'Pengumuman Uji',
            'isi' => 'Isi pengumuman',
            'file_lampiran' => $this->pdf('lampiran.pdf'),
        ])->assertRedirect();

        $pengumuman = Pengumuman::firstOrFail();
        $this->assertNotNull($pengumuman->file_lampiran);
        Storage::disk('local')->assertExists($pengumuman->file_lampiran);
        Storage::disk('public')->assertMissing($pengumuman->file_lampiran);
    }

    public function test_fr025_notifikasi_mengirim_email_via_mailable(): void
    {
        Mail::fake();
        $user = User::factory()->create(['email' => 'mahasiswa@itg.ac.id']);

        NotifikasiService::kirim($user->id, 'Pesan uji notifikasi');

        Mail::assertSent(NotifikasiMail::class, function ($mail) {
            return $mail->hasTo('mahasiswa@itg.ac.id')
                && $mail->hasSubject('Notifikasi SKIN - Sistem Informasi Kemahasiswaan ITG')
                && str_contains($mail->pesan, 'Pesan uji notifikasi');
        });
        $this->assertDatabaseHas('notifikasi', ['user_id' => $user->id, 'status_baca' => 'belum']);
    }

    public function test_fr025_mailable_render_konten_dan_footer_institusi(): void
    {
        $mailable = new NotifikasiMail("Proposal Anda telah disetujui.\nSilakan koordinasi.");
        $rendered = $mailable->render();

        $this->assertStringContainsString('Proposal Anda telah disetujui.<br />', $rendered);
        $this->assertStringContainsString('Institut Teknologi Garut', $rendered);
        $this->assertStringContainsString('Mohon tidak membalas email ini', $rendered);
    }

    public function test_fr025_notifikasi_kirim_ke_role_mengirim_email_ke_semua_anggota_role(): void
    {
        Mail::fake();
        $bpm1 = $this->pengaju('bpm', 0);
        $bpm2 = $this->pengaju('bpm', 0);

        NotifikasiService::kirimKeRole('bpm', 'Aspirasi baru membutuhkan verifikasi');

        Mail::assertSent(NotifikasiMail::class, 2);
        Mail::assertSent(NotifikasiMail::class, fn ($mail) => $mail->hasTo($bpm1->email));
        Mail::assertSent(NotifikasiMail::class, fn ($mail) => $mail->hasTo($bpm2->email));
    }

    public function test_fr025_kegagalan_email_tidak_menggagalkan_in_app_notification(): void
    {
        $user = User::factory()->create(['email' => 'error@itg.ac.id']);

        // Simulasikan Mail::to melempar exception
        Mail::shouldReceive('to')->andThrow(new \Exception('SMTP connection timeout'));

        // Harus tidak melempar exception karena ditangani secara graceful di try-catch NotifikasiService
        NotifikasiService::kirim($user->id, 'Pesan saat email server down');

        $this->assertDatabaseHas('notifikasi', [
            'user_id' => $user->id,
            'pesan' => 'Pesan saat email server down',
            'status_baca' => 'belum',
        ]);
    }

    public function test_fr012_revisi_lpj_oleh_bkhm_mengembalikan_ke_funds_disbursed_dan_catatan_tersimpan(): void
    {
        $user = $this->pengaju('ormawa', 0);
        $bkhm = $this->pengaju('bkhm', 0);
        $stateLpjSubmitted = WorkflowState::where('name', 'lpj_submitted')->firstOrFail();
        $transitionRevisi = WorkflowTransition::where('from_state_id', $stateLpjSubmitted->id)
            ->where('action_label', 'Revisi LPJ')
            ->where('required_role', 'bkhm')
            ->firstOrFail();

        $pengajuan = Pengajuan::create([
            'user_id' => $user->id,
            'nama_kegiatan' => 'Workshop UI/UX LPJ Revisi BKHM',
            'dana_diajukan' => 1500000,
            'tanggal_pengajuan' => now()->toDateString(),
            'workflow_state_id' => $stateLpjSubmitted->id,
            'file_proposal' => 'proposals/test.pdf',
            'file_lpj' => 'lpj/test.pdf',
        ]);

        // Coba revisi tanpa catatan -> harus gagal / validasi error
        $resNoNote = $this->actingAs($bkhm)->post(route('verifikasi.process', $pengajuan), [
            'transition_id' => $transitionRevisi->id,
            'catatan' => '',
        ]);
        $resNoNote->assertSessionHas('error');
        $this->assertEquals('lpj_submitted', $pengajuan->fresh()->state->name);

        // Revisi dengan catatan
        $resWithNote = $this->actingAs($bkhm)->post(route('verifikasi.process', $pengajuan), [
            'transition_id' => $transitionRevisi->id,
            'catatan' => 'Kwitansi konsumsi belum distempel resmi.',
        ]);

        $resWithNote->assertRedirect(route('verifikasi.index'));
        $this->assertEquals('funds_disbursed', $pengajuan->fresh()->state->name);
        $this->assertDatabaseHas('histori_status', [
            'pengajuan_id' => $pengajuan->id,
            'workflow_state_id' => $transitionRevisi->to_state_id,
            'catatan_kendala' => 'Kwitansi konsumsi belum distempel resmi.',
        ]);
    }

    public function test_fr012_revisi_lpj_oleh_wr3_mengembalikan_ke_funds_disbursed(): void
    {
        $user = $this->pengaju('ormawa', 0);
        $wr3 = $this->pengaju('wr3', 0);
        $stateWr3Review = WorkflowState::where('name', 'lpj_wr3_review')->firstOrFail();
        $transitionRevisi = WorkflowTransition::where('from_state_id', $stateWr3Review->id)
            ->where('action_label', 'Revisi LPJ')
            ->where('required_role', 'wr3')
            ->firstOrFail();

        $pengajuan = Pengajuan::create([
            'user_id' => $user->id,
            'nama_kegiatan' => 'Lomba Robotik LPJ Revisi WR3',
            'dana_diajukan' => 2500000,
            'tanggal_pengajuan' => now()->toDateString(),
            'workflow_state_id' => $stateWr3Review->id,
            'file_proposal' => 'proposals/test.pdf',
            'file_lpj' => 'lpj/test.pdf',
        ]);

        $res = $this->actingAs($wr3)->post(route('verifikasi.process', $pengajuan), [
            'transition_id' => $transitionRevisi->id,
            'catatan' => 'Foto dokumentasi kegiatan belum dilampirkan.',
        ]);

        $res->assertRedirect(route('verifikasi.index'));
        $this->assertEquals('funds_disbursed', $pengajuan->fresh()->state->name);
        $this->assertDatabaseHas('histori_status', [
            'pengajuan_id' => $pengajuan->id,
            'workflow_state_id' => $transitionRevisi->to_state_id,
            'catatan_kendala' => 'Foto dokumentasi kegiatan belum dilampirkan.',
        ]);
    }

    public function test_fr012_ormawa_dapat_mengunggah_ulang_setelah_lpj_direvisi(): void
    {
        Storage::fake('local');
        $user = $this->pengaju('ormawa', 0);
        $stateDisbursed = WorkflowState::where('name', 'funds_disbursed')->firstOrFail();

        $pengajuan = Pengajuan::create([
            'user_id' => $user->id,
            'nama_kegiatan' => 'Kegiatan LPJ Re-upload',
            'dana_diajukan' => 1000000,
            'tanggal_pengajuan' => now()->toDateString(),
            'workflow_state_id' => $stateDisbursed->id,
            'file_proposal' => 'proposals/test.pdf',
            'file_lpj' => 'lpj/old_lpj.pdf',
        ]);

        // Catat histori revisi
        \App\Models\HistoriStatus::create([
            'pengajuan_id' => $pengajuan->id,
            'user_id' => $this->pengaju('bkhm', 0)->id,
            'workflow_state_id' => $stateDisbursed->id,
            'catatan' => 'Perbaiki nota.',
            'catatan_kendala' => 'Perbaiki nota.',
        ]);

        // Cek halaman upload LPJ menampilkan catatan revisi
        $pageRes = $this->actingAs($user)->get(route('lpj.create', $pengajuan));
        $pageRes->assertOk();
        $pageRes->assertSee('Catatan Revisi LPJ:');
        $pageRes->assertSee('Perbaiki nota.');

        // Unggah LPJ baru yang diperbaiki
        $file = \Illuminate\Http\UploadedFile::fake()->create('lpj_revisi.pdf', 500, 'application/pdf');
        $postRes = $this->actingAs($user)->post(route('lpj.store', $pengajuan), [
            'file_lpj' => $file,
        ]);

        $postRes->assertRedirect(route('lpj.index'));
        $this->assertEquals('lpj_submitted', $pengajuan->fresh()->state->name);
        $this->assertNotEquals('lpj/old_lpj.pdf', $pengajuan->fresh()->file_lpj);
    }

    public function test_fr002_admin_dapat_mengubah_role_dan_status_akun_pengguna(): void
    {
        $admin = $this->pengaju('admin', 0);
        $targetUser = User::factory()->create([
            'username' => 'target_user_fr002',
            'status_akun' => 'aktif',
            'password' => \Illuminate\Support\Facades\Hash::make('Password123!'),
        ]);
        $targetUser->assignRole('ormawa');
        $this->assertTrue($targetUser->hasRole('ormawa'));

        // Ubah role menjadi bem dan status_akun menjadi nonaktif
        $response = $this->actingAs($admin)->put(route('admin.users.update', $targetUser), [
            'name' => 'Target User Updated',
            'email' => $targetUser->email,
            'username' => 'target_user_fr002',
            'role' => 'bem',
            'status_akun' => 'nonaktif',
        ]);

        $response->assertRedirect(route('admin.users.index'));
        $targetUser->refresh();
        $this->assertEquals('Target User Updated', $targetUser->name);
        $this->assertEquals('nonaktif', $targetUser->status_akun);
        $this->assertTrue($targetUser->hasRole('bem'));
        $this->assertFalse($targetUser->hasRole('ormawa'));
    }

    public function test_fr002_pengguna_berstatus_nonaktif_tidak_dapat_login(): void
    {
        $userNonaktif = User::factory()->create([
            'email' => 'nonaktif@itg.ac.id',
            'username' => 'user_nonaktif',
            'status_akun' => 'nonaktif',
            'password' => \Illuminate\Support\Facades\Hash::make('Secret123!'),
        ]);
        $userNonaktif->assignRole('ormawa');

        // Percobaan login dengan email
        $resLogin = $this->post(route('login'), [
            'email' => 'nonaktif@itg.ac.id',
            'password' => 'Secret123!',
        ]);

        $resLogin->assertSessionHasErrors('email');
        $this->assertGuest();

        // Aktifkan kembali akun
        $userNonaktif->update(['status_akun' => 'aktif']);

        $resLoginAktif = $this->post(route('login'), [
            'email' => 'nonaktif@itg.ac.id',
            'password' => 'Secret123!',
        ]);

        $resLoginAktif->assertRedirect();
        $this->assertAuthenticatedAs($userNonaktif);
    }

    public function test_br03_br14_verifikator_tidak_dapat_memalsukan_transisi_draft_milik_pengguna_lain(): void
    {
        $ormawa = $this->pengaju('ormawa', 0);
        $bem = $this->pengaju('bem', 0);
        $stateDraft = WorkflowState::where('name', 'draft')->firstOrFail();

        // Transisi origin draft milik BEM: draft -> bem_approved
        $transisiBem = WorkflowTransition::where('from_state_id', $stateDraft->id)
            ->where('required_role', 'bem')
            ->firstOrFail();

        // Pengajuan draft milik Ormawa
        $pengajuan = Pengajuan::create([
            'user_id' => $ormawa->id,
            'nama_kegiatan' => 'Proposal Ormawa Anti-Skip',
            'dana_diajukan' => 1000000,
            'tanggal_pengajuan' => now()->toDateString(),
            'workflow_state_id' => $stateDraft->id,
            'file_proposal' => 'proposals/test.pdf',
        ]);

        // BEM mencoba menjalankan transisi draft miliknya pada pengajuan Ormawa (BR-03 / BR-14)
        $response = $this->actingAs($bem)->post(route('verifikasi.process', $pengajuan), [
            'transition_id' => $transisiBem->id,
            'catatan' => 'Mencoba memotong alur',
        ]);

        // Harus ditolak dengan 403 Forbidden
        $response->assertStatus(403);
        $this->assertEquals('draft', $pengajuan->fresh()->state->name);
    }

    public function test_br03_transisi_dari_state_tidak_cocok_ditolak(): void
    {
        $bkhm = $this->pengaju('bkhm', 0);
        $ormawa = $this->pengaju('ormawa', 0);
        $stateDraft = WorkflowState::where('name', 'draft')->firstOrFail();
        $stateBpmApproved = WorkflowState::where('name', 'bpm_approved')->firstOrFail();

        // Transisi BKHM (bpm_approved -> bkhm_approved)
        $transisiBkhm = WorkflowTransition::where('from_state_id', $stateBpmApproved->id)
            ->where('required_role', 'bkhm')
            ->firstOrFail();

        // Pengajuan masih draft
        $pengajuan = Pengajuan::create([
            'user_id' => $ormawa->id,
            'nama_kegiatan' => 'Proposal State Mismatch',
            'dana_diajukan' => 1000000,
            'tanggal_pengajuan' => now()->toDateString(),
            'workflow_state_id' => $stateDraft->id,
            'file_proposal' => 'proposals/test.pdf',
        ]);

        // BKHM mencoba memproses pengajuan yang masih draft
        $response = $this->actingAs($bkhm)->post(route('verifikasi.process', $pengajuan), [
            'transition_id' => $transisiBkhm->id,
            'catatan' => 'Bypass tahap BEM dan BPM',
        ]);

        $response->assertStatus(403);
        $this->assertEquals('draft', $pengajuan->fresh()->state->name);
    }

    public function test_fr005_duplikasi_proker_nama_dan_tanggal_sama_ditolak(): void
    {
        $ormawa = $this->pengaju('ormawa', 0);

        // Buat proker pertama
        ProgramKerja::create([
            'user_id' => $ormawa->id,
            'nama_proker' => 'Latihan Dasar Kepemimpinan 2026',
            'deskripsi' => 'LDK prodi IF',
            'rencana_pelaksanaan' => '2026-11-10',
            'status' => 'rencana',
        ]);

        // Ajukan lagi dengan nama dan tanggal pelaksanaan yang persis sama
        $response = $this->actingAs($ormawa)->post(route('proker.store'), [
            'nama_proker' => 'Latihan Dasar Kepemimpinan 2026',
            'deskripsi' => 'Deskripsi duplikat',
            'rencana_pelaksanaan' => '2026-11-10',
        ]);

        $response->assertSessionHas('error');
        // Pastikan record tidak bertambah menjadi 2
        $this->assertEquals(1, ProgramKerja::where('nama_proker', 'Latihan Dasar Kepemimpinan 2026')->count());
    }

    public function test_fr005_proker_nama_sama_tanggal_berbeda_diizinkan(): void
    {
        $ormawa = $this->pengaju('ormawa', 0);

        ProgramKerja::create([
            'user_id' => $ormawa->id,
            'nama_proker' => 'Seminar Bulanan',
            'deskripsi' => 'Seminar Batch 1',
            'rencana_pelaksanaan' => '2026-05-01',
            'status' => 'rencana',
        ]);

        // Ajukan nama sama tapi tanggal beda
        $response = $this->actingAs($ormawa)->post(route('proker.store'), [
            'nama_proker' => 'Seminar Bulanan',
            'deskripsi' => 'Seminar Batch 2',
            'rencana_pelaksanaan' => '2026-06-01',
        ]);

        $response->assertRedirect(route('proker.index'));
        $this->assertEquals(2, ProgramKerja::where('nama_proker', 'Seminar Bulanan')->count());
    }

    public function test_fr022_penolakan_oleh_lembaga_mengirim_notifikasi_ke_role_bem(): void
    {
        $ormawa = $this->pengaju('ormawa', 0);
        $bkhm = $this->pengaju('bkhm', 0);
        $bem = $this->pengaju('bem', 0);

        $stateBpmApproved = WorkflowState::where('name', 'bpm_approved')->firstOrFail();
        $stateDraft = WorkflowState::where('name', 'draft')->firstOrFail();

        // Transisi revisi/tolak BKHM ke draft
        $transisiRevisi = WorkflowTransition::where('from_state_id', $stateBpmApproved->id)
            ->where('to_state_id', $stateDraft->id)
            ->where('required_role', 'bkhm')
            ->firstOrFail();

        $pengajuan = Pengajuan::create([
            'user_id' => $ormawa->id,
            'nama_kegiatan' => 'Festival Budaya Ormawa',
            'dana_diajukan' => 3000000,
            'tanggal_pengajuan' => now()->toDateString(),
            'workflow_state_id' => $stateBpmApproved->id,
            'file_proposal' => 'proposals/festival.pdf',
        ]);

        // BKHM merevisi proposal
        $response = $this->actingAs($bkhm)->post(route('verifikasi.process', $pengajuan), [
            'transition_id' => $transisiRevisi->id,
            'catatan' => 'Format RAB tidak sesuai standar institusi.',
        ]);

        $response->assertRedirect(route('verifikasi.index'));

        // Pastikan akun pengaju (Ormawa) menerima notifikasi
        $this->assertDatabaseHas('notifikasi', [
            'user_id' => $ormawa->id,
            'status_baca' => 'belum',
        ]);

        // Sesuai FR-022 §22 no.9: Akun BEM HARUS menerima notifikasi penolakan proposal Ormawa
        $this->assertDatabaseHas('notifikasi', [
            'user_id' => $bem->id,
            'pesan' => 'Proposal "Festival Budaya Ormawa" ditolak/dikembalikan pada tahap ' . $stateBpmApproved->label . '.',
            'status_baca' => 'belum',
        ]);
    }

    public function test_fr018_peminjaman_dua_ruangan_berbeda_pada_waktu_sama_diizinkan(): void
    {
        $ormawa1 = $this->pengaju('ormawa', 0);
        $ormawa2 = $this->pengaju('ormawa', 1);

        $ruanganA = MasterRuangan::firstOrCreate(
            ['nama_ruangan' => 'Auditorium Utama'],
            ['kapasitas' => 300, 'status_aktif' => true]
        );

        $ruanganB = MasterRuangan::firstOrCreate(
            ['nama_ruangan' => 'Ruang Rapat Gedung B'],
            ['kapasitas' => 50, 'status_aktif' => true]
        );

        $tgl = now()->addDays(5)->toDateString();

        // 1. Ormawa 1 meminjam Ruangan A pada jam 09:00 - 12:00
        $res1 = $this->actingAs($ormawa1)->post(route('peminjaman.tempat.store'), [
            'ruangan_id' => $ruanganA->id,
            'nama_kegiatan' => 'Seminar Ormawa 1',
            'tgl_mulai' => $tgl,
            'tgl_selesai' => $tgl,
            'jam_mulai' => '09:00',
            'jam_selesai' => '12:00',
            'deskripsi_kegiatan' => 'Seminar Nasional',
        ]);

        $res1->assertRedirect(route('peminjaman.tempat.index'));
        $res1->assertSessionHas('success');
        $this->assertDatabaseHas('peminjaman_tempat', [
            'ruangan_id' => $ruanganA->id,
            'nama_kegiatan' => 'Seminar Ormawa 1',
        ]);

        // 2. Ormawa 2 meminjam Ruangan B pada rentang waktu persis sama (09:00 - 12:00)
        // Harus BERHASIL dan TIDAK terkena false positive bentrok antar ruangan
        $res2 = $this->actingAs($ormawa2)->post(route('peminjaman.tempat.store'), [
            'ruangan_id' => $ruanganB->id,
            'nama_kegiatan' => 'Rapat Kerja Ormawa 2',
            'tgl_mulai' => $tgl,
            'tgl_selesai' => $tgl,
            'jam_mulai' => '09:00',
            'jam_selesai' => '12:00',
            'deskripsi_kegiatan' => 'Rapat Kerja Tahunan',
        ]);

        $res2->assertRedirect(route('peminjaman.tempat.index'));
        $res2->assertSessionHas('success');
        $this->assertDatabaseHas('peminjaman_tempat', [
            'ruangan_id' => $ruanganB->id,
            'nama_kegiatan' => 'Rapat Kerja Ormawa 2',
        ]);

        // 3. Namun jika Ormawa 2 mencoba meminjam Ruangan A pada waktu beririsan (10:00 - 11:00), harus DITOLAK karena bentrok internal Ruangan A
        $res3 = $this->actingAs($ormawa2)->post(route('peminjaman.tempat.store'), [
            'ruangan_id' => $ruanganA->id,
            'nama_kegiatan' => 'Workshop Dadakan',
            'tgl_mulai' => $tgl,
            'tgl_selesai' => $tgl,
            'jam_mulai' => '10:00',
            'jam_selesai' => '11:00',
            'deskripsi_kegiatan' => 'Workshop',
        ]);

        $res3->assertSessionHas('error', 'Ruangan sudah dibooking pada tanggal/waktu tersebut.');
    }

    public function test_sec04_matrix_rbac_non_bkhm_admin_ditolak_di_seluruh_area_admin(): void
    {
        $adminRoutes = [
            route('admin.users.index'),
            route('admin.konfigurasi.edit'),
        ];

        $nonAdminRoles = ['ormawa', 'bem', 'bpm', 'wr3', 'bendahara', 'sarpras', 'mahasiswa'];

        foreach ($nonAdminRoles as $role) {
            $user = $this->pengaju($role, 0);

            foreach ($adminRoutes as $adminUrl) {
                $response = $this->actingAs($user)->get($adminUrl);
                $this->assertEquals(
                    403,
                    $response->getStatusCode(),
                    "Role '{$role}' seharusnya ditolak (403) saat mengakses URL admin: {$adminUrl}"
                );
            }
        }

        // Sebaliknya, bkhm dan admin harus dapat mengakses area admin
        $bkhm = $this->pengaju('bkhm', 0);
        $admin = $this->pengaju('admin', 0);

        foreach ($adminRoutes as $adminUrl) {
            $this->actingAs($bkhm)->get($adminUrl)->assertOk();
            $this->actingAs($admin)->get($adminUrl)->assertOk();
        }
    }

    public function test_nfr02_performance_load_time_di_bawah_dua_detik(): void
    {
        $ormawa = $this->pengaju('ormawa', 0);
        $stateDraft = WorkflowState::where('name', 'draft')->firstOrFail();

        // Seed 50 pengajuan untuk pengujian performance budget
        $dataPengajuan = [];
        for ($i = 1; $i <= 50; $i++) {
            $dataPengajuan[] = [
                'user_id' => $ormawa->id,
                'nama_kegiatan' => "Kegiatan Performance Budget {$i}",
                'dana_diajukan' => 1500000 + ($i * 10000),
                'tanggal_pengajuan' => now()->toDateString(),
                'workflow_state_id' => $stateDraft->id,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }
        Pengajuan::insert($dataPengajuan);

        // Ukur waktu response halaman daftar pengajuan
        $startTime = microtime(true);
        $response = $this->actingAs($ormawa)->get(route('pengajuan.index'));
        $durationMs = (microtime(true) - $startTime) * 1000;

        $response->assertOk();
        $this->assertLessThan(
            2000,
            $durationMs,
            "Load time /pengajuan dengan 50 data ({$durationMs} ms) harus di bawah 2000 ms (NFR-02)"
        );
    }

    public function test_fr011_pesan_follow_up_mengirim_notifikasi_ke_pihak_terkait(): void
    {
        $ormawa = $this->pengaju('ormawa', 0);
        $bem = $this->pengaju('bem', 0);

        $stateSubmitted = WorkflowState::where('name', 'submitted')->firstOrFail();

        $pengajuan = Pengajuan::create([
            'user_id' => $ormawa->id,
            'nama_kegiatan' => 'Latihan Kepemimpinan Mahasiswa',
            'dana_diajukan' => 2500000,
            'tanggal_pengajuan' => now()->toDateString(),
            'workflow_state_id' => $stateSubmitted->id,
            'file_proposal' => 'proposals/lkm.pdf',
        ]);

        // 1. Ormawa mengirim pesan follow-up -> harus mengirim notifikasi ke role verifikator saat ini (BEM)
        $res1 = $this->actingAs($ormawa)->post(route('pengajuan.komunikasi.store', $pengajuan), [
            'pesan' => 'Mohon arahan terkait berkas proposal kami.',
        ]);

        $res1->assertRedirect();
        $this->assertDatabaseHas('komunikasi_pengajuans', [
            'pengajuan_id' => $pengajuan->id,
            'user_id' => $ormawa->id,
            'pesan' => 'Mohon arahan terkait berkas proposal kami.',
        ]);

        $this->assertDatabaseHas('notifikasi', [
            'user_id' => $bem->id,
            'pesan' => 'Pesan baru pada pengajuan "' . $pengajuan->nama_kegiatan . '".',
            'status_baca' => 'belum',
        ]);

        // 2. BEM membalas pesan follow-up -> harus mengirim notifikasi ke pengaju (Ormawa)
        $res2 = $this->actingAs($bem)->post(route('pengajuan.komunikasi.store', $pengajuan), [
            'pesan' => 'Proposal sedang kami tinjau bersama pengurus.',
        ]);

        $res2->assertRedirect();
        $this->assertDatabaseHas('komunikasi_pengajuans', [
            'pengajuan_id' => $pengajuan->id,
            'user_id' => $bem->id,
            'pesan' => 'Proposal sedang kami tinjau bersama pengurus.',
        ]);

        $this->assertDatabaseHas('notifikasi', [
            'user_id' => $ormawa->id,
            'pesan' => 'Pesan baru dari verifikator pada pengajuan "' . $pengajuan->nama_kegiatan . '".',
            'status_baca' => 'belum',
        ]);
    }

    public function test_fr007_filter_status_dan_search_keyword_pengajuan(): void
    {
        $ormawa = $this->pengaju('ormawa', 0);
        $stateDraft = WorkflowState::where('name', 'draft')->firstOrFail();
        $stateSubmitted = WorkflowState::where('name', 'submitted')->firstOrFail();

        // 1. Buat beberapa pengajuan dengan status dan nama yang berbeda
        $p1 = Pengajuan::create([
            'user_id' => $ormawa->id,
            'nama_kegiatan' => 'Workshop Robotika Nasional',
            'dana_diajukan' => 2000000,
            'tanggal_pengajuan' => now()->toDateString(),
            'workflow_state_id' => $stateDraft->id,
            'file_proposal' => 'proposals/robotika.pdf',
        ]);

        $p2 = Pengajuan::create([
            'user_id' => $ormawa->id,
            'nama_kegiatan' => 'Seminar Artificial Intelligence',
            'dana_diajukan' => 1500000,
            'tanggal_pengajuan' => now()->toDateString(),
            'workflow_state_id' => $stateSubmitted->id,
            'file_proposal' => 'proposals/ai.pdf',
        ]);

        // 2. Uji Filter Status: hanya meminta status=draft
        $resFilter = $this->actingAs($ormawa)->get(route('pengajuan.index', ['status' => 'draft']));
        $resFilter->assertOk();
        $resFilter->assertSee('Workshop Robotika Nasional');
        $resFilter->assertDontSee('Seminar Artificial Intelligence');

        // 3. Uji Search Keyword: mencari keyword "Artificial"
        $resSearch = $this->actingAs($ormawa)->get(route('pengajuan.index', ['q' => 'Artificial']));
        $resSearch->assertOk();
        $resSearch->assertSee('Seminar Artificial Intelligence');
        $resSearch->assertDontSee('Workshop Robotika Nasional');

        // 4. Uji Kombinasi Search & Filter yang tidak cocok -> tabel kosong
        $resKombinasiNol = $this->actingAs($ormawa)->get(route('pengajuan.index', [
            'status' => 'draft',
            'q' => 'Artificial',
        ]));
        $resKombinasiNol->assertOk();
        $resKombinasiNol->assertDontSee('Seminar Artificial Intelligence');
        $resKombinasiNol->assertDontSee('Workshop Robotika Nasional');
    }
}
