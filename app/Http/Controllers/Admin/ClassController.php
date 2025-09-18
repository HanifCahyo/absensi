<?php

namespace App\Http\Controllers\Admin;

use App\Models\ClassModel;
use App\Models\Teacher;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ClassController extends Controller
{
    public function index()
    {
        $classes = ClassModel::with(['teacher.user'])
            ->withCount('students')
            ->orderBy('name')
            ->get();

        return view('admin.classes.index', compact('classes'));
    }

    // Form tambah kelas
    public function create()
    {
        $teachers = Teacher::with('user')->get();
        return view('admin.classes.create', compact('teachers'));
    }

    // Simpan kelas baru
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:classes,name',
            'teacher_id' => 'nullable|exists:teachers,id',
        ]);

        ClassModel::create([
            'name' => $request->name,
            'teacher_id' => $request->teacher_id,
        ]);

        return redirect()->route('admin.classes.index')->with('success', 'Kelas berhasil ditambahkan');
    }

    // Form edit kelas
    public function edit($id)
    {
        $class = ClassModel::findOrFail($id);
        $teachers = Teacher::with('user')->get();
        return view('admin.classes.edit', compact('class', 'teachers'));
    }

    // Update kelas
    public function update(Request $request, $id)
    {
        $class = ClassModel::findOrFail($id);

        $request->validate([
            'name' => 'required|unique:classes,name,' . $class->id,
            'teacher_id' => 'nullable|exists:teachers,id',
        ]);

        $class->update([
            'name' => $request->name,
            'teacher_id' => $request->teacher_id,
        ]);

        return redirect()->route('admin.classes.index')->with('success', 'Kelas berhasil diperbarui');
    }

    // Hapus kelas
    public function destroy($id)
    {
        $class = ClassModel::findOrFail($id);
        $class->delete();

        return redirect()->route('admin.classes.index')->with('success', 'Kelas berhasil dihapus');
    }
}
