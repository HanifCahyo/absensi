<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Student;
use Illuminate\Support\Facades\Storage;
use BaconQrCode\Writer;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;

class StudentSeeder extends Seeder
{
    public function run(): void
    {
        $students = [
            [
                'nisn' => '1234567890',
                'nama' => 'Budi Santoso',
                'alamat' => 'Jl. Merdeka No. 10',
                'kelas' => '10A',
            ],
            [
                'nisn' => '9876543210',
                'nama' => 'Siti Aminah',
                'alamat' => 'Jl. Diponegoro No. 5',
                'kelas' => '10B',
            ],
            [
                'nisn' => '1928374650',
                'nama' => 'Agus Pratama',
                'alamat' => 'Jl. Sudirman No. 7',
                'kelas' => '11A',
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

            $qrCode = $writer->writeString($student->nisn);

            // Simpan ke storage
            $path = 'qrcodes/' . $student->nisn . '.svg';
            Storage::disk('public')->put($path, $qrCode);

            // Update kolom qr_code_path
            $student->update(['qr_code_path' => $path]);
        }
    }
}
