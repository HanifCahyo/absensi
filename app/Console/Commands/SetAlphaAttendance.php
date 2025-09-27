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
    protected $signature = 'app:set-alpha-attendance {--date= : Target date (YYYY-MM-DD)}';

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
        $targetDate = $this->option('date')
            ? Carbon::parse($this->option('date'))->toDateString()
            : Carbon::today()->toDateString();

        $cutoffTime = Carbon::createFromTime(7, 15, 0);
        $currentTime = Carbon::now();

        // Hanya jalankan jika sudah lewat jam cutoff atau untuk tanggal masa lalu
        if (Carbon::parse($targetDate)->isToday() && $currentTime->lt($cutoffTime)) {
            $this->info('Masih belum lewat jam 07:15. Command dibatalkan.');
            return;
        }

        // Ambil semua siswa
        $students = Student::all();
        $alphaCount = 0;
        $processedCount = 0;

        foreach ($students as $student) {
            // Cek absensi pada tanggal target
            $attendance = Attendance::where('student_id', $student->id)
                ->whereDate('date', $targetDate)
                ->first();

            // Jika belum ada record absensi sama sekali, buat Alpha
            if (!$attendance) {
                Attendance::create([
                    'student_id' => $student->id,
                    'date' => $targetDate,
                    'status' => 'Alpha',
                    'reason' => 'Tidak hadir tanpa keterangan',
                    'check_in' => null,
                    'check_out' => null,
                ]);
                $alphaCount++;
            }

            $processedCount++;
        }

        $this->info("Processed {$processedCount} students for date {$targetDate}");
        $this->info("Created {$alphaCount} Alpha attendance records");
    }
}
