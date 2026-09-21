<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'School Discipline') }} - @yield('title', 'Dashboard')</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body>
        <div class="d-flex min-vh-100">
            <!-- Sidebar -->
            <aside class="sidebar flex-shrink-0" id="sidebar" x-data="{ open: false }">
                <div class="sidebar-brand d-flex align-items-center justify-content-between">
                    <span class="mb-0">
                        <i class="bi bi-shield-check me-2"></i>
                        School Discipline
                    </span>
                    <button class="btn btn-link text-white d-lg-none p-0" onclick="toggleSidebar()">
                        <i class="bi bi-x-lg"></i>
                    </button>
                </div>

                <div class="sidebar-section-title">Main Menu</div>
                <ul class="nav flex-column mb-2">
                    <li class="nav-item">
                        <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                            <i class="bi bi-speedometer2 me-2"></i>
                            Dashboard
                        </a>
                    </li>
                </ul>

                @if(auth()->user()->isSuperAdmin())
                    <div class="sidebar-section-title">Super Admin</div>
                    <ul class="nav flex-column mb-2">
                        <li class="nav-item">
                            <a href="{{ route('schools.index') }}" class="nav-link {{ request()->routeIs('schools.*') ? 'active' : '' }}">
                                <i class="bi bi-building me-2"></i>
                                Schools
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('users.index') }}" class="nav-link {{ request()->routeIs('users.*') ? 'active' : '' }}">
                                <i class="bi bi-people me-2"></i>
                                Users
                            </a>
                        </li>
                    </ul>
                @endif

                <div class="sidebar-section-title">Data Akademik</div>
                <ul class="nav flex-column mb-2">
                    <li class="nav-item">
                        <a href="{{ route('academic-years.index') }}" class="nav-link {{ request()->routeIs('academic-years.*') ? 'active' : '' }}">
                            <i class="bi bi-calendar3 me-2"></i>
                            Tahun Ajaran
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('departments.index') }}" class="nav-link {{ request()->routeIs('departments.*') ? 'active' : '' }}">
                            <i class="bi bi-book me-2"></i>
                            Program Keahlian
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('classes.index') }}" class="nav-link {{ request()->routeIs('classes.*') ? 'active' : '' }}">
                            <i class="bi bi-collection me-2"></i>
                            Kelas
                        </a>
                    </li>
                </ul>

                <div class="sidebar-section-title">Data Siswa</div>
                <ul class="nav flex-column mb-2">
                    <li class="nav-item">
                        <a href="{{ route('students.index') }}" class="nav-link {{ request()->routeIs('students.*') ? 'active' : '' }}">
                            <i class="bi bi-mortarboard me-2"></i>
                            Daftar Siswa
                        </a>
                    </li>
                </ul>

                <div class="sidebar-section-title">PKS</div>
                <ul class="nav flex-column mb-2">
                    <li class="nav-item">
                        <a href="{{ route('pks-members.index') }}" class="nav-link {{ request()->routeIs('pks-members.*') ? 'active' : '' }}">
                            <i class="bi bi-people me-2"></i>
                            Anggota PKS
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('pks-shifts.index') }}" class="nav-link {{ request()->routeIs('pks-shifts.*') ? 'active' : '' }}">
                            <i class="bi bi-clock me-2"></i>
                            Shift Piket
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('pks-duty-locations.index') }}" class="nav-link {{ request()->routeIs('pks-duty-locations.*') ? 'active' : '' }}">
                            <i class="bi bi-geo-alt me-2"></i>
                            Lokasi Piket
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('pks-duty-schedules.index') }}" class="nav-link {{ request()->routeIs('pks-duty-schedules.*') ? 'active' : '' }}">
                            <i class="bi bi-calendar-check me-2"></i>
                            Jadwal Piket
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('pks-duty-assignments.index') }}" class="nav-link {{ request()->routeIs('pks-duty-assignments.*') ? 'active' : '' }}">
                            <i class="bi bi-person-check me-2"></i>
                            Penugasan Piket
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('pks-duty-attendances.index') }}" class="nav-link {{ request()->routeIs('pks-duty-attendances.*') ? 'active' : '' }}">
                            <i class="bi bi-clipboard-check me-2"></i>
                            Kehadiran Piket
                        </a>
                    </li>
                </ul>

                <div class="sidebar-section-title">Settings</div>
                <ul class="nav flex-column">
                    <li class="nav-item">
                        <a href="{{ route('profile.edit') }}" class="nav-link {{ request()->routeIs('profile.edit') ? 'active' : '' }}">
                            <i class="bi bi-person me-2"></i>
                            Profile
                        </a>
                    </li>
                </ul>

                <!-- School Context -->
                @if(auth()->user()->school)
                    <div class="sidebar-section-title mt-3">Current School</div>
                    <div class="px-3 py-2 mx-2 rounded bg-dark bg-opacity-25">
                        <div class="small fw-medium text-white text-truncate">
                            {{ auth()->user()->school->name }}
                        </div>
                        <div class="small text-white-50 text-truncate">
                            {{ auth()->user()->school->email ?? 'No email' }}
                        </div>
                    </div>
                @endif
            </aside>

            <!-- Sidebar Overlay for Mobile -->
            <div class="sidebar-overlay" id="sidebarOverlay" onclick="toggleSidebar()"></div>

            <!-- Main Content -->
            <div class="flex-grow-1 d-flex flex-column">
                <!-- Top Navigation -->
                <nav class="navbar navbar-expand-lg navbar-light bg-white border-bottom shadow-sm">
                    <div class="container-fluid">
                        <button class="btn btn-link text-dark d-lg-none p-0 me-2" onclick="toggleSidebar()">
                            <i class="bi bi-list fs-4"></i>
                        </button>

                        <span class="navbar-brand mb-0 h5 d-none d-sm-inline">@yield('title', 'Dashboard')</span>

                        <div class="d-flex align-items-center gap-2">
                            <span class="text-secondary small d-none d-md-inline">
                                {{ auth()->user()->name }}
                                <span class="badge bg-{{ auth()->user()->isSuperAdmin() ? 'danger' : 'primary' }} ms-1">
                                    {{ ucwords(str_replace('_', ' ', auth()->user()->role)) }}
                                </span>
                            </span>

                            <div class="dropdown">
                                <button class="btn btn-light btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                    <i class="bi bi-person-circle"></i>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end">
                                    <li>
                                        <a class="dropdown-item" href="{{ route('profile.edit') }}">
                                            <i class="bi bi-person me-2"></i>Profile
                                        </a>
                                    </li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li>
                                        <form method="POST" action="{{ route('logout') }}">
                                            @csrf
                                            <button type="submit" class="dropdown-item">
                                                <i class="bi bi-box-arrow-right me-2"></i>Logout
                                            </button>
                                        </form>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </nav>

                <!-- Page Content -->
                <main class="flex-grow-1 bg-light">
                    <div class="container-fluid py-4">
                        {{ $slot }}
                    </div>
                </main>
            </div>
        </div>

        <script>
            function toggleSidebar() {
                const sidebar = document.getElementById('sidebar');
                const overlay = document.getElementById('sidebarOverlay');
                sidebar.classList.toggle('show');
                overlay.classList.toggle('show');
            }
        </script>
    </body>
</html>
