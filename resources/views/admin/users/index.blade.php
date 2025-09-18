<!-- filepath: /Users/hanif/Magang iNDIEPOOL/absensi/resources/views/admin/users/index.blade.php -->
<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold">Manajemen User</h2>
    </x-slot>

    <div class="max-w-6xl px-4 py-6 mx-auto space-y-6">

        {{-- Action Buttons --}}
        <div class="flex flex-wrap gap-3">
            <a href="{{ route('admin.users.teacher.create') }}"
                class="px-4 py-2 text-white bg-blue-600 rounded hover:bg-blue-700">
                + Tambah Guru
            </a>
            <a href="{{ route('admin.users.student.create') }}"
                class="px-4 py-2 text-white bg-green-600 rounded hover:bg-green-700">
                + Tambah Siswa
            </a>
            <a href="{{ route('admin.users.teacher.import.page') }}"
                class="px-4 py-2 text-white bg-indigo-600 rounded hover:bg-indigo-700">
                Import Guru
            </a>
            <a href="{{ route('admin.users.student.import.page') }}"
                class="px-4 py-2 text-white bg-purple-600 rounded hover:bg-purple-700">
                Import Siswa
            </a>
        </div>

        {{-- Filter & User List --}}
        <div class="p-4 bg-white rounded shadow">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium">Daftar User</h3>

                {{-- Filter --}}
                <div class="flex gap-2">
                    <a href="{{ route('admin.users.index', ['filter' => 'all']) }}"
                        class="px-3 py-1 text-sm rounded {{ $filter === 'all' ? 'bg-gray-800 text-white' : 'bg-gray-200' }}">
                        Semua
                    </a>
                    <a href="{{ route('admin.users.index', ['filter' => 'guru']) }}"
                        class="px-3 py-1 text-sm rounded {{ $filter === 'guru' ? 'bg-blue-600 text-white' : 'bg-gray-200' }}">
                        Guru
                    </a>
                    <a href="{{ route('admin.users.index', ['filter' => 'siswa']) }}"
                        class="px-3 py-1 text-sm rounded {{ $filter === 'siswa' ? 'bg-green-600 text-white' : 'bg-gray-200' }}">
                        Siswa
                    </a>
                </div>
            </div>

            @if ($users->isEmpty())
                <p class="text-sm text-gray-500">Belum ada user</p>
            @else
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead>
                            <tr class="bg-gray-50">
                                <th class="px-4 py-2 text-xs font-medium text-left text-gray-500 uppercase">Nama</th>
                                <th class="px-4 py-2 text-xs font-medium text-left text-gray-500 uppercase">Email</th>
                                <th class="px-4 py-2 text-xs font-medium text-left text-gray-500 uppercase">Role</th>
                                <th class="px-4 py-2 text-xs font-medium text-left text-gray-500 uppercase">Keterangan
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach ($users as $user)
                                <tr>
                                    <td class="px-4 py-2 text-sm text-gray-900">{{ $user->name }}</td>
                                    <td class="px-4 py-2 text-sm text-gray-900">{{ $user->email }}</td>
                                    <td class="px-4 py-2 text-sm">
                                        <span
                                            class="px-2 py-1 text-xs rounded-full {{ $user->role === 'guru' ? 'bg-blue-100 text-blue-800' : 'bg-green-100 text-green-800' }}">
                                            {{ ucfirst($user->role) }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-2 text-sm text-gray-900">
                                        @if ($user->role === 'guru' && $user->teacher)
                                            <div>NIP: {{ $user->teacher->nip }}</div>
                                            @if ($user->teacher->classes->count() > 0)
                                                <div class="text-xs text-gray-500">
                                                    Wali Kelas:
                                                    {{ $user->teacher->classes->pluck('name')->join(', ') }}
                                                </div>
                                            @endif
                                        @elseif($user->role === 'siswa' && $user->student)
                                            <div>NIS: {{ $user->student->nis }}</div>
                                            <div class="text-xs text-gray-500">Kelas:
                                                {{ $user->student->class->name ?? '-' }}</div>
                                        @else
                                            -
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>

    </div>
</x-app-layout>
