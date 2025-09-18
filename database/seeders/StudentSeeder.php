<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Student;
use App\Models\User;
use App\Models\ClassModel;
use Illuminate\Support\Facades\Storage;
use BaconQrCode\Writer;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;

class StudentSeeder extends Seeder
{
    public function run(): void
    {
        // Ambil class yang sudah ada
        $class10A = ClassModel::where('name', '10A')->first();

        $user_id = User::where('role', 'siswa')->first()->id;

        $students = [
            [
                'user_id' => $user_id,
                'nis' => '1234567890',
                'class_id' => $class10A->id,
                'parent_contact' => '08121234567',
            ],

        ];
        foreach ($students as $s) {
            $student = Student::create($s);

            // Render QR pakai GD (tanpa Imagick)
            $renderer = new ImageRenderer(
                new RendererStyle(200),
                new SvgImageBackEnd()
            );
            $writer = new Writer($renderer);

            $qrCode = $writer->writeString($student->nis);

            // Simpan ke storage
            $path = 'qrcodes/' . $student->nis . '.svg';
            Storage::disk('public')->put($path, $qrCode);

            // Update kolom qr_code_path
            $student->update(['qr_code_path' => $path]);
        }
    }
}
