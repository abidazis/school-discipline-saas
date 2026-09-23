<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'School Discipline') }} - {{ $title ?? 'Dashboard' }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700&display=swap" rel="stylesheet" />

    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body x-data="{ sidebarOpen: false }">
    <div class="app-wrapper">
        <!-- Sidebar -->
        <aside class="app-sidebar" :class="{ 'show': sidebarOpen }" id="sidebar">
            <!-- Brand -->
            <div class="sidebar-brand">
                <div class="sidebar-brand-icon">
                    <i class="bi bi-shield-check"></i>
                </div>
                <span class="sidebar-brand-text">School Discipline</span>
            </div>

            <!-- Navigation -->
            <nav class="sidebar-nav">
                <!-- Dashboard -->
                <div class="sidebar-section">
                    <a href="{{ route('dashboard') }}" class="sidebar-link single-link @if(request()->routeIs('dashboard')) active @endif">
                        <i class="bi bi-grid sidebar-link-icon"></i>
                        <span>Dashboard</span>
                    </a>
                </div>

                <!-- Super Admin Section -->
                @if(auth()->user()->isSuperAdmin())
                <div class="sidebar-section">
                    <button class="sidebar-section-header" @click="open = !open" :class="{ 'open': open }">
                        <span>
                            <i class="bi bi-shield-lock"></i>
                            Super Admin
                        </span>
                        <i class="bi bi-chevron-down sidebar-chevron"></i>
                    </button>
                    <ul class="sidebar-submenu" x-show="open" x-collapse>
                        <li>
                            <a href="{{ route('schools.index') }}" class="sidebar-submenu-link @if(request()->routeIs('schools.*')) active @endif">
                                <i class="bi bi-building"></i>
                                <span>Sekolah</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('users.index') }}" class="sidebar-submenu-link @if(request()->routeIs('users.*')) active @endif">
                                <i class="bi bi-people"></i>
                                <span>Pengguna</span>
                            </a>
                        </li>
                    </ul>
                </div>
                @endif

                <!-- Akademik Section -->
                <div class="sidebar-section">
                    <button class="sidebar-section-header" @click="akademik = !akademik" :class="{ 'open': akademik }">
                        <span>
                            <i class="bi bi-book"></i>
                            Akademik
                        </span>
                        <i class="bi bi-chevron-down sidebar-chevron"></i>
                    </button>
                    <ul class="sidebar-submenu" x-show="akademik" x-collapse>
                        <li>
                            <a href="{{ route('academic-years.index') }}" class="sidebar-submenu-link @if(request()->routeIs('academic-years.*')) active @endif">
                                <i class="bi bi-calendar"></i>
                                <span>Tahun Ajaran</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('departments.index') }}" class="sidebar-submenu-link @if(request()->routeIs('departments.*')) active @endif">
                                <i class="bi bi-diagram-3"></i>
                                <span>Program Keahlian</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('classes.index') }}" class="sidebar-submenu-link @if(request()->routeIs('classes.*')) active @endif">
                                <i class="bi bi-mortarboard"></i>
                                <span>Kelas</span>
                            </a>
                        </li>
                    </ul>
                </div>

                <!-- Siswa Section -->
                <div class="sidebar-section">
                    <a href="{{ route('students.index') }}" class="sidebar-link single-link @if(request()->routeIs('students.*')) active @endif">
                        <i class="bi bi-person-badge sidebar-link-icon"></i>
                        <span>Daftar Siswa</span>
                    </a>
                </div>

                <!-- Kedisiplinan Section -->
                <div class="sidebar-section">
                    <button class="sidebar-section-header" @click="kedisiplinan = !kedisiplinan" :class="{ 'open': kedisiplinan }">
                        <span>
                            <i class="bi bi-shield-exclamation"></i>
                            Kedisiplinan
                        </span>
                        <i class="bi bi-chevron-down sidebar-chevron"></i>
                    </button>
                    <ul class="sidebar-submenu" x-show="kedisiplinan" x-collapse>
                        <li>
                            <a href="{{ route('violation-types.index') }}" class="sidebar-submenu-link @if(request()->routeIs('violation-types.*')) active @endif">
                                <i class="bi bi-list-check"></i>
                                <span>Jenis Pelanggaran</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('violations.index') }}" class="sidebar-submenu-link @if(request()->routeIs('violations.*')) active @endif">
                                <i class="bi bi-exclamation-triangle"></i>
                                <span>Pelanggaran</span>
                            </a>
                        </li>
                    </ul>
                </div>

                <!-- PKS Section -->
                <div class="sidebar-section">
                    <button class="sidebar-section-header" @click="pks = !pks" :class="{ 'open': pks }">
                        <span>
                            <i class="bi bi-shield-check"></i>
                            PKS
                        </span>
                        <i class="bi bi-chevron-down sidebar-chevron"></i>
                    </button>
                    <ul class="sidebar-submenu" x-show="pks" x-collapse>
                        <li>
                            <a href="{{ route('pks-members.index') }}" class="sidebar-submenu-link @if(request()->routeIs('pks-members.*')) active @endif">
                                <i class="bi bi-people"></i>
                                <span>Anggota PKS</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('pks-shifts.index') }}" class="sidebar-submenu-link @if(request()->routeIs('pks-shifts.*')) active @endif">
                                <i class="bi bi-clock"></i>
                                <span>Shift Piket</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('pks-duty-locations.index') }}" class="sidebar-submenu-link @if(request()->routeIs('pks-duty-locations.*')) active @endif">
                                <i class="bi bi-geo-alt"></i>
                                <span>Lokasi Piket</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('pks-duty-schedules.index') }}" class="sidebar-submenu-link @if(request()->routeIs('pks-duty-schedules.*')) active @endif">
                                <i class="bi bi-calendar-week"></i>
                                <span>Jadwal Piket</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('pks-duty-assignments.index') }}" class="sidebar-submenu-link @if(request()->routeIs('pks-duty-assignments.*')) active @endif">
                                <i class="bi bi-clipboard-check"></i>
                                <span>Penugasan</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('pks-duty-attendances.index') }}" class="sidebar-submenu-link @if(request()->routeIs('pks-duty-attendances.*')) active @endif">
                                <i class="bi bi-person-check"></i>
                                <span>Kehadiran</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('pks-field-activities.index') }}" class="sidebar-submenu-link @if(request()->routeIs('pks-field-activities.*')) active @endif">
                                <i class="bi bi-map"></i>
                                <span>Aktivitas Lapangan</span>
                            </a>
                        </li>
                    </ul>
                </div>

                <!-- Laporan Section -->
                <div class="sidebar-section">
                    <button class="sidebar-section-header" @click="laporan = !laporan" :class="{ 'open': laporan }">
                        <span>
                            <i class="bi bi-file-earmark-bar-graph"></i>
                            Laporan
                        </span>
                        <i class="bi bi-chevron-down sidebar-chevron"></i>
                    </button>
                    <ul class="sidebar-submenu" x-show="laporan" x-collapse>
                        <li>
                            <a href="{{ route('reports.daily') }}" class="sidebar-submenu-link @if(request()->routeIs('reports.daily*')) active @endif">
                                <i class="bi bi-calendar-day"></i>
                                <span>Laporan Harian</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('reports.monthly') }}" class="sidebar-submenu-link @if(request()->routeIs('reports.monthly*')) active @endif">
                                <i class="bi bi-calendar-event"></i>
                                <span>Laporan Bulanan</span>
                            </a>
                        </li>
                    </ul>
                </div>

                <!-- Settings Section -->
                <div class="sidebar-section">
                    <a href="{{ route('profile.edit') }}" class="sidebar-link single-link @if(request()->routeIs('profile.edit')) active @endif">
                        <i class="bi bi-sliders sidebar-link-icon"></i>
                        <span>Pengaturan</span>
                    </a>
                </div>
            </nav>

            <!-- School Info Footer -->
            @if(auth()->user()->school)
            <div class="sidebar-footer">
                <div class="sidebar-school-box">
                    <div class="sidebar-school-icon">
                        <i class="bi bi-building"></i>
                    </div>
                    <div class="sidebar-school-info">
                        <div class="sidebar-school-name">{{ auth()->user()->school->name }}</div>
                        <div class="sidebar-school-role">{{ ucwords(str_replace('_', ' ', auth()->user()->role)) }}</div>
                    </div>
                </div>
            </div>
            @endif
        </aside>

        <!-- Sidebar Overlay -->
        <div class="sidebar-overlay" :class="{ 'show': sidebarOpen }" @click="sidebarOpen = false"></div>

        <!-- Main Content -->
        <div class="app-content">
            <!-- Header -->
            <header class="app-header">
                <div class="app-header-left">
                    <button class="btn btn-outline-secondary btn-sm d-lg-none" @click="sidebarOpen = !sidebarOpen" aria-label="Toggle menu">
                        <i class="bi bi-list"></i>
                    </button>
                    <h1 class="app-header-title">{{ $title ?? 'Dashboard' }}</h1>
                </div>

                <div class="app-header-right">
                    <!-- Notifications -->
                    <button class="btn btn-outline-secondary btn-sm position-relative" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="bi bi-bell"></i>
                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size: 0.6rem;">
                            0
                        </span>
                    </button>

                    <!-- User Dropdown -->
                    <div class="dropdown">
                        <button class="btn btn-outline-secondary btn-sm d-flex align-items-center gap-2" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <div class="avatar avatar-sm">{{ substr(auth()->user()->name, 0, 1) }}</div>
                            <span class="d-none d-md-inline">{{ auth()->user()->name }}</span>
                            <i class="bi bi-chevron-down" style="font-size: 0.75rem;"></i>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end shadow-sm">
                            <li>
                                <div class="dropdown-item-text">
                                    <div class="fw-semibold">{{ auth()->user()->name }}</div>
                                    <div class="text-muted small">{{ auth()->user()->email }}</div>
                                </div>
                            </li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <a class="dropdown-item" href="{{ route('profile.edit') }}">
                                    <i class="bi bi-person me-2"></i>Profil Saya
                                </a>
                            </li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="dropdown-item text-danger">
                                        <i class="bi bi-box-arrow-right me-2"></i>Keluar
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </div>
                </div>
            </header>

            <!-- Main -->
            <main class="app-main">
                {{ $slot }}
            </main>
        </div>
    </div>
</body>
</html>
