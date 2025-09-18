<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Teacher;
use App\Models\Student;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\TeachersImport;
use App\Imports\StudentsImport;
use Illuminate\Support\Facades\Storage;
use BaconQrCode\Writer;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;

class AdminUserController extends Controller
{
    // List users with filter
    public function index(Request $request)
    {
        $filter = $request->get('filter', 'all'); // all, guru, siswa

        $query = User::with(['teacher.classes', 'student.class'])
            ->whereIn('role', ['guru', 'siswa']); // Exclude admin

        if ($filter === 'guru') {
            $query->where('role', 'guru');
        } elseif ($filter === 'siswa') {
            $query->where('role', 'siswa');
        }

        $users = $query->orderBy('name')->get();

        return view('admin.users.index', compact('users', 'filter'));
    }

    // Halaman tambah guru
    public function createTeacher()
    {
        return view('admin.users.create-teacher');
    }

    // Halaman tambah siswa
    public function createStudent()
    {
        $classes = \App\Models\ClassModel::all();
        return view('admin.users.create-student', compact('classes'));
    }

    // Halaman import guru
    public function importTeachersPage()
    {
        return view('admin.users.import-teachers');
    }

    // Halaman import siswa
    public function importStudentsPage()
    {
        return view('admin.users.import-students');
    }

    // Store methods (sama seperti sebelumnya)
    public function storeTeacher(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:191',
            'email' => 'required|email|unique:users',
            'nip' => 'required|unique:teachers',
            'password' => 'required|min:6',
            'address' => 'nullable|string',
            'phone' => 'nullable|string|max:20',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'guru',
            'phone' => $request->phone,
            'address' => $request->address,
        ]);

        Teacher::create([
            'user_id' => $user->id,
            'nip' => $request->nip,
        ]);

        return redirect()->route('admin.users.index')->with('success', 'Guru berhasil ditambahkan.');
    }

    public function storeStudent(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:191',
            'email' => 'required|email|unique:users',
            'nis' => 'required|unique:students',
            'class_id' => 'required|exists:classes,id',
            'password' => 'required|min:6',
            'address' => 'nullable|string',
            'phone' => 'nullable|string|max:20',
            'parent_contact' => 'nullable|string|max:20',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'siswa',
            'phone' => $request->phone,
            'address' => $request->address,
        ]);

        $student = Student::create([
            'user_id' => $user->id,
            'nis' => $request->nis,
            'class_id' => $request->class_id,
            'parent_contact' => $request->parent_contact,
        ]);

        // Generate QR Code
        $renderer = new ImageRenderer(
            new RendererStyle(200),
            new SvgImageBackEnd()
        );
        $writer = new Writer($renderer);
        $qrCode = $writer->writeString($student->nis);
        $path = 'qrcodes/' . $student->nis . '.svg';
        Storage::disk('public')->put($path, $qrCode);
        $student->update(['qr_code_path' => $path]);

        return redirect()->route('admin.users.index')->with('success', 'Siswa berhasil ditambahkan dengan QR Code');
    }

    // Import methods (sama seperti sebelumnya)
    public function importTeachers(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,csv,xls'
        ]);

        Excel::import(new TeachersImport, $request->file('file'));

        return redirect()->route('admin.users.index')->with('success', 'Data guru berhasil diimport');
    }

    public function importStudents(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,csv,xls'
        ]);

        Excel::import(new StudentsImport, $request->file('file'));

        return redirect()->route('admin.users.index')->with('success', 'Data siswa berhasil diimport');
    }
}
