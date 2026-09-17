<?php

namespace Tests\Feature;

use App\Models\Notifikasi;
use App\Models\PeminjamanBarang;
use App\Models\Pengajuan;
use App\Models\PeriodeAnggaran;
use App\Models\User;
use App\Models\WorkflowState;
use App\Models\WorkflowTransition;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

class NotifikasiSkenarioPemicuTest extends TestCase
{
    use RefreshDatabase;

    protected User $ormawa;
    protected User $bem;
    protected User $bpm;
    protected User $bkhm;
    protected User $wr3;
    protected User $bendahara;
    protected User $sarpras;
    protected PeriodeAnggaran $periode;

    protected function setUp(): void
    {
        parent::setUp();
        Artisan::call('db:seed', ['--class' => 'RolePermissionSeeder', '--force' => true]);
        Artisan::call('db:seed', ['--class' => 'WorkflowSeeder', '--force' => true]);

        $this->periode = PeriodeAnggaran::create([
            'nama' => '2026/2027',
            'is_active' => true,
            'tanggal_mulai' => now()->startOfYear(),
            'tanggal_selesai' => now()->endOfYear(),
        ]);

        $this->ormawa = User::factory()->create(['saldo' => 5000000, 'saldo_awal' => 5000000]);
        $this->ormawa->assignRole('ormawa');

        $this->bem = User::factory()->create();
        $this->bem->assignRole('bem');

        $this->bpm = User::factory()->create();
        $this->bpm->assignRole('bpm');

        $this->bkhm = User::factory()->create();
        $this->bkhm->assignRole('bkhm');

        $this->wr3 = User::factory()->create();
        $this->wr3->assignRole('wr3');

        $this->bendahara = User::factory()->create();
        $this->bendahara->assignRole('bendahara');

        $this->sarpras = User::factory()->create();
        $this->sarpras->assignRole('sarpras');
    }

    private function state(string $name): WorkflowState
    {
        return WorkflowState::where('name', $name)->firstOrFail();
    }

    private function buatPengajuan(string $stateName): Pengajuan
    {
        return Pengajuan::create([
            'user_id' => $this->ormawa->id,
            'nama_kegiatan' => 'Kegiatan Notif Test',
            'dana_diajukan' => 1000000,
            'tanggal_pengajuan' => now()->toDateString(),
            'workflow_state_id' => $this->state($stateName)->id,
        ]);
    }

    /** FR-022 #3: Perubahan status pengajuan mengirim notifikasi ke pengaju */
    public function test_pemicu_3_perubahan_status_pengajuan_memberitahu_pengaju(): void
    {
        $pengajuan = $this->buatPengajuan('submitted');

        // BEM approve -> bem_approved
        $transition = WorkflowTransition::where('from_state_id', $this->state('submitted')->id)
            ->where('to_state_id', $this->state('bem_approved')->id)
            ->where('required_role', 'bem')
            ->firstOrFail();

        $response = $this->actingAs($this->bem)->post(route('verifikasi.process', $pengajuan), [
            'transition_id' => $transition->id,
            'catatan' => 'Disetujui BEM',
        ]);

        $response->assertRedirect(route('verifikasi.index'));

        // Cek notifikasi terkirim ke pengaju
        $this->assertDatabaseHas('notifikasi', [
            'user_id' => $this->ormawa->id,
        ]);

        $notif = Notifikasi::where('user_id', $this->ormawa->id)->latest()->first();
        $this->assertStringContainsString('kini berstatus:', $notif->pesan);
        $this->assertStringContainsString('Kegiatan Notif Test', $notif->pesan);
    }

    /** FR-022 #4: Penolakan proposal ormawa mengirim notifikasi ke pengaju dan akun BEM (PRD §22 no.9) */
    public function test_pemicu_4_penolakan_proposal_memberitahu_pengaju_dan_bem(): void
    {
        $pengajuan = $this->buatPengajuan('submitted');

        // BEM tolak -> rejected
        $transition = WorkflowTransition::where('from_state_id', $this->state('submitted')->id)
            ->where('to_state_id', $this->state('rejected')->id)
            ->where('required_role', 'bem')
            ->firstOrFail();

        $response = $this->actingAs($this->bem)->post(route('verifikasi.process', $pengajuan), [
            'transition_id' => $transition->id,
            'catatan' => 'Proposal ditolak karena format salah',
        ]);

        $response->assertRedirect(route('verifikasi.index'));

        // 1. Pengaju dapat notif
        $this->assertDatabaseHas('notifikasi', [
            'user_id' => $this->ormawa->id,
        ]);

        // 2. Akun BEM juga menerima tembusan notifikasi penolakan
        $this->assertDatabaseHas('notifikasi', [
            'user_id' => $this->bem->id,
        ]);

        $notifBem = Notifikasi::where('user_id', $this->bem->id)->latest()->first();
        $this->assertStringContainsString('Proposal "Kegiatan Notif Test" ditolak/dikembalikan', $notifBem->pesan);
    }

    /** FR-022 #5: Pencairan dana oleh bendahara mengirim notifikasi ke pengaju */
    public function test_pemicu_5_dana_dicairkan_memberitahu_pengaju(): void
    {
        $pengajuan = $this->buatPengajuan('to_treasurer');

        $response = $this->actingAs($this->bendahara)->post(route('bendahara.proses', $pengajuan), [
            'nominal_cair' => 500000,
            'tanggal_cair' => now()->toDateString(),
            'catatan' => 'Pencairan termin 1 selesai',
        ]);

        $response->assertRedirect(route('verifikasi.index'));

        $this->assertDatabaseHas('notifikasi', [
            'user_id' => $this->ormawa->id,
        ]);

        $notif = Notifikasi::where('user_id', $this->ormawa->id)->latest()->first();
        $this->assertStringContainsString('Dana termin 1 untuk pengajuan "Kegiatan Notif Test" telah dicairkan', $notif->pesan);
    }

    /** FR-022 #8: Komunikasi pesan follow-up mengirim notifikasi 2 arah */
    public function test_pemicu_8_pesan_follow_up_mengirim_notifikasi(): void
    {
        $pengajuan = $this->buatPengajuan('submitted');

        // Ormawa kirim pesan ke verifikator (saat submitted -> targetRole = bem)
        $resOrmawa = $this->actingAs($this->ormawa)->post(route('pengajuan.komunikasi.store', $pengajuan), [
            'pesan' => 'Mohon dicek kembali dokumen pendukung kami.',
        ]);
        $resOrmawa->assertRedirect();

        // BEM menerima notifikasi
        $this->assertDatabaseHas('notifikasi', [
            'user_id' => $this->bem->id,
        ]);

        // Verifikator membalas pesan ormawa
        $resBem = $this->actingAs($this->bem)->post(route('pengajuan.komunikasi.store', $pengajuan), [
            'pesan' => 'Siap, sedang ditinjau kembali.',
        ]);
        $resBem->assertRedirect();

        // Ormawa menerima notifikasi balasan
        $this->assertDatabaseHas('notifikasi', [
            'user_id' => $this->ormawa->id,
        ]);

        $notifOrmawa = Notifikasi::where('user_id', $this->ormawa->id)->latest()->first();
        $this->assertStringContainsString('Pesan baru dari verifikator', $notifOrmawa->pesan);
    }

    /** FR-022 #10: Command barang terlambat mengirim notifikasi ke sarpras dan peminjam */
    public function test_pemicu_10_barang_terlambat_mengirim_notifikasi(): void
    {
        // Buat peminjaman barang lewat tempo (kemarin)
        $peminjaman = PeminjamanBarang::create([
            'user_id' => $this->ormawa->id,
            'nama_kegiatan' => 'Peminjaman Terlambat Test',
            'tgl_mulai' => now()->subDays(5)->toDateString(),
            'tgl_selesai' => now()->subDays(1)->toDateString(),
            'kebutuhan_barang' => [['barang_id' => 1, 'qty' => 2]],
            'status_akhir' => 'Sedang Digunakan',
        ]);

        // Jalankan artisan command notifikasi:barang-terlambat
        Artisan::call('notifikasi:barang-terlambat');

        // Notifikasi untuk ormawa peminjam
        $this->assertDatabaseHas('notifikasi', [
            'user_id' => $this->ormawa->id,
        ]);
        $notifOrmawa = Notifikasi::where('user_id', $this->ormawa->id)->latest()->first();
        $this->assertStringContainsString('Batas pengembalian barang untuk kegiatan "Peminjaman Terlambat Test" telah lewat', $notifOrmawa->pesan);

        // Notifikasi untuk role sarpras
        $this->assertDatabaseHas('notifikasi', [
            'user_id' => $this->sarpras->id,
        ]);
        $notifSarpras = Notifikasi::where('user_id', $this->sarpras->id)->latest()->first();
        $this->assertStringContainsString('belum dikembalikan', $notifSarpras->pesan);
    }
}
