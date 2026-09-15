<?php

namespace Tests\Feature;

use App\Models\Aspirasi;
use App\Models\Notifikasi;
use App\Models\Prestasi;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class Phase3Test extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Artisan::call('db:seed', ['--class' => 'RolePermissionSeeder', '--force' => true]);
        Artisan::call('db:seed', ['--class' => 'WorkflowSeeder', '--force' => true]);
    }

    /** FR-020: mahasiswa dapat melaporkan prestasi; bukti disimpan privat. */
    public function test_mahasiswa_can_report_prestasi_with_private_proof(): void
    {
        Storage::fake('local');

        $mahasiswa = User::factory()->create();
        $mahasiswa->assignRole('mahasiswa');

        $response = $this->actingAs($mahasiswa)->post(route('prestasi.store'), [
            'nama_kegiatan' => 'Hackathon Nasional',
            'tingkat' => 'Nasional',
            'afiliasi' => 'individu',
            'file_bukti' => UploadedFile::fake()->create('sertifikat.pdf', 100, 'application/pdf'),
        ]);

        $response->assertRedirect(route('prestasi.index'));
        $prestasi = Prestasi::latest()->first();
        $this->assertEquals('pending', $prestasi->status);
        Storage::disk('local')->assertExists($prestasi->file_bukti);
    }

    /** FR-020: verifikator menyetujui prestasi & pelapor mendapat notifikasi. */
    public function test_bkhm_can_verify_prestasi_and_notify(): void
    {
        $mahasiswa = User::factory()->create();
        $mahasiswa->assignRole('mahasiswa');
        $prestasi = Prestasi::create([
            'user_id' => $mahasiswa->id,
            'nama_kegiatan' => 'Lomba Karya',
            'tingkat' => 'Universitas',
            'afiliasi' => 'individu',
            'status' => 'pending',
        ]);

        $bkhm = User::factory()->create();
        $bkhm->assignRole('bkhm');

        $response = $this->actingAs($bkhm)->patch(route('prestasi.verify', $prestasi), [
            'status' => 'terverifikasi',
            'catatan_bkhm' => 'Bukti valid',
        ]);

        $response->assertRedirect();
        $this->assertEquals('terverifikasi', $prestasi->fresh()->status);
        $this->assertTrue(Notifikasi::where('user_id', $mahasiswa->id)->exists());
    }

    /** FR-015/016: aspirasi menyimpan identitas & tanda anonim; pengirim dapat melacak. */
    public function test_aspirasi_tracks_status_and_keeps_identity(): void
    {
        $mahasiswa = User::factory()->create();
        $mahasiswa->assignRole('mahasiswa');

        $this->actingAs($mahasiswa)->post(route('aspirasi.store'), [
            'judul' => 'Fasilitas Aula',
            'isi' => 'Mohon perbaikan ventilasi.',
            'kategori' => 'Fasilitas',
            'anonim' => 1,
        ]);

        $aspirasi = Aspirasi::latest()->first();
        // Identitas tetap disimpan walau anonim.
        $this->assertEquals($mahasiswa->id, $aspirasi->user_id);
        $this->assertTrue($aspirasi->anonim);

        $this->actingAs($mahasiswa)->get(route('aspirasi.mine'))->assertOk();
    }

    /** FR-025: NotifikasiService membuat notifikasi in-app. */
    public function test_notifikasi_service_creates_in_app_notification(): void
    {
        $user = User::factory()->create();

        \App\Services\NotifikasiService::kirim($user->id, 'Pesan uji notifikasi.');

        $notif = Notifikasi::where('user_id', $user->id)->first();
        $this->assertNotNull($notif);
        $this->assertEquals('Pesan uji notifikasi.', $notif->pesan);
        $this->assertEquals('belum', $notif->status_baca);

        $this->actingAs($user)->get(route('notifikasi.index'))->assertOk();
    }

    /** FR-011: pengaju dapat mengirim follow-up pada pengajuan. */
    public function test_owner_can_send_followup_message(): void
    {
        $ormawa = User::factory()->create();
        $ormawa->assignRole('ormawa');

        $submitted = \App\Models\WorkflowState::where('name', 'submitted')->firstOrFail();
        $pengajuan = \App\Models\Pengajuan::create([
            'user_id' => $ormawa->id,
            'nama_kegiatan' => 'Kegiatan Follow-up',
            'dana_diajukan' => 100000,
            'tanggal_pengajuan' => now()->toDateString(),
            'workflow_state_id' => $submitted->id,
        ]);

        $response = $this->actingAs($ormawa)->post(route('pengajuan.komunikasi.store', $pengajuan), [
            'pesan' => 'Mohon info progres verifikasi.',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('komunikasi_pengajuans', [
            'pengajuan_id' => $pengajuan->id,
            'user_id' => $ormawa->id,
        ]);
    }
}
