<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Student;
use App\Models\Attendance;
use Carbon\Carbon;

class SetAlphaAttendance extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:set-alpha-attendance';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Set attendance status to Alpha for students who did not check in by 07:15 and mark as Terlambat if checked in late';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $today = Carbon::today()->toDateString();

        // Ambil semua siswa
        $students = Student::all();

        foreach ($students as $student) {
            // Cek absensi hari ini
            $attendance = Attendance::where('student_id', $student->id)
                ->whereDate('date', $today)
                ->first();

            // Jika belum absen sama sekali, buat Alpha
            if (!$attendance) {
                Attendance::create([
                    'student_id' => $student->id,
                    'date' => $today,
                    'status' => 'Alpha',
                    'reason' => 'Tidak hadir tanpa keterangan',
                ]);
            }
            // Jika sudah absen masuk, cek apakah terlambat
            elseif ($attendance->check_in) {
                $check_in = Carbon::createFromFormat('H:i:s', $attendance->check_in);
                if ($check_in->gt(Carbon::createFromTime(7, 15, 0)) && $attendance->status == 'Hadir') {
                    $attendance->status = 'Terlambat';
                    $attendance->save();
                }
            }
        }
        $this->info('Absensi Alpha dan Terlambat telah diperbarui.');
    }
}
