<?php

namespace Tests\Feature;

use App\Mail\JadwalKonselingMail;
use App\Models\JadwalKuliah;
use App\Models\MasterRuangan;
use App\Models\PeminjamanTempat;
use App\Models\TiketLayanan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class KonselingDanSarprasPenyempurnaanTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Artisan::call('db:seed', ['--class' => 'RolePermissionSeeder', '--force' => true]);
    }

    private function createBkhm(): User
    {
        $user = User::factory()->create(['name' => 'Staf BKHM']);
        $user->assignRole('bkhm');
        return $user;
    }

    private function createOrmawa(): User
    {
        $user = User::factory()->create(['name' => 'Pengurus Ormawa']);
        $user->assignRole('ormawa');
        return $user;
    }

    public function test_bkhm_menetapkan_jadwal_temu_konseling_dan_mengirim_mailable(): void
    {
        Mail::fake();
        $bkhm = $this->createBkhm();

        $tiket = TiketLayanan::create([
            'kode_tiket' => 'SKIN-TKT-2026-0001',
            'kategori' => 'konseling',
            'nim' => '2206001',
            'nama_mahasiswa' => 'Budi Santoso',
            'email' => 'budi@itg.ac.id',
            'topik_konseling' => 'Masalah Akademik',
            'metode_konseling' => 'Tatap Muka',
            'deskripsi_masalah' => 'Saya merasa kewalahan mengatur waktu kuliah.',
            'status' => 'pending',
        ]);

        $response = $this->actingAs($bkhm)->post(route('bkhm.konseling.update', $tiket), [
            'status' => 'jadwal_ditentukan',
            'jadwal_temu' => '2026-10-05 10:00:00',
            'lokasi_atau_link' => 'Ruang Konseling BKHM Lantai 2',
            'tanggapan_bkhm' => 'Halo Budi, silakan hadir di ruangan BKHM tepat waktu ya.',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('tiket_layanans', [
            'id' => $tiket->id,
            'status' => 'jadwal_ditentukan',
            'lokasi_atau_link' => 'Ruang Konseling BKHM Lantai 2',
        ]);

        Mail::assertSent(JadwalKonselingMail::class, function ($mail) use ($tiket) {
            return $mail->hasTo($tiket->email) &&
                   $mail->tiket->id === $tiket->id &&
                   str_contains($mail->pesanBkhm, 'tepat waktu');
        });
    }

    public function test_mahasiswa_dapat_mengonfirmasi_kesediaan_hadir_konseling(): void
    {
        $tiket = TiketLayanan::create([
            'kode_tiket' => 'SKIN-TKT-2026-0002',
            'kategori' => 'konseling',
            'nim' => '2206002',
            'nama_mahasiswa' => 'Siti Rahma',
            'email' => 'siti@itg.ac.id',
            'topik_konseling' => 'Keluarga',
            'metode_konseling' => 'Daring',
            'deskripsi_masalah' => 'Butuh teman bercerita.',
            'status' => 'jadwal_ditentukan',
            'jadwal_temu' => '2026-10-06 14:00:00',
            'lokasi_atau_link' => 'https://meet.google.com/abc-defg-hij',
        ]);

        $response = $this->post(route('layanan.konseling.konfirmasi', $tiket), [
            'email' => 'siti@itg.ac.id',
            'konfirmasi' => 'bersedia_hadir',
            'catatan' => 'Terima kasih, saya akan login 5 menit sebelum sesi dimulai.',
        ]);

        $response->assertRedirect(route('layanan.cek-status', [
            'kode' => $tiket->kode_tiket,
            'email' => $tiket->email,
        ]));

        $tiket->refresh();
        $this->assertEquals('bersedia_hadir', $tiket->konfirmasi_mahasiswa);
        $this->assertEquals('Terima kasih, saya akan login 5 menit sebelum sesi dimulai.', $tiket->catatan_konfirmasi_mahasiswa);
    }

    public function test_mahasiswa_mengajukan_reschedule_konseling(): void
    {
        $tiket = TiketLayanan::create([
            'kode_tiket' => 'SKIN-TKT-2026-0003',
            'kategori' => 'konseling',
            'nim' => '2206003',
            'nama_mahasiswa' => 'Ahmad Fauzi',
            'email' => 'ahmad@itg.ac.id',
            'topik_konseling' => 'Stres Ujian',
            'metode_konseling' => 'Tatap Muka',
            'deskripsi_masalah' => 'Kurang konsentrasi.',
            'status' => 'jadwal_ditentukan',
            'jadwal_temu' => '2026-10-07 09:00:00',
        ]);

        $response = $this->post(route('layanan.konseling.konfirmasi', $tiket), [
            'email' => 'ahmad@itg.ac.id',
            'konfirmasi' => 'minta_reschedule',
            'catatan' => 'Mohon maaf di jam tersebut saya ada kuis, jika boleh diundur pukul 13:00 WIB.',
        ]);

        $response->assertRedirect();

        $tiket->refresh();
        $this->assertEquals('minta_reschedule', $tiket->konfirmasi_mahasiswa);
        $this->assertEquals('Mohon maaf di jam tersebut saya ada kuis, jika boleh diundur pukul 13:00 WIB.', $tiket->catatan_konfirmasi_mahasiswa);
    }

    public function test_enkripsi_at_rest_data_sensitif_konseling(): void
    {
        $curahanHati = 'Ini adalah rahasia pribadi yang sangat sensitif dan hanya boleh dibaca BKHM.';
        $tiket = TiketLayanan::create([
            'kode_tiket' => 'SKIN-TKT-2026-0004',
            'kategori' => 'konseling',
            'nim' => '2206004',
            'nama_mahasiswa' => 'Anonim',
            'email' => 'anonim@itg.ac.id',
            'topik_konseling' => 'Privasi Tinggi',
            'metode_konseling' => 'Tatap Muka',
            'deskripsi_masalah' => $curahanHati,
            'status' => 'pending',
        ]);

        // Cek data mentah di database via DB Facade langsung (harus terenkripsi, bukan plain text)
        $rawRow = DB::table('tiket_layanans')->where('id', $tiket->id)->first();
        $this->assertNotEquals($curahanHati, $rawRow->deskripsi_masalah);
        $this->assertStringStartsWith('eyJ', $rawRow->deskripsi_masalah); // Karakteristik base64 payload terenkripsi Laravel

        // Melalui Eloquent model, didekripsi secara transparan
        $model = TiketLayanan::find($tiket->id);
        $this->assertEquals($curahanHati, $model->deskripsi_masalah);
    }

    public function test_slot_checker_ketersediaan_ruangan_sarpras_mengembalikan_jadwal_kuliah_dan_ormawa(): void
    {
        $ruangan = MasterRuangan::create([
            'nama_ruangan' => 'Laboratorium Rekayasa Perangkat Lunak',
            'kapasitas' => 40,
            'lokasi' => 'Gedung C Lantai 3',
            'status_aktif' => true,
        ]);

        // Buat jadwal kuliah pada hari Senin (1)
        JadwalKuliah::create([
            'ruangan_id' => $ruangan->id,
            'hari' => 1, // Senin
            'jam_mulai' => '08:00:00',
            'jam_selesai' => '10:30:00',
            'mata_kuliah' => 'Rekayasa Perangkat Lunak Lanjut',
            'semester' => 5,
            'aktif' => true,
        ]);

        $user = $this->createOrmawa();

        // Peminjaman ormawa pada tanggal tertentu (2026-10-05 = Senin)
        PeminjamanTempat::create([
            'user_id' => $user->id,
            'ruangan_id' => $ruangan->id,
            'nama_kegiatan' => 'Workshop Git & GitHub',
            'tgl_mulai' => '2026-10-05',
            'tgl_selesai' => '2026-10-05',
            'jam_mulai' => '13:00:00',
            'jam_selesai' => '15:30:00',
            'status_akhir' => 'Selesai / Disetujui',
        ]);

        $response = $this->actingAs($user)->getJson(route('peminjaman.tempat.jadwal-ruangan', [
            'ruangan_id' => $ruangan->id,
            'tanggal' => '2026-10-05', // Hari Senin
        ]));

        $response->assertOk();
        $data = $response->json();

        $this->assertEquals($ruangan->id, $data['ruangan_id']);
        $this->assertEquals('Senin', $data['hari_nama']);

        // Verifikasi jadwal kuliah ditemukan
        $this->assertCount(1, $data['kuliah']);
        $this->assertEquals('08:00', $data['kuliah'][0]['jam_mulai']);
        $this->assertStringContainsString('Rekayasa Perangkat Lunak Lanjut', $data['kuliah'][0]['judul']);

        // Verifikasi jadwal peminjaman ormawa ditemukan
        $this->assertCount(1, $data['peminjaman']);
        $this->assertEquals('13:00', $data['peminjaman'][0]['jam_mulai']);
        $this->assertStringContainsString('Workshop Git & GitHub', $data['peminjaman'][0]['judul']);
    }
}
