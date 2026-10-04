<?php

namespace Tests\Feature;

use App\Models\Dana;
use App\Models\HistoriStatus;
use App\Models\Pengajuan;
use App\Models\TandaTanganDigital;
use App\Models\User;
use App\Models\WorkflowState;
use App\Models\WorkflowTransition;
use App\Services\DigitalSignatureService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AlurLpjLegalisirTtdDigitalTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Artisan::call('db:seed', ['--class' => 'RolePermissionSeeder', '--force' => true]);
        Artisan::call('db:seed', ['--class' => 'WorkflowSeeder', '--force' => true]);
        Storage::fake('local');
    }

    private function createUser(string $role, array $attributes = []): User
    {
        $user = User::factory()->create(array_merge([
            'saldo' => 10000000,
            'saldo_awal' => 10000000,
        ], $attributes));
        $user->assignRole($role);
        return $user;
    }

    public function test_alur_lengkap_lpj_dari_ormawa_ke_bkhm_lalu_wr3_dengan_legalisir_ttd_digital()
    {
        $ormawa = $this->createUser('ormawa', [
            'name' => 'HIMA Informatika',
            'nama_ketua' => 'Ahmad Fauzi',
            'nim_ketua' => '2106001',
        ]);
        $bkhm = $this->createUser('bkhm', ['name' => 'Encep Jianul Hayat']);
        $wr3 = $this->createUser('wr3', ['name' => 'Dr. Ayu Latifah']);

        $stateFundsDisbursed = WorkflowState::where('name', 'funds_disbursed')->firstOrFail();
        $stateLpjSubmitted = WorkflowState::where('name', 'lpj_submitted')->firstOrFail();
        $stateLpjWr3Review = WorkflowState::where('name', 'lpj_wr3_review')->firstOrFail();
        $stateCompleted = WorkflowState::where('name', 'completed')->firstOrFail();

        // 1. Buat pengajuan yang sudah cair dan memiliki TTD proposal sebelumnya
        $pengajuan = Pengajuan::create([
            'user_id' => $ormawa->id,
            'nama_kegiatan' => 'IT Festival 2026',
            'dana_diajukan' => 3000000,
            'tanggal_pengajuan' => now()->toDateString(),
            'workflow_state_id' => $stateFundsDisbursed->id,
            'file_proposal' => 'proposals/dummy.pdf',
        ]);

        Dana::create([
            'pengajuan_id' => $pengajuan->id,
            'nominal_cair' => 3000000,
            'tanggal_cair' => now()->toDateString(),
            'termin_ke' => 1,
        ]);

        // Simulasikan TTD proposal oleh BKHM dan WR3 sebelum pencairan dana
        DigitalSignatureService::sign($pengajuan, $bkhm, 'bkhm');
        DigitalSignatureService::sign($pengajuan, $wr3, 'wr3');

        $this->assertCount(2, $pengajuan->fresh()->tandaTanganProposal());
        $this->assertCount(0, $pengajuan->fresh()->tandaTanganLpj());

        // 2. Tahap 1: Ormawa Mengunggah LPJ
        $fileLpj = UploadedFile::fake()->create('lpj_it_festival.pdf', 500, 'application/pdf');
        $resUpload = $this->actingAs($ormawa)->post(route('lpj.store', $pengajuan), [
            'file_lpj' => $fileLpj,
        ]);

        $resUpload->assertRedirect(route('lpj.index'));
        $pengajuan->refresh();

        $this->assertEquals('lpj_submitted', $pengajuan->state->name);
        $this->assertNotNull($pengajuan->file_lpj);
        $this->assertNotNull($pengajuan->tanggal_upload_lpj);

        // Pastikan TTD Digital Ormawa (Pelampir LPJ) langsung terbit
        $ttdOrmawaLpj = $pengajuan->tandaTanganLpjOrmawa();
        $this->assertNotNull($ttdOrmawaLpj);
        $this->assertEquals('ormawa_lpj', $ttdOrmawaLpj->role);
        $this->assertEquals('Ahmad Fauzi', $ttdOrmawaLpj->nama_penandatangan);
        $this->assertEquals('2106001', $ttdOrmawaLpj->nidn_penandatangan);
        $this->assertEquals('NIM', $ttdOrmawaLpj->identitas_label);
        $this->assertTrue($ttdOrmawaLpj->is_valid);

        // Verifikasi publik QR Code Ormawa LPJ valid
        $resVerifyOrmawa = DigitalSignatureService::verify($ttdOrmawaLpj->token_verifikasi);
        $this->assertNotNull($resVerifyOrmawa);
        $this->assertTrue($resVerifyOrmawa['is_authentic']);
        $this->assertEquals('Pengesahan Laporan Pertanggungjawaban (LPJ)', $resVerifyOrmawa['snapshot']['document_type']);

        // 3. Tahap 2: BKHM Mengonfirmasi/Menyetujui LPJ
        $transitionBkhm = WorkflowTransition::where('from_state_id', $stateLpjSubmitted->id)
            ->where('to_state_id', $stateLpjWr3Review->id)
            ->where('required_role', 'bkhm')
            ->firstOrFail();

        $resBkhm = $this->actingAs($bkhm)->post(route('verifikasi.process', $pengajuan), [
            'transition_id' => $transitionBkhm->id,
            'catatan' => 'LPJ telah diperiksa dan sesuai secara administratif. Diteruskan ke WR3.',
        ]);

        $resBkhm->assertRedirect(route('verifikasi.index'));
        $pengajuan->refresh();

        $this->assertEquals('lpj_wr3_review', $pengajuan->state->name);

        // Pastikan TTD Digital BKHM LPJ terbit dan tidak menimpa TTD Proposal BKHM
        $ttdBkhmLpj = $pengajuan->tandaTanganLpjBkhm();
        $this->assertNotNull($ttdBkhmLpj);
        $this->assertEquals('bkhm_lpj', $ttdBkhmLpj->role);
        $this->assertTrue($ttdBkhmLpj->is_valid);

        // TTD Proposal BKHM tetap utuh
        $ttdProposalBkhm = $pengajuan->tandaTanganDigitals->where('role', 'bkhm')->first();
        $this->assertNotNull($ttdProposalBkhm);
        $this->assertNotEquals($ttdProposalBkhm->token_verifikasi, $ttdBkhmLpj->token_verifikasi);

        // Verifikasi publik QR Code BKHM LPJ valid
        $resVerifyBkhm = DigitalSignatureService::verify($ttdBkhmLpj->token_verifikasi);
        $this->assertNotNull($resVerifyBkhm);
        $this->assertTrue($resVerifyBkhm['is_authentic']);

        // 4. Tahap 3: WR3 Memverifikasi/Mengesahkan LPJ
        $transitionWr3 = WorkflowTransition::where('from_state_id', $stateLpjWr3Review->id)
            ->where('to_state_id', $stateCompleted->id)
            ->where('required_role', 'wr3')
            ->firstOrFail();

        $resWr3 = $this->actingAs($wr3)->post(route('verifikasi.process', $pengajuan), [
            'transition_id' => $transitionWr3->id,
            'catatan' => 'LPJ disahkan secara resmi oleh Wakil Rektor III.',
        ]);

        $resWr3->assertRedirect(route('verifikasi.index'));
        $pengajuan->refresh();

        $this->assertEquals('completed', $pengajuan->state->name);
        $this->assertTrue((bool) $pengajuan->evaluasi_termin_ok);

        // Pastikan TTD Digital WR3 LPJ terbit dan tidak menimpa TTD Proposal WR3
        $ttdWr3Lpj = $pengajuan->tandaTanganLpjWr3();
        $this->assertNotNull($ttdWr3Lpj);
        $this->assertEquals('wr3_lpj', $ttdWr3Lpj->role);
        $this->assertTrue($ttdWr3Lpj->is_valid);

        // TTD Proposal WR3 tetap utuh
        $ttdProposalWr3 = $pengajuan->tandaTanganDigitals->where('role', 'wr3')->first();
        $this->assertNotNull($ttdProposalWr3);
        $this->assertNotEquals($ttdProposalWr3->token_verifikasi, $ttdWr3Lpj->token_verifikasi);

        // Total TTD LPJ lengkap ada 3 (Ormawa, BKHM, WR3)
        $this->assertCount(3, $pengajuan->tandaTanganLpj());
        // Total TTD Proposal tetap 2 (BKHM, WR3)
        $this->assertCount(2, $pengajuan->tandaTanganProposal());

        // 5. Verifikasi tampilan antarmuka
        // Halaman LPJ Index menampilkan status dan badge TTD
        $resIndex = $this->actingAs($ormawa)->get(route('lpj.index'));
        $resIndex->assertOk();
        $resIndex->assertSee('Selesai &amp; Dilegalisir', false);
        $resIndex->assertSee('Ormawa');
        $resIndex->assertSee('BKHM');
        $resIndex->assertSee('WR3');

        // Halaman Detail Pengajuan menampilkan blok Legalisir TTD Digital LPJ
        $resShow = $this->actingAs($ormawa)->get(route('pengajuan.show', $pengajuan));
        $resShow->assertOk();
        $resShow->assertSee('Legalisir TTD Digital LPJ Kegiatan');
        $resShow->assertSee('Pelapor LPJ (Ormawa)');
        $resShow->assertSee('Verifikasi LPJ (BKHM)');
        $resShow->assertSee('Pengesahan LPJ (WR3)');

        // 6. Verifikasi penyajian dokumen LPJ terlegalisir langsung dari DocumentController
        $resDocDefault = $this->actingAs($ormawa)->get(route('dokumen.lpj', $pengajuan));
        $resDocDefault->assertOk();
        $this->assertEquals('application/pdf', $resDocDefault->headers->get('Content-Type'));

        // Mode Lembar Pengesahan Saja
        $resDocPengesahan = $this->actingAs($ormawa)->get(route('dokumen.lpj', ['pengajuan' => $pengajuan, 'mode' => 'pengesahan']));
        $resDocPengesahan->assertOk();
        $this->assertEquals('application/pdf', $resDocPengesahan->headers->get('Content-Type'));
        $this->assertStringContainsString('lembar-pengesahan-lpj', $resDocPengesahan->headers->get('Content-Disposition'));

        // Mode Berkas Asli
        $resDocAsli = $this->actingAs($ormawa)->get(route('dokumen.lpj', ['pengajuan' => $pengajuan, 'mode' => 'asli']));
        $resDocAsli->assertOk();
    }

    public function test_avatar_url_prioritizes_logo_ormawa_and_has_custom_avatar()
    {
        $userWithoutAvatar = User::factory()->create([
            'foto_profil' => null,
            'logo_ormawa' => null,
        ]);
        $this->assertFalse($userWithoutAvatar->hasCustomAvatar());
        $this->assertStringContainsString('ui-avatars.com', $userWithoutAvatar->avatar_url);

        $userWithLogo = User::factory()->create([
            'foto_profil' => null,
            'logo_ormawa' => 'profil/test_logo.png',
        ]);
        $this->assertTrue($userWithLogo->hasCustomAvatar());
    }
}
