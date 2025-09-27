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
use Picqer\Barcode\BarcodeGeneratorSVG;

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
                'nisn' => '0987654321',
                'date_of_birth' => 'Senin, 15 Mei 2008',
                'religion' => 'Islam',
                'major' => 'Science',
                'class_id' => $class10A->id,
                'parent_contact' => '08121234567',
            ],

        ];
        foreach ($students as $s) {
            $student = Student::create($s);

            // Get user information for QR code
            $user = User::find($student->user_id);

            // 1. Generate BARCODE untuk NIS (hanya berisi NIS)
            $barcodeGenerator = new BarcodeGeneratorSVG();
            $barcodeSvg = $barcodeGenerator->getBarcode($student->nis, $barcodeGenerator::TYPE_CODE_128);

            // Simpan file SVG Barcode
            $barcodePath = 'barcodes/' . $student->nis . '.svg';
            Storage::disk('public')->put($barcodePath, $barcodeSvg);

            // 2. Generate QR CODE untuk informasi lengkap siswa
            $qrData = json_encode([
                'name' => $user->name,
                'email' => $user->email,
                'address' => $user->address,
                'nis' => $student->nis,
                'nisn' => $student->nisn,
                'religion' => $student->religion,
                'major' => $student->major,
                'parent_contact' => $student->parent_contact,
            ]);
            $renderer = new ImageRenderer(
                new RendererStyle(400),
                new SvgImageBackEnd()
            );
            $writer = new Writer($renderer);
            $qrCodeSvg = $writer->writeString($qrData);

            $qrCodePath = 'qrcodes/' . $student->nis . '.svg';
            Storage::disk('public')->put($qrCodePath, $qrCodeSvg);

            // Update kedua kolom path
            $student->update([
                'barcode_path' => $barcodePath,
                'qr_code_path' => $qrCodePath
            ]);
        }
    }
}
