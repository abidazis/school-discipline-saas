<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'School Discipline') }} - {{ $title ?? 'Dashboard' }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        /* ============================================
           BASE STYLES
           ============================================ */
        * {
            box-sizing: border-box;
        }

        html, body {
            margin: 0;
            padding: 0;
            min-height: 100vh;
            font-family: 'Figtree', ui-sans-serif, system-ui, -apple-system, sans-serif;
            font-size: 14px;
            line-height: 1.5;
            color: #334155;
            background-color: #f1f5f9;
            -webkit-font-smoothing: antialiased;
        }

        a {
            text-decoration: none;
        }

        /* ============================================
           LAYOUT
           ============================================ */
        .layout-wrapper {
            display: flex;
            min-height: 100vh;
        }

        /* ============================================
           SIDEBAR
           ============================================ */
        .sidebar {
            width: 256px;
            min-width: 256px;
            min-height: 100vh;
            background: #ffffff;
            border-right: 1px solid #e2e8f0;
            display: flex;
            flex-direction: column;
            transition: all 0.3s ease;
            position: fixed;
            left: 0;
            top: 0;
            bottom: 0;
            z-index: 100;
        }

        .sidebar-brand {
            height: 64px;
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 0 16px;
            border-bottom: 1px solid #e2e8f0;
        }

        .sidebar-brand-icon {
            width: 36px;
            height: 36px;
            background: #4f46e5;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-size: 18px;
        }

        .sidebar-brand-text {
            font-weight: 600;
            font-size: 14px;
            color: #1e293b;
        }

        .sidebar-brand-sub {
            font-size: 11px;
            color: #94a3b8;
        }

        .sidebar-nav {
            flex: 1;
            padding: 12px;
            overflow-y: auto;
        }

        .sidebar-section {
            margin-bottom: 4px;
        }

        .sidebar-section + .sidebar-section {
            margin-top: 12px;
            padding-top: 12px;
            border-top: 1px solid #e2e8f0;
        }

        /* Menu Links */
        .menu-link {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 10px 12px;
            font-size: 14px;
            font-weight: 500;
            color: #475569;
            border-radius: 8px;
            transition: all 0.2s ease;
            cursor: pointer;
        }

        .menu-link:hover {
            background: #f1f5f9;
            color: #4f46e5;
        }

        .menu-link.active {
            background: #eef2ff;
            color: #4f46e5;
            font-weight: 600;
        }

        .menu-link i {
            font-size: 18px;
            width: 20px;
            text-align: center;
        }

        /* Section Header (Accordion) */
        .section-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            width: 100%;
            padding: 8px 12px;
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: #94a3b8;
            background: transparent;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .section-header:hover {
            color: #475569;
            background: #f8fafc;
        }

        .section-header.open {
            color: #475569;
        }

        .section-header-left {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .section-chevron {
            width: 16px;
            height: 16px;
            transition: transform 0.2s ease;
        }

        .section-header.open .section-chevron {
            transform: rotate(90deg);
        }

        /* Submenu */
        .submenu {
            padding-left: 20px;
            margin-top: 4px;
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .submenu-item {
            margin-bottom: 2px;
        }

        .submenu-link {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 8px 12px;
            font-size: 13px;
            font-weight: 500;
            color: #64748b;
            border-radius: 8px;
            transition: all 0.2s ease;
        }

        .submenu-link:hover {
            background: #f1f5f9;
            color: #4f46e5;
        }

        .submenu-link.active {
            background: #eef2ff;
            color: #4f46e5;
            font-weight: 600;
        }

        .submenu-link i {
            font-size: 16px;
            width: 18px;
            text-align: center;
        }

        /* Hide submenu with x-show */
        [x-show] {
            display: none;
        }

        [x-show="true"] {
            display: block;
        }

        /* ============================================
           SIDEBAR FOOTER
           ============================================ */
        .sidebar-footer {
            padding: 12px;
            border-top: 1px solid #e2e8f0;
            background: #f8fafc;
        }

        .school-box {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 10px;
            background: #fff;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            margin-bottom: 8px;
        }

        .school-icon {
            width: 32px;
            height: 32px;
            background: #eef2ff;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #4f46e5;
            font-size: 14px;
        }

        .school-name {
            font-size: 12px;
            font-weight: 600;
            color: #1e293b;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .school-role {
            font-size: 11px;
            color: #94a3b8;
            text-transform: capitalize;
        }

        .user-box {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 8px;
        }

        .user-avatar {
            width: 32px;
            height: 32px;
            background: #4f46e5;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-size: 13px;
            font-weight: 600;
        }

        .user-name {
            flex: 1;
            font-size: 13px;
            font-weight: 500;
            color: #1e293b;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .logout-btn {
            padding: 6px;
            color: #94a3b8;
            border-radius: 6px;
            transition: all 0.2s ease;
            background: transparent;
            border: none;
            cursor: pointer;
        }

        .logout-btn:hover {
            color: #ef4444;
            background: #fef2f2;
        }

        /* ============================================
           MAIN CONTENT
           ============================================ */
        .main-content {
            flex: 1;
            margin-left: 256px;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        /* Header */
        .main-header {
            height: 64px;
            background: #ffffff;
            border-bottom: 1px solid #e2e8f0;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 24px;
            position: sticky;
            top: 0;
            z-index: 50;
        }

        .header-left {
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .header-title {
            font-size: 18px;
            font-weight: 600;
            color: #1e293b;
        }

        .header-right {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .header-btn {
            padding: 8px;
            color: #64748b;
            border-radius: 8px;
            transition: all 0.2s ease;
            background: transparent;
            border: none;
            cursor: pointer;
            position: relative;
        }

        .header-btn:hover {
            background: #f1f5f9;
            color: #475569;
        }

        .header-btn i {
            font-size: 20px;
        }

        .notif-dot {
            position: absolute;
            top: 6px;
            right: 6px;
            width: 8px;
            height: 8px;
            background: #ef4444;
            border-radius: 50%;
        }

        .user-dropdown {
            position: relative;
        }

        .user-dropdown-btn {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 6px 12px;
            color: #475569;
            border-radius: 8px;
            transition: all 0.2s ease;
            background: transparent;
            border: none;
            cursor: pointer;
        }

        .user-dropdown-btn:hover {
            background: #f1f5f9;
        }

        .user-dropdown-menu {
            position: absolute;
            right: 0;
            top: 100%;
            margin-top: 8px;
            width: 224px;
            background: #fff;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            box-shadow: 0 10px 15px -3px rgba(0,0,0,0.1), 0 4px 6px -2px rgba(0,0,0,0.05);
            z-index: 100;
            display: none;
        }

        .user-dropdown-menu.show {
            display: block;
        }

        .dropdown-header {
            padding: 12px 16px;
            border-bottom: 1px solid #f1f5f9;
        }

        .dropdown-header-name {
            font-size: 14px;
            font-weight: 600;
            color: #1e293b;
        }

        .dropdown-header-email {
            font-size: 12px;
            color: #94a3b8;
        }

        .dropdown-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 10px 16px;
            font-size: 14px;
            color: #475569;
            transition: all 0.15s ease;
            border: none;
            background: transparent;
            width: 100%;
            text-align: left;
            cursor: pointer;
        }

        .dropdown-item:hover {
            background: #f8fafc;
        }

        .dropdown-item.danger {
            color: #ef4444;
        }

        .dropdown-item.danger:hover {
            background: #fef2f2;
        }

        .dropdown-divider {
            border-top: 1px solid #f1f5f9;
            margin: 4px 0;
        }

        /* Main Area */
        .main-area {
            flex: 1;
            padding: 24px;
        }

        /* ============================================
           RESPONSIVE
           ============================================ */
        @media (max-width: 1024px) {
            .sidebar {
                transform: translateX(-100%);
            }

            .sidebar.show {
                transform: translateX(0);
            }

            .main-content {
                margin-left: 0;
            }
        }
    </style>
</head>
<body>
    <div class="layout-wrapper">
        <!-- ============================================
             SIDEBAR
             ============================================ -->
        <aside class="sidebar" id="sidebar">
            <!-- Brand -->
            <div class="sidebar-brand">
                <div class="sidebar-brand-icon">
                    <i class="bi bi-shield-check"></i>
                </div>
                <div>
                    <div class="sidebar-brand-text">School Discipline</div>
                    <div class="sidebar-brand-sub">Management System</div>
                </div>
            </div>

            <!-- Navigation -->
            <nav class="sidebar-nav">
                <!-- Dashboard -->
                <div class="sidebar-section">
                    <a href="{{ route('dashboard') }}"
                       class="menu-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                        <i class="bi bi-grid"></i>
                        <span>Dashboard</span>
                    </a>
                </div>

                <!-- Super Admin Section -->
                @if(auth()->user()->isSuperAdmin())
                <div class="sidebar-section">
                    <div x-data="{ open: false }">
                        <button @click="open = !open" class="section-header" :class="{ 'open': open }">
                            <span class="section-header-left">
                                <i class="bi bi-shield-lock"></i>
                                <span>Super Admin</span>
                            </span>
                            <svg class="section-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                            </svg>
                        </button>
                        <ul class="submenu" x-show="open">
                            <li class="submenu-item">
                                <a href="{{ route('schools.index') }}"
                                   class="submenu-link {{ request()->routeIs('schools.*') ? 'active' : '' }}">
                                    <i class="bi bi-building"></i>
                                    <span>Sekolah</span>
                                </a>
                            </li>
                            <li class="submenu-item">
                                <a href="{{ route('users.index') }}"
                                   class="submenu-link {{ request()->routeIs('users.*') ? 'active' : '' }}">
                                    <i class="bi bi-people"></i>
                                    <span>Pengguna</span>
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
                @endif

                <!-- Akademik Section -->
                <div class="sidebar-section">
                    <div x-data="{ open: true }">
                        <button @click="open = !open" class="section-header" :class="{ 'open': open }">
                            <span class="section-header-left">
                                <i class="bi bi-book"></i>
                                <span>Akademik</span>
                            </span>
                            <svg class="section-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                            </svg>
                        </button>
                        <ul class="submenu" x-show="open">
                            <li class="submenu-item">
                                <a href="{{ route('academic-years.index') }}"
                                   class="submenu-link {{ request()->routeIs('academic-years.*') ? 'active' : '' }}">
                                    <i class="bi bi-calendar-range"></i>
                                    <span>Tahun Ajaran</span>
                                </a>
                            </li>
                            <li class="submenu-item">
                                <a href="{{ route('departments.index') }}"
                                   class="submenu-link {{ request()->routeIs('departments.*') ? 'active' : '' }}">
                                    <i class="bi bi-diagram-3"></i>
                                    <span>Program Keahlian</span>
                                </a>
                            </li>
                            <li class="submenu-item">
                                <a href="{{ route('classes.index') }}"
                                   class="submenu-link {{ request()->routeIs('classes.*') ? 'active' : '' }}">
                                    <i class="bi bi-mortarboard"></i>
                                    <span>Kelas</span>
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>

                <!-- Siswa Section -->
                <div class="sidebar-section">
                    <a href="{{ route('students.index') }}"
                       class="menu-link {{ request()->routeIs('students.*') ? 'active' : '' }}">
                        <i class="bi bi-person-badge"></i>
                        <span>Daftar Siswa</span>
                    </a>
                </div>

                <!-- Kedisiplinan Section -->
                <div class="sidebar-section">
                    <div x-data="{ open: true }">
                        <button @click="open = !open" class="section-header" :class="{ 'open': open }">
                            <span class="section-header-left">
                                <i class="bi bi-shield-exclamation"></i>
                                <span>Kedisiplinan</span>
                            </span>
                            <svg class="section-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                            </svg>
                        </button>
                        <ul class="submenu" x-show="open">
                            <li class="submenu-item">
                                <a href="{{ route('violation-types.index') }}"
                                   class="submenu-link {{ request()->routeIs('violation-types.*') ? 'active' : '' }}">
                                    <i class="bi bi-list-check"></i>
                                    <span>Jenis Pelanggaran</span>
                                </a>
                            </li>
                            <li class="submenu-item">
                                <a href="{{ route('violations.index') }}"
                                   class="submenu-link {{ request()->routeIs('violations.*') ? 'active' : '' }}">
                                    <i class="bi bi-exclamation-triangle"></i>
                                    <span>Pelanggaran</span>
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>

                <!-- PKS Section -->
                <div class="sidebar-section">
                    <div x-data="{ open: false }">
                        <button @click="open = !open" class="section-header" :class="{ 'open': open }">
                            <span class="section-header-left">
                                <i class="bi bi-shield-check"></i>
                                <span>PKS</span>
                            </span>
                            <svg class="section-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                            </svg>
                        </button>
                        <ul class="submenu" x-show="open">
                            <li class="submenu-item">
                                <a href="{{ route('pks-members.index') }}"
                                   class="submenu-link {{ request()->routeIs('pks-members.*') ? 'active' : '' }}">
                                    <i class="bi bi-people"></i>
                                    <span>Anggota PKS</span>
                                </a>
                            </li>
                            <li class="submenu-item">
                                <a href="{{ route('pks-shifts.index') }}"
                                   class="submenu-link {{ request()->routeIs('pks-shifts.*') ? 'active' : '' }}">
                                    <i class="bi bi-clock"></i>
                                    <span>Shift Piket</span>
                                </a>
                            </li>
                            <li class="submenu-item">
                                <a href="{{ route('pks-duty-locations.index') }}"
                                   class="submenu-link {{ request()->routeIs('pks-duty-locations.*') ? 'active' : '' }}">
                                    <i class="bi bi-geo-alt"></i>
                                    <span>Lokasi Piket</span>
                                </a>
                            </li>
                            <li class="submenu-item">
                                <a href="{{ route('pks-duty-schedules.index') }}"
                                   class="submenu-link {{ request()->routeIs('pks-duty-schedules.*') ? 'active' : '' }}">
                                    <i class="bi bi-calendar-week"></i>
                                    <span>Jadwal Piket</span>
                                </a>
                            </li>
                            <li class="submenu-item">
                                <a href="{{ route('pks-duty-assignments.index') }}"
                                   class="submenu-link {{ request()->routeIs('pks-duty-assignments.*') ? 'active' : '' }}">
                                    <i class="bi bi-clipboard-check"></i>
                                    <span>Penugasan</span>
                                </a>
                            </li>
                            <li class="submenu-item">
                                <a href="{{ route('pks-duty-attendances.index') }}"
                                   class="submenu-link {{ request()->routeIs('pks-duty-attendances.*') ? 'active' : '' }}">
                                    <i class="bi bi-person-check"></i>
                                    <span>Kehadiran</span>
                                </a>
                            </li>
                            <li class="submenu-item">
                                <a href="{{ route('pks-field-activities.index') }}"
                                   class="submenu-link {{ request()->routeIs('pks-field-activities.*') ? 'active' : '' }}">
                                    <i class="bi bi-map"></i>
                                    <span>Aktivitas Lapangan</span>
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>

                <!-- Laporan Section -->
                <div class="sidebar-section">
                    <div x-data="{ open: false }">
                        <button @click="open = !open" class="section-header" :class="{ 'open': open }">
                            <span class="section-header-left">
                                <i class="bi bi-file-earmark-bar-graph"></i>
                                <span>Laporan</span>
                            </span>
                            <svg class="section-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                            </svg>
                        </button>
                        <ul class="submenu" x-show="open">
                            <li class="submenu-item">
                                <a href="{{ route('reports.daily') }}"
                                   class="submenu-link {{ request()->routeIs('reports.daily*') ? 'active' : '' }}">
                                    <i class="bi bi-calendar-day"></i>
                                    <span>Laporan Harian</span>
                                </a>
                            </li>
                            <li class="submenu-item">
                                <a href="{{ route('reports.monthly') }}"
                                   class="submenu-link {{ request()->routeIs('reports.monthly*') ? 'active' : '' }}">
                                    <i class="bi bi-calendar-event"></i>
                                    <span>Laporan Bulanan</span>
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>

                <!-- Pengaturan Section -->
                <div class="sidebar-section">
                    <a href="{{ route('profile.edit') }}"
                       class="menu-link {{ request()->routeIs('profile.edit') ? 'active' : '' }}">
                        <i class="bi bi-sliders"></i>
                        <span>Pengaturan</span>
                    </a>
                </div>
            </nav>

            <!-- Footer -->
            <div class="sidebar-footer">
                @if(auth()->user()->school)
                <div class="school-box">
                    <div class="school-icon">
                        <i class="bi bi-building"></i>
                    </div>
                    <div>
                        <div class="school-name">{{ auth()->user()->school->name }}</div>
                        <div class="school-role">{{ str_replace('_', ' ', auth()->user()->role) }}</div>
                    </div>
                </div>
                @endif

                <div class="user-box">
                    <div class="user-avatar">{{ substr(auth()->user()->name, 0, 1) }}</div>
                    <div class="user-name">{{ auth()->user()->name }}</div>
                    <form method="POST" action="{{ route('logout') }}" class="m-0">
                        @csrf
                        <button type="submit" class="logout-btn" title="Keluar">
                            <i class="bi bi-box-arrow-right"></i>
                        </button>
                    </form>
                </div>
            </div>
        </aside>

        <!-- ============================================
             MAIN CONTENT
             ============================================ -->
        <div class="main-content">
            <!-- Header -->
            <header class="main-header">
                <div class="header-left">
                    <button class="header-btn d-lg-none" onclick="toggleSidebar()" aria-label="Toggle menu">
                        <i class="bi bi-list"></i>
                    </button>
                    <h1 class="header-title">{{ $title ?? 'Dashboard' }}</h1>
                </div>

                <div class="header-right">
                    <button class="header-btn">
                        <i class="bi bi-bell"></i>
                        <span class="notif-dot"></span>
                    </button>

                    <div class="user-dropdown" x-data="{ open: false }">
                        <button @click="open = !open" class="user-dropdown-btn">
                            <div class="user-avatar" style="width: 32px; height: 32px; font-size: 13px;">
                                {{ substr(auth()->user()->name, 0, 1) }}
                            </div>
                            <span class="d-none d-md-inline">{{ auth()->user()->name }}</span>
                            <i class="bi bi-chevron-down" style="font-size: 12px;"></i>
                        </button>

                        <div class="user-dropdown-menu" :class="{ 'show': open }" @click.away="open = false">
                            <div class="dropdown-header">
                                <div class="dropdown-header-name">{{ auth()->user()->name }}</div>
                                <div class="dropdown-header-email">{{ auth()->user()->email }}</div>
                            </div>
                            <a href="{{ route('profile.edit') }}" class="dropdown-item">
                                <i class="bi bi-person" style="width: 16px;"></i>
                                Profil Saya
                            </a>
                            <div class="dropdown-divider"></div>
                            <form method="POST" action="{{ route('logout') }}" class="m-0">
                                @csrf
                                <button type="submit" class="dropdown-item danger">
                                    <i class="bi bi-box-arrow-right" style="width: 16px;"></i>
                                    Keluar
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Main Area -->
            <main class="main-area">
                {{ $slot }}
            </main>
        </div>
    </div>

    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            if (sidebar) {
                sidebar.classList.toggle('show');
            }
        }
    </script>
</body>
</html>
