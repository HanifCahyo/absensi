<!-- filepath: /Users/hanif/Magang iNDIEPOOL/absensi/resources/views/admin/users/create-student.blade.php -->
<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-semibold">Tambah Siswa</h2>
            <a href="{{ route('admin.users.index') }}"
                class="px-4 py-2 text-gray-600 bg-gray-200 rounded hover:bg-gray-300">
                Kembali
            </a>
        </div>
    </x-slot>

    <div class="max-w-2xl py-6 mx-auto">
        <div class="p-6 bg-white rounded shadow">
            <form action="{{ route('admin.users.student.store') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-sm font-medium text-gray-700">Nama</label>
                    <input type="text" name="name" class="w-full mt-1 border rounded-md" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Email</label>
                    <input type="email" name="email" class="w-full mt-1 border rounded-md" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">NIS</label>
                    <input type="text" name="nis" class="w-full mt-1 border rounded-md" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Kelas</label>
                    <select name="class_id" class="w-full mt-1 border rounded-md" required>
                        <option value="">Pilih Kelas</option>
                        @foreach ($classes as $class)
                            <option value="{{ $class->id }}">{{ $class->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Kontak Orang Tua</label>
                    <input type="text" name="parent_contact" class="w-full mt-1 border rounded-md">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Alamat</label>
                    <textarea name="address" class="w-full mt-1 border rounded-md" rows="3"></textarea>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Nomor Telepon</label>
                    <input type="text" name="phone" class="w-full mt-1 border rounded-md">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Password</label>
                    <input type="password" name="password" class="w-full mt-1 border rounded-md" required>
                </div>
                <button type="submit" class="w-full px-4 py-2 text-white bg-green-600 rounded hover:bg-green-700">
                    Simpan Siswa
                </button>
            </form>
        </div>
    </div>
</x-app-layout>
