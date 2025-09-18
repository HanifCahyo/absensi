<!-- filepath: /Users/hanif/Magang iNDIEPOOL/absensi/resources/views/admin/users/import-students.blade.php -->
<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-semibold">Import Siswa</h2>
            <a href="{{ route('admin.users.index') }}"
                class="px-4 py-2 text-gray-600 bg-gray-200 rounded hover:bg-gray-300">
                Kembali
            </a>
        </div>
    </x-slot>

    <div class="max-w-2xl py-6 mx-auto">
        <div class="p-6 bg-white rounded shadow">
            <div class="mb-4">
                <h3 class="text-lg font-medium">Upload File Excel</h3>
                <p class="mt-1 text-sm text-gray-600">Format Excel dengan header: name, email, nis, class_id,
                    parent_contact, password, address, phone</p>
            </div>

            <form action="{{ route('admin.users.import.students') }}" method="POST" enctype="multipart/form-data"
                class="space-y-4">
                @csrf
                <div>
                    <label class="block text-sm font-medium text-gray-700">File Excel</label>
                    <input type="file" name="file" accept=".xlsx,.xls,.csv" class="w-full mt-1 border rounded-md"
                        required>
                </div>
                <button type="submit" class="w-full px-4 py-2 text-white bg-purple-600 rounded hover:bg-purple-700">
                    Import Data Siswa
                </button>
            </form>
        </div>
    </div>
</x-app-layout>
