<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            Detail Absensi: {{ $student->user->name ?? '-' }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-5xl mx-auto space-y-6 sm:px-6 lg:px-8">
            <div class="p-4 bg-white rounded-lg shadow-sm">
                <div class="mb-4">
                    <p class="text-sm text-gray-600">NIS: {{ $student->nis }}</p>
                    <p class="text-sm text-gray-600">Kelas: {{ $student->class->name ?? '-' }}</p>
                </div>

                {{-- Filter tanggal --}}
                <form method="GET" action="{{ route('guru.detail.attendances', $student->id) }}"
                    class="flex items-center gap-4 mb-6">
                    <div>
                        <label class="block text-xs text-gray-500">Dari</label>
                        <input type="date" name="from" value="{{ $from }}"
                            class="px-2 py-1 text-sm border rounded">
                    </div>
                    <div>
                        <label class="block text-xs text-gray-500">Sampai</label>
                        <input type="date" name="to" value="{{ $to }}"
                            class="px-2 py-1 text-sm border rounded">
                    </div>
                    <div class="self-end">
                        <button type="submit"
                            class="px-3 py-1 text-sm text-white bg-indigo-600 rounded">Filter</button>
                        <a href="{{ route('guru.detail.attendances', $student->id) }}"
                            class="px-3 py-1 text-sm bg-gray-200 rounded">Reset</a>
                    </div>
                </form>

                @if ($student->attendances->isEmpty())
                    <p class="text-sm text-gray-500">Tidak ada catatan absensi untuk periode ini.</p>
                @else
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
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
                                            {{ \Carbon\Carbon::parse($att->date)->format('d M Y') }}
                                        </td>
                                        <td class="px-4 py-2 text-sm text-gray-900 whitespace-nowrap">
                                            {{ ucfirst($att->status) }}
                                        </td>
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
