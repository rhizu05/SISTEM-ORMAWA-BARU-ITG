<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = [
            [
                'name' => 'Administrator Sistem',
                'username' => 'admin',
                'email' => 'admin@test.com',
                'password' => Hash::make('password'),
                'role' => 'admin'
            ],
            [
                'name' => 'BEM ITG',
                'username' => 'bem',
                'email' => 'bem@test.com',
                'password' => Hash::make('password'),
                'saldo' => 10000000,
                'saldo_awal' => 10000000,
                'role' => 'bem'
            ],
            [
                'name' => 'BPM ITG',
                'username' => 'bpm',
                'email' => 'bpm@test.com',
                'password' => Hash::make('password'),
                'saldo' => 10000000,
                'saldo_awal' => 10000000,
                'role' => 'bpm'
            ],
            [
                'name' => 'BKHM',
                'username' => 'bkhm',
                'email' => 'bkhm@test.com',
                'password' => Hash::make('password'),
                'role' => 'bkhm'
            ],
            [
                'name' => 'Wakil Rektor 3',
                'username' => 'wr3',
                'email' => 'wr3@test.com',
                'password' => Hash::make('password'),
                'role' => 'wr3'
            ],
            [
                'name' => 'Bendahara ITG',
                'username' => 'bendahara',
                'email' => 'bendahara@test.com',
                'password' => Hash::make('password'),
                'role' => 'bendahara'
            ],
            [
                'name' => 'Sarpras ITG',
                'username' => 'sarpras',
                'email' => 'sarpras@test.com',
                'password' => Hash::make('password'),
                'role' => 'sarpras'
            ],
            [
                'name' => 'HIMA Informatika',
                'username' => 'himaif',
                'email' => 'himaif@test.com',
                'password' => Hash::make('password'),
                'saldo' => 10000000,
                'saldo_awal' => 10000000,
                'role' => 'ormawa'
            ],
            [
                'name' => 'Mahasiswa Umum',
                'username' => 'mahasiswa',
                'email' => 'mahasiswa@test.com',
                'password' => Hash::make('password'),
                'role' => 'mahasiswa'
            ],
        ];

        foreach ($users as $userData) {
            $role = $userData['role'];
            unset($userData['role']);
            
            $user = User::updateOrCreate(['email' => $userData['email']], $userData);
            if (!$user->hasRole($role)) {
                $user->assignRole($role);
            }
        }
    }
}
