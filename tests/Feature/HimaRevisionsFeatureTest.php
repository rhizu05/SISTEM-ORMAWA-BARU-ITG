<?php

namespace Tests\Feature;

use App\Models\Letter;
use App\Models\MasterBarang;
use App\Models\MasterRuangan;
use App\Models\PeminjamanBarang;
use App\Models\PeminjamanTempat;
use App\Models\TandaTanganDigital;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class HimaRevisionsFeatureTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Artisan::call('db:seed', ['--class' => 'RolePermissionSeeder', '--force' => true]);
        Storage::fake('public');
    }

    private function buatUserOrmawa(array $overrides = []): User
    {
        $user = User::factory()->create(array_merge([
            'nama_ketua'           => 'Rasyid Ridha',
            'nim_ketua'            => '2206001',
            'nama_sekretaris'      => 'Aisyah Putri',
            'nim_sekretaris'       => '2206002',
            'nama_bendahara'       => 'Farhan Fauzi',
            'nim_bendahara'        => '2206003',
            'prodi'                => 'Teknik Informatika',
            'website'              => 'https://hima-if.itg.ac.id',
            'periode_kepengurusan' => '2025/2026',
        ], $overrides));
        $user->assignRole('ormawa');
        return $user;
    }

    public function test_profile_update_hima_kop_fields_success(): void
    {
        $user = $this->buatUserOrmawa([
            'prodi'                => null,
            'website'              => null,
            'periode_kepengurusan' => null,
        ]);

        $response = $this->actingAs($user)->patch(route('profile.data.update'), [
            'nama_organisasi'      => 'Himpunan Mahasiswa Informatika',
            'singkatan'            => 'HIMA-IF',
            'kategori'             => 'HIMA',
            'prodi'                => 'S1 Informatika',
            'website'              => 'https://himaif.itg.ac.id',
            'periode_kepengurusan' => '2025/2026',
            'nama_ketua'           => 'Rasyid Ridha',
            'nim_ketua'            => '2206001',
            'nama_sekretaris'      => 'Aisyah Putri',
            'nim_sekretaris'       => '2206002',
            'nama_bendahara'       => 'Farhan Fauzi',
            'nim_bendahara'        => '2206003',
        ]);

        $response->assertSessionHasNoErrors();
        $user->refresh();

        $this->assertEquals('S1 Informatika', $user->prodi);
        $this->assertEquals('https://himaif.itg.ac.id', $user->website);
        $this->assertEquals('2025/2026', $user->periode_kepengurusan);
    }

    public function test_letter_store_with_lampiran_and_file_pdf(): void
    {
        $user = $this->buatUserOrmawa();
        $fakePdf = UploadedFile::fake()->create('lampiran_rundown.pdf', 500, 'application/pdf');

        $payload = [
            'type'              => 'undangan',
            'nomor_surat'       => '005/HIMA-IF/X/2026',
            'perihal'           => 'Undangan Pembukaan Informatics Expo 2026',
            'lampiran'          => '1 Berkas',
            'tujuan'            => 'Yth. Ketua BEM ITG',
            'kalimat_pembuka'   => 'Dengan hormat, sehubungan dengan diadakannya agenda tahunan Informatics Expo,',
            'kalimat_penutup'   => 'Demikian surat undangan ini kami sampaikan, atas kehadiran Saudara kami ucapkan terima kasih.',
            'nama_acara'        => 'Informatics Expo 2026',
            'hari_tanggal'      => 'Senin, 12 Oktober 2026',
            'waktu'             => '08:30 WIB - Selesai',
            'tempat'            => 'Aula Utama Institut Teknologi Garut',
            'file_lampiran_pdf' => $fakePdf,
            'penandatangan_jenis'   => ['internal'],
            'penandatangan_role'    => ['ketua'],
            'penandatangan_nama'    => ['Rasyid Ridha'],
            'penandatangan_jabatan' => ['Ketua Umum HIMA-IF'],
            'penandatangan_nim'     => ['2206001'],
        ];

        $response = $this->actingAs($user)->post(route('generator.letters.store'), $payload);

        $response->assertSessionHasNoErrors();
        $letter = Letter::where('nomor_surat', '005/HIMA-IF/X/2026')->first();
        $this->assertNotNull($letter);
        $this->assertEquals('1 Berkas', $letter->lampiran);
        $this->assertNotNull($letter->file_lampiran_pdf);
        Storage::disk('public')->assertExists($letter->file_lampiran_pdf);

        $penandatangan = $letter->penandatangan_list;
        $this->assertCount(1, $penandatangan);
        $this->assertEquals('2206001', $penandatangan[0]['nim']);
        $this->assertEquals('Ketua Umum HIMA-IF', $penandatangan[0]['jabatan']);
    }

    public function test_peminjaman_tempat_automatically_generates_permohonan_letter(): void
    {
        $user = $this->buatUserOrmawa();
        $ruangan = MasterRuangan::create([
            'nama_ruangan' => 'Laboratorium Rekayasa Perangkat Lunak',
            'lokasi'       => 'Gedung D Lantai 2',
            'kapasitas'    => 40,
            'status_aktif' => true,
        ]);

        $payload = [
            'ruangan_id'         => $ruangan->id,
            'nama_kegiatan'      => 'Workshop Fullstack Laravel & Alpine',
            'tgl_mulai'          => now()->addDays(2)->toDateString(),
            'tgl_selesai'        => now()->addDays(2)->toDateString(),
            'jam_mulai'          => '09:00',
            'jam_selesai'        => '15:00',
            'deskripsi_kegiatan' => 'Pelatihan intensif rekayasa web untuk mahasiswa prodi informatika.',
        ];

        $response = $this->actingAs($user)->post(route('peminjaman.tempat.store'), $payload);
        $response->assertSessionHasNoErrors();

        $peminjaman = PeminjamanTempat::where('nama_kegiatan', 'Workshop Fullstack Laravel & Alpine')->first();
        $this->assertNotNull($peminjaman);

        // Periksa surat permohonan yang dibuat otomatis
        $letter = $peminjaman->suratPermohonan;
        $this->assertNotNull($letter, 'Surat permohonan peminjaman ruangan tidak otomatis di-generate.');
        $this->assertEquals('permohonan', $letter->type);
        $this->assertEquals($user->id, $letter->user_id);
        $this->assertStringContainsString('Workshop Fullstack Laravel & Alpine', $letter->perihal);

        // Periksa tanda tangan digital
        $sig = TandaTanganDigital::where('signable_type', Letter::class)
            ->where('signable_id', $letter->id)
            ->first();
        $this->assertNotNull($sig, 'Tanda tangan digital tidak tersemat pada surat permohonan peminjaman ruangan.');
    }

    public function test_peminjaman_barang_automatically_generates_permohonan_letter(): void
    {
        $user = $this->buatUserOrmawa();
        $barang = MasterBarang::create([
            'kode_barang'          => 'BRG-001',
            'nama_barang'          => 'Proyektor Epson EB-X400',
            'kategori'             => 'Elektronik',
            'stok_total'           => 5,
            'stok_tersedia'        => 5,
            'kondisi'              => 'Baik',
            'status_aktif'         => true,
            'boleh_dibawa_keluar'  => true,
        ]);

        $payload = [
            'nama_kegiatan' => 'Kajian Rutin Departemen Keagamaan',
            'tgl_mulai'     => now()->addDays(3)->toDateString(),
            'tgl_selesai'   => now()->addDays(3)->toDateString(),
            'barang_id'     => [$barang->id],
            'qty'           => [2],
        ];

        $response = $this->actingAs($user)->post(route('peminjaman.barang.store'), $payload);
        $response->assertSessionHasNoErrors();

        $peminjaman = PeminjamanBarang::where('nama_kegiatan', 'Kajian Rutin Departemen Keagamaan')->first();
        $this->assertNotNull($peminjaman);

        $letter = $peminjaman->suratPermohonan;
        $this->assertNotNull($letter, 'Surat permohonan peminjaman barang tidak otomatis di-generate.');
        $this->assertEquals('permohonan', $letter->type);
        $this->assertStringContainsString('Proyektor Epson EB-X400', $letter->metadata['nama_alat_tempat'] ?? '');

        $sig = TandaTanganDigital::where('signable_type', Letter::class)
            ->where('signable_id', $letter->id)
            ->first();
        $this->assertNotNull($sig);
    }

    public function test_store_lpj_with_file_lampiran_pdf_and_internal_signers_nim(): void
    {
        $user = $this->buatUserOrmawa();
        $fakePdf = UploadedFile::fake()->create('laporan_keuangan_detail.pdf', 800, 'application/pdf');

        $payload = [
            'nama_kegiatan'      => 'Informatics Championship 2026',
            'tahun_akademik'     => '2025-2026',
            'waktu_hari_tanggal' => 'Sabtu, 24 Oktober 2026',
            'waktu_jam'          => '08:00 - 17:00 WIB',
            'tempat'             => 'Kampus ITG Garut',
            'tujuan_kegiatan'    => 'Meningkatkan jiwa kompetisi mahasiswa ITG',
            'sasaran_kegiatan'   => 'Seluruh mahasiswa ITG',
            'action'             => 'print',
            'file_lampiran_pdf'  => $fakePdf,
            'daftar_hadir_items' => [
                ['nama' => 'Rasyid Ridha', 'jabatan' => 'Peserta', 'no_hp' => '085712345678'],
                ['nama' => 'Aisyah Putri', 'jabatan' => 'Panitia', 'no_hp' => '081234567890'],
            ],
            'penandatangan_jenis'   => ['internal', 'internal'],
            'penandatangan_role'    => ['ketua', 'sekretaris'],
            'penandatangan_nama'    => ['Rasyid Ridha', 'Aisyah Putri'],
            'penandatangan_jabatan' => ['Ketua HIMA', 'Sekretaris HIMA'],
            'penandatangan_nim'     => ['2206001', '2206002'],
        ];

        $response = $this->actingAs($user)->post(route('generator.lpj.store'), $payload);
        $response->assertSessionHasNoErrors();

        $lpj = Letter::where('type', 'lpj')->where('perihal', 'like', '%Informatics Championship%')->first();
        $this->assertNotNull($lpj);
        $this->assertNotNull($lpj->file_lampiran_pdf);
        Storage::disk('public')->assertExists($lpj->file_lampiran_pdf);

        $penandatangan = $lpj->penandatangan_list;
        $this->assertCount(2, $penandatangan);
        $this->assertEquals('2206001', $penandatangan[0]['nim']);
        $this->assertEquals('2206002', $penandatangan[1]['nim']);
    }

    public function test_download_pdf_letter_returns_pdf_response(): void
    {
        $user = $this->buatUserOrmawa();
        $letter = Letter::create([
            'user_id'     => $user->id,
            'type'        => 'undangan',
            'nomor_surat' => '001/TEST/2026',
            'perihal'     => 'Undangan Rapat Evaluasi',
            'lampiran'    => '1 Berkas',
            'content'     => 'Isi surat undangan...',
            'metadata'    => [
                'tujuan'             => 'Yth. Anggota HIMA',
                'kalimat_pembuka'    => 'Dengan hormat,',
                'kalimat_penutup'    => 'Terima kasih.',
                'penandatangan_list' => [
                    [
                        'jenis'   => 'internal',
                        'role'    => 'ketua',
                        'nama'    => 'Rasyid Ridha',
                        'jabatan' => 'Ketua Umum',
                        'nim'     => '2206001',
                    ]
                ],
            ],
        ]);

        $response = $this->actingAs($user)->get(route('generator.letters.pdf', $letter));
        $response->assertStatus(200);
        $response->assertHeader('content-type', 'application/pdf');
    }

    public function test_proposal_store_with_profile_signers_autofills_name_and_nim(): void
    {
        $user = $this->buatUserOrmawa();

        $payload = [
            'nama_kegiatan'         => 'Pekan Ilmiah Mahasiswa Informatika 2026',
            'latar_belakang'        => 'Latar belakang kegiatan ilmiah...',
            'tujuan'                => 'Tujuan kegiatan...',
            'sasaran'               => 'Seluruh mahasiswa ITG',
            'penutup'               => 'Demikian proposal ini dibuat.',
            'action'                => 'print',
            'penandatangan_jenis'   => ['internal', 'internal'],
            'penandatangan_role'    => ['ketua', 'sekretaris'],
            'penandatangan_nama'    => ['', ''], // Tidak diinput manual karena dari profil
            'penandatangan_jabatan' => ['Ketua', 'Sekretaris'],
            'penandatangan_nim'     => ['', ''], // Tidak diinput manual karena dari profil
        ];

        $response = $this->actingAs($user)->post(route('generator.store'), $payload);
        $response->assertSessionHasNoErrors();

        $proposal = \App\Models\ProposalOtomatis::where('nama_kegiatan', 'Pekan Ilmiah Mahasiswa Informatika 2026')->first();
        $this->assertNotNull($proposal);

        $list = $proposal->penandatangan_list;
        $this->assertCount(2, $list);
        $this->assertEquals('Rasyid Ridha', $list[0]['nama']);
        $this->assertEquals('2206001', $list[0]['nim']);
        $this->assertEquals('Ketua', $list[0]['jabatan']);

        $this->assertEquals('Aisyah Putri', $list[1]['nama']);
        $this->assertEquals('2206002', $list[1]['nim']);
        $this->assertEquals('Sekretaris', $list[1]['jabatan']);
    }

    public function test_proposal_store_with_custom_jabatan_signers(): void
    {
        $user = $this->buatUserOrmawa();

        $payload = [
            'nama_kegiatan'         => 'Seminar Nasional Cyber Defense 2026',
            'latar_belakang'        => 'Latar belakang seminar...',
            'tujuan'                => 'Tujuan seminar...',
            'sasaran'               => 'Seluruh mahasiswa ITG',
            'penutup'               => 'Demikian proposal ini dibuat.',
            'action'                => 'print',
            'penandatangan_jenis'   => ['internal', 'internal', 'internal'],
            'penandatangan_role'    => ['ketua', 'ketua_pelaksana', 'custom'],
            'penandatangan_nama'    => ['', 'Daffa Al-Faruq', 'Bambang Pamungkas'],
            'penandatangan_jabatan' => ['Ketua', 'Ketua Pelaksana', 'Koordinator Sie Acara'],
            'penandatangan_nim'     => ['', '2206050', '2206099'],
        ];

        $response = $this->actingAs($user)->post(route('generator.store'), $payload);
        $response->assertSessionHasNoErrors();

        $proposal = \App\Models\ProposalOtomatis::where('nama_kegiatan', 'Seminar Nasional Cyber Defense 2026')->first();
        $this->assertNotNull($proposal);

        $list = $proposal->penandatangan_list;
        $this->assertCount(3, $list);
        
        // Signer 1: Ketua Ormawa (profil)
        $this->assertEquals('Rasyid Ridha', $list[0]['nama']);
        $this->assertEquals('2206001', $list[0]['nim']);

        // Signer 2: Ketua Pelaksana (panitia)
        $this->assertEquals('Daffa Al-Faruq', $list[1]['nama']);
        $this->assertEquals('2206050', $list[1]['nim']);
        $this->assertEquals('Ketua Pelaksana', $list[1]['jabatan']);

        // Signer 3: Jabatan Custom
        $this->assertEquals('Bambang Pamungkas', $list[2]['nama']);
        $this->assertEquals('2206099', $list[2]['nim']);
        $this->assertEquals('Koordinator Sie Acara', $list[2]['jabatan']);
    }

    /**
     * Memverifikasi bahwa surat dengan berkas lampiran PDF menampilkan banner info,
     * tautan buka berkas, dan embedded viewer di preview web serta badge di arsip.
     */
    public function test_letter_show_and_archive_displays_uploaded_pdf_attachment(): void
    {
        $user = $this->buatUserOrmawa();
        $dummyPdf = UploadedFile::fake()->create('lampiran_agenda.pdf', 100, 'application/pdf');

        $payload = [
            'type'              => 'undangan',
            'perihal'           => 'Undangan Rapat Koordinasi Anggota',
            'lampiran'          => '1 Berkas',
            'tujuan'            => 'Bapak Dekan FTI di Tempat',
            'kalimat_pembuka'   => 'Sehubungan dengan akan dilaksanakannya agenda kerja...',
            'kalimat_penutup'   => 'Demikian undangan ini kami sampaikan.',
            'nama_acara'        => 'Rapat Kerja HIMA',
            'hari_tanggal'      => 'Sabtu, 18 Oktober 2026',
            'waktu'             => '09:00 WIB',
            'tempat'            => 'Ruang Rapat FTI',
            'file_lampiran_pdf' => $dummyPdf,
        ];

        $resStore = $this->actingAs($user)->post(route('generator.letters.store'), $payload);
        $resStore->assertRedirect();

        $letter = Letter::where('perihal', 'Undangan Rapat Koordinasi Anggota')->firstOrFail();
        $this->assertNotNull($letter->file_lampiran_pdf);
        Storage::disk('public')->assertExists($letter->file_lampiran_pdf);

        // Preview Web (letters.show)
        $resShow = $this->actingAs($user)->get(route('generator.letters.show', $letter));
        $resShow->assertOk();
        $resShow->assertSee('Lampiran Berkas PDF Terlampir');
        $resShow->assertSee('Dokumen Berkas Lampiran PDF');
        $resShow->assertSee(asset('storage/' . $letter->file_lampiran_pdf));

        // Arsip Web (archive.index)
        $resArchive = $this->actingAs($user)->get(route('archive.index'));
        $resArchive->assertOk();
        $resArchive->assertSee('Ada Lampiran PDF');
        $resArchive->assertSee(asset('storage/' . $letter->file_lampiran_pdf));
    }

    /**
     * Memverifikasi bahwa KOP Ormawa menyeragamkan font 12pt bold untuk Nama HIMA, Prodi, dan ITG.
     */
    public function test_kop_ormawa_has_standardized_12pt_font(): void
    {
        $user = $this->buatUserOrmawa([
            'nama_kop' => 'HIMPUNAN MAHASISWA TEKNIK INFORMATIKA',
        ]);

        $letter = Letter::create([
            'user_id'     => $user->id,
            'type'        => 'undangan',
            'nomor_surat' => '001/HIMA/2026',
            'perihal'     => 'Uji Coba KOP Seragam',
            'content'     => 'Isi surat...',
        ]);

        $res = $this->actingAs($user)->get(route('generator.letters.show', $letter));
        $res->assertOk();
        // Cek bahwa baris nama ormawa dan nama kampus menggunakan font-size 12pt bold seragam
        $res->assertSee('HIMPUNAN MAHASISWA TEKNIK INFORMATIKA');
        $res->assertSee('INSTITUT TEKNOLOGI GARUT');
        $res->assertSee('font-size: 12pt; font-weight: bold; text-transform: uppercase;');
    }

    /**
     * Memverifikasi bahwa profil ormawa dapat mengupdate pengaturan KOP (nama_kop, email_kop, website, alamat).
     */
    public function test_profile_update_nama_kop_and_email_kop(): void
    {
        $user = $this->buatUserOrmawa();

        $response = $this->actingAs($user)->patch(route('profile.data.update'), [
            'nama_kop'             => 'HIMPUNAN MAHASISWA TEKNIK INFORMATIKA ITG',
            'email_kop'            => 'sekretariat.himatif@itg.ac.id',
            'website'              => 'himatif.itg.ac.id',
            'alamat'               => 'Gedung Ormawa Kampus ITG Lt. 1',
            'telepon'              => '081234567890',
            'periode_kepengurusan' => '2025/2026',
        ]);

        $response->assertSessionHas('status', 'profile-data-updated');

        $user->refresh();
        $this->assertEquals('HIMPUNAN MAHASISWA TEKNIK INFORMATIKA ITG', $user->nama_kop);
        $this->assertEquals('sekretariat.himatif@itg.ac.id', $user->email_kop);
        $this->assertEquals('himatif.itg.ac.id', $user->website);
        $this->assertEquals('Gedung Ormawa Kampus ITG Lt. 1', $user->alamat);
    }

    /**
     * Memverifikasi bahwa pada KOP proposal, teks Kementerian dihilangkan,
     * diganti nama ormawa, dan menampilkan 4 baris KOP baku resmi ITG.
     */
    public function test_proposal_print_displays_ormawa_kop_and_removes_kementerian(): void
    {
        $user = $this->buatUserOrmawa([
            'nama_kop'  => 'HIMPUNAN MAHASISWA TEKNIK INFORMATIKA',
            'email_kop' => 'himatif@itg.ac.id',
            'website'   => 'himatif.itg.ac.id',
        ]);

        $proposal = \App\Models\ProposalOtomatis::create([
            'user_id'       => $user->id,
            'nama_kegiatan' => 'Dies Natalis Informatika ITG 2026',
            'latar_belakang'=> 'Latar belakang...',
            'tujuan'        => 'Tujuan...',
            'sasaran'       => 'Mahasiswa ITG',
            'penutup'       => 'Penutup...',
            'total_anggaran'=> 5000000,
        ]);

        // Cek mode Web Print
        $resWeb = $this->actingAs($user)->get(route('generator.print', $proposal));
        $resWeb->assertOk();
        $resWeb->assertDontSee('KEMENTERIAN PENDIDIKAN, KEBUDAYAAN, RISET, DAN TEKNOLOGI');
        $resWeb->assertSee('HIMPUNAN MAHASISWA TEKNIK INFORMATIKA');
        $resWeb->assertSee('INSTITUT TEKNOLOGI GARUT');
        $resWeb->assertSee('himatif.itg.ac.id');
        $resWeb->assertSee('himatif@itg.ac.id');

        // Cek mode Unduh PDF
        $resPdf = $this->actingAs($user)->get(route('generator.pdf', $proposal));
        $resPdf->assertOk();
        $this->assertEquals('application/pdf', $resPdf->headers->get('Content-Type'));
    }

    /**
     * Memverifikasi bahwa form create proposal, surat, dan LPJ menampilkan
     * banner keterangan bahwa KOP dapat diatur di halaman profil.
     */
    public function test_generator_forms_display_kop_configuration_banner(): void
    {
        $user = $this->buatUserOrmawa();

        // 1. Form Proposal
        $resProp = $this->actingAs($user)->get(route('generator.create'));
        $resProp->assertOk();
        $resProp->assertSee('KOP Proposal Otomatis:');
        $resProp->assertSee(route('profile.edit'));

        // 2. Form Surat
        $resLetter = $this->actingAs($user)->get(route('generator.letters.create'));
        $resLetter->assertOk();
        $resLetter->assertSee('KOP Surat Otomatis:');
        $resLetter->assertSee(route('profile.edit'));

        // 3. Form LPJ
        $resLpj = $this->actingAs($user)->get(route('generator.lpj.create'));
        $resLpj->assertOk();
        $resLpj->assertSee('KOP Dokumen LPJ Otomatis:');
        $resLpj->assertSee(route('profile.edit'));
    }

    /**
     * Memverifikasi bahwa form create tempat tidak lagi memiliki input persetujuan prodi,
     * dan pengajuan peminjaman tempat selalu masuk ke antrian BKHM.
     */
    public function test_peminjaman_tempat_standardized_to_bkhm_without_prodi_shortcut(): void
    {
        $user = $this->buatUserOrmawa();
        $ruangan = MasterRuangan::create([
            'nama_ruangan' => 'Laboratorium Rekayasa Perangkat Lunak',
            'lokasi'       => 'Gedung D Lt. 2',
            'kapasitas'    => 40,
            'status_aktif' => true,
        ]);

        // Cek halaman form create tempat
        $resCreate = $this->actingAs($user)->get(route('peminjaman.tempat.create'));
        $resCreate->assertOk();
        $resCreate->assertDontSee('name="file_persetujuan_prodi"', false);
        $resCreate->assertDontSee('Jika dilampirkan, pengajuan dapat langsung diteruskan untuk verifikasi sarpras');

        // Ajukan peminjaman tempat
        $resStore = $this->actingAs($user)->post(route('peminjaman.tempat.store'), [
            'ruangan_id'         => $ruangan->id,
            'nama_kegiatan'      => 'Bootcamp Web Framework Modern',
            'tgl_mulai'          => '2026-10-25',
            'tgl_selesai'        => '2026-10-25',
            'jam_mulai'          => '08:30',
            'jam_selesai'        => '12:30',
            'deskripsi_kegiatan' => 'Kegiatan workshop pemrograman',
        ]);

        $resStore->assertRedirect(route('peminjaman.tempat.index'));
        $resStore->assertSessionHas('success', 'Pengajuan peminjaman ruangan berhasil dikirim dan menunggu konfirmasi BKHM.');

        $this->assertDatabaseHas('peminjaman_tempat', [
            'user_id'                => $user->id,
            'ruangan_id'             => $ruangan->id,
            'nama_kegiatan'          => 'Bootcamp Web Framework Modern',
            'file_persetujuan_prodi' => null,
            'status_bkhm'            => 'pending',
            'status_sarpras'         => 'pending',
            'status_akhir'           => 'Proses BKHM',
        ]);
    }

    /**
     * Memverifikasi penempatan logo KOP surat resmi Ormawa:
     * Logo ITG berada di kolom kiri, dan Logo Ormawa berada di kolom kanan.
     */
    public function test_kop_letter_and_profile_renders_itg_logo_on_left_and_ormawa_logo_on_right(): void
    {
        $user = $this->buatUserOrmawa();
        $user->update([
            'nama_kop'    => 'HIMPUNAN MAHASISWA TEKNIK INFORMATIKA',
            'logo_ormawa' => 'logos/hima_test.png',
        ]);

        $letter = Letter::create([
            'user_id'     => $user->id,
            'nomor_surat' => '001/HIMA-IF/X/2026',
            'perihal'     => 'Undangan Pembukaan Acara',
            'lampiran'    => '1 Berkas',
            'type'        => 'undangan',
            'content'     => json_encode([
                'tujuan'            => "Yth. Ketua Program Studi Teknik Informatika\nDi Tempat",
                'kalimat_pembuka'   => 'Dengan hormat,',
                'kalimat_penutup'   => 'Demikian surat ini kami sampaikan.',
                'nama_acara'        => 'Pembukaan Informatics Expo',
                'hari_tanggal'      => 'Senin, 10 Oktober 2026',
                'waktu'             => '09:00 WIB',
                'tempat'            => 'Aula ITG',
            ]),
            'metadata'    => [
                'penandatangan_list' => [
                    [
                        'jenis'   => 'internal',
                        'role'    => 'ketua',
                        'nama'    => 'Ahmad Fauzi',
                        'jabatan' => 'Ketua Umum HIMA-IF',
                        'nim'     => '2206001',
                    ],
                ],
            ],
        ]);

        // Cek preview surat
        $resLetter = $this->actingAs($user)->get(route('generator.letters.show', $letter));
        $resLetter->assertOk();

        $content = $resLetter->getContent();
        // Pastikan alt Logo ITG muncul sebelum teks KOP dan alt Logo Ormawa muncul setelah teks KOP di tabel KOP
        $posLogoItg = strpos($content, 'alt="Logo ITG"');
        $posNamaOrmawa = strpos($content, 'HIMPUNAN MAHASISWA TEKNIK INFORMATIKA');
        $posLogoOrmawa = strpos($content, 'alt="Logo Ormawa"');

        $this->assertNotFalse($posLogoItg, 'Logo ITG tidak ditemukan di tampilan surat.');
        $this->assertNotFalse($posNamaOrmawa, 'Nama KOP Ormawa tidak ditemukan di tampilan surat.');
        $this->assertNotFalse($posLogoOrmawa, 'Logo Ormawa tidak ditemukan di tampilan surat.');

        // Memastikan urutan posisi: Logo ITG (kiri) < Teks KOP (tengah) < Logo Ormawa (kanan)
        $this->assertLessThan($posNamaOrmawa, $posLogoItg, 'Logo ITG harus berada sebelum (kiri) teks KOP.');
        $this->assertLessThan($posLogoOrmawa, $posNamaOrmawa, 'Logo Ormawa harus berada sesudah (kanan) teks KOP.');

        // Cek halaman preview profil
        $resProfile = $this->actingAs($user)->get(route('profile.edit'));
        $resProfile->assertOk();
        $profileContent = $resProfile->getContent();

        $posProfileItg = strpos($profileContent, 'alt="Logo ITG"');
        $posProfileOrmawa = strpos($profileContent, 'alt="Logo Ormawa"');

        $this->assertNotFalse($posProfileItg, 'Logo ITG tidak ditemukan di preview KOP profil.');
        $this->assertNotFalse($posProfileOrmawa, 'Logo Ormawa tidak ditemukan di preview KOP profil.');
        $this->assertLessThan($posProfileOrmawa, $posProfileItg, 'Di preview profil, Logo ITG harus di kiri dan Logo Ormawa di kanan.');
    }

    /**
     * Memverifikasi bahwa file PDF surat resmi yang diunduh menyematkan gambar logo
     * (Base64 data URI) dan menghasilkan dokumen PDF berukuran penuh dengan logo.
     */
    public function test_unduh_pdf_surat_embeds_logo_itg_dan_logo_ormawa(): void
    {
        $user = $this->buatUserOrmawa();
        
        // Simpan gambar dummy PNG untuk logo ormawa di storage publik
        $dummyPngPath = public_path('images/logos/logo_itg.png');
        $dummyBytes = file_exists($dummyPngPath) ? file_get_contents($dummyPngPath) : base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNk+M9QDwADhgGAWjR9awAAAABJRU5ErkJggg==');
        Storage::disk('public')->put('logos/hima_itg_official.png', $dummyBytes);

        $user->update([
            'nama_kop'    => 'HIMPUNAN MAHASISWA TEKNIK INFORMATIKA',
            'logo_ormawa' => 'logos/hima_itg_official.png',
        ]);

        $letter = Letter::create([
            'user_id'     => $user->id,
            'nomor_surat' => '009/HIMA-IF/X/2026',
            'perihal'     => 'Surat Permohonan Fasilitas',
            'lampiran'    => '1 Berkas',
            'type'        => 'permohonan',
            'content'     => json_encode([
                'tujuan'            => "Yth. Kepala Bagian BKHM ITG\nDi Tempat",
                'kalimat_pembuka'   => 'Dengan hormat,',
                'kalimat_penutup'   => 'Demikian surat permohonan ini disampaikan.',
                'nama_alat_tempat'  => 'Ruang Aula ITG',
                'waktu_penggunaan'  => '15 Oktober 2026',
                'alasan_tujuan'     => 'Kegiatan Pelatihan Web Mahasiswa',
            ]),
            'metadata'    => [
                'penandatangan_list' => [
                    [
                        'jenis'   => 'internal',
                        'role'    => 'ketua',
                        'nama'    => 'Ahmad Fauzi',
                        'jabatan' => 'Ketua Umum HIMA-IF',
                        'nim'     => '2206001',
                    ],
                ],
            ],
        ]);

        $resPdf = $this->actingAs($user)->get(route('generator.letters.pdf', $letter));
        $resPdf->assertOk();
        $this->assertEquals('application/pdf', $resPdf->headers->get('Content-Type'));

        // Dokumen PDF dengan logo yang tersemat sempurna berukuran jauh lebih besar daripada sekadar teks kosong (< 5 KB)
        $pdfBytes = $resPdf->getContent();
        $this->assertGreaterThan(50000, strlen($pdfBytes), 'Ukuran PDF surat terlalu kecil, logo kemungkinan tidak tersemat.');
    }

    /**
     * Memverifikasi pembuatan Surat Tugas / Mandat dengan banyak penerima tugas (multi-petugas/delegasi):
     * Form mendukung penambahan petugas, tersimpan ke DB, dan tampil dalam tabel baik di preview maupun PDF.
     */
    public function test_surat_tugas_supports_multiple_assignees(): void
    {
        $user = $this->buatUserOrmawa();

        $payload = [
            'type'                   => 'tugas',
            'nomor_surat'            => '010/ST-HIMA/X/2026',
            'perihal'                => 'Surat Tugas Delegasi Lomba Informatika Nasional',
            'lampiran'               => '-',
            'tujuan'                 => 'Yth. Panitia Penyelenggara INFEST 2026',
            'kalimat_pembuka_tugas'  => 'Dengan ini kami menugaskan mahasiswa berikut sebagai delegasi resmi:',
            'nama_petugas'           => 'Ahmad Fauzi',
            'nim'                    => '2206001',
            'petugas_tambahan_nama'  => ['Nurul Aisyah', 'Rizky Pratama'],
            'petugas_tambahan_nim'   => ['2206002', '2206003'],
            'uraian_tugas'           => 'Mengikuti Lomba Competitive Programming dan UI/UX Design',
            'tanggal_pelaksanaan'    => '20 - 22 Oktober 2026',
            'kalimat_penutup_tugas'  => 'Untuk melaksanakan tugas dengan penuh tanggung jawab.',
            'penandatangan_jenis'    => ['internal'],
            'penandatangan_role'     => ['ketua'],
            'penandatangan_nama'     => ['Ahmad Fauzi'],
            'penandatangan_jabatan'  => ['Ketua Umum HIMA-IF'],
            'penandatangan_nim'      => ['2206001'],
        ];

        $response = $this->actingAs($user)->post(route('generator.letters.store'), $payload);
        $response->assertSessionHasNoErrors();

        $letter = Letter::where('nomor_surat', '010/ST-HIMA/X/2026')->first();
        $this->assertNotNull($letter);
        $this->assertEquals('tugas', $letter->type);

        $meta = $letter->metadata;
        $this->assertIsArray($meta['petugas_list']);
        $this->assertCount(3, $meta['petugas_list']);
        $this->assertEquals('Ahmad Fauzi', $meta['petugas_list'][0]['nama']);
        $this->assertEquals('2206001', $meta['petugas_list'][0]['nim']);
        $this->assertEquals('Nurul Aisyah', $meta['petugas_list'][1]['nama']);
        $this->assertEquals('2206002', $meta['petugas_list'][1]['nim']);
        $this->assertEquals('Rizky Pratama', $meta['petugas_list'][2]['nama']);
        $this->assertEquals('2206003', $meta['petugas_list'][2]['nim']);

        // Pastikan tampil di Web Preview
        $resShow = $this->actingAs($user)->get(route('generator.letters.show', $letter));
        $resShow->assertOk();
        $resShow->assertSee('Nama Penerima Tugas / Mandat');
        $resShow->assertSee('Ahmad Fauzi');
        $resShow->assertSee('2206001');
        $resShow->assertSee('Nurul Aisyah');
        $resShow->assertSee('2206002');
        $resShow->assertSee('Rizky Pratama');
        $resShow->assertSee('2206003');

        // Pastikan ter-render di PDF
        $resPdf = $this->actingAs($user)->get(route('generator.letters.pdf', $letter));
        $resPdf->assertOk();
        $this->assertEquals('application/pdf', $resPdf->headers->get('Content-Type'));
        $this->assertGreaterThan(10000, strlen($resPdf->getContent()));
    }

    /**
     * Memverifikasi pembuatan Surat Keterangan (Aktif) dengan banyak mahasiswa:
     * Form mendukung penambahan nama, nim, dan jabatan mahasiswa, tersimpan ke DB dalam metadata['mahasiswa_list'],
     * serta tampil dalam tabel pada preview web dan dokumen PDF.
     */
    public function test_surat_keterangan_supports_multiple_students(): void
    {
        $user = $this->buatUserOrmawa();

        $payload = [
            'type'                        => 'keterangan_aktif',
            'nomor_surat'                 => '011/SK-HIMA/X/2026',
            'perihal'                     => 'Surat Keterangan Aktif Organisasi Mahasiswa',
            'lampiran'                    => '-',
            'tujuan'                      => 'Yth. Tim Penilai Beasiswa Kemendikbud',
            'kalimat_pembuka_ket'         => 'Pengurus Organisasi Kemahasiswaan ITG dengan ini menerangkan bahwa:',
            'nama_mahasiswa'              => 'Deni Firmansyah',
            'nim'                         => '2206001',
            'jabatan'                     => 'Ketua Departemen Kominfo',
            'mahasiswa_tambahan_nama'     => ['Siti Nurhaliza', 'Rian Hidayat'],
            'mahasiswa_tambahan_nim'      => ['2206002', '2206003'],
            'mahasiswa_tambahan_jabatan'  => ['Sekretaris Departemen', 'Staff Ahli Departemen'],
            'keperluan'                   => 'Persyaratan Pengajuan Beasiswa Unggulan Prestasi 2026',
            'kalimat_penutup_ket'         => 'Adalah benar mahasiswa dan pengurus aktif organisasi kami.',
            'penandatangan_jenis'         => ['internal'],
            'penandatangan_role'          => ['ketua'],
            'penandatangan_nama'          => ['Ahmad Fauzi'],
            'penandatangan_jabatan'       => ['Ketua Umum HIMA-IF'],
            'penandatangan_nim'           => ['2206001'],
        ];

        $response = $this->actingAs($user)->post(route('generator.letters.store'), $payload);
        $response->assertSessionHasNoErrors();

        $letter = Letter::where('nomor_surat', '011/SK-HIMA/X/2026')->first();
        $this->assertNotNull($letter);
        $this->assertEquals('keterangan_aktif', $letter->type);

        $meta = $letter->metadata;
        $this->assertIsArray($meta['mahasiswa_list']);
        $this->assertCount(3, $meta['mahasiswa_list']);
        $this->assertEquals('Deni Firmansyah', $meta['mahasiswa_list'][0]['nama']);
        $this->assertEquals('2206001', $meta['mahasiswa_list'][0]['nim']);
        $this->assertEquals('Ketua Departemen Kominfo', $meta['mahasiswa_list'][0]['jabatan']);
        $this->assertEquals('Siti Nurhaliza', $meta['mahasiswa_list'][1]['nama']);
        $this->assertEquals('2206002', $meta['mahasiswa_list'][1]['nim']);
        $this->assertEquals('Sekretaris Departemen', $meta['mahasiswa_list'][1]['jabatan']);
        $this->assertEquals('Rian Hidayat', $meta['mahasiswa_list'][2]['nama']);
        $this->assertEquals('2206003', $meta['mahasiswa_list'][2]['nim']);
        $this->assertEquals('Staff Ahli Departemen', $meta['mahasiswa_list'][2]['jabatan']);

        // Pastikan tampil di Web Preview
        $resShow = $this->actingAs($user)->get(route('generator.letters.show', $letter));
        $resShow->assertOk();
        $resShow->assertSee('Nama Mahasiswa');
        $resShow->assertSee('Jabatan di Organisasi');
        $resShow->assertSee('Deni Firmansyah');
        $resShow->assertSee('2206001');
        $resShow->assertSee('Ketua Departemen Kominfo');
        $resShow->assertSee('Siti Nurhaliza');
        $resShow->assertSee('2206002');
        $resShow->assertSee('Sekretaris Departemen');
        $resShow->assertSee('Rian Hidayat');
        $resShow->assertSee('2206003');
        $resShow->assertSee('Staff Ahli Departemen');

        // Pastikan ter-render di PDF
        $resPdf = $this->actingAs($user)->get(route('generator.letters.pdf', $letter));
        $resPdf->assertOk();
        $this->assertEquals('application/pdf', $resPdf->headers->get('Content-Type'));
        $this->assertGreaterThan(10000, strlen($resPdf->getContent()));
    }
}
