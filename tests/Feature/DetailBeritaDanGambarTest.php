<?php

namespace Tests\Feature;

use App\Models\Pengumuman;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class DetailBeritaDanGambarTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Role::findOrCreate('ormawa', 'web');
        Role::findOrCreate('bem', 'web');
        Role::findOrCreate('bkhm', 'web');
        Role::findOrCreate('admin', 'web');
        Storage::fake('public');
        Storage::fake('local');
    }

    public function test_ormawa_can_upload_pengumuman_with_cover_image_into_curation_queue(): void
    {
        $ormawa = User::factory()->create(['name' => 'HIMA Informatika']);
        $ormawa->assignRole('ormawa');

        $image = UploadedFile::fake()->image('poster_seminar.jpg', 800, 600);

        $response = $this->actingAs($ormawa)->post(route('informasi.pengumuman.store'), [
            'judul' => 'Seminar Teknologi AI 2026',
            'isi' => 'Menghadirkan pembicara industri AI terkemuka untuk seluruh mahasiswa ITG.',
            'tanggal_kegiatan' => '2026-10-15',
            'gambar_sampul' => $image,
        ]);

        $response->assertRedirect(route('informasi.index'));
        $this->assertDatabaseHas('pengumuman', [
            'judul' => 'Seminar Teknologi AI 2026',
            'status' => 'pending_kurasi',
        ]);

        $pengumuman = Pengumuman::first();
        $this->assertNotNull($pengumuman->gambar_sampul);
        Storage::disk('public')->assertExists($pengumuman->gambar_sampul);
        $this->assertNotNull($pengumuman->gambar_url);
    }

    public function test_bem_can_view_curation_queue_with_image_and_approve_it(): void
    {
        $ormawa = User::factory()->create(['name' => 'UKM Robotika']);
        $ormawa->assignRole('ormawa');

        $image = UploadedFile::fake()->image('poster_lomba.png');
        $path = $image->store('pengumuman/sampul', 'public');

        $pengumuman = Pengumuman::create([
            'user_id' => $ormawa->id,
            'judul' => 'Lomba Desain Robot 2026',
            'isi' => 'Kompetisi perancangan robot tingkat internal ITG.',
            'status' => 'pending_kurasi',
            'gambar_sampul' => $path,
        ]);

        $bem = User::factory()->create(['name' => 'BEM ITG']);
        $bem->assignRole('bem');

        $curationResponse = $this->actingAs($bem)->get(route('bem.kurasi.index'));
        $curationResponse->assertStatus(200);
        $curationResponse->assertSee('Lomba Desain Robot 2026');
        $curationResponse->assertSee($pengumuman->gambar_url);

        $approveResponse = $this->actingAs($bem)->post(route('bem.kurasi.approve', $pengumuman));
        $approveResponse->assertRedirect(route('bem.kurasi.index'));

        $this->assertEquals('published', $pengumuman->fresh()->status);
        $this->assertEquals($bem->id, $pengumuman->fresh()->disetujui_oleh_id);
    }

    public function test_public_guest_can_view_published_news_detail(): void
    {
        $bkhm = User::factory()->create(['name' => 'BKHM Official']);
        $bkhm->assignRole('bkhm');

        $image = UploadedFile::fake()->image('beasiswa_banner.webp');
        $path = $image->store('pengumuman/sampul', 'public');

        $pengumuman = Pengumuman::create([
            'user_id' => $bkhm->id,
            'judul' => 'Pendaftaran Beasiswa Prestasi Semester Genap',
            'isi' => 'Dibuka pendaftaran beasiswa prestasi untuk seluruh mahasiswa ber-IPK di atas 3.50.',
            'kategori' => 'resmi_kampus',
            'status' => 'published',
            'tanggal_kegiatan' => '2026-11-01',
            'gambar_sampul' => $path,
        ]);

        $response = $this->get(route('informasi.show', $pengumuman));

        $response->assertStatus(200);
        $response->assertSee('Pendaftaran Beasiswa Prestasi Semester Genap');
        $response->assertSee($pengumuman->gambar_url);
        $response->assertSee('Dibuka pendaftaran beasiswa prestasi');
        $response->assertSee('BKHM Official');
    }

    public function test_public_guest_cannot_view_unapproved_draft_detail(): void
    {
        $ormawa = User::factory()->create();
        $ormawa->assignRole('ormawa');

        $draft = Pengumuman::create([
            'user_id' => $ormawa->id,
            'judul' => 'Draf Belum Terbit',
            'isi' => 'Konten rahasia draf kegiatan.',
            'status' => 'pending_kurasi',
        ]);

        // Tamu tanpa login tidak boleh melihat draf
        $response = $this->get(route('informasi.show', $draft));
        $response->assertStatus(404);
    }

    public function test_author_and_bem_can_preview_unapproved_draft_detail(): void
    {
        $ormawa = User::factory()->create();
        $ormawa->assignRole('ormawa');

        $draft = Pengumuman::create([
            'user_id' => $ormawa->id,
            'judul' => 'Draf Berita HIMA',
            'isi' => 'Konten draf yang dapat dipratinjau pemilik dan kurator.',
            'status' => 'pending_kurasi',
        ]);

        // Pemilik pengajuan dapat melihat pratinjau
        $authorResponse = $this->actingAs($ormawa)->get(route('informasi.show', $draft));
        $authorResponse->assertStatus(200);
        $authorResponse->assertSee('Mode Pratinjau Draf (Belum Terbit)');

        // BEM kurator dapat melihat pratinjau
        $bem = User::factory()->create();
        $bem->assignRole('bem');

        $bemResponse = $this->actingAs($bem)->get(route('informasi.show', $draft));
        $bemResponse->assertStatus(200);
        $bemResponse->assertSee('Mode Pratinjau Draf (Belum Terbit)');
    }

    public function test_deleting_news_removes_cover_image_from_storage(): void
    {
        $bkhm = User::factory()->create();
        $bkhm->assignRole('bkhm');

        $image = UploadedFile::fake()->image('hapus_banner.jpg');
        $path = $image->store('pengumuman/sampul', 'public');

        $pengumuman = Pengumuman::create([
            'user_id' => $bkhm->id,
            'judul' => 'Pengumuman Akan Dihapus',
            'isi' => 'Isi berita akan dihapus.',
            'status' => 'published',
            'gambar_sampul' => $path,
        ]);

        Storage::disk('public')->assertExists($path);

        $response = $this->actingAs($bkhm)->delete(route('informasi.pengumuman.destroy', $pengumuman));
        $response->assertRedirect(route('informasi.index'));

        $this->assertDatabaseMissing('pengumuman', ['id' => $pengumuman->id]);
        Storage::disk('public')->assertMissing($path);
    }
}