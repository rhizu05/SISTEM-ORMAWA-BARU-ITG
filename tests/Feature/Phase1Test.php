<?php

namespace Tests\Feature;

use App\Models\Dana;
use App\Models\Pengajuan;
use App\Models\User;
use App\Models\WorkflowState;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class Phase1Test extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Artisan::call('db:seed', ['--class' => 'RolePermissionSeeder', '--force' => true]);
        Artisan::call('db:seed', ['--class' => 'WorkflowSeeder', '--force' => true]);
    }

    private function state(string $name): WorkflowState
    {
        return WorkflowState::where('name', $name)->firstOrFail();
    }

    private function pengajuan(User $user, string $stateName): Pengajuan
    {
        return Pengajuan::create([
            'user_id' => $user->id,
            'nama_kegiatan' => 'Uji Fase 1',
            'dana_diajukan' => 1000000,
            'tanggal_pengajuan' => now()->toDateString(),
            'workflow_state_id' => $this->state($stateName)->id,
        ]);
    }

    /** SEC-01: file proposal disimpan di disk privat, bukan public. */
    public function test_proposal_stored_on_private_disk(): void
    {
        Storage::fake('local');
        Storage::fake('public');

        $ormawa = User::factory()->create();
        $ormawa->assignRole('ormawa');
        $ormawa->update(['saldo' => 5000000, 'saldo_awal' => 5000000]);

        $response = $this->actingAs($ormawa)->post(route('pengajuan.store'), [
            'nama_kegiatan' => 'Kegiatan Privat',
            'dana_diajukan' => 100000,
            'tanggal_pengajuan' => now()->toDateString(),
            'file_proposal' => UploadedFile::fake()->create('proposal.pdf', 100, 'application/pdf'),
        ]);

        $response->assertRedirect(route('pengajuan.index'));

        $pengajuan = Pengajuan::latest()->first();
        $this->assertNotNull($pengajuan->file_proposal);
        Storage::disk('local')->assertExists($pengajuan->file_proposal);
        Storage::disk('public')->assertMissing($pengajuan->file_proposal);
    }

    /** SEC-01: pengguna tak berwenang tidak bisa mengunduh proposal orang lain. */
    public function test_unauthorized_user_cannot_download_proposal(): void
    {
        $owner = User::factory()->create();
        $owner->assignRole('ormawa');
        $other = User::factory()->create();
        $other->assignRole('ormawa');

        $pengajuan = $this->pengajuan($owner, 'draft');
        $pengajuan->update(['file_proposal' => 'proposals/dummy.pdf']);

        $this->actingAs($other)->get(route('dokumen.proposal', $pengajuan))->assertForbidden();
    }

    /** SEC-01: pemilik dapat mengunduh proposal sendiri. */
    public function test_owner_can_download_own_proposal(): void
    {
        Storage::fake('local');
        Storage::disk('local')->put('proposals/milik.pdf', '%PDF-1.4 dummy');

        $owner = User::factory()->create();
        $owner->assignRole('ormawa');
        $pengajuan = $this->pengajuan($owner, 'draft');
        $pengajuan->update(['file_proposal' => 'proposals/milik.pdf']);

        $this->actingAs($owner)->get(route('dokumen.proposal', $pengajuan))->assertOk();
    }

    /** Termin: pencairan pertama tercatat termin 1 dan saldo terpotong. */
    public function test_bendahara_first_disbursement_is_termin_one(): void
    {
        $ormawa = User::factory()->create();
        $ormawa->assignRole('ormawa');
        $ormawa->update(['saldo' => 5000000, 'saldo_awal' => 5000000]);

        $bendahara = User::factory()->create();
        $bendahara->assignRole('bendahara');

        $pengajuan = $this->pengajuan($ormawa, 'to_treasurer');

        $response = $this->actingAs($bendahara)->post(route('bendahara.proses', $pengajuan), [
            'nominal_cair' => 400000,
            'tanggal_cair' => now()->toDateString(),
            'catatan' => 'Termin pertama',
        ]);

        $response->assertRedirect(route('verifikasi.index'));
        $dana = Dana::where('pengajuan_id', $pengajuan->id)->firstOrFail();
        $this->assertEquals(1, $dana->termin_ke);
        $this->assertEquals(4600000, (float) $ormawa->fresh()->saldo);
    }

    /** FR-009 / PRD v3.0 BR-13: setelah revisi diajukan kembali, alur di-reset ke tahap paling awal (submitted). */
    public function test_rejected_pengajuan_resubmits_to_initial_stage(): void
    {
        $ormawa = User::factory()->create();
        $ormawa->assignRole('ormawa');

        $pengajuan = $this->pengajuan($ormawa, 'draft');
        // Simulasikan ditolak BKHM: state kembali draft, titik penolakan bpm_approved.
        $pengajuan->update([
            'workflow_state_id' => $this->state('draft')->id,
            'rejected_from_state_id' => $this->state('bpm_approved')->id,
        ]);

        $response = $this->actingAs($ormawa)->post(route('pengajuan.ajukan', $pengajuan));

        $response->assertRedirect(route('pengajuan.index'));
        $fresh = $pengajuan->fresh();
        $this->assertEquals('submitted', $fresh->state->name);
        $this->assertNull($fresh->rejected_from_state_id);
    }

    /** Termin: totalDicairkan & terminBerikutnya benar. */
    public function test_termin_helpers(): void
    {
        $ormawa = User::factory()->create();
        $ormawa->assignRole('ormawa');
        $pengajuan = $this->pengajuan($ormawa, 'funds_disbursed');
        $pengajuan->update(['file_lpj' => 'lpj/x.pdf']);

        Dana::create(['pengajuan_id' => $pengajuan->id, 'termin_ke' => 1, 'nominal_cair' => 300000, 'tanggal_cair' => now()]);
        Dana::create(['pengajuan_id' => $pengajuan->id, 'termin_ke' => 2, 'nominal_cair' => 200000, 'tanggal_cair' => now()]);

        $this->assertEquals(500000, $pengajuan->totalDicairkan());
        $this->assertEquals(3, $pengajuan->terminBerikutnya());
    }
}
