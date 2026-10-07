<?php
namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\MasterRuangan;
use App\Models\MasterBarang;
use App\Models\PeminjamanTempat;
use App\Models\PeminjamanBarang;
use App\Models\JadwalKuliah;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Artisan;

class SarprasNegativeAndConflictE2ETest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Artisan::call('db:seed', ['--class' => 'RolePermissionSeeder', '--force' => true]);
        Artisan::call('db:seed', ['--class' => 'WorkflowSeeder', '--force' => true]);
        Artisan::call('db:seed', ['--class' => 'MasterDataSeeder', '--force' => true]);
    }

    private function createUser(string $role, string $name = 'User Test'): User
    {
        $user = User::factory()->create([
            'name' => $name,
        ]);
        $user->assignRole($role);
        return $user;
    }

    /** Skenario 1: Bentrok jadwal perkuliahan rutin mingguan (Q-SAR-02) */
    public function test_bentrok_jadwal_perkuliahan_rutin_ditolak(): void
    {
        $hima = $this->createUser('ormawa', 'HIMA Informatika', true);
        $ruangan = MasterRuangan::where('nama_ruangan', 'Ruang Kelas A101')->first();
        $file = UploadedFile::fake()->create('persetujuan_prodi.pdf', 100, 'application/pdf');

        // Jadwal kuliah di Ruang Kelas A101 hari Senin (1) jam 08:00 - 10:30
        // Coba pinjam di hari Senin mendatang jam 09:00 - 11:00 (beririsan)
        $seninDepan = now()->next(\Carbon\Carbon::MONDAY)->toDateString();
        $response = $this->actingAs($hima)->post(route('peminjaman.tempat.store'), [
            'ruangan_id' => $ruangan->id,
            'nama_kegiatan' => 'Kegiatan Bentrok Jadwal Kuliah',
            'tgl_mulai' => $seninDepan,
            'tgl_selesai' => $seninDepan,
            'jam_mulai' => '09:00',
            'jam_selesai' => '11:00',
            'file_persetujuan_prodi' => $file,
        ]);

        $response->assertSessionHas('error', 'Waktu yang dipilih bentrok dengan jadwal perkuliahan di ruangan tersebut.');
        $this->assertDatabaseMissing('peminjaman_tempat', [
            'nama_kegiatan' => 'Kegiatan Bentrok Jadwal Kuliah',
        ]);
    }

    /** Skenario 2: Bentrok peminjaman ruangan ganda pada waktu beririsan (Double Booking) */
    public function test_bentrok_peminjaman_ruangan_ganda_ditolak(): void
    {
        $bem = $this->createUser('bem', 'BEM ITG');
        $hima = $this->createUser('ormawa', 'HIMA Informatika', true);
        $ruangan = MasterRuangan::where('nama_ruangan', 'Aula Gedung Rektorat')->first();
        $file = UploadedFile::fake()->create('persetujuan_prodi.pdf', 100, 'application/pdf');

        $tglBooking = now()->addDays(5)->toDateString();
        // Buat booking awal oleh BEM
        PeminjamanTempat::create([
            'user_id' => $bem->id,
            'ruangan_id' => $ruangan->id,
            'nama_kegiatan' => 'Booking Awal BEM',
            'tgl_mulai' => $tglBooking,
            'tgl_selesai' => $tglBooking,
            'jam_mulai' => '13:00:00',
            'jam_selesai' => '16:00:00',
            'status_bkhm' => 'disetujui',
            'status_sarpras' => 'disetujui',
            'status_akhir' => 'Selesai / Disetujui',
        ]);

        // HIMA mencoba booking pada jam 14:00 - 17:00 (overlap)
        $response = $this->actingAs($hima)->post(route('peminjaman.tempat.store'), [
            'ruangan_id' => $ruangan->id,
            'nama_kegiatan' => 'Booking Ganda HIMA',
            'tgl_mulai' => $tglBooking,
            'tgl_selesai' => $tglBooking,
            'jam_mulai' => '14:00',
            'jam_selesai' => '17:00',
            'file_persetujuan_prodi' => $file,
        ]);

        $response->assertSessionHas('error', 'Ruangan sudah dibooking pada tanggal/waktu tersebut.');
        $this->assertDatabaseMissing('peminjaman_tempat', [
            'nama_kegiatan' => 'Booking Ganda HIMA',
        ]);
    }

    /** Skenario 3: Penolakan peminjaman ruangan oleh Sarpras beserta catatan */
    public function test_penolakan_ruangan_oleh_sarpras(): void
    {
        $bem = $this->createUser('bem', 'BEM ITG');
        $sarpras = $this->createUser('sarpras', 'Sarpras Kampus');
        $ruangan = MasterRuangan::first();

        $tglTolak = now()->addDays(7)->toDateString();
        $peminjaman = PeminjamanTempat::create([
            'user_id' => $bem->id,
            'ruangan_id' => $ruangan->id,
            'nama_kegiatan' => 'Permohonan Ruangan Uji Tolak',
            'tgl_mulai' => $tglTolak,
            'tgl_selesai' => $tglTolak,
            'jam_mulai' => '08:00:00',
            'jam_selesai' => '12:00:00',
            'status_bkhm' => 'disetujui',
            'status_sarpras' => 'pending',
            'status_akhir' => 'Proses Sarpras',
        ]);

        $response = $this->actingAs($sarpras)->post(route('peminjaman.tempat.proses', $peminjaman), [
            'aksi' => 'tolak',
            'catatan' => 'Ruangan sedang dalam persiapan gladi bersih wisuda kampus.',
        ]);

        $response->assertSessionHas('success');
        $fresh = $peminjaman->fresh();
        $this->assertEquals('ditolak', $fresh->status_sarpras);
        $this->assertEquals('Ditolak Sarpras', $fresh->status_akhir);
        $this->assertEquals('Ruangan sedang dalam persiapan gladi bersih wisuda kampus.', $fresh->catatan_penolakan);
    }

    /** Skenario 4: Peminjaman barang melebihi stok tersedia dilarang */
    public function test_peminjaman_barang_melebihi_stok_tersedia_ditolak(): void
    {
        $bem = $this->createUser('bem', 'BEM ITG');
        $barang = MasterBarang::where('nama_barang', 'Proyektor EPSON')->first(); // Stok: 5, boleh_dibawa_keluar: true
        $tglBarang = now()->addDays(10)->toDateString();

        $response = $this->actingAs($bem)->post(route('peminjaman.barang.store'), [
            'nama_kegiatan' => 'Kegiatan Pinjam Proyektor Berlebih',
            'tgl_mulai' => $tglBarang,
            'tgl_selesai' => $tglBarang,
            'barang_id' => [$barang->id],
            'qty' => [15], // Meminjam 15 padahal stok hanya 5
        ]);

        $response->assertSessionHas('error', 'Stok untuk barang Proyektor EPSON tidak mencukupi.');
        $this->assertDatabaseMissing('peminjaman_barang', [
            'nama_kegiatan' => 'Kegiatan Pinjam Proyektor Berlebih',
        ]);
    }

    /** Skenario 5: Peminjaman barang yang tidak boleh dibawa keluar kampus dilarang */
    public function test_peminjaman_barang_tidak_boleh_keluar_kampus_ditolak(): void
    {
        $bem = $this->createUser('bem', 'BEM ITG');
        // Kursi lipat memiliki boleh_dibawa_keluar = false
        $barang = MasterBarang::where('nama_barang', 'Kursi Lipat')->first();
        $tglKursi = now()->addDays(10)->toDateString();

        $response = $this->actingAs($bem)->post(route('peminjaman.barang.store'), [
            'nama_kegiatan' => 'Kegiatan Bawa Kursi Keluar',
            'tgl_mulai' => $tglKursi,
            'tgl_selesai' => $tglKursi,
            'barang_id' => [$barang->id],
            'qty' => [5],
        ]);

        $response->assertSessionHas('error', 'Barang Kursi Lipat tidak boleh dibawa keluar kampus.');
        $this->assertDatabaseMissing('peminjaman_barang', [
            'nama_kegiatan' => 'Kegiatan Bawa Kursi Keluar',
        ]);
    }

    /** Skenario 6: Penolakan peminjaman barang oleh Sarpras menjaga stok tidak berkurang */
    public function test_penolakan_barang_oleh_sarpras_tidak_mengurangi_stok(): void
    {
        $bem = $this->createUser('bem', 'BEM ITG');
        $sarpras = $this->createUser('sarpras', 'Sarpras Kampus');
        $barang = MasterBarang::where('nama_barang', 'Proyektor EPSON')->first();
        $stokAwal = $barang->stok_tersedia;
        $tglPinjam = now()->addDays(12)->toDateString();

        $peminjaman = PeminjamanBarang::create([
            'user_id' => $bem->id,
            'nama_kegiatan' => 'Permohonan Proyektor Uji Tolak',
            'tgl_mulai' => $tglPinjam,
            'tgl_selesai' => $tglPinjam,
            'kebutuhan_barang' => [
                ['id_barang' => $barang->id, 'nama_barang' => $barang->nama_barang, 'qty' => 2]
            ],
            'status_bkhm' => 'disetujui',
            'status_sarpras' => 'pending',
            'status_akhir' => 'Proses Sarpras',
        ]);

        $response = $this->actingAs($sarpras)->post(route('peminjaman.barang.proses', $peminjaman), [
            'aksi' => 'tolak',
            'catatan' => 'Peralatan sedang dalam masa pemeliharaan berkala.',
        ]);

        $response->assertSessionHas('success');
        $fresh = $peminjaman->fresh();
        $this->assertEquals('ditolak', $fresh->status_sarpras);
        $this->assertEquals('Ditolak Sarpras', $fresh->status_akhir);
        $this->assertEquals('Peralatan sedang dalam masa pemeliharaan berkala.', $fresh->catatan_penolakan);

        // Stok harus tetap sama
        $this->assertEquals($stokAwal, $barang->fresh()->stok_tersedia);
    }
}
