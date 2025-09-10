<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\ClassModel;
use App\Models\Teacher;

class ClassSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $classes = [
            [
                'name' => '10A',
                'teacher_id' => Teacher::first()->id,
            ],
            [
                'name' => '10B',
                'teacher_id' => Teacher::skip(1)->first()->id,
            ],
            [
                'name' => '11A',
                'teacher_id' => Teacher::skip(2)->first()->id,
            ],
            [
                'name' => '11B',
                'teacher_id' => Teacher::first()->id,
            ],
            [
                'name' => '12A',
                'teacher_id' => Teacher::skip(1)->first()->id,
            ],
        ];

        foreach ($classes as $class) {
            ClassModel::create($class);
        }
    }
}
