<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
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
                'name' => 'Admin Utama',
                'email' => 'admin@school.com',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'phone' => '0800000000',
                'address' => 'Jl. Admin No. 1',
            ],
            [
                'name' => 'Guru Matematika',
                'email' => 'guru1@school.com',
                'password' => Hash::make('password'),
                'role' => 'guru',
                'phone' => '08123456789',
                'address' => 'Jl. Pendidikan No. 1',
            ],
            [
                'name' => 'Guru Bahasa Indonesia',
                'email' => 'guru2@school.com',
                'password' => Hash::make('password'),
                'role' => 'guru',
                'phone' => '08234567890',
                'address' => 'Jl. Pahlawan No. 1',
            ],
            [
                'name' => 'Guru Fisika',
                'email' => 'guru3@school.com',
                'password' => Hash::make('password'),
                'role' => 'guru',
                'phone' => '08374827102',
                'address' => 'Jl. Mawar No. 1',
            ],
            [
                'name' => 'Siswa Test',
                'email' => 'siswa@school.com',
                'password' => Hash::make('password'),
                'role' => 'siswa',
                'phone' => '08129876543',
                'address' => 'Jl. Merdeka No. 10',
            ],
            [
                'name' => 'Satpam Sekolah',
                'email' => 'satpam@school.com',
                'password' => Hash::make('password'),
                'role' => 'satpam',
                'phone' => '08129876544',
                'address' => 'Jl. Merdeka No. 11',
            ],
        ];

        foreach ($users as $user) {
            User::create($user);
        }
    }
}
