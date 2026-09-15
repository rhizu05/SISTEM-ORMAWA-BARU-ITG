<?php

namespace Tests\Feature;

use App\Models\JadwalKuliah;
use App\Models\MasterRuangan;
use App\Models\ProgramKerja;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

class Phase2Test extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Artisan::call('db:seed', ['--class' => 'RolePermissionSeeder', '--force' => true]);
        Artisan::call('db:seed', ['--class' => 'WorkflowSeeder', '--force' => true]);
    }

    /** Q-SAR-01: role sarpras tunggal menggantikan sarpras_ruangan & sarpras_barang. */
    public function test_sarpras_role_is_consolidated(): void
    {
        $this->assertTrue(\Spatie\Permission\Models\Role::where('name', 'sarpras')->exists());
        $this->assertFalse(\Spatie\Permission\Models\Role::where('name', 'sarpras_ruangan')->exists());
        $this->assertFalse(\Spatie\Permission\Models\Role::where('name', 'sarpras_barang')->exists());
    }

    /** Q-SAR-02: model JadwalKuliah mendeteksi bentrok berdasarkan hari & jam. */
    public function test_jadwal_kuliah_bentrok_detection(): void
    {
        $ruangan = MasterRuangan::create(['nama_ruangan' => 'R. Uji', 'kapasitas' => 40, 'status_aktif' => true]);

        JadwalKuliah::create([
            'ruangan_id' => $ruangan->id,
            'hari' => 1, // Senin
            'jam_mulai' => '08:00',
            'jam_selesai' => '10:00',
            'mata_kuliah' => 'Pemrograman',
            'aktif' => true,
        ]);

        // Cari Senin berikutnya.
        $senin = now()->next(\Carbon\Carbon::MONDAY)->toDateString();

        $this->assertTrue(JadwalKuliah::bentrok($ruangan->id, $senin, $senin, '09:00', '11:00'));
        $this->assertFalse(JadwalKuliah::bentrok($ruangan->id, $senin, $senin, '10:00', '12:00'));
    }

    /** Q-SAR-04: HIMA terdeteksi & wajib melampirkan persetujuan Prodi. */
    public function test_hima_requires_prodi_approval_document(): void
    {
        $hima = User::factory()->create(['name' => 'HIMA Informatika']);
        $hima->assignRole('ormawa');
        $this->assertTrue($hima->isHima());

        $nonHima = User::factory()->create(['name' => 'UKM Musik']);
        $nonHima->assignRole('ormawa');
        $this->assertFalse($nonHima->isHima());
    }

    /** FR-006: status proker diperbarui manual oleh BPM (rapat evaluasi). */
    public function test_bpm_updates_proker_status_manually(): void
    {
        $ormawa = User::factory()->create();
        $ormawa->assignRole('ormawa');

        $proker = ProgramKerja::create([
            'user_id' => $ormawa->id,
            'nama_proker' => 'Pelatihan',
            'deskripsi' => 'Deskripsi',
            'rencana_pelaksanaan' => now()->addMonth()->toDateString(),
            'status' => 'rencana',
        ]);

        $bpm = User::factory()->create();
        $bpm->assignRole('bpm');

        $response = $this->actingAs($bpm)->put(route('proker.update', $proker), [
            'status' => 'proses',
            'catatan_bpm' => 'Hasil rapat evaluasi triwulan',
        ]);

        $response->assertRedirect();
        $this->assertEquals('proses', $proker->fresh()->status);
    }
}
