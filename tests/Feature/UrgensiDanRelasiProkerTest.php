<?php

namespace Tests\Feature;

use App\Models\Pengajuan;
use App\Models\ProgramKerja;
use App\Models\User;
use App\Models\WorkflowState;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class UrgensiDanRelasiProkerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Artisan::call('db:seed', ['--class' => 'RolePermissionSeeder', '--force' => true]);
        Artisan::call('db:seed', ['--class' => 'WorkflowSeeder', '--force' => true]);
        Storage::fake('local');
    }

    private function createUserWithRole(string $roleName, string $email): User
    {
        $user = User::factory()->create([
            'email' => $email,
            'saldo' => 50000000,
        ]);
        $user->assignRole($roleName);

        return $user;
    }

    public function test_ormawa_can_create_proposal_with_event_dates_and_linked_proker(): void
    {
        $ormawa = $this->createUserWithRole('ormawa', 'hima_informatika@itg.ac.id');
        $proker = ProgramKerja::create([
            'user_id' => $ormawa->id,
            'nama_proker' => 'Tech Day 2026',
            'deskripsi' => 'Seminar dan expo teknologi tahunan.',
            'rencana_pelaksanaan' => now()->addMonth()->toDateString(),
            'status' => 'rencana',
        ]);

        $file = UploadedFile::fake()->create('proposal.pdf', 500, 'application/pdf');

        $mulai = now()->addDays(5)->toDateString();
        $selesai = now()->addDays(7)->toDateString();

        $response = $this->actingAs($ormawa)->post(route('pengajuan.store'), [
            'nama_kegiatan' => 'Tech Day 2026',
            'dana_diajukan' => 2500000,
            'tanggal_pengajuan' => now()->toDateString(),
            'tanggal_mulai_kegiatan' => $mulai,
            'tanggal_selesai_kegiatan' => $selesai,
            'program_kerja_id' => $proker->id,
            'file_proposal' => $file,
        ]);

        $response->assertRedirect(route('pengajuan.index'));
        $pengajuan = Pengajuan::where('nama_kegiatan', 'Tech Day 2026')->first();
        $this->assertNotNull($pengajuan);
        $this->assertEquals($ormawa->id, $pengajuan->user_id);
        $this->assertEquals($proker->id, $pengajuan->program_kerja_id);
        $this->assertEquals($mulai, $pengajuan->tanggal_mulai_kegiatan->format('Y-m-d'));
        $this->assertEquals($selesai, $pengajuan->tanggal_selesai_kegiatan->format('Y-m-d'));
    }

    public function test_validation_fails_if_tanggal_selesai_kegiatan_is_before_tanggal_mulai(): void
    {
        $ormawa = $this->createUserWithRole('ormawa', 'hima_sipil@itg.ac.id');
        $file = UploadedFile::fake()->create('proposal.pdf', 500, 'application/pdf');

        $response = $this->actingAs($ormawa)->from(route('pengajuan.create'))->post(route('pengajuan.store'), [
            'nama_kegiatan' => 'Lomba Sipil',
            'dana_diajukan' => 1000000,
            'tanggal_pengajuan' => now()->toDateString(),
            'tanggal_mulai_kegiatan' => now()->addDays(10)->toDateString(),
            'tanggal_selesai_kegiatan' => now()->addDays(5)->toDateString(), // Tidak valid: sebelum mulai
            'file_proposal' => $file,
        ]);

        $response->assertRedirect(route('pengajuan.create'));
        $response->assertSessionHasErrors(['tanggal_selesai_kegiatan']);
    }

    public function test_pengajuan_urgency_calculation_helper(): void
    {
        $ormawa = $this->createUserWithRole('ormawa', 'hima_industri@itg.ac.id');
        $draftState = WorkflowState::where('name', 'draft')->firstOrFail();

        // 1. Acara sudah lewat (kemarin)
        $pengajuanLewat = Pengajuan::create([
            'user_id' => $ormawa->id,
            'nama_kegiatan' => 'Acara Kemarin',
            'dana_diajukan' => 500000,
            'tanggal_pengajuan' => now()->subDays(10)->toDateString(),
            'tanggal_mulai_kegiatan' => now()->subDay()->toDateString(),
            'workflow_state_id' => $draftState->id,
        ]);
        $urgensiLewat = $pengajuanLewat->statusUrgensi();
        $this->assertTrue($urgensiLewat['is_urgent']);
        $this->assertStringContainsString('Hari-H Terlewat', $urgensiLewat['label']);

        // 2. Acara Hari Ini (H-0)
        $pengajuanHariIni = Pengajuan::create([
            'user_id' => $ormawa->id,
            'nama_kegiatan' => 'Acara Hari Ini',
            'dana_diajukan' => 500000,
            'tanggal_pengajuan' => now()->toDateString(),
            'tanggal_mulai_kegiatan' => now()->toDateString(),
            'workflow_state_id' => $draftState->id,
        ]);
        $urgensiHariIni = $pengajuanHariIni->statusUrgensi();
        $this->assertTrue($urgensiHariIni['is_urgent']);
        $this->assertStringContainsString('Hari-H Kegiatan (Hari Ini!)', $urgensiHariIni['label']);

        // 3. Mendesak (H-2)
        $pengajuanMendesak = Pengajuan::create([
            'user_id' => $ormawa->id,
            'nama_kegiatan' => 'Acara Mendesak H-2',
            'dana_diajukan' => 500000,
            'tanggal_pengajuan' => now()->toDateString(),
            'tanggal_mulai_kegiatan' => now()->addDays(2)->toDateString(),
            'workflow_state_id' => $draftState->id,
        ]);
        $urgensiMendesak = $pengajuanMendesak->statusUrgensi();
        $this->assertTrue($urgensiMendesak['is_urgent']);
        $this->assertStringContainsString('Mendesak: H-2 Kegiatan', $urgensiMendesak['label']);

        // 4. Perhatian (H-6)
        $pengajuanPerhatian = Pengajuan::create([
            'user_id' => $ormawa->id,
            'nama_kegiatan' => 'Acara Perhatian H-6',
            'dana_diajukan' => 500000,
            'tanggal_pengajuan' => now()->toDateString(),
            'tanggal_mulai_kegiatan' => now()->addDays(6)->toDateString(),
            'workflow_state_id' => $draftState->id,
        ]);
        $urgensiPerhatian = $pengajuanPerhatian->statusUrgensi();
        $this->assertTrue($urgensiPerhatian['is_urgent']);
        $this->assertStringContainsString('Perhatian: H-6 Kegiatan', $urgensiPerhatian['label']);

        // 5. Aman (> 7 hari, misal H-14)
        $pengajuanAman = Pengajuan::create([
            'user_id' => $ormawa->id,
            'nama_kegiatan' => 'Acara Masih Aman',
            'dana_diajukan' => 500000,
            'tanggal_pengajuan' => now()->toDateString(),
            'tanggal_mulai_kegiatan' => now()->addDays(14)->toDateString(),
            'workflow_state_id' => $draftState->id,
        ]);
        $urgensiAman = $pengajuanAman->statusUrgensi();
        $this->assertFalse($urgensiAman['is_urgent']);
        $this->assertStringContainsString('H-14 Kegiatan', $urgensiAman['label']);
    }

    public function test_verifikator_queue_displays_urgency_badge_and_proker_pill(): void
    {
        $ormawa = $this->createUserWithRole('ormawa', 'hima_elektro@itg.ac.id');
        $bem = $this->createUserWithRole('bem', 'bem_verifikator@itg.ac.id');

        $proker = ProgramKerja::create([
            'user_id' => $ormawa->id,
            'nama_proker' => 'Robotik Competition',
            'deskripsi' => 'Lomba robotik tahunan.',
            'rencana_pelaksanaan' => now()->addDays(3)->toDateString(),
            'status' => 'rencana',
        ]);

        $stateSubmitted = WorkflowState::where('name', WorkflowState::SUBMITTED)->firstOrFail();

        $pengajuan = Pengajuan::create([
            'user_id' => $ormawa->id,
            'nama_kegiatan' => 'Lomba Robotik Mahasiswa',
            'dana_diajukan' => 3000000,
            'tanggal_pengajuan' => now()->toDateString(),
            'tanggal_mulai_kegiatan' => now()->addDays(2)->toDateString(),
            'program_kerja_id' => $proker->id,
            'workflow_state_id' => $stateSubmitted->id,
            'file_proposal' => 'proposals/dummy.pdf',
        ]);

        $response = $this->actingAs($bem)->get(route('verifikasi.index'));

        $response->assertStatus(200);
        $response->assertSee('Lomba Robotik Mahasiswa');
        $response->assertSee('Robotik Competition');
        $response->assertSee('Mendesak: H-2 Kegiatan');
        // Row highlighting test
        $response->assertSee('bg-rose-50/70');
    }

    public function test_proker_index_displays_linked_proposals_count(): void
    {
        $ormawa = $this->createUserWithRole('ormawa', 'hima_arsitektur@itg.ac.id');

        $proker = ProgramKerja::create([
            'user_id' => $ormawa->id,
            'nama_proker' => 'Pameran Karya Arsitektur',
            'deskripsi' => 'Pameran maket dan desain.',
            'rencana_pelaksanaan' => now()->addMonth()->toDateString(),
            'status' => 'rencana',
        ]);

        $draftState = WorkflowState::where('name', 'draft')->firstOrFail();

        // Buat 2 proposal yang terhubung ke proker ini
        Pengajuan::create([
            'user_id' => $ormawa->id,
            'nama_kegiatan' => 'Proposal Pameran Tahap 1',
            'dana_diajukan' => 1000000,
            'tanggal_pengajuan' => now()->toDateString(),
            'program_kerja_id' => $proker->id,
            'workflow_state_id' => $draftState->id,
        ]);

        Pengajuan::create([
            'user_id' => $ormawa->id,
            'nama_kegiatan' => 'Proposal Pameran Tahap 2',
            'dana_diajukan' => 1200000,
            'tanggal_pengajuan' => now()->toDateString(),
            'program_kerja_id' => $proker->id,
            'workflow_state_id' => $draftState->id,
        ]);

        $response = $this->actingAs($ormawa)->get(route('proker.index'));

        $response->assertStatus(200);
        $response->assertSee('Pameran Karya Arsitektur');
        $response->assertSee('2 Proposal Diajukan');
    }

    public function test_ormawa_can_update_proposal_event_dates_and_proker(): void
    {
        $ormawa = $this->createUserWithRole('ormawa', 'ukm_musik@itg.ac.id');
        $draftState = WorkflowState::where('name', 'draft')->firstOrFail();

        $pengajuan = Pengajuan::create([
            'user_id' => $ormawa->id,
            'nama_kegiatan' => 'Konser Amal',
            'dana_diajukan' => 2000000,
            'tanggal_pengajuan' => now()->toDateString(),
            'workflow_state_id' => $draftState->id,
        ]);

        $proker = ProgramKerja::create([
            'user_id' => $ormawa->id,
            'nama_proker' => 'Konser Musik Tahunan',
            'deskripsi' => 'Konser musik mahasiswa.',
            'rencana_pelaksanaan' => now()->addMonth()->toDateString(),
            'status' => 'rencana',
        ]);

        $mulai = now()->addDays(10)->toDateString();
        $selesai = now()->addDays(11)->toDateString();

        $response = $this->actingAs($ormawa)->put(route('pengajuan.update', $pengajuan), [
            'nama_kegiatan' => 'Konser Amal 2026 Updated',
            'dana_diajukan' => 2200000,
            'tanggal_pengajuan' => now()->toDateString(),
            'tanggal_mulai_kegiatan' => $mulai,
            'tanggal_selesai_kegiatan' => $selesai,
            'program_kerja_id' => $proker->id,
        ]);

        $response->assertRedirect(route('pengajuan.show', $pengajuan));

        $pengajuan->refresh();
        $this->assertEquals('Konser Amal 2026 Updated', $pengajuan->nama_kegiatan);
        $this->assertEquals($proker->id, $pengajuan->program_kerja_id);
        $this->assertEquals($mulai, $pengajuan->tanggal_mulai_kegiatan->format('Y-m-d'));
        $this->assertEquals($selesai, $pengajuan->tanggal_selesai_kegiatan->format('Y-m-d'));
    }
}
