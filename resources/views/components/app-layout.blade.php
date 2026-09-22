<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'School Discipline') }} - {{ $title ?? 'Dashboard' }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    <div class="app-wrapper">
        <aside class="app-sidebar" id="sidebar">
            <div class="sidebar-brand">
                <div class="sidebar-brand-icon">
                    <i class="bi bi-shield-check"></i>
                </div>
                <span class="sidebar-brand-text">School Discipline</span>
            </div>

            <nav class="sidebar-nav">
                <div class="sidebar-section">
                    <div class="sidebar-section-title">Menu Utama</div>
                    <ul class="sidebar-menu">
                        <li class="sidebar-item">
                            <a href="{{ route('dashboard') }}" class="sidebar-link @if(request()->routeIs('dashboard')) active @endif">
                                <i class="bi bi-grid sidebar-link-icon"></i>
                                <span>Dashboard</span>
                            </a>
                        </li>
                    </ul>
                </div>

                @if(auth()->user()->isSuperAdmin())
                <div class="sidebar-section">
                    <div class="sidebar-section-title">Super Admin</div>
                    <ul class="sidebar-menu">
                        <li class="sidebar-item">
                            <a href="{{ route('schools.index') }}" class="sidebar-link @if(request()->routeIs('schools.*')) active @endif">
                                <i class="bi bi-building sidebar-link-icon"></i>
                                <span>Sekolah</span>
                            </a>
                        </li>
                        <li class="sidebar-item">
                            <a href="{{ route('users.index') }}" class="sidebar-link @if(request()->routeIs('users.*')) active @endif">
                                <i class="bi bi-people sidebar-link-icon"></i>
                                <span>Pengguna</span>
                            </a>
                        </li>
                    </ul>
                </div>
                @endif

                <div class="sidebar-section">
                    <div class="sidebar-section-title">Akademik</div>
                    <ul class="sidebar-menu">
                        <li class="sidebar-item">
                            <a href="{{ route('academic-years.index') }}" class="sidebar-link @if(request()->routeIs('academic-years.*')) active @endif">
                                <i class="bi bi-calendar sidebar-link-icon"></i>
                                <span>Tahun Ajaran</span>
                            </a>
                        </li>
                        <li class="sidebar-item">
                            <a href="{{ route('departments.index') }}" class="sidebar-link @if(request()->routeIs('departments.*')) active @endif">
                                <i class="bi bi-diagram-3 sidebar-link-icon"></i>
                                <span>Program Keahlian</span>
                            </a>
                        </li>
                        <li class="sidebar-item">
                            <a href="{{ route('classes.index') }}" class="sidebar-link @if(request()->routeIs('classes.*')) active @endif">
                                <i class="bi bi-mortarboard sidebar-link-icon"></i>
                                <span>Kelas</span>
                            </a>
                        </li>
                    </ul>
                </div>

                <div class="sidebar-section">
                    <div class="sidebar-section-title">Siswa</div>
                    <ul class="sidebar-menu">
                        <li class="sidebar-item">
                            <a href="{{ route('students.index') }}" class="sidebar-link @if(request()->routeIs('students.*')) active @endif">
                                <i class="bi bi-person-badge sidebar-link-icon"></i>
                                <span>Daftar Siswa</span>
                            </a>
                        </li>
                    </ul>
                </div>

                <div class="sidebar-section">
                    <div class="sidebar-section-title">Kedisiplinan</div>
                    <ul class="sidebar-menu">
                        <li class="sidebar-item">
                            <a href="{{ route('violation-types.index') }}" class="sidebar-link @if(request()->routeIs('violation-types.*')) active @endif">
                                <i class="bi bi-list-check sidebar-link-icon"></i>
                                <span>Jenis Pelanggaran</span>
                            </a>
                        </li>
                        <li class="sidebar-item">
                            <a href="{{ route('violations.index') }}" class="sidebar-link @if(request()->routeIs('violations.*')) active @endif">
                                <i class="bi bi-exclamation-triangle sidebar-link-icon"></i>
                                <span>Pelanggaran</span>
                            </a>
                        </li>
                    </ul>
                </div>

                <div class="sidebar-section">
                    <div class="sidebar-section-title">PKS</div>
                    <ul class="sidebar-menu">
                        <li class="sidebar-item">
                            <a href="{{ route('pks-members.index') }}" class="sidebar-link @if(request()->routeIs('pks-members.*')) active @endif">
                                <i class="bi bi-shield sidebar-link-icon"></i>
                                <span>Anggota PKS</span>
                            </a>
                        </li>
                        <li class="sidebar-item">
                            <a href="{{ route('pks-shifts.index') }}" class="sidebar-link @if(request()->routeIs('pks-shifts.*')) active @endif">
                                <i class="bi bi-clock sidebar-link-icon"></i>
                                <span>Shift Piket</span>
                            </a>
                        </li>
                        <li class="sidebar-item">
                            <a href="{{ route('pks-duty-locations.index') }}" class="sidebar-link @if(request()->routeIs('pks-duty-locations.*')) active @endif">
                                <i class="bi bi-geo-alt sidebar-link-icon"></i>
                                <span>Lokasi Piket</span>
                            </a>
                        </li>
                        <li class="sidebar-item">
                            <a href="{{ route('pks-duty-schedules.index') }}" class="sidebar-link @if(request()->routeIs('pks-duty-schedules.*')) active @endif">
                                <i class="bi bi-calendar-week sidebar-link-icon"></i>
                                <span>Jadwal Piket</span>
                            </a>
                        </li>
                        <li class="sidebar-item">
                            <a href="{{ route('pks-duty-assignments.index') }}" class="sidebar-link @if(request()->routeIs('pks-duty-assignments.*')) active @endif">
                                <i class="bi bi-clipboard-check sidebar-link-icon"></i>
                                <span>Penugasan</span>
                            </a>
                        </li>
                        <li class="sidebar-item">
                            <a href="{{ route('pks-duty-attendances.index') }}" class="sidebar-link @if(request()->routeIs('pks-duty-attendances.*')) active @endif">
                                <i class="bi bi-person-check sidebar-link-icon"></i>
                                <span>Kehadiran</span>
                            </a>
                        </li>
                        <li class="sidebar-item">
                            <a href="{{ route('pks-field-activities.index') }}" class="sidebar-link @if(request()->routeIs('pks-field-activities.*')) active @endif">
                                <i class="bi bi-map sidebar-link-icon"></i>
                                <span>Aktivitas Lapangan</span>
                            </a>
                        </li>
                    </ul>
                </div>

                <div class="sidebar-section">
                    <div class="sidebar-section-title">Laporan</div>
                    <ul class="sidebar-menu">
                        <li class="sidebar-item">
                            <a href="{{ route('reports.daily') }}" class="sidebar-link @if(request()->routeIs('reports.daily*')) active @endif">
                                <i class="bi bi-calendar-day sidebar-link-icon"></i>
                                <span>Laporan Harian</span>
                            </a>
                        </li>
                        <li class="sidebar-item">
                            <a href="{{ route('reports.monthly') }}" class="sidebar-link @if(request()->routeIs('reports.monthly*')) active @endif">
                                <i class="bi bi-calendar-event sidebar-link-icon"></i>
                                <span>Laporan Bulanan</span>
                            </a>
                        </li>
                    </ul>
                </div>

                <div class="sidebar-section">
                    <div class="sidebar-section-title">Pengaturan</div>
                    <ul class="sidebar-menu">
                        <li class="sidebar-item">
                            <a href="{{ route('profile.edit') }}" class="sidebar-link @if(request()->routeIs('profile.edit')) active @endif">
                                <i class="bi bi-gear sidebar-link-icon"></i>
                                <span>Profil</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </nav>

            @if(auth()->user()->school)
            <div class="sidebar-footer">
                <div class="sidebar-school-box">
                    <div class="sidebar-school-name">{{ auth()->user()->school->name }}</div>
                    <div class="sidebar-school-email">{{ auth()->user()->school->email ?? '-' }}</div>
                </div>
            </div>
            @endif
        </aside>

        <div class="sidebar-overlay" id="sidebarOverlay" onclick="toggleSidebar()"></div>

        <div class="app-content">
            <header class="app-header">
                <div class="app-header-left">
                    <button class="btn btn-outline-secondary btn-sm d-lg-none" onclick="toggleSidebar()" aria-label="Toggle menu">
                        <i class="bi bi-list"></i>
                    </button>
                    <h1 class="app-header-title">{{ $title ?? 'Dashboard' }}</h1>
                </div>

                <div class="app-header-right">
                    <div class="user-info d-none d-md-block">
                        <div class="user-name">{{ auth()->user()->name }}</div>
                        <div class="user-role">
                            @if(auth()->user()->isSuperAdmin())
                                <span class="badge bg-danger">Super Admin</span>
                            @else
                                <span class="badge bg-primary">{{ ucwords(str_replace('_', ' ', auth()->user()->role) }}</span>
                            @endif
                        </div>
                    </div>

                    <div class="dropdown">
                        <button class="btn btn-outline-secondary btn-sm" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <div class="avatar">{{ substr(auth()->user()->name, 0, 1) }}</div>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="{{ route('profile.edit') }}"><i class="bi bi-person me-2"></i>Profil</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="dropdown-item"><i class="bi bi-box-arrow-right me-2"></i>Keluar</button>
                                </form>
                            </li>
                        </ul>
                    </div>
                </div>
            </header>

            <main class="app-main">
                {{ $slot }}
            </main>
        </div>
    </div>
</body>
</html>
