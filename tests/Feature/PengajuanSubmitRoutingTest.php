<?php

namespace Tests\Feature;

use App\Models\Pengajuan;
use App\Models\User;
use App\Models\WorkflowState;
use App\Models\WorkflowTransition;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

class PengajuanSubmitRoutingTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Artisan::call('db:seed', ['--class' => 'RolePermissionSeeder', '--force' => true]);
        Artisan::call('db:seed', ['--class' => 'WorkflowSeeder', '--force' => true]);
    }

    private function makeDraftPengajuan(User $user): Pengajuan
    {
        $draft = WorkflowState::where('name', WorkflowState::DRAFT)->firstOrFail();

        return Pengajuan::create([
            'user_id' => $user->id,
            'nama_kegiatan' => 'Uji Routing Submit',
            'dana_diajukan' => 100000,
            'tanggal_pengajuan' => now()->toDateString(),
            'workflow_state_id' => $draft->id,
        ]);
    }

    /** Transisi baru dari draft untuk pengaju BEM & BPM tersedia di seeder. */
    public function test_draft_transitions_for_bem_and_bpm_exist(): void
    {
        $draft = WorkflowState::where('name', WorkflowState::DRAFT)->firstOrFail();
        $bemApproved = WorkflowState::where('name', WorkflowState::BEM_APPROVED)->firstOrFail();
        $bpmApproved = WorkflowState::where('name', WorkflowState::BPM_APPROVED)->firstOrFail();

        $this->assertTrue(
            WorkflowTransition::where('from_state_id', $draft->id)
                ->where('to_state_id', $bemApproved->id)
                ->where('required_role', 'bem')
                ->exists(),
            'Transisi draft -> bem_approved (role bem) tidak ditemukan'
        );

        $this->assertTrue(
            WorkflowTransition::where('from_state_id', $draft->id)
                ->where('to_state_id', $bpmApproved->id)
                ->where('required_role', 'bpm')
                ->exists(),
            'Transisi draft -> bpm_approved (role bpm) tidak ditemukan'
        );
    }

    public function test_ormawa_submit_goes_to_bem(): void
    {
        $ormawa = User::factory()->create();
        $ormawa->assignRole('ormawa');
        $pengajuan = $this->makeDraftPengajuan($ormawa);

        $response = $this->actingAs($ormawa)->post(route('pengajuan.ajukan', $pengajuan));

        $response->assertRedirect(route('pengajuan.index'));
        $response->assertSessionHas('success', 'Pengajuan berhasil dikirim ke BEM.');
        $this->assertEquals(WorkflowState::SUBMITTED, $pengajuan->fresh()->state->name);
    }

    public function test_bem_submit_goes_to_bpm(): void
    {
        $bem = User::factory()->create();
        $bem->assignRole('bem');
        $pengajuan = $this->makeDraftPengajuan($bem);

        $response = $this->actingAs($bem)->post(route('pengajuan.ajukan', $pengajuan));

        $response->assertRedirect(route('pengajuan.index'));
        $response->assertSessionHas('success', 'Pengajuan berhasil dikirim ke BPM.');
        $this->assertEquals(WorkflowState::BEM_APPROVED, $pengajuan->fresh()->state->name);
    }

    public function test_bpm_submit_goes_to_bkhm(): void
    {
        $bpm = User::factory()->create();
        $bpm->assignRole('bpm');
        $pengajuan = $this->makeDraftPengajuan($bpm);

        $response = $this->actingAs($bpm)->post(route('pengajuan.ajukan', $pengajuan));

        $response->assertRedirect(route('pengajuan.index'));
        $response->assertSessionHas('success', 'Pengajuan berhasil dikirim ke BKHM.');
        $this->assertEquals(WorkflowState::BPM_APPROVED, $pengajuan->fresh()->state->name);
    }
}
