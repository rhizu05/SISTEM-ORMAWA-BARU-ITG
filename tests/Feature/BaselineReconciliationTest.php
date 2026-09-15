<?php

namespace Tests\Feature;

use App\Models\MasterBarang;
use App\Models\PeminjamanBarang;
use App\Models\Pengajuan;
use App\Models\User;
use App\Models\WorkflowState;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

class BaselineReconciliationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Artisan::call('db:seed', ['--class' => 'RolePermissionSeeder', '--force' => true]);
        Artisan::call('db:seed', ['--class' => 'WorkflowSeeder', '--force' => true]);
    }

    /** BASE-01: state yang dipakai dashboard harus ada di seeder. */
    public function test_dashboard_referenced_states_exist_in_seeder(): void
    {
        foreach ([
            WorkflowState::SUBMITTED,
            WorkflowState::BEM_APPROVED,
            WorkflowState::BPM_APPROVED,
            WorkflowState::BKHM_APPROVED,
            WorkflowState::WR3_APPROVED,
            WorkflowState::TO_TREASURER,
            WorkflowState::FUNDS_DISBURSED,
        ] as $name) {
            $this->assertTrue(WorkflowState::where('name', $name)->exists(), "State {$name} tidak ditemukan");
        }

        // State lama yang tidak pernah ada di seeder tidak boleh dirujuk lagi.
        foreach (['bem_review', 'bpm_review', 'bkhm_review', 'approved'] as $legacy) {
            $this->assertFalse(WorkflowState::where('name', $legacy)->exists(), "State legacy {$legacy} seharusnya tidak ada");
        }
    }

    /** BASE-01: dashboard BKHM menghitung antrean pada state bpm_approved. */
    public function test_bkhm_dashboard_counts_queue_from_bpm_approved(): void
    {
        $bkhm = User::factory()->create();
        $bkhm->assignRole('bkhm');

        $ormawa = User::factory()->create();
        $ormawa->assignRole('ormawa');

        $state = WorkflowState::where('name', WorkflowState::BPM_APPROVED)->firstOrFail();

        Pengajuan::create([
            'user_id' => $ormawa->id,
            'nama_kegiatan' => 'Uji Antrean BKHM',
            'dana_diajukan' => 100000,
            'tanggal_pengajuan' => now()->toDateString(),
            'workflow_state_id' => $state->id,
        ]);

        $response = $this->actingAs($bkhm)->get('/dashboard');

        $response->assertStatus(200);
        $response->assertViewHas('counts', function ($counts) {
            return ($counts['verifikasi_proposal'] ?? 0) === 1;
        });
    }

    /** BASE-06: pengembalian barang memulihkan stok. */
    public function test_returning_borrowed_item_restores_stock(): void
    {
        $sarpras = User::factory()->create();
        $sarpras->assignRole('sarpras');

        $ormawa = User::factory()->create();
        $ormawa->assignRole('ormawa');

        $barang = MasterBarang::create([
            'nama_barang' => 'Tenda Uji',
            'stok_tersedia' => 8,
            'status_aktif' => true,
        ]);

        $peminjaman = PeminjamanBarang::create([
            'user_id' => $ormawa->id,
            'nama_kegiatan' => 'Uji Pengembalian',
            'tgl_mulai' => now()->toDateString(),
            'tgl_selesai' => now()->addDay()->toDateString(),
            'kebutuhan_barang' => [
                ['id_barang' => $barang->id, 'nama_barang' => $barang->nama_barang, 'qty' => 2],
            ],
            'status_bkhm' => 'disetujui',
            'status_sarpras' => 'disetujui',
            'status_akhir' => 'Sedang Digunakan',
        ]);

        $response = $this->actingAs($sarpras)->post(route('peminjaman.barang.kembali', $peminjaman));

        $response->assertRedirect();
        $this->assertEquals(10, $barang->fresh()->stok_tersedia);
        $this->assertEquals('Dikembalikan', $peminjaman->fresh()->status_akhir);
    }
}
