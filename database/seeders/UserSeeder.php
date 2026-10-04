<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Ready for both local development and secure production deployment.
     */
    public function run(): void
    {
        $defaultAlamat = 'Jl. Mayor Syamsu No.1, Jayaraga, Kec. Tarogong Kidul, Kabupaten Garut, Jawa Barat 44151';
        $isProduction = app()->isProduction() || env('APP_ENV') === 'production';

        // Konfigurasi domain email: @itg.ac.id untuk production, @test.com untuk local/dev
        $domain = env('SEED_USER_DOMAIN', $isProduction ? 'itg.ac.id' : 'test.com');

        // Password awal: diambil dari env jika ada, atau password aman
        $envPassword = env('SEED_DEFAULT_PASSWORD');
        $generatedPassword = null;

        if (!empty($envPassword)) {
            $defaultPassword = $envPassword;
        } elseif ($isProduction) {
            // Di lingkungan production jika tidak ada env, buat password acak 16 karakter yang kuat
            $generatedPassword = Str::password(16, true, true, true, false);
            $defaultPassword = $generatedPassword;
        } else {
            $defaultPassword = 'password';
        }

        // 1. Akun Bootstrap Resmi Produksi (Hanya Admin dan BKHM)
        // Seluruh akun lainnya (BEM, BPM, WR3, Bendahara, Sarpras, HIMA, UKM) akan ditambahkan langsung oleh BKHM melalui antarmuka /admin/users
        $bootstrapUsers = [
            [
                'name' => 'Administrator Sistem ITG',
                'username' => 'admin',
                'email' => env('ADMIN_EMAIL', 'admin@' . $domain),
                'password' => Hash::make(env('ADMIN_PASSWORD', $defaultPassword)),
                'alamat' => $defaultAlamat,
                'role' => 'admin'
            ],
            [
                'name' => 'Biro Kemahasiswaan & Hubungan Masyarakat (BKHM)',
                'username' => 'bkhm',
                'email' => env('BKHM_EMAIL', 'bkhm@' . $domain),
                'password' => Hash::make(env('BKHM_PASSWORD', $defaultPassword)),
                'alamat' => $defaultAlamat,
                'role' => 'bkhm'
            ],
        ];

        // 2. Akun Tambahan Khusus Lingkungan Lokal / Development / Testing
        $devOnlyUsers = [
            [
                'name' => 'Badan Eksekutif Mahasiswa (BEM ITG)',
                'username' => 'bem',
                'email' => env('BEM_EMAIL', 'bem@' . $domain),
                'password' => Hash::make(env('BEM_PASSWORD', $defaultPassword)),
                'saldo' => 10000000,
                'saldo_awal' => 10000000,
                'foto_profil' => 'profil/logo_bem.png',
                'logo_ormawa' => 'profil/logo_bem.png',
                'alamat' => $defaultAlamat,
                'role' => 'bem'
            ],
            [
                'name' => 'Badan Perwakilan Mahasiswa (BPM ITG)',
                'username' => 'bpm',
                'email' => env('BPM_EMAIL', 'bpm@' . $domain),
                'password' => Hash::make(env('BPM_PASSWORD', $defaultPassword)),
                'saldo' => 10000000,
                'saldo_awal' => 10000000,
                'foto_profil' => 'profil/logo_bpm.png',
                'logo_ormawa' => 'profil/logo_bpm.png',
                'alamat' => $defaultAlamat,
                'role' => 'bpm'
            ],
            [
                'name' => 'Wakil Rektor 3 Bidang Kemahasiswaan',
                'username' => 'wr3',
                'email' => env('WR3_EMAIL', 'wr3@' . $domain),
                'password' => Hash::make(env('WR3_PASSWORD', $defaultPassword)),
                'foto_profil' => 'profil/logo_itg.png',
                'logo_ormawa' => 'profil/logo_itg.png',
                'alamat' => $defaultAlamat,
                'role' => 'wr3'
            ],
            [
                'name' => 'Bagian Keuangan / Bendahara ITG',
                'username' => 'bendahara',
                'email' => env('BENDAHARA_EMAIL', 'bendahara@' . $domain),
                'password' => Hash::make(env('BENDAHARA_PASSWORD', $defaultPassword)),
                'foto_profil' => 'profil/logo_itg.png',
                'logo_ormawa' => 'profil/logo_itg.png',
                'alamat' => $defaultAlamat,
                'role' => 'bendahara'
            ],
            [
                'name' => 'Bagian Sarana & Prasarana ITG',
                'username' => 'sarpras',
                'email' => env('SARPRAS_EMAIL', 'sarpras@' . $domain),
                'password' => Hash::make(env('SARPRAS_PASSWORD', $defaultPassword)),
                'alamat' => $defaultAlamat,
                'role' => 'sarpras'
            ],
            [
                'name' => 'HIMA Informatika ITG',
                'username' => 'himaif',
                'email' => env('HIMAIF_EMAIL', 'himaif@' . $domain),
                'password' => Hash::make(env('HIMAIF_PASSWORD', $defaultPassword)),
                'saldo' => 10000000,
                'saldo_awal' => 10000000,
                'foto_profil' => 'profil/logo_himatif.png',
                'logo_ormawa' => 'profil/logo_himatif.png',
                'alamat' => $defaultAlamat,
                'role' => 'ormawa'
            ],
            [
                'name' => 'UKM Olahraga ITG',
                'username' => 'ukmolahraga',
                'email' => env('UKM_OLAHRAGA_EMAIL', 'ukm.olahraga@' . $domain),
                'password' => Hash::make(env('UKM_OLAHRAGA_PASSWORD', $defaultPassword)),
                'saldo' => 10000000,
                'saldo_awal' => 10000000,
                'alamat' => $defaultAlamat,
                'role' => 'ormawa'
            ],
        ];

        // Di lingkungan produksi hanya buat akun bootstrap (Admin & BKHM).
        // Di lokal/dev buat seluruh akun lengkap untuk keperluan testing dan fixture.
        $users = $isProduction ? $bootstrapUsers : array_merge($bootstrapUsers, $devOnlyUsers);

        // Pastikan direktori profil pada storage publik tersedia dan logo default tersalin
        $profilStorageDir = storage_path('app/public/profil');
        if (!File::isDirectory($profilStorageDir)) {
            File::makeDirectory($profilStorageDir, 0755, true);
        }

        foreach (['logo_himatif.png', 'logo_bem.png', 'logo_bpm.png', 'logo_itg.png'] as $logo) {
            $source = public_path('images/logos/' . $logo);
            $destination = $profilStorageDir . '/' . $logo;
            if (File::exists($source) && !File::exists($destination)) {
                File::copy($source, $destination);
            }
        }

        $createdCount = 0;
        $updatedCount = 0;

        foreach ($users as $userData) {
            $role = $userData['role'];
            unset($userData['role']);
            
            // Periksa apakah pengguna sudah ada berdasarkan email atau username
            $existingUser = User::where('email', $userData['email'])
                ->orWhere('username', $userData['username'])
                ->first();

            if ($existingUser) {
                // Di lingkungan production: JANGAN timpa password akun yang sudah ada (menghindari reset tak disengaja)
                if ($isProduction) {
                    unset($userData['password']);
                }
                $existingUser->update($userData);
                if (!$existingUser->hasRole($role)) {
                    $existingUser->assignRole($role);
                }
                $updatedCount++;
            } else {
                $user = User::create($userData);
                $user->assignRole($role);
                $createdCount++;
            }
        }

        if ($this->command) {
            $this->command->info("UserSeeder selesai: {$createdCount} dibuat baru, {$updatedCount} diperbarui.");
            if ($isProduction) {
                $this->command->info("Mode Produksi: Hanya akun bootstrap Admin & BKHM yang diinisialisasi.");
                $this->command->info("Akun lainnya (BEM, BPM, WR3, Bendahara, Sarpras, HIMA, UKM) dapat ditambahkan oleh BKHM melalui menu Kelola Pengguna (/admin/users).");
                if ($generatedPassword) {
                    $this->command->warn("--------------------------------------------------------------------------------");
                    $this->command->warn("PERHATIAN DEVOPS: Password acak awal yang dibuat untuk akun bootstrap adalah:");
                    $this->command->line("<fg=yellow;options=bold>{$generatedPassword}</>");
                    $this->command->warn("Harap catat password ini dan bagikan secara aman kepada Admin dan BKHM.");
                    $this->command->warn("--------------------------------------------------------------------------------");
                }
            }
        }
    }
}
