<?php

namespace Tests\Feature;

use App\Models\KomunikasiPengajuan;
use App\Models\Notifikasi;
use App\Models\Pengajuan;
use App\Models\User;
use App\Models\WorkflowState;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

class NudgePengajuanTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Artisan::call('db:seed', ['--class' => 'RolePermissionSeeder', '--force' => true]);
        Artisan::call('db:seed', ['--class' => 'WorkflowSeeder', '--force' => true]);
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

    public function test_ormawa_can_send_nudge_to_bem_when_submitted(): void
    {
        $ormawa = $this->createUserWithRole('ormawa', 'hima@itg.ac.id');
        $bem = $this->createUserWithRole('bem', 'bem@itg.ac.id');

        $stateSubmitted = WorkflowState::where('name', WorkflowState::SUBMITTED)->firstOrFail();

        $pengajuan = Pengajuan::create([
            'user_id' => $ormawa->id,
            'nama_kegiatan' => 'Seminar Teknologi Informasi',
            'dana_diajukan' => 1500000,
            'tanggal_pengajuan' => now()->toDateString(),
            'workflow_state_id' => $stateSubmitted->id,
        ]);

        $response = $this->actingAs($ormawa)->post(route('pengajuan.nudge', $pengajuan));

        $response->assertSessionHas('success');

        // Check pengajuan fields updated
        $pengajuan->refresh();
        $this->assertNotNull($pengajuan->terakhir_diingatkan_at);
        $this->assertEquals(1, $pengajuan->jumlah_nudge);

        // Check communication log created
        $this->assertDatabaseHas('komunikasi_pengajuans', [
            'pengajuan_id' => $pengajuan->id,
            'user_id' => $ormawa->id,
        ]);

        // Check in-app notification created for BEM
        $this->assertDatabaseHas('notifikasi', [
            'user_id' => $bem->id,
        ]);
    }

    public function test_cooldown_12_hours_prevents_spam_nudge(): void
    {
        $ormawa = $this->createUserWithRole('ormawa', 'hima2@itg.ac.id');
        $this->createUserWithRole('bem', 'bem2@itg.ac.id');

        $stateSubmitted = WorkflowState::where('name', WorkflowState::SUBMITTED)->firstOrFail();

        $pengajuan = Pengajuan::create([
            'user_id' => $ormawa->id,
            'nama_kegiatan' => 'Workshop UI/UX',
            'dana_diajukan' => 2000000,
            'tanggal_pengajuan' => now()->toDateString(),
            'workflow_state_id' => $stateSubmitted->id,
            'terakhir_diingatkan_at' => now()->subHours(2), // 2 jam yang lalu (masih dalam cooldown 12 jam)
            'jumlah_nudge' => 1,
        ]);

        $response = $this->actingAs($ormawa)->post(route('pengajuan.nudge', $pengajuan));

        $response->assertSessionHas('error');

        // Check pengajuan still has count 1
        $pengajuan->refresh();
        $this->assertEquals(1, $pengajuan->jumlah_nudge);
    }

    public function test_can_nudge_again_after_12_hours(): void
    {
        $ormawa = $this->createUserWithRole('ormawa', 'hima3@itg.ac.id');
        $this->createUserWithRole('bem', 'bem3@itg.ac.id');

        $stateSubmitted = WorkflowState::where('name', WorkflowState::SUBMITTED)->firstOrFail();

        $pengajuan = Pengajuan::create([
            'user_id' => $ormawa->id,
            'nama_kegiatan' => 'Lomba Coding Nasional',
            'dana_diajukan' => 3000000,
            'tanggal_pengajuan' => now()->toDateString(),
            'workflow_state_id' => $stateSubmitted->id,
            'terakhir_diingatkan_at' => now()->subHours(13), // 13 jam yang lalu (cooldown sudah habis)
            'jumlah_nudge' => 1,
        ]);

        $response = $this->actingAs($ormawa)->post(route('pengajuan.nudge', $pengajuan));

        $response->assertSessionHas('success');

        $pengajuan->refresh();
        $this->assertEquals(2, $pengajuan->jumlah_nudge);
    }

    public function test_unauthorized_user_cannot_send_nudge(): void
    {
        $owner = $this->createUserWithRole('ormawa', 'owner@itg.ac.id');
        $intruder = $this->createUserWithRole('ormawa', 'intruder@itg.ac.id');

        $stateSubmitted = WorkflowState::where('name', WorkflowState::SUBMITTED)->firstOrFail();

        $pengajuan = Pengajuan::create([
            'user_id' => $owner->id,
            'nama_kegiatan' => 'Kegiatan Rahasia',
            'dana_diajukan' => 1000000,
            'tanggal_pengajuan' => now()->toDateString(),
            'workflow_state_id' => $stateSubmitted->id,
        ]);

        $response = $this->actingAs($intruder)->post(route('pengajuan.nudge', $pengajuan));

        $response->assertStatus(403);
    }

    public function test_bem_author_can_nudge_bpm(): void
    {
        $bem = $this->createUserWithRole('bem', 'bempengaju@itg.ac.id');
        $bpm = $this->createUserWithRole('bpm', 'bpmpenerima@itg.ac.id');

        $stateBemApproved = WorkflowState::where('name', WorkflowState::BEM_APPROVED)->firstOrFail();

        $pengajuan = Pengajuan::create([
            'user_id' => $bem->id,
            'nama_kegiatan' => 'Festival Kampus Terpadu',
            'dana_diajukan' => 10000000,
            'tanggal_pengajuan' => now()->toDateString(),
            'workflow_state_id' => $stateBemApproved->id,
        ]);

        $response = $this->actingAs($bem)->post(route('pengajuan.nudge', $pengajuan));

        $response->assertSessionHas('success');

        $this->assertDatabaseHas('notifikasi', [
            'user_id' => $bpm->id,
        ]);
    }

    public function test_bpm_author_can_nudge_bkhm(): void
    {
        $bpm = $this->createUserWithRole('bpm', 'bpmpengaju@itg.ac.id');
        $bkhm = $this->createUserWithRole('bkhm', 'bkhmpenerima@itg.ac.id');

        $stateBpmApproved = WorkflowState::where('name', WorkflowState::BPM_APPROVED)->firstOrFail();

        $pengajuan = Pengajuan::create([
            'user_id' => $bpm->id,
            'nama_kegiatan' => 'Sidang Umum Kemahasiswaan',
            'dana_diajukan' => 5000000,
            'tanggal_pengajuan' => now()->toDateString(),
            'workflow_state_id' => $stateBpmApproved->id,
        ]);

        $response = $this->actingAs($bpm)->post(route('pengajuan.nudge', $pengajuan));

        $response->assertSessionHas('success');

        $this->assertDatabaseHas('notifikasi', [
            'user_id' => $bkhm->id,
        ]);
    }
}
