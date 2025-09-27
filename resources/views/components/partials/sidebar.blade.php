<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <a href="#" class="brand-link">
        <span class="brand-text font-weight-light">Absensi</span>
    </a>

    <!-- Sidebar -->
    <div class="sidebar d-flex flex-column" style="min-height: 100vh;">
        <!-- Sidebar user (optional) -->
        <div class="pb-3 mt-3 mb-3 user-panel d-flex">

            <div class="info">
                <a href="#" class="d-block">Halo! {{ Auth::user()->name }}</a>
            </div>
        </div>

        <!-- Sidebar Menu -->
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu"
                data-accordion="false">
                {{-- ROLE ADMIN --}}
                @if (request()->is('admin*'))
                    <li class="nav-item">
                        <a href="{{ route('admin.dashboard') }}"
                            class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-tachometer-alt"></i>
                            <p>Dashboard Admin</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('admin.users.index') }}"
                            class="nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-users"></i>
                            <p>Manajemen User</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('admin.classes.index') }}"
                            class="nav-link {{ request()->routeIs('admin.classes.*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-school"></i>
                            <p>Kelas</p>
                        </a>
                    </li>
                @endif

                {{-- ROLE GURU --}}
                @if (request()->is('guru*'))
                    <li class="nav-item">
                        <a href="{{ route('guru.dashboard') }}"
                            class="nav-link {{ request()->routeIs('guru.dashboard') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-th"></i>
                            <p>Dashboard Guru</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('guru.attendances') }}"
                            class="nav-link {{ request()->routeIs('guru.attendances') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-check-square"></i>
                            <p>Absensi</p>
                        </a>
                    </li>
                @endif

                {{-- ROLE SISWA --}}
                @if (request()->is('siswa*'))
                    <li class="nav-item">
                        <a href="{{ route('siswa.dashboard') }}"
                            class="nav-link {{ request()->routeIs('siswa.dashboard') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-th"></i>
                            <p>Dashboard Siswa</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('siswa.attendances') }}"
                            class="nav-link {{ request()->routeIs('siswa.attendances') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-check-square"></i>
                            <p>Absensi</p>
                        </a>
                    </li>
                @endif

                {{-- ROLE SATPAM --}}
                @if (request()->is('satpam*'))
                    <li class="nav-item">
                        <a href="{{ route('satpam.dashboard') }}"
                            class="nav-link {{ request()->routeIs('satpam.dashboard') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-th"></i>
                            <p>Dashboard Satpam</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('satpam.attendance.scan.page') }}"
                            class="nav-link {{ request()->routeIs('satpam.attendance.scan.page') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-check-square"></i>
                            <p>Absensi</p>
                        </a>
                    </li>
                @endif

                <!-- Logout Menu -->
                <li class="mt-auto nav-item">
                    <form method="POST" action="{{ route('logout') }}" class="p-0 nav-link">
                        @csrf
                        <button type="submit" class="text-left text-white btn btn-link nav-link w-100"
                            style="text-decoration: none; border: none; background: none;"
                            onclick="return confirm('Yakin ingin logout?')">
                            <i class="nav-icon fas fa-sign-out-alt text-danger"></i>
                            <p class="text-white">Logout</p>
                        </button>
                    </form>
                </li>
            </ul>
        </nav>
    </div>
</aside>
