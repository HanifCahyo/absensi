<?php

namespace App\Imports;

use App\Models\User;
use App\Models\Student;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Writer;

class StudentsImport implements ToModel, WithHeadingRow
{
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        $user = User::create([
            'name' => $row['name'],
            'email' => $row['email'],
            'role' => 'siswa',
            'password' => Hash::make($row['password'] ?? 'default123'),
            'address' => $row['address'] ?? null,
            'phone' => $row['phone'] ?? null,
        ]);

        $student = new Student([
            'user_id' => $user->id,
            'nis' => $row['nis'],
            'class_id' => $row['class_id'],
            'parent_contact' => $row['parent_contact'] ?? null,
        ]);

        // Generate QR Code untuk NIS
        $renderer = new ImageRenderer(
            new RendererStyle(200),
            new SvgImageBackEnd()
        );
        $writer = new Writer($renderer);

        $qrCode = $writer->writeString($student->nis);
        $path = 'qrcodes/' . $student->nis . '.svg';

        Storage::disk('public')->put($path, $qrCode);
        $student->update(['qr_code_path' => $path]);

        return $student;
    }
}
