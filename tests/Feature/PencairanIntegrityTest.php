<?php

namespace Tests\Feature;

use App\Models\Dana;
use App\Models\Pengajuan;
use App\Models\PeriodeAnggaran;
use App\Models\SaldoHistori;
use App\Models\User;
use App\Models\WorkflowState;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class PencairanIntegrityTest extends TestCase
{
    use RefreshDatabase;

    protected User $ormawa;
    protected User $bendahara;
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

        $this->ormawa = User::factory()->create([
            'saldo' => 5000000,
            'saldo_awal' => 5000000,
        ]);
        $this->ormawa->assignRole('ormawa');

        $this->bendahara = User::factory()->create();
        $this->bendahara->assignRole('bendahara');
    }

    private function state(string $name): WorkflowState
    {
        return WorkflowState::where('name', $name)->firstOrFail();
    }

    private function buatPengajuan(int $nominal = 1200000, string $stateName = 'to_treasurer'): Pengajuan
    {
        return Pengajuan::create([
            'user_id' => $this->ormawa->id,
            'nama_kegiatan' => 'Kegiatan Test Pencairan',
            'dana_diajukan' => $nominal,
            'tanggal_pengajuan' => now()->toDateString(),
            'workflow_state_id' => $this->state($stateName)->id,
        ]);
    }

    /** TC-DBL-001: Baseline satu pencairan valid */
    public function test_baseline_satu_pencairan_valid(): void
    {
        $pengajuan = $this->buatPengajuan(1200000);

        $response = $this->actingAs($this->bendahara)->post(route('bendahara.proses', $pengajuan), [
            'nominal_cair' => 400000,
            'tanggal_cair' => now()->toDateString(),
            'catatan' => 'Pencairan termin 1',
        ]);

        $response->assertRedirect(route('verifikasi.index'));
        $this->assertDatabaseHas('dana', [
            'pengajuan_id' => $pengajuan->id,
            'termin_ke' => 1,
            'nominal_cair' => 400000,
        ]);

        $this->assertEquals(4600000, $this->ormawa->fresh()->saldo);
        $this->assertEquals('funds_disbursed', $pengajuan->fresh()->state->name);
        $this->assertDatabaseHas('saldo_histori', [
            'user_id' => $this->ormawa->id,
            'tipe' => 'pencairan',
            'nominal_sebelum' => 5000000,
            'nominal_sesudah' => 4600000,
            'selisih' => -400000,
        ]);
    }

    /** TC-DBL-002: Dua panggilan pencairan berurutan (invarian idempoten) */
    public function test_dua_panggilan_pencairan_berurutan_hanya_cair_sekali(): void
    {
        $pengajuan = $this->buatPengajuan(1200000);

        // Request 1: Valid
        $res1 = $this->actingAs($this->bendahara)->post(route('bendahara.proses', $pengajuan), [
            'nominal_cair' => 400000,
            'tanggal_cair' => now()->toDateString(),
            'catatan' => 'Pencairan termin 1',
        ]);
        $res1->assertRedirect(route('verifikasi.index'));

        // Request 2: Duplikat berurutan
        $res2 = $this->actingAs($this->bendahara)->post(route('bendahara.proses', $pengajuan), [
            'nominal_cair' => 400000,
            'tanggal_cair' => now()->toDateString(),
            'catatan' => 'Pencairan termin 1 repeat',
        ]);

        $res2->assertSessionHas('error');
        $this->assertEquals(1, Dana::where('pengajuan_id', $pengajuan->id)->count());
        $this->assertEquals(1, SaldoHistori::where('user_id', $this->ormawa->id)->where('tipe', 'pencairan')->count());
        $this->assertEquals(4600000, $this->ormawa->fresh()->saldo);
    }

    /** TC-DBL-003: Unique constraint pada tabel dana (pengajuan_id, termin_ke) */
    public function test_unique_constraint_pada_tabel_dana(): void
    {
        $pengajuan = $this->buatPengajuan(1200000);

        Dana::create([
            'pengajuan_id' => $pengajuan->id,
            'termin_ke' => 1,
            'nominal_cair' => 400000,
            'tanggal_cair' => now()->toDateString(),
        ]);

        $this->expectException(\Illuminate\Database\QueryException::class);

        Dana::create([
            'pengajuan_id' => $pengajuan->id,
            'termin_ke' => 1,
            'nominal_cair' => 400000,
            'tanggal_cair' => now()->toDateString(),
        ]);
    }

    /** TC-DBL-004: Nominal melebihi dana diajukan ditolak */
    public function test_nominal_melebihi_dana_diajukan_ditolak(): void
    {
        $pengajuan = $this->buatPengajuan(1200000);

        $response = $this->actingAs($this->bendahara)->post(route('bendahara.proses', $pengajuan), [
            'nominal_cair' => 1200001,
            'tanggal_cair' => now()->toDateString(),
        ]);

        $response->assertSessionHasErrors(['nominal_cair']);
        $this->assertEquals(0, Dana::where('pengajuan_id', $pengajuan->id)->count());
        $this->assertEquals(5000000, $this->ormawa->fresh()->saldo);
    }

    /** TC-DBL-005: Nominal melebihi saldo pengaju ditolak */
    public function test_nominal_melebihi_saldo_pengaju_ditolak(): void
    {
        // Ormawa saldo hanya 500.000, pengajuan 1.000.000
        $this->ormawa->update(['saldo' => 500000]);
        $pengajuan = $this->buatPengajuan(1000000);

        $response = $this->actingAs($this->bendahara)->post(route('bendahara.proses', $pengajuan), [
            'nominal_cair' => 600000,
            'tanggal_cair' => now()->toDateString(),
        ]);

        $response->assertSessionHasErrors(['nominal_cair']);
        $this->assertEquals(0, Dana::where('pengajuan_id', $pengajuan->id)->count());
        $this->assertEquals(500000, $this->ormawa->fresh()->saldo);
    }

    /** TC-DBL-006: Pencairan bernilai nol atau negatif ditolak */
    public function test_pencairan_bernilai_nol_atau_negatif_ditolak(): void
    {
        $pengajuan = $this->buatPengajuan(1200000);

        $resNol = $this->actingAs($this->bendahara)->post(route('bendahara.proses', $pengajuan), [
            'nominal_cair' => 0,
            'tanggal_cair' => now()->toDateString(),
        ]);
        $resNol->assertSessionHasErrors(['nominal_cair']);

        $resNegatif = $this->actingAs($this->bendahara)->post(route('bendahara.proses', $pengajuan), [
            'nominal_cair' => -10000,
            'tanggal_cair' => now()->toDateString(),
        ]);
        $resNegatif->assertSessionHasErrors(['nominal_cair']);

        $this->assertEquals(0, Dana::where('pengajuan_id', $pengajuan->id)->count());
        $this->assertEquals(5000000, $this->ormawa->fresh()->saldo);
    }

    /** TC-DBL-008: Termin 2 setelah LPJ termin 1 */
    public function test_termin_dua_setelah_lpj_termin_satu(): void
    {
        $pengajuan = $this->buatPengajuan(1200000);

        // Termin 1
        $this->actingAs($this->bendahara)->post(route('bendahara.proses', $pengajuan), [
            'nominal_cair' => 400000,
            'tanggal_cair' => now()->toDateString(),
        ]);

        // Simulasikan LPJ diunggah, evaluasi termin OK, dan status dikembalikan ke to_treasurer untuk termin 2
        $pengajuan->refresh();
        $pengajuan->update([
            'file_lpj' => 'lpj/test.pdf',
            'evaluasi_termin_ok' => true,
            'workflow_state_id' => $this->state('to_treasurer')->id,
        ]);

        // Termin 2
        $response = $this->actingAs($this->bendahara)->post(route('bendahara.proses', $pengajuan), [
            'nominal_cair' => 500000,
            'tanggal_cair' => now()->toDateString(),
        ]);

        $response->assertRedirect(route('verifikasi.index'));
        $this->assertEquals(2, Dana::where('pengajuan_id', $pengajuan->id)->count());
        $this->assertDatabaseHas('dana', [
            'pengajuan_id' => $pengajuan->id,
            'termin_ke' => 2,
            'nominal_cair' => 500000,
        ]);
        // Saldo ormawa: 5.000.000 - 400.000 - 500.000 = 4.100.000
        $this->assertEquals(4100000, $this->ormawa->fresh()->saldo);
        // Evaluasi termin direset false
        $this->assertFalse((bool) $pengajuan->fresh()->evaluasi_termin_ok);
    }
}
