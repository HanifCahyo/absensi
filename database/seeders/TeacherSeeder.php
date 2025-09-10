<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Teacher;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TeacherSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $teachers = [
            [
                'user_id' => User::where('role', 'guru')->first()->id,
                'nip' => '123456',
            ],
            [
                'user_id' => User::where('role', 'guru')->skip(1)->first()->id,
                'nip' => '678901',
            ],
            [
                'user_id' => User::where('role', 'guru')->skip(2)->first()->id,
                'nip' => '345678',
            ],
        ];

        foreach ($teachers as $teacher) {
            Teacher::create($teacher);
        }
    }
}
