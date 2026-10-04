<?php

namespace Tests\Feature;

use App\Models\HistoriStatus;
use App\Models\Notifikasi;
use App\Models\Pengajuan;
use App\Models\User;
use App\Models\WorkflowState;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PengajuanPembatalanTest extends TestCase
{
    use RefreshDatabase;

    private User $ormawa;
    private User $otherOrmawa;
    private User $bem;

    protected function setUp(): void
    {
        parent::setUp();

        \Illuminate\Support\Facades\Artisan::call('db:seed', ['--class' => 'RolePermissionSeeder', '--force' => true]);
        \Illuminate\Support\Facades\Artisan::call('db:seed', ['--class' => 'WorkflowSeeder', '--force' => true]);

        $this->ormawa = User::factory()->create([
            'name' => 'Himasisfo ITG',
            'username' => 'himasisfo',
            'email' => 'himasisfo@test.com',
            'saldo' => 10000000,
        ]);
        $this->ormawa->assignRole('ormawa');

        $this->otherOrmawa = User::factory()->create([
            'name' => 'HIMA IF',
            'username' => 'himaif',
            'email' => 'himaif@test.com',
            'saldo' => 10000000,
        ]);
        $this->otherOrmawa->assignRole('ormawa');

        $this->bem = User::factory()->create([
            'name' => 'BEM ITG',
            'username' => 'bemitg',
            'email' => 'bem@test.com',
        ]);
        $this->bem->assignRole('bem');
    }

    public function test_ormawa_bisa_menghapus_draft_proposal_dan_file_fisik(): void
    {
        Storage::fake('local');
        $fakePath = 'proposals/test-draft-' . time() . '.pdf';
        Storage::disk('local')->put($fakePath, 'fake-pdf-content');

        $draftState = WorkflowState::where('name', WorkflowState::DRAFT)->firstOrFail();

        $pengajuan = Pengajuan::create([
            'user_id' => $this->ormawa->id,
            'nama_kegiatan' => 'Draft Kegiatan Akan Dihapus',
            'dana_diajukan' => 1500000,
            'tanggal_pengajuan' => now()->toDateString(),
            'file_proposal' => $fakePath,
            'workflow_state_id' => $draftState->id,
            'unique_code' => 'TESTDEL01',
        ]);

        HistoriStatus::create([
            'pengajuan_id' => $pengajuan->id,
            'user_id' => $this->ormawa->id,
            'workflow_state_id' => $draftState->id,
            'catatan' => 'Draft dibuat',
        ]);

        $this->assertDatabaseHas('pengajuan', ['id' => $pengajuan->id]);
        $this->assertTrue(Storage::disk('local')->exists($fakePath));

        $response = $this->actingAs($this->ormawa)->delete(route('pengajuan.destroy', $pengajuan));

        $response->assertRedirect(route('pengajuan.index'));
        $response->assertSessionHas('success', 'Draft proposal berhasil dihapus.');

        $this->assertDatabaseMissing('pengajuan', ['id' => $pengajuan->id]);
        $this->assertDatabaseMissing('histori_status', ['pengajuan_id' => $pengajuan->id]);
        $this->assertFalse(Storage::disk('local')->exists($fakePath));
    }

    public function test_user_lain_tidak_bisa_menghapus_draft_ormawa(): void
    {
        $draftState = WorkflowState::where('name', WorkflowState::DRAFT)->firstOrFail();

        $pengajuan = Pengajuan::create([
            'user_id' => $this->ormawa->id,
            'nama_kegiatan' => 'Draft Milik Himasisfo',
            'dana_diajukan' => 1000000,
            'tanggal_pengajuan' => now()->toDateString(),
            'file_proposal' => null,
            'workflow_state_id' => $draftState->id,
            'unique_code' => 'TESTDEL02',
        ]);

        $response = $this->actingAs($this->otherOrmawa)->delete(route('pengajuan.destroy', $pengajuan));
        $response->assertForbidden();

        $this->assertDatabaseHas('pengajuan', ['id' => $pengajuan->id]);
    }

    public function test_tidak_bisa_menghapus_proposal_yang_sudah_bukan_draft(): void
    {
        $submittedState = WorkflowState::where('name', WorkflowState::SUBMITTED)->firstOrFail();

        $pengajuan = Pengajuan::create([
            'user_id' => $this->ormawa->id,
            'nama_kegiatan' => 'Proposal Sudah Masuk BEM',
            'dana_diajukan' => 2000000,
            'tanggal_pengajuan' => now()->toDateString(),
            'file_proposal' => null,
            'workflow_state_id' => $submittedState->id,
            'unique_code' => 'TESTDEL03',
        ]);

        $response = $this->actingAs($this->ormawa)->delete(route('pengajuan.destroy', $pengajuan));
        $response->assertRedirect(route('pengajuan.index'));
        $response->assertSessionHas('error');

        $this->assertDatabaseHas('pengajuan', ['id' => $pengajuan->id]);
    }

    public function test_ormawa_bisa_membatalkan_proposal_yang_sedang_diverifikasi_dengan_alasan(): void
    {
        $submittedState = WorkflowState::where('name', WorkflowState::SUBMITTED)->firstOrFail();

        $pengajuan = Pengajuan::create([
            'user_id' => $this->ormawa->id,
            'nama_kegiatan' => 'Seminar AI Batal Digelar',
            'dana_diajukan' => 2500000,
            'tanggal_pengajuan' => now()->toDateString(),
            'file_proposal' => null,
            'workflow_state_id' => $submittedState->id,
            'unique_code' => 'TESTCAN01',
        ]);

        $response = $this->actingAs($this->ormawa)->post(route('pengajuan.batalkan', $pengajuan), [
            'alasan' => 'Kegiatan dibatalkan oleh kepanitiaan karena pemateri berhalangan hadir.',
        ]);

        $response->assertRedirect(route('pengajuan.index'));
        $response->assertSessionHas('success', 'Pengajuan proposal berhasil dibatalkan.');

        $pengajuan->refresh();
        $this->assertEquals(WorkflowState::CANCELLED, $pengajuan->state->name);

        $this->assertDatabaseHas('histori_status', [
            'pengajuan_id' => $pengajuan->id,
            'user_id' => $this->ormawa->id,
            'workflow_state_id' => $pengajuan->workflow_state_id,
        ]);

        $latestHistori = $pengajuan->histori()->latest()->first();
        $this->assertStringContainsString('karena pemateri berhalangan hadir', $latestHistori->catatan);
    }

    public function test_pembatalan_wajib_menyertakan_alasan_minimal_5_karakter(): void
    {
        $submittedState = WorkflowState::where('name', WorkflowState::SUBMITTED)->firstOrFail();

        $pengajuan = Pengajuan::create([
            'user_id' => $this->ormawa->id,
            'nama_kegiatan' => 'Lomba Desain Grafis',
            'dana_diajukan' => 1500000,
            'tanggal_pengajuan' => now()->toDateString(),
            'file_proposal' => null,
            'workflow_state_id' => $submittedState->id,
            'unique_code' => 'TESTCAN02',
        ]);

        $response = $this->actingAs($this->ormawa)->post(route('pengajuan.batalkan', $pengajuan), [
            'alasan' => 'abc', // Kurang dari 5 karakter
        ]);

        $response->assertSessionHasErrors('alasan');
        $pengajuan->refresh();
        $this->assertEquals(WorkflowState::SUBMITTED, $pengajuan->state->name);
    }

    public function test_tidak_bisa_membatalkan_proposal_yang_dananya_sudah_cair(): void
    {
        $fundsState = WorkflowState::where('name', WorkflowState::FUNDS_DISBURSED)->firstOrFail();

        $pengajuan = Pengajuan::create([
            'user_id' => $this->ormawa->id,
            'nama_kegiatan' => 'Kegiatan Dana Sudah Cair',
            'dana_diajukan' => 3000000,
            'tanggal_pengajuan' => now()->toDateString(),
            'file_proposal' => null,
            'workflow_state_id' => $fundsState->id,
            'unique_code' => 'TESTCAN03',
        ]);

        $response = $this->actingAs($this->ormawa)->post(route('pengajuan.batalkan', $pengajuan), [
            'alasan' => 'Ingin dibatalkan padahal uang sudah ditransfer',
        ]);

        $response->assertRedirect(route('pengajuan.show', $pengajuan));
        $response->assertSessionHas('error');

        $pengajuan->refresh();
        $this->assertEquals(WorkflowState::FUNDS_DISBURSED, $pengajuan->state->name);
    }

    public function test_proposal_dibatalkan_tidak_memblokir_pengajuan_proposal_berikutnya(): void
    {
        $cancelledState = WorkflowState::where('name', WorkflowState::CANCELLED)->firstOrFail();
        $draftState = WorkflowState::where('name', WorkflowState::DRAFT)->firstOrFail();

        // 1. Buat proposal pertama yang statusnya CANCELLED
        $proposalLama = Pengajuan::create([
            'user_id' => $this->ormawa->id,
            'nama_kegiatan' => 'Proposal Lama Yang Dibatalkan',
            'dana_diajukan' => 1000000,
            'tanggal_pengajuan' => now()->toDateString(),
            'file_proposal' => null,
            'workflow_state_id' => $cancelledState->id,
            'unique_code' => 'TESTCAN04',
        ]);

        // 2. Buat proposal baru berstatus draft
        $proposalBaru = Pengajuan::create([
            'user_id' => $this->ormawa->id,
            'nama_kegiatan' => 'Proposal Baru Yang Sah',
            'dana_diajukan' => 1500000,
            'tanggal_pengajuan' => now()->toDateString(),
            'file_proposal' => null,
            'workflow_state_id' => $draftState->id,
            'unique_code' => 'TESTCAN05',
        ]);

        // 3. Ajukan proposal baru -> Tidak boleh ada error blocking!
        $response = $this->actingAs($this->ormawa)->post(route('pengajuan.ajukan', $proposalBaru));
        $response->assertRedirect(route('pengajuan.index'));
        $response->assertSessionHas('success');

        $proposalBaru->refresh();
        $this->assertEquals(WorkflowState::SUBMITTED, $proposalBaru->state->name);
    }
}
