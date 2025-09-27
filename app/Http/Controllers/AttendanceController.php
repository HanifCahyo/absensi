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
        return view('satpam.attendance.scan');
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
        $cutoffTime = Carbon::createFromTime(7, 15, 0);

        // Cek absensi hari ini
        $attendance = Attendance::where('student_id', $student->id)
            ->whereDate('date', $today)
            ->first();

        // Jika sudah ada absensi hari ini
        if ($attendance) {
            // Jika status Alpha, update jadi masuk
            if ($attendance->status == 'Alpha') {
                $check_in = $now->toTimeString();
                $status = $now->gt($cutoffTime) ? 'Terlambat' : 'Hadir';

                $attendance->update([
                    'check_in' => $check_in,
                    'status' => $status,
                    'reason' => $status === 'Terlambat' ? 'Datang terlambat' : null,
                ]);

                return response()->json([
                    'status' => 'success',
                    'message' => 'Absensi masuk berhasil dicatat',
                    'student' => [
                        'nis' => $student->nis,
                        'name' => $student->user->name
                    ],
                    'attendance' => $attendance,
                ]);
            }

            // Jika sudah check-in tapi belum check-out, isi column check-out
            if ($attendance->check_in && !$attendance->check_out && in_array($attendance->status, ['Hadir', 'Terlambat'])) {
                $attendance->update([
                    'check_out' => $now->toTimeString(),
                ]);

                return response()->json([
                    'status' => 'success',
                    'message' => 'Absensi pulang berhasil dicatat',
                    'student' => [
                        'nis' => $student->nis,
                        'name' => $student->user->name
                    ],
                    'attendance' => $attendance,
                ]);
            }


            // Jika sudah check-in dan check-out, atau status Izin
            return response()->json([
                'status' => 'warning',
                'message' => 'Absensi sudah tercatat hari ini',
                'student' => [
                    'nis' => $student->nis,
                    'name' => $student->user->name
                ],
                'attendance' => $attendance,
            ]);
        }

        // Buat absensi baru
        $check_in = $now->toTimeString();
        $status = $now->gt($cutoffTime) ? 'Terlambat' : 'Hadir';

        $attendance = Attendance::create([
            'student_id' => $student->id,
            'date' => $today,
            'check_in' => $check_in,
            'status' => $status,
            'reason' => $status === 'Terlambat' ? 'Datang terlambat' : null,
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

    // Proses input izin manual
    public function setPermission(Request $request)
    {
        $nis = $request->input('nis');
        $reason = $request->input('reason');

        // Cari siswa
        $student = Student::where('nis', $nis)->first();

        // Set izin
        $attendance = Attendance::updateOrCreate([
            'student_id' => $student->id,
            'date' => Carbon::today(),
        ], [
            'status' => 'Izin',
            'reason' => $reason,
            'check_in' => null,
            'check_out' => null,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Status izin berhasil dicatat',
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
