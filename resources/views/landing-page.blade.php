<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name', 'Sistem Absensi') }} - Manajemen Kehadiran Siswa</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
</head>

<body class="flex items-center justify-center min-h-screen bg-gradient-to-br from-blue-50 to-indigo-100">
    <div class="max-w-4xl px-6 py-12 mx-auto">
        <!-- Hero Section -->
        <div class="mb-12 text-center">
            <!-- Logo/Brand -->
            <div class="mb-8">
                <div class="inline-flex items-center justify-center w-20 h-20 mb-4 bg-blue-600 rounded-full">
                    <i class="text-2xl text-white fas fa-user-check"></i>
                </div>
                <h1 class="mb-4 text-4xl font-bold text-gray-800 md:text-6xl">
                    <span class="text-blue-600">Sistem</span> <span class="text-indigo-600">Absensi</span>
                </h1>
                <p class="text-xl font-light text-gray-600 md:text-2xl">
                    Solusi Digital untuk Manajemen Kehadiran Siswa
                </p>
            </div>

            <!-- Features -->
            <div class="grid gap-8 mb-12 md:grid-cols-3">
                <div class="p-6 transition-shadow bg-white shadow-lg rounded-xl hover:shadow-xl">
                    <div class="flex items-center justify-center w-12 h-12 mx-auto mb-4 bg-blue-100 rounded-lg">
                        <i class="text-xl text-blue-600 fas fa-users"></i>
                    </div>
                    <h3 class="mb-2 text-lg font-semibold text-gray-800">Manajemen Siswa</h3>
                    <p class="text-gray-600">Kelola data siswa dan kelas dengan mudah dan efisien</p>
                </div>

                <div class="p-6 transition-shadow bg-white shadow-lg rounded-xl hover:shadow-xl">
                    <div class="flex items-center justify-center w-12 h-12 mx-auto mb-4 bg-green-100 rounded-lg">
                        <i class="text-xl text-green-600 fas fa-calendar-check"></i>
                    </div>
                    <h3 class="mb-2 text-lg font-semibold text-gray-800">Pencatatan Absensi</h3>
                    <p class="text-gray-600">Catat kehadiran siswa secara real-time dan akurat</p>
                </div>

                <div class="p-6 transition-shadow bg-white shadow-lg rounded-xl hover:shadow-xl">
                    <div class="flex items-center justify-center w-12 h-12 mx-auto mb-4 bg-purple-100 rounded-lg">
                        <i class="text-xl text-purple-600 fas fa-chart-pie"></i>
                    </div>
                    <h3 class="mb-2 text-lg font-semibold text-gray-800">Laporan Kehadiran</h3>
                    <p class="text-gray-600">Analisis data kehadiran untuk monitoring yang lebih baik</p>
                </div>
            </div>

            <!-- Call to Action -->
            <div class="relative z-10 p-8 bg-white shadow-xl rounded-2xl">
                <h2 class="mb-4 text-2xl font-bold text-gray-800 md:text-3xl">
                    Siap Memulai Sekolah Digital Anda?
                </h2>
                <p class="mb-8 text-lg text-gray-600">
                    Masuk ke sistem dan nikmati kemudahan mengelola Sistem Pembelajaran Anda
                </p>

                <!-- Login Button -->
                <div class="space-y-4">
                    @if (Route::has('login'))
                        @auth
                            @php
                                $user = auth()->user();
                                $dashboardRoute = match ($user->role) {
                                    'admin' => 'admin.dashboard',
                                    'guru' => 'guru.dashboard',
                                    'siswa' => 'siswa.dashboard',
                                    'satpam' => 'satpam.dashboard',
                                    default => 'dashboard',
                                };
                                $roleText = match ($user->role) {
                                    'admin' => 'Dashboard Admin',
                                    'guru' => 'Dashboard Guru',
                                    'siswa' => 'Dashboard Siswa',
                                    'satpam' => 'Dashboard Satpam',
                                    default => 'Dashboard',
                                };
                            @endphp

                            @if (Route::has($dashboardRoute))
                                <a href="{{ route($dashboardRoute) }}"
                                    class="inline-flex items-center px-8 py-4 font-semibold text-white transition-colors duration-200 bg-blue-600 shadow-lg hover:bg-blue-700 rounded-xl hover:shadow-xl">
                                    <i class="mr-3 fas fa-tachometer-alt"></i>
                                    {{ $roleText }}
                                </a>
                            @else
                                <div class="p-4 text-red-600 bg-red-100 rounded-lg">
                                    Dashboard untuk role {{ $user->role }} belum tersedia.
                                </div>
                            @endif
                        @else
                            <a href="{{ route('login') }}"
                                class="inline-flex items-center px-8 py-4 font-semibold text-white transition-colors duration-200 bg-blue-600 shadow-lg hover:bg-blue-700 rounded-xl hover:shadow-xl">
                                <i class="mr-3 fas fa-sign-in-alt"></i>
                                Masuk ke Sistem
                            </a>
                        @endauth
                    @endif
                </div>
            </div>

            <!-- Additional Info -->
            <div class="mt-12 text-center">
                <p class="text-sm text-gray-500">
                    © {{ date('Y') }} Absensi. Sistem manajemen absensi sekolah yang mudah dan terpercaya.
                </p>
            </div>
        </div>
    </div>

    <!-- Background Pattern -->
    <div class="fixed inset-0 overflow-hidden -z-10">
        <div
            class="absolute bg-blue-300 rounded-full -top-40 -right-32 w-80 h-80 mix-blend-multiply filter blur-xl opacity-20">
        </div>
        <div
            class="absolute bg-purple-300 rounded-full -bottom-40 -left-32 w-80 h-80 mix-blend-multiply filter blur-xl opacity-20">
        </div>
        <div
            class="absolute transform -translate-x-1/2 bg-indigo-300 rounded-full top-40 left-1/2 w-80 h-80 mix-blend-multiply filter blur-xl opacity-20">
        </div>
    </div>
</body>

</html>
