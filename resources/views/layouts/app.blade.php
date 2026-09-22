<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'School Discipline') }} - @yield('title', 'Dashboard')</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700&display=swap" rel="stylesheet" />

        <!-- Bootstrap Icons -->
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body>
        <div class="app-wrapper">
            <!-- Sidebar -->
            <aside class="sidebar" id="sidebar">
                <!-- Brand -->
                <div class="sidebar-brand">
                    <div class="brand-icon">
                        <i class="bi bi-shield-check"></i>
                    </div>
                    <span>School Discipline</span>
                </div>

                <!-- Navigation -->
                <nav class="sidebar-nav">
                    <!-- Main Menu -->
                    <div class="sidebar-section">
                        <div class="sidebar-section-title">Menu Utama</div>
                        <ul class="sidebar-menu">
                            <li class="sidebar-menu-item">
                                <a href="{{ route('dashboard') }}" class="sidebar-menu-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                                    <i class="bi bi-grid-1x2 sidebar-menu-icon"></i>
                                    <span>Dashboard</span>
                                </a>
                            </li>
                        </ul>
                    </div>

                    <!-- Super Admin Section -->
                    @if(auth()->user()->isSuperAdmin())
                        <div class="sidebar-section">
                            <div class="sidebar-section-title">Super Admin</div>
                            <ul class="sidebar-menu">
                                <li class="sidebar-menu-item">
                                    <a href="{{ route('schools.index') }}" class="sidebar-menu-link {{ request()->routeIs('schools.*') ? 'active' : '' }}">
                                        <i class="bi bi-building sidebar-menu-icon"></i>
                                        <span>Sekolah</span>
                                    </a>
                                </li>
                                <li class="sidebar-menu-item">
                                    <a href="{{ route('users.index') }}" class="sidebar-menu-link {{ request()->routeIs('users.*') ? 'active' : '' }}">
                                        <i class="bi bi-person-gear sidebar-menu-icon"></i>
                                        <span>Pengguna</span>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    @endif

                    <!-- Akademik Section -->
                    <div class="sidebar-section">
                        <div class="sidebar-section-title">Akademik</div>
                        <ul class="sidebar-menu">
                            <li class="sidebar-menu-item">
                                <a href="{{ route('academic-years.index') }}" class="sidebar-menu-link {{ request()->routeIs('academic-years.*') ? 'active' : '' }}">
                                    <i class="bi bi-calendar-range sidebar-menu-icon"></i>
                                    <span>Tahun Ajaran</span>
                                </a>
                            </li>
                            <li class="sidebar-menu-item">
                                <a href="{{ route('departments.index') }}" class="sidebar-menu-link {{ request()->routeIs('departments.*') ? 'active' : '' }}">
                                    <i class="bi bi-diagram-3 sidebar-menu-icon"></i>
                                    <span>Program Keahlian</span>
                                </a>
                            </li>
                            <li class="sidebar-menu-item">
                                <a href="{{ route('classes.index') }}" class="sidebar-menu-link {{ request()->routeIs('classes.*') ? 'active' : '' }}">
                                    <i class="bi bi-chalkboard sidebar-menu-icon"></i>
                                    <span>Kelas</span>
                                </a>
                            </li>
                        </ul>
                    </div>

                    <!-- Siswa Section -->
                    <div class="sidebar-section">
                        <div class="sidebar-section-title">Siswa</div>
                        <ul class="sidebar-menu">
                            <li class="sidebar-menu-item">
                                <a href="{{ route('students.index') }}" class="sidebar-menu-link {{ request()->routeIs('students.*') ? 'active' : '' }}">
                                    <i class="bi bi-person-badge sidebar-menu-icon"></i>
                                    <span>Daftar Siswa</span>
                                </a>
                            </li>
                        </ul>
                    </div>

                    <!-- Kedisiplinan Section -->
                    <div class="sidebar-section">
                        <div class="sidebar-section-title">Kedisiplinan</div>
                        <ul class="sidebar-menu">
                            <li class="sidebar-menu-item">
                                <a href="{{ route('violation-types.index') }}" class="sidebar-menu-link {{ request()->routeIs('violation-types.*') ? 'active' : '' }}">
                                    <i class="bi bi-clipboard-check sidebar-menu-icon"></i>
                                    <span>Jenis Pelanggaran</span>
                                </a>
                            </li>
                            <li class="sidebar-menu-item">
                                <a href="{{ route('violations.index') }}" class="sidebar-menu-link {{ request()->routeIs('violations.*') ? 'active' : '' }}">
                                    <i class="bi bi-exclamation-triangle sidebar-menu-icon"></i>
                                    <span>Pelanggaran</span>
                                </a>
                            </li>
                        </ul>
                    </div>

                    <!-- PKS Section -->
                    <div class="sidebar-section">
                        <div class="sidebar-section-title">PKS</div>
                        <ul class="sidebar-menu">
                            <li class="sidebar-menu-item">
                                <a href="{{ route('pks-members.index') }}" class="sidebar-menu-link {{ request()->routeIs('pks-members.*') ? 'active' : '' }}">
                                    <i class="bi bi-shield-check sidebar-menu-icon"></i>
                                    <span>Anggota PKS</span>
                                </a>
                            </li>
                            <li class="sidebar-menu-item">
                                <a href="{{ route('pks-shifts.index') }}" class="sidebar-menu-link {{ request()->routeIs('pks-shifts.*') ? 'active' : '' }}">
                                    <i class="bi bi-clock sidebar-menu-icon"></i>
                                    <span>Shift Piket</span>
                                </a>
                            </li>
                            <li class="sidebar-menu-item">
                                <a href="{{ route('pks-duty-locations.index') }}" class="sidebar-menu-link {{ request()->routeIs('pks-duty-locations.*') ? 'active' : '' }}">
                                    <i class="bi bi-geo-alt sidebar-menu-icon"></i>
                                    <span>Lokasi Piket</span>
                                </a>
                            </li>
                            <li class="sidebar-menu-item">
                                <a href="{{ route('pks-duty-schedules.index') }}" class="sidebar-menu-link {{ request()->routeIs('pks-duty-schedules.*') ? 'active' : '' }}">
                                    <i class="bi bi-calendar-week sidebar-menu-icon"></i>
                                    <span>Jadwal Piket</span>
                                </a>
                            </li>
                            <li class="sidebar-menu-item">
                                <a href="{{ route('pks-duty-assignments.index') }}" class="sidebar-menu-link {{ request()->routeIs('pks-duty-assignments.*') ? 'active' : '' }}">
                                    <i class="bi bi-calendar-check sidebar-menu-icon"></i>
                                    <span>Penugasan</span>
                                </a>
                            </li>
                            <li class="sidebar-menu-item">
                                <a href="{{ route('pks-duty-attendances.index') }}" class="sidebar-menu-link {{ request()->routeIs('pks-duty-attendances.*') ? 'active' : '' }}">
                                    <i class="bi bi-person-check sidebar-menu-icon"></i>
                                    <span>Kehadiran</span>
                                </a>
                            </li>
                            <li class="sidebar-menu-item">
                                <a href="{{ route('pks-field-activities.index') }}" class="sidebar-menu-link {{ request()->routeIs('pks-field-activities.*') ? 'active' : '' }}">
                                    <i class="bi bi-map sidebar-menu-icon"></i>
                                    <span>Aktivitas Lapangan</span>
                                </a>
                            </li>
                        </ul>
                    </div>

                    <!-- Laporan Section -->
                    <div class="sidebar-section">
                        <div class="sidebar-section-title">Laporan</div>
                        <ul class="sidebar-menu">
                            <li class="sidebar-menu-item">
                                <a href="{{ route('reports.daily') }}" class="sidebar-menu-link {{ request()->routeIs('reports.daily*') ? 'active' : '' }}">
                                    <i class="bi bi-calendar-day sidebar-menu-icon"></i>
                                    <span>Laporan Harian</span>
                                </a>
                            </li>
                            <li class="sidebar-menu-item">
                                <a href="{{ route('reports.monthly') }}" class="sidebar-menu-link {{ request()->routeIs('reports.monthly*') ? 'active' : '' }}">
                                    <i class="bi bi-calendar-event sidebar-menu-icon"></i>
                                    <span>Laporan Bulanan</span>
                                </a>
                            </li>
                        </ul>
                    </div>

                    <!-- Settings Section -->
                    <div class="sidebar-section">
                        <div class="sidebar-section-title">Pengaturan</div>
                        <ul class="sidebar-menu">
                            <li class="sidebar-menu-item">
                                <a href="{{ route('profile.edit') }}" class="sidebar-menu-link {{ request()->routeIs('profile.edit') ? 'active' : '' }}">
                                    <i class="bi bi-sliders sidebar-menu-icon"></i>
                                    <span>Profil</span>
                                </a>
                            </li>
                        </ul>
                    </div>
                </nav>

                <!-- School Info Footer -->
                @if(auth()->user()->school)
                    <div class="sidebar-footer">
                        <div class="sidebar-school-info">
                            <div class="sidebar-school-name">{{ auth()->user()->school->name }}</div>
                            <div class="sidebar-school-email">{{ auth()->user()->school->email ?? '-' }}</div>
                        </div>
                    </div>
                @endif
            </aside>

            <!-- Sidebar Overlay for Mobile -->
            <div class="sidebar-overlay" id="sidebarOverlay" onclick="toggleSidebar()"></div>

            <!-- Main Content -->
            <div class="app-content">
                <!-- Header -->
                <header class="app-header">
                    <div class="header-left">
                        <button class="btn btn-icon btn-outline-secondary navbar-toggle" onclick="toggleSidebar()" aria-label="Toggle menu">
                            <i class="bi bi-list"></i>
                        </button>
                        <h1 class="header-title">@yield('title', 'Dashboard')</h1>
                    </div>

                    <div class="header-right">
                        <div class="header-user">
                            <div class="header-user-info d-none d-md-block">
                                <div class="header-user-name">{{ auth()->user()->name }}</div>
                                <div class="header-user-role">
                                    <span class="badge bg-{{ auth()->user()->isSuperAdmin() ? 'danger' : 'primary' }}">
                                        {{ ucwords(str_replace('_', ' ', auth()->user()->role)) }}
                                    </span>
                                </div>
                            </div>

                            <div class="dropdown">
                                <button class="btn btn-icon btn-outline-secondary" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    <div class="header-avatar">{{ substr(auth()->user()->name, 0, 1) }}</div>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end">
                                    <li>
                                        <a class="dropdown-item" href="{{ route('profile.edit') }}">
                                            <i class="bi bi-person me-2"></i>Profil
                                        </a>
                                    </li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li>
                                        <form method="POST" action="{{ route('logout') }}">
                                            @csrf
                                            <button type="submit" class="dropdown-item">
                                                <i class="bi bi-box-arrow-right me-2"></i>Keluar
                                            </button>
                                        </form>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </header>

                <!-- Main -->
                <main class="app-main">
                    {{ $slot }}
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
