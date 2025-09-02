<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Student;
use App\Models\Attendance;
use Carbon\Carbon;

class AttendanceController extends Controller
{
    // Proses scan QR untuk absensi
    public function scan(Request $request)
    {
        // Data NISN dikirim dari hasil scan QR
        $nisn = $request->input('nisn');

        // Cari siswa berdasarkan NISN
        $student = Student::where('nisn', $nisn)->first();

        if (!$student) {
            return response()->json([
                'status' => 'error',
                'message' => 'Siswa tidak ditemukan'
            ], 404);
        }

        $today = Carbon::today()->toDateString();

        // Cek apakah siswa sudah absen hari ini
        $alreadyAttendance = Attendance::where('student_id', $student->id)
            ->whereDate('tanggal', $today)
            ->first();

        if ($alreadyAttendance) {
            return response()->json([
                'status' => 'warning',
                'message' => 'Siswa sudah melakukan absensi hari ini',
                'student' => $student
            ]);
        }

        // Buat absensi baru
        $attendance = Attendance::create([
            'student_id' => $student->id,
            'tanggal' => $today,
            'jam' => Carbon::now()->toTimeString(),
            'status' => 'Hadir',
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Absensi berhasil dicatat',
            'student' => $student,
            'attendance' => $attendance
        ]);
    }
}
