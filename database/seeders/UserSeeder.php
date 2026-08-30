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
                'name' => 'Administrator',
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
                'role' => 'bem'
            ],
            [
                'name' => 'BPM ITG',
                'username' => 'bpm',
                'email' => 'bpm@test.com',
                'password' => Hash::make('password'),
                'role' => 'bpm'
            ],
            [
                'name' => 'BKKH',
                'username' => 'bkh',
                'email' => 'bkh@test.com',
                'password' => Hash::make('password'),
                'role' => 'bkh'
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
                'name' => 'HIMA Informatika',
                'username' => 'himaif',
                'email' => 'himaif@test.com',
                'password' => Hash::make('password'),
                'role' => 'ormawa'
            ],
        ];

        foreach ($users as $userData) {
            $role = $userData['role'];
            unset($userData['role']);
            
            $user = User::create($userData);
            $user->assignRole($role);
        }
    }
}
