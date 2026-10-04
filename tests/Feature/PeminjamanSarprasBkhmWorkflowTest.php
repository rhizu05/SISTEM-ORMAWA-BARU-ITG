<?php

namespace Tests\Feature;

use App\Models\MasterBarang;
use App\Models\MasterRuangan;
use App\Models\PeminjamanBarang;
use App\Models\PeminjamanTempat;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

class PeminjamanSarprasBkhmWorkflowTest extends TestCase
{
    use RefreshDatabase;

    protected User $hima;
    protected User $ukm;
    protected User $bem;
    protected User $bkhm;
    protected User $sarpras;
    protected MasterRuangan $ruangan;
    protected MasterBarang $barang;

    protected function setUp(): void
    {
        parent::setUp();

        Artisan::call('db:seed', ['--class' => 'RolePermissionSeeder', '--force' => true]);

        // HIMA (Organisasi Mahasiswa Prodi)
        $this->hima = User::factory()->create([
            'name' => 'HIMA Sistem Informasi ITG',
            'username' => 'himasisfo',
            'email' => 'himasisfo@itg.ac.id',
        ]);
        $this->hima->assignRole('ormawa');

        // UKM (Organisasi Unit Kegiatan Mahasiswa)
        $this->ukm = User::factory()->create([
            'name' => 'UKM Robotika ITG',
            'username' => 'ukmrobotika',
            'email' => 'robotika@itg.ac.id',
        ]);
        $this->ukm->assignRole('ormawa');

        // BEM
        $this->bem = User::factory()->create([
            'name' => 'Badan Eksekutif Mahasiswa ITG',
            'username' => 'bem_itg',
            'email' => 'bem@itg.ac.id',
        ]);
        $this->bem->assignRole('bem');

        // BKHM
        $this->bkhm = User::factory()->create([
            'name' => 'Staf BKHM ITG',
            'username' => 'bkhm_staf',
            'email' => 'bkhm@itg.ac.id',
        ]);
        $this->bkhm->assignRole('bkhm');

        // Sarpras
        $this->sarpras = User::factory()->create([
            'name' => 'Petugas Sarpras ITG',
            'username' => 'sarpras_petugas',
            'email' => 'sarpras@itg.ac.id',
        ]);
        $this->sarpras->assignRole('sarpras');

        // Fixtures
        $this->ruangan = MasterRuangan::create([
            'nama_ruangan' => 'Aula Rektorat Lt. 3',
            'lokasi' => 'Gedung Rektorat',
            'kapasitas' => 200,
            'status_aktif' => true,
        ]);

        $this->barang = MasterBarang::create([
            'kode_barang' => 'PRJ-001',
            'nama_barang' => 'Proyektor Epson HD',
            'kategori' => 'Elektronik',
            'total_stok' => 5,
            'stok_tersedia' => 5,
            'boleh_dibawa_keluar' => true,
            'status_aktif' => true,
        ]);
    }

    /**
     * HIMA mengajukan peminjaman tempat TANPA dokumen persetujuan prodi.
     * Harus berhasil disimpan dan langsung diarahkan ke 'Proses BKHM'.
     */
    public function test_hima_submits_tempat_without_prodi_file_enters_bkhm_queue(): void
    {
        $response = $this->actingAs($this->hima)->post(route('peminjaman.tempat.store'), [
            'ruangan_id' => $this->ruangan->id,
            'nama_kegiatan' => 'Seminar Teknologi Informasi HIMA SI',
            'tgl_mulai' => '2026-10-15',
            'tgl_selesai' => '2026-10-15',
            'jam_mulai' => '08:00',
            'jam_selesai' => '12:00',
            'deskripsi_kegiatan' => 'Kegiatan seminar tahunan HIMA SI',
        ]);

        $response->assertRedirect(route('peminjaman.tempat.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('peminjaman_tempat', [
            'user_id' => $this->hima->id,
            'ruangan_id' => $this->ruangan->id,
            'nama_kegiatan' => 'Seminar Teknologi Informasi HIMA SI',
            'file_persetujuan_prodi' => null,
            'status_bkhm' => 'pending',
            'status_sarpras' => 'pending',
            'status_akhir' => 'Proses BKHM',
        ]);
    }

    /**
     * UKM mengajukan peminjaman barang TANPA dokumen persetujuan prodi.
     * Harus berhasil disimpan dan berstatus 'Proses BKHM'.
     */
    public function test_ukm_submits_barang_without_prodi_file_enters_bkhm_queue(): void
    {
        $response = $this->actingAs($this->ukm)->post(route('peminjaman.barang.store'), [
            'nama_kegiatan' => 'Workshop Robotika UKM',
            'tgl_mulai' => '2026-10-16',
            'tgl_selesai' => '2026-10-16',
            'barang_id' => [$this->barang->id],
            'qty' => [2],
        ]);

        $response->assertRedirect(route('peminjaman.barang.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('peminjaman_barang', [
            'user_id' => $this->ukm->id,
            'nama_kegiatan' => 'Workshop Robotika UKM',
            'file_persetujuan_prodi' => null,
            'status_bkhm' => 'pending',
            'status_sarpras' => 'pending',
            'status_akhir' => 'Proses BKHM',
        ]);
    }

    /**
     * Pengajuan BEM juga melalui alur yang seragam (Proses BKHM terlebih dahulu).
     */
    public function test_bem_submits_tempat_enters_bkhm_queue(): void
    {
        $response = $this->actingAs($this->bem)->post(route('peminjaman.tempat.store'), [
            'ruangan_id' => $this->ruangan->id,
            'nama_kegiatan' => 'Sidang Pleno BEM ITG',
            'tgl_mulai' => '2026-10-20',
            'tgl_selesai' => '2026-10-20',
            'jam_mulai' => '13:00',
            'jam_selesai' => '17:00',
        ]);

        $response->assertRedirect(route('peminjaman.tempat.index'));
        $this->assertDatabaseHas('peminjaman_tempat', [
            'user_id' => $this->bem->id,
            'status_bkhm' => 'pending',
            'status_sarpras' => 'pending',
            'status_akhir' => 'Proses BKHM',
        ]);
    }

    /**
     * Verifikasi alur berjenjang: BKHM menyetujui -> diteruskan ke Sarpras -> Sarpras menyetujui.
     */
    public function test_full_approval_lifecycle_from_bkhm_to_sarpras(): void
    {
        // 1. HIMA mengajukan peminjaman ruangan
        $this->actingAs($this->hima)->post(route('peminjaman.tempat.store'), [
            'ruangan_id' => $this->ruangan->id,
            'nama_kegiatan' => 'Pelatihan Coding Bersama HIMA',
            'tgl_mulai' => '2026-10-25',
            'tgl_selesai' => '2026-10-25',
            'jam_mulai' => '09:00',
            'jam_selesai' => '11:00',
        ]);

        $peminjaman = PeminjamanTempat::where('nama_kegiatan', 'Pelatihan Coding Bersama HIMA')->firstOrFail();

        // 2. Sarpras belum melihat pengajuan ini di antriannya karena belum disetujui BKHM
        $responseSarprasAwal = $this->actingAs($this->sarpras)->get(route('peminjaman.verifikasi.index'));
        $responseSarprasAwal->assertOk();
        $responseSarprasAwal->assertDontSee('Pelatihan Coding Bersama HIMA');

        // 3. BKHM melihat di antrian dan menyetujui
        $responseBkhm = $this->actingAs($this->bkhm)->get(route('peminjaman.verifikasi.index'));
        $responseBkhm->assertOk();
        $responseBkhm->assertSee('Pelatihan Coding Bersama HIMA');

        $approveBkhm = $this->actingAs($this->bkhm)->post(route('peminjaman.tempat.proses', $peminjaman), [
            'aksi' => 'setuju',
        ]);
        $approveBkhm->assertRedirect();

        $peminjaman->refresh();
        $this->assertEquals('disetujui', $peminjaman->status_bkhm);
        $this->assertEquals('pending', $peminjaman->status_sarpras);
        $this->assertEquals('Proses Sarpras', $peminjaman->status_akhir);

        // 4. Sekarang Sarpras melihat pengajuan di antrian dan menyetujui
        $responseSarpras = $this->actingAs($this->sarpras)->get(route('peminjaman.verifikasi.index'));
        $responseSarpras->assertOk();
        $responseSarpras->assertSee('Pelatihan Coding Bersama HIMA');

        $approveSarpras = $this->actingAs($this->sarpras)->post(route('peminjaman.tempat.proses', $peminjaman), [
            'aksi' => 'setuju',
        ]);
        $approveSarpras->assertRedirect();

        $peminjaman->refresh();
        $this->assertEquals('disetujui', $peminjaman->status_sarpras);
        $this->assertEquals('Selesai / Disetujui', $peminjaman->status_akhir);
    }
}
