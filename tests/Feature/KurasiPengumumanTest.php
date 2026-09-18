<?php

namespace Tests\Feature;

use App\Models\Pengumuman;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class KurasiPengumumanTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Artisan::call('db:seed', ['--class' => 'RolePermissionSeeder', '--force' => true]);
    }

    private function createUserWithRole(string $role, string $name = 'User Test'): User
    {
        $user = User::factory()->create(['name' => $name]);
        $user->assignRole($role);
        return $user;
    }

    public function test_bkhm_dapat_mempublikasikan_pengumuman_resmi_secara_langsung(): void
    {
        $bkhm = $this->createUserWithRole('bkhm', 'BKHM Official');

        $response = $this->actingAs($bkhm)->post(route('informasi.pengumuman.store'), [
            'judul' => 'Edaran Resmi Libur Semester',
            'isi' => 'Seluruh kegiatan akademik diliburkan mulai pekan depan.',
        ]);

        $response->assertRedirect(route('informasi.index'));
        $this->assertDatabaseHas('pengumuman', [
            'judul' => 'Edaran Resmi Libur Semester',
            'status' => 'published',
            'kategori' => 'resmi_kampus',
            'user_id' => $bkhm->id,
        ]);

        // Harus muncul di halaman publik
        $indexResponse = $this->get(route('informasi.index'));
        $indexResponse->assertOk();
        $indexResponse->assertSee('Edaran Resmi Libur Semester');
    }

    public function test_bem_dapat_mempublikasikan_agenda_kegiatan_secara_langsung(): void
    {
        $bem = $this->createUserWithRole('bem', 'Kementerian BEM');

        $response = $this->actingAs($bem)->post(route('informasi.pengumuman.store'), [
            'judul' => 'Masa Bimbingan Mahasiswa Baru 2026',
            'isi' => 'Pendaftaran Mabim resmi dibuka untuk seluruh fakultas.',
            'tanggal_kegiatan' => '2026-10-15',
        ]);

        $response->assertRedirect(route('informasi.index'));
        $this->assertDatabaseHas('pengumuman', [
            'judul' => 'Masa Bimbingan Mahasiswa Baru 2026',
            'status' => 'published',
            'kategori' => 'kegiatan_kemahasiswaan',
            'user_id' => $bem->id,
            'tanggal_kegiatan' => '2026-10-15 00:00:00',
        ]);

        // Harus muncul di halaman publik
        $indexResponse = $this->get(route('informasi.index'));
        $indexResponse->assertOk();
        $indexResponse->assertSee('Masa Bimbingan Mahasiswa Baru 2026');
    }

    public function test_ormawa_mengajukan_berita_berstatus_pending_kurasi_dan_tidak_tampil_ke_publik(): void
    {
        $ormawa = $this->createUserWithRole('ormawa', 'HIMA Informatika');
        $lainnya = $this->createUserWithRole('mahasiswa', 'Mahasiswa Umum');

        $response = $this->actingAs($ormawa)->post(route('informasi.pengumuman.store'), [
            'judul' => 'Seminar Nasional AI 2026',
            'isi' => 'HIMA Informatika menyelenggarakan seminar nasional kecerdasan buatan.',
            'tanggal_kegiatan' => '2026-11-20',
        ]);

        $response->assertRedirect(route('informasi.index'));
        $response->assertSessionHas('status', 'Pengajuan berita berhasil dikirim dan menunggu kurasi BEM sebelum diterbitkan.');

        $this->assertDatabaseHas('pengumuman', [
            'judul' => 'Seminar Nasional AI 2026',
            'status' => 'pending_kurasi',
            'kategori' => 'kegiatan_kemahasiswaan',
            'user_id' => $ormawa->id,
        ]);

        // Mahasiswa umum dan guest tidak melihat berita ini di daftar pengumuman publik
        $this->actingAs($lainnya)->get(route('informasi.index'))
            ->assertDontSee('Seminar Nasional AI 2026');

        $this->get(route('informasi.index'))
            ->assertDontSee('Seminar Nasional AI 2026');

        // Tetapi Ormawa pemilik melihatnya di tab/section pengajuan saya
        $this->actingAs($ormawa)->get(route('informasi.index'))
            ->assertSee('Seminar Nasional AI 2026')
            ->assertSee('Menunggu Kurasi BEM');
    }

    public function test_bem_dapat_melihat_antrean_kurasi_dan_role_lain_ditolak(): void
    {
        $bem = $this->createUserWithRole('bem', 'BEM Kominfo');
        $ormawa = $this->createUserWithRole('ormawa', 'UKM Musik');
        $mahasiswa = $this->createUserWithRole('mahasiswa', 'Mahasiswa Biasa');

        Pengumuman::create([
            'judul' => 'Audisi Terbuka UKM Musik',
            'isi' => 'Ayo ikuti audisi terbuka anggota baru.',
            'user_id' => $ormawa->id,
            'kategori' => 'kegiatan_kemahasiswaan',
            'status' => 'pending_kurasi',
        ]);

        // Mahasiswa biasa tidak bisa mengakses antrean kurasi
        $this->actingAs($mahasiswa)->get(route('bem.kurasi.index'))
            ->assertForbidden();

        // Ormawa juga tidak bisa mengakses halaman kurasi BEM
        $this->actingAs($ormawa)->get(route('bem.kurasi.index'))
            ->assertForbidden();

        // BEM bisa mengakses
        $bemResponse = $this->actingAs($bem)->get(route('bem.kurasi.index'));
        $bemResponse->assertOk();
        $bemResponse->assertSee('Audisi Terbuka UKM Musik');
        $bemResponse->assertSee('UKM Musik');
    }

    public function test_bem_dapat_menyetujui_pengajuan_berita_ormawa_sehingga_tayang_ke_publik(): void
    {
        $bem = $this->createUserWithRole('bem', 'BEM Approver');
        $ormawa = $this->createUserWithRole('ormawa', 'UKM Tari');

        $pengumuman = Pengumuman::create([
            'judul' => 'Pentas Seni Budaya 2026',
            'isi' => 'Penampilan spektakuler tari tradisional.',
            'user_id' => $ormawa->id,
            'kategori' => 'kegiatan_kemahasiswaan',
            'status' => 'pending_kurasi',
        ]);

        $response = $this->actingAs($bem)->post(route('bem.kurasi.approve', $pengumuman));
        $response->assertRedirect(route('bem.kurasi.index'));
        $response->assertSessionHas('status', 'Pengumuman / berita telah disetujui dan diterbitkan.');

        $this->assertDatabaseHas('pengumuman', [
            'id' => $pengumuman->id,
            'status' => 'published',
            'disetujui_oleh_id' => $bem->id,
        ]);

        // Publik sekarang dapat melihatnya
        $this->get(route('informasi.index'))
            ->assertOk()
            ->assertSee('Pentas Seni Budaya 2026')
            ->assertSee('UKM Tari');
    }

    public function test_bem_dapat_menolak_pengajuan_berita_dengan_catatan_kurasi(): void
    {
        $bem = $this->createUserWithRole('bem', 'BEM Reviewer');
        $ormawa = $this->createUserWithRole('ormawa', 'HIMA Industri');

        $pengumuman = Pengumuman::create([
            'judul' => 'Kegiatan Tanpa Detail Jelas',
            'isi' => 'Isi sangat singkat.',
            'user_id' => $ormawa->id,
            'kategori' => 'kegiatan_kemahasiswaan',
            'status' => 'pending_kurasi',
        ]);

        $response = $this->actingAs($bem)->post(route('bem.kurasi.reject', $pengumuman), [
            'catatan_kurasi' => 'Mohon sertakan pamflet acara dan rincian kontak person yang valid.',
        ]);

        $response->assertRedirect(route('bem.kurasi.index'));
        $response->assertSessionHas('status', 'Pengumuman / berita telah ditolak.');

        $this->assertDatabaseHas('pengumuman', [
            'id' => $pengumuman->id,
            'status' => 'ditolak',
            'catatan_kurasi' => 'Mohon sertakan pamflet acara dan rincian kontak person yang valid.',
            'disetujui_oleh_id' => null,
        ]);

        // Publik tidak melihat berita yang ditolak
        $this->get(route('informasi.index'))
            ->assertDontSee('Kegiatan Tanpa Detail Jelas');

        // Ormawa melihat status ditolak beserta catatan kurasi dari BEM
        $this->actingAs($ormawa)->get(route('informasi.index'))
            ->assertSee('Kegiatan Tanpa Detail Jelas')
            ->assertSee('Ditolak')
            ->assertSee('Mohon sertakan pamflet acara dan rincian kontak person yang valid.');
    }

    public function test_filter_kategori_pengumuman(): void
    {
        $bkhm = $this->createUserWithRole('bkhm');
        $bem = $this->createUserWithRole('bem');

        Pengumuman::create([
            'judul' => 'Pengumuman Beasiswa Rektor',
            'isi' => 'Pendaftaran beasiswa dibuka.',
            'user_id' => $bkhm->id,
            'kategori' => 'resmi_kampus',
            'status' => 'published',
        ]);

        Pengumuman::create([
            'judul' => 'Turnamen Futsal Kemahasiswaan',
            'isi' => 'Pendaftaran tim futsal.',
            'user_id' => $bem->id,
            'kategori' => 'kegiatan_kemahasiswaan',
            'status' => 'published',
        ]);

        // Filter resmi_kampus
        $responseResmi = $this->get(route('informasi.index', ['kategori' => 'resmi_kampus']));
        $responseResmi->assertSee('Pengumuman Beasiswa Rektor');
        $responseResmi->assertDontSee('Turnamen Futsal Kemahasiswaan');

        // Filter kegiatan_kemahasiswaan
        $responseKegiatan = $this->get(route('informasi.index', ['kategori' => 'kegiatan_kemahasiswaan']));
        $responseKegiatan->assertSee('Turnamen Futsal Kemahasiswaan');
        $responseKegiatan->assertDontSee('Pengumuman Beasiswa Rektor');
    }
}
