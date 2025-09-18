<!-- filepath: /Users/hanif/Magang iNDIEPOOL/absensi/resources/views/admin/classes/index.blade.php -->
<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold">Daftar Kelas</h2>
    </x-slot>

    <div class="max-w-5xl px-4 py-6 mx-auto">
        <a href="{{ route('admin.classes.create') }}"
            class="px-4 py-2 text-white bg-indigo-600 rounded hover:bg-indigo-700">
            Tambah Kelas
        </a>

        <div class="p-4 mt-6 bg-white rounded shadow">
            @if (session('success'))
                <div class="p-3 mb-3 text-green-700 bg-green-100 rounded">{{ session('success') }}</div>
            @endif

            @if (session('error'))
                <div class="p-3 mb-3 text-red-700 bg-red-100 rounded">{{ session('error') }}</div>
            @endif

            <table class="min-w-full divide-y divide-gray-200">
                <thead>
                    <tr class="bg-gray-50">
                        <th class="px-4 py-2 text-xs font-medium text-left text-gray-500 uppercase">Nama Kelas</th>
                        <th class="px-4 py-2 text-xs font-medium text-left text-gray-500 uppercase">Wali Kelas</th>
                        <th class="px-4 py-2 text-xs font-medium text-left text-gray-500 uppercase">Jumlah Siswa</th>
                        <th class="px-4 py-2 text-xs font-medium text-left text-gray-500 uppercase">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse ($classes as $class)
                        <tr>
                            <td class="px-4 py-2 text-sm text-gray-900">{{ $class->name }}</td>
                            <td class="px-4 py-2 text-sm text-gray-900">
                                @if ($class->teacher && $class->teacher->user)
                                    {{ $class->teacher->user->name }}
                                    @if ($class->teacher->nip)
                                        <div class="text-xs text-gray-500">NIP: {{ $class->teacher->nip }}</div>
                                    @endif
                                @else
                                    <span class="text-gray-400">Belum ada wali kelas</span>
                                @endif
                            </td>
                            <td class="px-4 py-2 text-sm text-gray-900">
                                {{ $class->students_count ?? 0 }} siswa
                            </td>
                            <td class="px-4 py-2 text-sm">
                                <a href="{{ route('admin.classes.edit', $class->id) }}"
                                    class="mr-3 text-indigo-600 hover:text-indigo-900">
                                    Edit
                                </a>
                                <form action="{{ route('admin.classes.destroy', $class->id) }}" method="POST"
                                    class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-900"
                                        onclick="return confirm('Hapus kelas ini? Pastikan tidak ada siswa di kelas ini.')">
                                        Hapus
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-4 py-8 text-center text-gray-500">
                                <p>Belum ada data kelas.</p>
                                <a href="{{ route('admin.classes.create') }}"
                                    class="mt-2 text-indigo-600 hover:text-indigo-900">
                                    Tambah kelas pertama
                                </a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>
