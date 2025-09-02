<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    use HasFactory;

    protected $fillable = [
        'nisn',
        'nama',
        'alamat',
        'kelas',
        'qr_code_path',
    ];

    // 1 student can have many attendance records
    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }
}
