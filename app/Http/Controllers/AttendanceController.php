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
        $izin = $request->input('izin');

        // Cari siswa berdasarkan NISN
        $student = Student::where('nisn', $nisn)->first();
        if (!$student) {
            return response()->json([
                'status' => 'error',
                'message' => 'Siswa tidak ditemukan'
            ], 404);
        }

        $today = Carbon::today()->toDateString();
        $now = Carbon::now();

        // Cek absensi hari ini
        $attendance = Attendance::where('student_id', $student->id)
            ->whereDate('tanggal', $today)
            ->first();

        // Jika ada izin
        if ($izin) {
            if ($attendance) {
                return response()->json([
                    'status' => 'warning',
                    'message' => 'Absensi sudah tercatat hari ini',
                    'student' => $student,
                ]);
            }
            $attendance = Attendance::create([
                'student_id' => $student->id,
                'tanggal' => $today,
                'status' => 'Izin',
                'keterangan' => $izin,
                'jam_masuk' => null,
                'jam_keluar' => null,
            ]);
            return response()->json([
                'status' => 'success',
                'message' => 'Absensi izin berhasil dicatat',
                'student' => $student,
                'attendance' => $attendance
            ]);
        }

        // Jika sudah ada absensi hari ini
        if ($attendance) {
            // Jika status Alpha, update jadi masuk
            if ($attendance->status == 'Alpha') {
                $jam_masuk = $now->toTimeString();
                $status = $now->gt(Carbon::createFromTime(7, 15, 0)) ? 'Terlambat' : 'Hadir';

                $attendance->jam_masuk = $jam_masuk;
                $attendance->status = $status;
                $attendance->keterangan = null;
                $attendance->save();

                return response()->json([
                    'status' => 'success',
                    'message' => 'Absensi masuk berhasil dicatat',
                    'student' => $student,
                    'attendance' => $attendance
                ]);
            }

            // Jika sudah absen masuk dan jam_keluar belum diisi, isi jam_keluar
            if (!$attendance->jam_keluar) {
                $attendance->jam_keluar = $now->toTimeString();
                $attendance->save();

                return response()->json([
                    'status' => 'success',
                    'message' => 'Absensi pulang dicatat',
                    'student' => $student,
                    'attendance' => $attendance
                ]);
            }

            // Jika sudah absen masuk dan pulang
            return response()->json([
                'status' => 'warning',
                'message' => 'Siswa sudah absen pulang hari ini',
                'student' => $student,
                'attendance' => $attendance
            ]);
        }

        // Jika belum ada absensi hari ini, catat jam masuk
        if (!$attendance) {
            // Cek terlambat (misal jam masuk maksimal 07:15)
            $jam_masuk = $now->toTimeString();
            $status = $now->gt(Carbon::createFromTime(7, 15, 0)) ? 'Terlambat' : 'Hadir';

            $attendance = Attendance::create([
                'student_id' => $student->id,
                'tanggal' => $today,
                'jam_masuk' => $jam_masuk,
                'status' => $status,
            ]);
            return response()->json([
                'status' => 'success',
                'message' => 'Absensi masuk berhasil dicatat',
                'student' => $student,
                'attendance' => $attendance
            ]);
        }

        // // Jika sudah absen masuk, scan kedua = jam pulang
        // if ($attendance->jam_keluar) {
        //     return response()->json([
        //         'status' => 'warning',
        //         'message' => 'Siswa sudah absen pulang hari ini',
        //         'student' => $student,
        //         'attendance' => $attendance
        //     ]);
        // }

        // $attendance->jam_keluar = $now->toTimeString();
        // $attendance->save();

        // return response()->json([
        //     'status' => 'success',
        //     'message' => 'Absensi pulang dicatat',
        //     'student' => $student,
        //     'attendance' => $attendance
        // ]);

        // // Cek apakah siswa sudah absen hari ini
        // $alreadyAttendance = Attendance::where('student_id', $student->id)
        //     ->whereDate('tanggal', $today)
        //     ->first();

        // if ($alreadyAttendance) {
        //     return response()->json([
        //         'status' => 'warning',
        //         'message' => 'Siswa sudah melakukan absensi hari ini',
        //         'student' => $student
        //     ]);
        // }

        // // Buat absensi baru
        // $attendance = Attendance::create([
        //     'student_id' => $student->id,
        //     'tanggal' => $today,
        //     'jam' => Carbon::now()->toTimeString(),
        //     'status' => 'Hadir',
        // ]);

        // return response()->json([
        //     'status' => 'success',
        //     'message' => 'Absensi berhasil dicatat',
        //     'student' => $student,
        //     'attendance' => $attendance
        // ]);
    }
}
