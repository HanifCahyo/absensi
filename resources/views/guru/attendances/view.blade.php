<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            Daftar Murid & Riwayat Absensi
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="mx-auto space-y-6 max-w-7xl sm:px-6 lg:px-8">
            <div class="p-4 bg-white rounded-lg shadow-sm">
                <div class="mb-4">
                    <h3 class="text-lg font-medium">Guru: {{ $teacher->name }}</h3>
                </div>

                @if ($students->isEmpty())
                    <p class="text-sm text-gray-500">Belum ada murid untuk kelas yang diampu.</p>
                @else
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead>
                                <tr class="bg-gray-50">
                                    <th
                                        class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                                        #</th>
                                    <th
                                        class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                                        NIS</th>
                                    <th
                                        class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                                        Nama</th>
                                    <th
                                        class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                                        Kelas</th>
                                    <th
                                        class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                                        Absensi Terbaru</th>
                                    <th
                                        class="px-6 py-3 text-xs font-medium tracking-wider text-right text-gray-500 uppercase">
                                        Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach ($students as $index => $student)
                                    <tr>
                                        <td class="px-6 py-4 text-sm text-gray-900 whitespace-nowrap">
                                            {{ $index + 1 }}</td>
                                        <td class="px-6 py-4 text-sm text-gray-900 whitespace-nowrap">
                                            {{ $student->nis }}</td>
                                        <td class="px-6 py-4 text-sm text-gray-900 whitespace-nowrap">
                                            {{ $student->user->name ?? '-' }}</td>
                                        <td class="px-6 py-4 text-sm text-gray-900 whitespace-nowrap">
                                            {{ $student->class->name ?? '-' }}</td>
                                        <td class="px-6 py-4 text-sm text-gray-700 whitespace-nowrap">
                                            @if ($student->attendances->isNotEmpty())
                                                @php $last = $student->attendances->first(); @endphp
                                                <div class="text-sm">
                                                    <div>{{ \Carbon\Carbon::parse($last->date)->format('d M Y') }} —
                                                        {{ $last->status }}</div>
                                                    <div class="text-xs text-gray-500">
                                                        Masuk: {{ $last->check_in ?? '-' }} &nbsp; Keluar:
                                                        {{ $last->check_out ?? '-' }}
                                                    </div>
                                                </div>
                                            @else
                                                <span class="text-sm text-gray-400">Belum ada absensi</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 text-sm font-medium text-right whitespace-nowrap">
                                            <a href="{{ route('guru.detail.attendances', ['id' => $student->id]) }}"
                                                class="text-indigo-600 hover:text-indigo-900">Lihat detail</a>
                                        </td>
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
