<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Student;
use App\Models\Attendance;
use App\Models\Teacher;
use Carbon\Carbon;

class AttendanceController extends Controller
{
    // Tampilkan halaman scan QR
    public function scanPage()
    {
        return view('attendance.scan');
    }

    // Proses scan QR untuk absensi
    public function scan(Request $request)
    {
        // Data NIS dikirim dari hasil scan QR
        $nis = $request->input('nis');
        $izin = $request->input('izin');

        // Cari siswa berdasarkan NIS dengan relasi ke user
        $student = Student::with('user')->where('nis', $nis)->first();
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
            ->whereDate('date', $today)
            ->first();

        // Jika ada izin
        if ($izin) {
            if ($attendance) {
                return response()->json([
                    'status' => 'warning',
                    'message' => 'Absensi sudah tercatat hari ini',
                    'student' => [
                        'nis' => $student->nis,
                        'name' => $student->user->name
                    ],
                ]);
            }
            $attendance = Attendance::create([
                'student_id' => $student->id,
                'date' => $today,
                'status' => 'Izin',
                'reason' => $izin,
                'check_in' => null,
                'check_out' => null,
            ]);
            return response()->json([
                'status' => 'success',
                'message' => 'Absensi izin berhasil dicatat',
                'student' => [
                    'nis' => $student->nis,
                    'name' => $student->user->name
                ],
                'attendance' => $attendance
            ]);
        }

        // Jika sudah ada absensi hari ini
        if ($attendance) {
            // Jika status Alpha, update jadi masuk
            if ($attendance->status == 'Alpha') {
                $check_in = $now->toTimeString();
                $status = $now->gt(Carbon::createFromTime(7, 15, 0)) ? 'Terlambat' : 'Hadir';

                $attendance->update([
                    'check_in' => $check_in,
                    'status' => $status,
                    'reason' => null
                ]);

                return response()->json([
                    'status' => 'success',
                    'message' => 'Absensi masuk berhasil dicatat',
                    'student' => [
                        'nis' => $student->nis,
                        'name' => $student->user->name
                    ],
                    'attendance' => $attendance
                ]);
            }

            // Jika sudah absen masuk dan jam_keluar belum diisi, isi jam_keluar
            if ($attendance->check_in && !$attendance->check_out) {
                $attendance->update(['check_out' => $now->toTimeString()]);

                return response()->json([
                    'status' => 'success',
                    'message' => 'Absensi pulang dicatat',
                    'student' => [
                        'nis' => $student->nis,
                        'name' => $student->user->name
                    ],
                    'attendance' => $attendance
                ]);
            }

            // Jika sudah absen masuk dan pulang
            return response()->json([
                'status' => 'warning',
                'message' => 'Siswa sudah absen pulang hari ini',
                'student' => [
                    'nis' => $student->nis,
                    'name' => $student->user->name
                ],
                'attendance' => $attendance
            ]);
        }

        // Jika belum ada absensi hari ini, catat jam masuk (ini seharusnya tidak terjadi karena command sudah membuat Alpha)
        $check_in = $now->toTimeString();
        $status = $now->gt(Carbon::createFromTime(7, 15, 0)) ? 'Terlambat' : 'Hadir';

        $attendance = Attendance::create([
            'student_id' => $student->id,
            'date' => $today,
            'check_in' => $check_in,
            'status' => $status,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Absensi masuk berhasil dicatat',
            'student' => [
                'nis' => $student->nis,
                'name' => $student->user->name
            ],
            'attendance' => $attendance
        ]);
    }

    // Halaman untuk Guru (web) — ambil murid & riwayat absensi mereka
    public function teacherAttendancesPage(Request $request)
    {
        $user = $request->user();

        if (!$user || $user->role !== 'guru') {
            abort(403, 'Unauthorized');
        }

        // ambil teacher record
        $teacher = Teacher::where('user_id', $user->id)->firstOrFail();

        // ambil kelas yang diampu (bisa satu atau lebih) lalu muridnya,
        // eager load attendances (limit bisa ditambahkan jika perlu)
        $students = Student::whereHas('class', function ($q) use ($teacher) {
            $q->where('teacher_id', $teacher->id);
        })
            ->with([
                'class',
                'attendances' => function ($q) {
                    $q->orderBy('date', 'desc');
                }
            ])
            ->get();

        // kirim ke view
        return view('guru.attendances.view', [
            'teacher' => $user,
            'students' => $students,
        ]);
    }

    public function teacherStudentDetail(Request $request, $id)
    {
        $user = $request->user();
        if (!$user || $user->role !== 'guru') {
            abort(403, 'Unauthorized');
        }

        $teacher = Teacher::where('user_id', $user->id)->firstOrFail();

        // filter tanggal dari request
        $from = $request->query('from');
        $to = $request->query('to');

        $student = Student::where('id', $id)
            ->whereHas('class', function ($q) use ($teacher) {
                $q->where('teacher_id', $teacher->id);
            })
            ->with([
                'class',
                'attendances' => function ($q) use ($from, $to) {
                    $q->orderBy('date', 'desc');
                    if ($from) {
                        $q->where('date', '>=', Carbon::parse($from));
                    }
                    if ($to) {
                        $q->where('date', '<=', Carbon::parse($to));
                    }
                }
            ])
            ->firstOrFail();

        return view('guru.attendances.detail', [
            'student' => $student,
            'from' => $from,
            'to' => $to,
        ]);
    }

    // Halaman untuk Siswa (web) — lihat absensi dirinya sendiri
    public function studentAttendancesPage(Request $request)
    {
        $user = $request->user();

        if (!$user || $user->role !== 'siswa') {
            abort(403, 'Unauthorized');
        }

        $student = Student::where('user_id', $user->id)
            ->with([
                'class.teacher.user',
                'attendances' => function ($q) {
                    $q->orderBy('date', 'desc');
                }
            ])
            ->firstOrFail();

        return view('siswa.attendances.view', [
            'student' => $student,
            'user' => $user,
        ]);
    }
}
