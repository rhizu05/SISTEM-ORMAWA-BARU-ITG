<?php

namespace Tests\Feature;

use App\Models\Letter;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class OrmawaSkUploadAndArsipTest extends TestCase
{
    use RefreshDatabase;

    protected User $bkhm;
    protected User $wr3;
    protected User $bem;
    protected User $bpm;
    protected User $ormawaLain;

    protected function setUp(): void
    {
        parent::setUp();

        Artisan::call('db:seed', ['--class' => 'RolePermissionSeeder', '--force' => true]);

        $this->bkhm = User::factory()->create(['username' => 'bkhm_test', 'email' => 'bkhm@itg.ac.id']);
        $this->bkhm->assignRole('bkhm');

        $this->wr3 = User::factory()->create(['username' => 'wr3_test', 'email' => 'wr3@itg.ac.id']);
        $this->wr3->assignRole('wr3');

        $this->bem = User::factory()->create(['username' => 'bem_test', 'email' => 'bem@itg.ac.id']);
        $this->bem->assignRole('bem');

        $this->bpm = User::factory()->create(['username' => 'bpm_test', 'email' => 'bpm@itg.ac.id']);
        $this->bpm->assignRole('bpm');

        $this->ormawaLain = User::factory()->create(['username' => 'ormawa_lain', 'email' => 'lain@itg.ac.id']);
        $this->ormawaLain->assignRole('ormawa');
    }

    public function test_bkhm_cannot_create_ormawa_bem_or_bpm_without_sk_file(): void
    {
        $roles = ['ormawa', 'bem', 'bpm'];

        foreach ($roles as $role) {
            $response = $this->actingAs($this->bkhm)->post(route('admin.users.store'), [
                'name' => "Organisasi $role",
                'username' => "org_$role",
                'email' => "org_$role@itg.ac.id",
                'role' => $role,
                'password' => 'Password123!',
                'password_confirmation' => 'Password123!',
            ]);

            $response->assertSessionHasErrors(['file_sk']);
        }
    }

    public function test_bkhm_can_create_admin_or_wr3_without_sk_file(): void
    {
        $response = $this->actingAs($this->bkhm)->post(route('admin.users.store'), [
            'name' => 'Staf WR3 Baru',
            'username' => 'staf_wr3',
            'email' => 'staf_wr3@itg.ac.id',
            'role' => 'wr3',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
        ]);

        $response->assertSessionHasNoErrors();
        $this->assertDatabaseHas('users', ['username' => 'staf_wr3']);
    }

    public function test_bkhm_creates_ormawa_with_sk_file_and_creates_letter_archive(): void
    {
        Storage::fake('local');

        $skFile = UploadedFile::fake()->create('sk_pengesahan_himatif.pdf', 1500, 'application/pdf');

        $response = $this->actingAs($this->bkhm)->post(route('admin.users.store'), [
            'name' => 'HIMA TEKNIK INFORMATIKA',
            'username' => 'himatif_itg',
            'email' => 'himatif@itg.ac.id',
            'role' => 'ormawa',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
            'nomor_sk' => '045/SK/ITG/X/2026',
            'tanggal_sk' => '2026-10-01',
            'file_sk' => $skFile,
        ]);

        $response->assertRedirect(route('admin.users.index'));
        $response->assertSessionHas('success');

        $user = User::where('username', 'himatif_itg')->first();
        $this->assertNotNull($user);
        $this->assertEquals('045/SK/ITG/X/2026', $user->nomor_sk);
        $this->assertNotNull($user->file_sk);
        $this->assertEquals('2026-10-01', $user->tanggal_sk->format('Y-m-d'));

        // Cek file tersimpan di storage lokal
        Storage::disk('local')->assertExists($user->file_sk);

        // Cek Letter otomatis terbuat sebagai arsip persuratan digital
        $letter = Letter::where('user_id', $user->id)
            ->where('type', 'sk_kepengurusan')
            ->first();

        $this->assertNotNull($letter);
        $this->assertEquals('045/SK/ITG/X/2026', $letter->nomor_surat);
        $this->assertEquals($user->file_sk, $letter->metadata['file_path']);
        $this->assertEquals('sk_pengesahan_himatif.pdf', $letter->metadata['original_name']);
    }

    public function test_bkhm_can_update_sk_file_and_updates_letter_archive(): void
    {
        Storage::fake('local');

        $skFileInitial = UploadedFile::fake()->create('sk_awal.pdf', 1000, 'application/pdf');

        $this->actingAs($this->bkhm)->post(route('admin.users.store'), [
            'name' => 'HIMASISFO',
            'username' => 'himasisfo',
            'email' => 'himasisfo@itg.ac.id',
            'role' => 'ormawa',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
            'nomor_sk' => '010/SK/ITG/2026',
            'tanggal_sk' => '2026-01-15',
            'file_sk' => $skFileInitial,
        ]);

        $user = User::where('username', 'himasisfo')->first();
        $oldPath = $user->file_sk;

        // Update dengan file SK baru
        $skFileBaru = UploadedFile::fake()->create('sk_revisi.pdf', 2000, 'application/pdf');

        $updateResponse = $this->actingAs($this->bkhm)->put(route('admin.users.update', $user), [
            'name' => 'HIMASISFO TERBARU',
            'username' => 'himasisfo',
            'email' => 'himasisfo@itg.ac.id',
            'role' => 'ormawa',
            'status_akun' => 'aktif',
            'nomor_sk' => '010/SK-REV/ITG/2026',
            'tanggal_sk' => '2026-10-04',
            'file_sk' => $skFileBaru,
        ]);

        $updateResponse->assertRedirect(route('admin.users.index'));

        $user->refresh();
        $this->assertEquals('010/SK-REV/ITG/2026', $user->nomor_sk);
        $this->assertNotEquals($oldPath, $user->file_sk);
        Storage::disk('local')->assertExists($user->file_sk);

        // Letter harus ikut terbarui
        $letter = Letter::where('user_id', $user->id)
            ->where('type', 'sk_kepengurusan')
            ->first();

        $this->assertNotNull($letter);
        $this->assertEquals('010/SK-REV/ITG/2026', $letter->nomor_surat);
        $this->assertEquals($user->file_sk, $letter->metadata['file_path']);
        $this->assertEquals('sk_revisi.pdf', $letter->metadata['original_name']);
    }

    public function test_document_authorization_for_sk_ormawa(): void
    {
        Storage::fake('local');
        $skFile = UploadedFile::fake()->create('sk_resmi.pdf', 1000, 'application/pdf');

        $this->actingAs($this->bkhm)->post(route('admin.users.store'), [
            'name' => 'UKM Seni ITG',
            'username' => 'ukm_seni',
            'email' => 'seni@itg.ac.id',
            'role' => 'ormawa',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
            'nomor_sk' => '088/SK/SENI/2026',
            'tanggal_sk' => '2026-05-10',
            'file_sk' => $skFile,
        ]);

        $ormawa = User::where('username', 'ukm_seni')->first();

        // 1. Ormawa pemilik dokumen -> 200 OK
        $this->actingAs($ormawa)
            ->get(route('dokumen.sk-ormawa', $ormawa))
            ->assertOk();

        // 2. BKHM -> 200 OK
        $this->actingAs($this->bkhm)
            ->get(route('dokumen.sk-ormawa', $ormawa))
            ->assertOk();

        // 3. WR3 -> 200 OK
        $this->actingAs($this->wr3)
            ->get(route('dokumen.sk-ormawa', $ormawa))
            ->assertOk();

        // 4. BEM & BPM -> 200 OK
        $this->actingAs($this->bem)
            ->get(route('dokumen.sk-ormawa', $ormawa))
            ->assertOk();

        $this->actingAs($this->bpm)
            ->get(route('dokumen.sk-ormawa', $ormawa))
            ->assertOk();

        // 5. Ormawa lain -> 403 Forbidden
        $this->actingAs($this->ormawaLain)
            ->get(route('dokumen.sk-ormawa', $ormawa))
            ->assertForbidden();

        // 6. Guest -> 302 Redirect to Login
        auth()->logout();
        $this->get(route('dokumen.sk-ormawa', $ormawa))
            ->assertRedirect(route('login'));
    }

    public function test_sk_is_visible_in_ormawa_and_bkhm_archive(): void
    {
        Storage::fake('local');
        $skFile = UploadedFile::fake()->create('sk_robotika.pdf', 1000, 'application/pdf');

        $this->actingAs($this->bkhm)->post(route('admin.users.store'), [
            'name' => 'UKM Robotika ITG',
            'username' => 'ukm_robotika',
            'email' => 'robotika@itg.ac.id',
            'role' => 'ormawa',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
            'nomor_sk' => '102/SK/ROBOTIKA/2026',
            'tanggal_sk' => '2026-08-20',
            'file_sk' => $skFile,
        ]);

        $ormawa = User::where('username', 'ukm_robotika')->first();

        // Ormawa melihat SK di Arsip Digital Persuratan (/archive)
        $responseOrmawa = $this->actingAs($ormawa)->get(route('archive.index'));
        $responseOrmawa->assertOk();
        $responseOrmawa->assertSee('Surat Keputusan (SK) Pengesahan Kepengurusan');
        $responseOrmawa->assertSee('102/SK/ROBOTIKA/2026');
        $responseOrmawa->assertSee(route('dokumen.sk-ormawa', $ormawa));

        // BKHM melihat SK di Arsip Persuratan Tab SK (/bkhm/arsip-surat?tab=sk)
        $responseBkhm = $this->actingAs($this->bkhm)->get(route('bkhm.arsip.index', ['tab' => 'sk']));
        $responseBkhm->assertOk();
        $responseBkhm->assertSee('UKM Robotika ITG');
        $responseBkhm->assertSee('102/SK/ROBOTIKA/2026');
        $responseBkhm->assertSee(route('dokumen.sk-ormawa', $ormawa));
    }
}
