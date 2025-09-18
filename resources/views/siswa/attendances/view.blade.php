<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            Riwayat Absensi Saya
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-4xl mx-auto space-y-6 sm:px-6 lg:px-8">
            <div class="p-4 bg-white rounded-lg shadow-sm">
                <h4 class="mb-3 text-lg font-medium">Informasi Siswa</h4>
                <div class="grid grid-cols-1 gap-3 md:grid-cols-2">
                    <div>
                        <span class="text-sm font-medium text-gray-600">Nama:</span>
                        <span class="ml-2 text-sm text-gray-900">{{ $user->name }}</span>
                    </div>
                    <div>
                        <span class="text-sm font-medium text-gray-600">NIS:</span>
                        <span class="ml-2 text-sm text-gray-900">{{ $student->nis }}</span>
                    </div>
                    <div>
                        <span class="text-sm font-medium text-gray-600">Kelas:</span>
                        <span class="ml-2 text-sm text-gray-900">{{ $student->class->name ?? '-' }}</span>
                    </div>
                    <div>
                        <span class="text-sm font-medium text-gray-600">Wali Kelas:</span>
                        <span class="ml-2 text-sm text-gray-900">
                            {{ $student->class->teacher->user->name ?? '-' }}
                        </span>
                    </div>
                    <div>
                        <span class="text-sm font-medium text-gray-600">Kontak Pribadi</span>
                        <span class="ml-2 text-sm text-gray-900">
                            {{ $user->phone ?? '-' }}
                        </span>
                    </div>
                    <div>
                        <span class="text-sm font-medium text-gray-600">Alamat Rumah</span>
                        <span class="ml-2 text-sm text-gray-900">
                            {{ $user->address ?? '-' }}
                        </span>
                    </div>
                    <div>
                        <span class="text-sm font-medium text-gray-600">Kontak Orang Tua</span>
                        <span class="ml-2 text-sm text-gray-900">
                            {{ $student->parent_contact ?? '-' }}
                        </span>
                    </div>
                </div>
            </div>

            <div class="p-4 bg-white rounded-lg shadow-sm">
                @if ($student->attendances->isEmpty())
                    <p class="text-sm text-gray-500">Belum ada catatan absensi.</p>
                @else
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead>
                                <tr class="bg-gray-50">
                                    <th
                                        class="px-4 py-2 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                                        Tanggal</th>
                                    <th
                                        class="px-4 py-2 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                                        Status</th>
                                    <th
                                        class="px-4 py-2 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                                        Check In</th>
                                    <th
                                        class="px-4 py-2 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                                        Check Out</th>
                                    <th
                                        class="px-4 py-2 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                                        Alasan</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach ($student->attendances as $att)
                                    <tr>
                                        <td class="px-4 py-2 text-sm text-gray-900 whitespace-nowrap">
                                            {{ \Carbon\Carbon::parse($att->date)->format('d M Y') }}</td>
                                        <td class="px-4 py-2 text-sm text-gray-900 whitespace-nowrap">
                                            {{ ucfirst($att->status) }}</td>
                                        <td class="px-4 py-2 text-sm text-gray-900 whitespace-nowrap">
                                            {{ $att->check_in ?? '-' }}</td>
                                        <td class="px-4 py-2 text-sm text-gray-900 whitespace-nowrap">
                                            {{ $att->check_out ?? '-' }}</td>
                                        <td class="px-4 py-2 text-sm text-gray-700 whitespace-nowrap">
                                            {{ $att->reason ?? '-' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
