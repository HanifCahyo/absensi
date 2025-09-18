<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold">Tambah Kelas</h2>
    </x-slot>

    <div class="max-w-md p-6 py-6 mx-auto bg-white rounded shadow">
        <form action="{{ route('admin.classes.store') }}" method="POST">
            @csrf
            <div class="mb-4">
                <label class="block text-sm font-medium">Nama Kelas</label>
                <input type="text" name="name" class="w-full p-2 border rounded" required>
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium">Wali Kelas</label>
                <select name="teacher_id" class="w-full p-2 border rounded">
                    <option value="">-- Pilih Guru --</option>
                    @foreach ($teachers as $teacher)
                        <option value="{{ $teacher->id }}">{{ $teacher->user->name }}</option>
                    @endforeach
                </select>
            </div>
            <button class="px-4 py-2 text-white bg-indigo-600 rounded">Simpan</button>
        </form>
    </div>
</x-app-layout>
