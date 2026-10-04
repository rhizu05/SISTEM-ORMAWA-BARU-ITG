<?php

namespace Tests\Feature;

use App\Models\Letter;
use App\Models\ProposalOtomatis;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

class LetterLpjRedirectAndPrintTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        Artisan::call('db:seed', ['--class' => 'RolePermissionSeeder', '--force' => true]);

        $this->user = User::factory()->create([
            'name' => 'HIMA Sistem Informasi',
            'username' => 'himasisfo',
            'email' => 'himasisfo@itg.ac.id',
            'nama_ketua' => 'Budi Himasisfo',
            'nim_ketua' => '2106001',
        ]);
        $this->user->assignRole('ormawa');
    }

    public function test_letters_show_redirects_to_lpj_show_when_type_is_lpj(): void
    {
        $proposal = ProposalOtomatis::create([
            'user_id' => $this->user->id,
            'nama_kegiatan' => 'Seminar Teknologi Web 2026',
            'deskripsi_kegiatan' => 'Seminar nasional',
            'latar_belakang' => 'Latar belakang seminar',
            'tujuan' => 'Tujuan seminar',
            'sasaran' => 'Mahasiswa',
            'jadwal' => '10 Oktober 2026',
            'lokasi' => 'Aula ITG',
            'total_anggaran' => 5000000,
            'status' => 'disetujui',
        ]);

        $letter = Letter::create([
            'user_id' => $this->user->id,
            'proposal_otomatis_id' => $proposal->id,
            'type' => 'lpj',
            'nomor_surat' => '005/LPJ/HIMASISFO/2026',
            'perihal' => 'Laporan Pertanggungjawaban (LPJ) - Seminar Teknologi Web 2026',
            'content' => json_encode(['tahun_akademik' => '2026-2027']),
            'metadata' => [
                'proposal_id' => $proposal->id,
                'tahun_akademik' => '2026-2027',
            ],
        ]);

        $response = $this->actingAs($this->user)->get(route('generator.letters.show', $letter));
        $response->assertRedirect(route('generator.lpj.show', $letter));
    }

    public function test_letters_pdf_redirects_to_lpj_pdf_when_type_is_lpj(): void
    {
        $letter = Letter::create([
            'user_id' => $this->user->id,
            'type' => 'lpj',
            'nomor_surat' => '005/LPJ/HIMASISFO/2026',
            'perihal' => 'Laporan Pertanggungjawaban (LPJ) - Seminar Teknologi Web 2026',
            'content' => json_encode(['tahun_akademik' => '2026-2027']),
            'metadata' => [
                'tahun_akademik' => '2026-2027',
            ],
        ]);

        $response = $this->actingAs($this->user)->get(route('generator.letters.pdf', $letter));
        $response->assertRedirect(route('generator.lpj.pdf', $letter));
    }

    public function test_lpj_show_view_contains_media_print_styles_and_classes(): void
    {
        $proposal = ProposalOtomatis::create([
            'user_id' => $this->user->id,
            'nama_kegiatan' => 'Seminar Teknologi Web 2026',
            'deskripsi_kegiatan' => 'Seminar nasional',
            'latar_belakang' => 'Latar belakang seminar',
            'tujuan' => 'Tujuan seminar',
            'sasaran' => 'Mahasiswa',
            'jadwal' => '10 Oktober 2026',
            'lokasi' => 'Aula ITG',
            'total_anggaran' => 5000000,
            'status' => 'disetujui',
        ]);

        $letter = Letter::create([
            'user_id' => $this->user->id,
            'proposal_otomatis_id' => $proposal->id,
            'type' => 'lpj',
            'nomor_surat' => '005/LPJ/HIMASISFO/2026',
            'perihal' => 'Laporan Pertanggungjawaban (LPJ) - Seminar Teknologi Web 2026',
            'content' => json_encode(['tahun_akademik' => '2026-2027']),
            'metadata' => [
                'proposal_id' => $proposal->id,
                'tahun_akademik' => '2026-2027',
            ],
        ]);

        $response = $this->actingAs($this->user)->get(route('generator.lpj.show', $letter));
        $response->assertStatus(200);
        $response->assertSee('@media print', false);
        $response->assertSee('lpj-sheet', false);
        $response->assertSee('no-print', false);
        $response->assertSee('Cetak Laporan', false);
    }

    public function test_archive_view_links_lpj_to_generator_lpj_show(): void
    {
        $proposal = ProposalOtomatis::create([
            'user_id' => $this->user->id,
            'nama_kegiatan' => 'Seminar Teknologi Web 2026',
            'deskripsi_kegiatan' => 'Seminar nasional',
            'latar_belakang' => 'Latar belakang seminar',
            'tujuan' => 'Tujuan seminar',
            'sasaran' => 'Mahasiswa',
            'jadwal' => '10 Oktober 2026',
            'lokasi' => 'Aula ITG',
            'total_anggaran' => 5000000,
            'status' => 'disetujui',
        ]);

        $letter = Letter::create([
            'user_id' => $this->user->id,
            'proposal_otomatis_id' => $proposal->id,
            'type' => 'lpj',
            'nomor_surat' => '005/LPJ/HIMASISFO/2026',
            'perihal' => 'Laporan Pertanggungjawaban (LPJ) - Seminar Teknologi Web 2026',
            'content' => json_encode(['tahun_akademik' => '2026-2027']),
            'metadata' => [
                'proposal_id' => $proposal->id,
                'tahun_akademik' => '2026-2027',
            ],
        ]);

        $response = $this->actingAs($this->user)->get(route('archive.index'));
        $response->assertStatus(200);
        $response->assertSee(route('generator.lpj.show', $letter), false);
        $response->assertSee('LAPORAN (LPJ)', false);
        $response->assertSee('Lihat LPJ', false);
    }
}
