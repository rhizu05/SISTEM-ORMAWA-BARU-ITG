<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

class NavigationMenuTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Artisan::call('db:seed', ['--class' => 'RolePermissionSeeder', '--force' => true]);
        Artisan::call('db:seed', ['--class' => 'WorkflowSeeder', '--force' => true]);
    }

    private function createUserWithRole(string $roleName): User
    {
        $user = User::factory()->create(['saldo' => 10000000]);
        $user->assignRole($roleName);

        return $user;
    }

    public function test_bem_can_see_verifikasi_and_kurasi_menu(): void
    {
        $bem = $this->createUserWithRole('bem');

        $response = $this->actingAs($bem)->get(route('dashboard'));

        $response->assertStatus(200);
        $response->assertSee(route('verifikasi.index'));
        $response->assertSee(route('bem.kurasi.index'));
        $response->assertSee(route('pengajuan.create'));
    }

    public function test_bpm_can_see_verifikasi_and_aspirasi_menu(): void
    {
        $bpm = $this->createUserWithRole('bpm');

        $response = $this->actingAs($bpm)->get(route('dashboard'));

        $response->assertStatus(200);
        $response->assertSee(route('verifikasi.index'));
        $response->assertSee(route('bpm.aspirasi.index'));
        $response->assertSee(route('pengajuan.create'));
    }

    public function test_bkhm_can_see_verifikasi_and_layanan_menu(): void
    {
        $bkhm = $this->createUserWithRole('bkhm');

        $response = $this->actingAs($bkhm)->get(route('dashboard'));

        $response->assertStatus(200);
        $response->assertSee(route('verifikasi.index'));
        $response->assertSee(route('bkhm.konseling.index'));
        $response->assertSee(route('bkhm.tiket-aspirasi.index'));
        $response->assertSee(route('bkhm.export.excel'));
    }

    public function test_wr3_can_see_verifikasi_and_prestasi_menu(): void
    {
        $wr3 = $this->createUserWithRole('wr3');

        $response = $this->actingAs($wr3)->get(route('dashboard'));

        $response->assertStatus(200);
        $response->assertSee(route('verifikasi.index'));
        $response->assertSee(route('prestasi.index'));
    }

    public function test_bendahara_can_see_pencairan_and_export_menu(): void
    {
        $bendahara = $this->createUserWithRole('bendahara');

        $response = $this->actingAs($bendahara)->get(route('dashboard'));

        $response->assertStatus(200);
        $response->assertSee(route('verifikasi.index'));
        $response->assertSee(route('bendahara.export.excel'));
        $response->assertSee(route('bendahara.export.pdf'));
    }

    public function test_ormawa_can_see_prestasi_and_aspirasi_menu(): void
    {
        $ormawa = $this->createUserWithRole('ormawa');

        $response = $this->actingAs($ormawa)->get(route('dashboard'));

        $response->assertStatus(200);
        $response->assertSee(route('pengajuan.create'));
        $response->assertSee(route('prestasi.index'));
        $response->assertSee(route('aspirasi.mine'));
    }
}
