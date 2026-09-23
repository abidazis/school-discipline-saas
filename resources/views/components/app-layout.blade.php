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
</head>
<body class="bg-slate-100 font-sans antialiased">
    <div class="flex min-h-screen">

        <!-- ============================================
             SIDEBAR - Clean Tailwind Styled
             ============================================ -->
        <aside class="w-64 bg-white border-r border-slate-200 min-h-screen flex flex-col justify-between transition-all duration-300 flex-shrink-0">

            <!-- Brand Header -->
            <div>
                <div class="h-16 flex items-center gap-3 px-4 border-b border-slate-200">
                    <div class="w-9 h-9 bg-indigo-600 rounded-lg flex items-center justify-center text-white">
                        <i class="bi bi-shield-check text-lg"></i>
                    </div>
                    <div>
                        <h1 class="font-semibold text-slate-800 text-sm">School Discipline</h1>
                        <p class="text-xs text-slate-500">Management System</p>
                    </div>
                </div>

                <!-- Navigation Menu -->
                <nav class="p-3 space-y-1">
                    <!-- Dashboard - Single Link -->
                    <a href="{{ route('dashboard') }}"
                       class="flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-lg transition-all duration-200 no-underline
                              {{ request()->routeIs('dashboard')
                                  ? 'bg-indigo-50 text-indigo-600 font-semibold'
                                  : 'text-slate-600 hover:bg-slate-100 hover:text-indigo-600' }}">
                        <i class="bi bi-grid w-5 h-5 text-lg"></i>
                        <span>Dashboard</span>
                    </a>

                    <!-- ============================================
                         SUPER ADMIN SECTION
                         ============================================ -->
                    @if(auth()->user()->isSuperAdmin())
                        <div class="pt-3 mt-3 border-t border-slate-200">
                            <div x-data="{ open: false }" class="mb-1">
                                <button @click="open = !open"
                                        class="w-full flex items-center justify-between px-3 py-2 text-xs font-semibold text-slate-400 uppercase tracking-wider hover:text-slate-700 rounded-lg transition-colors duration-200">
                                    <span class="flex items-center gap-2">
                                        <i class="bi bi-shield-lock w-4 h-4"></i>
                                        Super Admin
                                    </span>
                                    <svg :class="open ? 'rotate-90' : ''"
                                         class="w-4 h-4 transition-transform duration-200"
                                         fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                    </svg>
                                </button>
                                <ul x-show="open"
                                    x-collapse
                                    class="pl-4 space-y-1 mt-1 list-none p-0 m-0">
                                    <li>
                                        <a href="{{ route('schools.index') }}"
                                           class="flex items-center gap-3 px-3 py-2 text-sm font-medium rounded-lg transition-all duration-200 no-underline
                                                  {{ request()->routeIs('schools.*')
                                                      ? 'bg-indigo-50 text-indigo-600 font-semibold'
                                                      : 'text-slate-600 hover:bg-slate-100 hover:text-indigo-600' }}">
                                            <i class="bi bi-building w-5 h-5 text-lg"></i>
                                            <span>Sekolah</span>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="{{ route('users.index') }}"
                                           class="flex items-center gap-3 px-3 py-2 text-sm font-medium rounded-lg transition-all duration-200 no-underline
                                                  {{ request()->routeIs('users.*')
                                                      ? 'bg-indigo-50 text-indigo-600 font-semibold'
                                                      : 'text-slate-600 hover:bg-slate-100 hover:text-indigo-600' }}">
                                            <i class="bi bi-people w-5 h-5 text-lg"></i>
                                            <span>Pengguna</span>
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    @endif

                    <!-- ============================================
                         AKADEMIK SECTION
                         ============================================ -->
                    <div class="pt-3 mt-3 border-t border-slate-200">
                        <div x-data="{ open: true }" class="mb-1">
                            <button @click="open = !open"
                                    class="w-full flex items-center justify-between px-3 py-2 text-xs font-semibold text-slate-400 uppercase tracking-wider hover:text-slate-700 rounded-lg transition-colors duration-200">
                                <span class="flex items-center gap-2">
                                    <i class="bi bi-book w-4 h-4"></i>
                                    Akademik
                                </span>
                                <svg :class="open ? 'rotate-90' : ''"
                                     class="w-4 h-4 transition-transform duration-200"
                                     fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                </svg>
                            </button>
                            <ul x-show="open"
                                x-collapse
                                class="pl-4 space-y-1 mt-1 list-none p-0 m-0">
                                <li>
                                    <a href="{{ route('academic-years.index') }}"
                                       class="flex items-center gap-3 px-3 py-2 text-sm font-medium rounded-lg transition-all duration-200 no-underline
                                              {{ request()->routeIs('academic-years.*')
                                                  ? 'bg-indigo-50 text-indigo-600 font-semibold'
                                                  : 'text-slate-600 hover:bg-slate-100 hover:text-indigo-600' }}">
                                        <i class="bi bi-calendar-range w-5 h-5 text-lg"></i>
                                        <span>Tahun Ajaran</span>
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('departments.index') }}"
                                       class="flex items-center gap-3 px-3 py-2 text-sm font-medium rounded-lg transition-all duration-200 no-underline
                                              {{ request()->routeIs('departments.*')
                                                  ? 'bg-indigo-50 text-indigo-600 font-semibold'
                                                  : 'text-slate-600 hover:bg-slate-100 hover:text-indigo-600' }}">
                                        <i class="bi bi-diagram-3 w-5 h-5 text-lg"></i>
                                        <span>Program Keahlian</span>
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('classes.index') }}"
                                       class="flex items-center gap-3 px-3 py-2 text-sm font-medium rounded-lg transition-all duration-200 no-underline
                                              {{ request()->routeIs('classes.*')
                                                  ? 'bg-indigo-50 text-indigo-600 font-semibold'
                                                  : 'text-slate-600 hover:bg-slate-100 hover:text-indigo-600' }}">
                                        <i class="bi bi-mortarboard w-5 h-5 text-lg"></i>
                                        <span>Kelas</span>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>

                    <!-- ============================================
                         SISWA SECTION - Single Link
                         ============================================ -->
                    <div class="pt-3 mt-3 border-t border-slate-200">
                        <a href="{{ route('students.index') }}"
                           class="flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-lg transition-all duration-200 no-underline
                                  {{ request()->routeIs('students.*')
                                      ? 'bg-indigo-50 text-indigo-600 font-semibold'
                                      : 'text-slate-600 hover:bg-slate-100 hover:text-indigo-600' }}">
                            <i class="bi bi-person-badge w-5 h-5 text-lg"></i>
                            <span>Daftar Siswa</span>
                        </a>
                    </div>

                    <!-- ============================================
                         KEDISIPLINAN SECTION
                         ============================================ -->
                    <div class="pt-3 mt-3 border-t border-slate-200">
                        <div x-data="{ open: true }" class="mb-1">
                            <button @click="open = !open"
                                    class="w-full flex items-center justify-between px-3 py-2 text-xs font-semibold text-slate-400 uppercase tracking-wider hover:text-slate-700 rounded-lg transition-colors duration-200">
                                <span class="flex items-center gap-2">
                                    <i class="bi bi-shield-exclamation w-4 h-4"></i>
                                    Kedisiplinan
                                </span>
                                <svg :class="open ? 'rotate-90' : ''"
                                     class="w-4 h-4 transition-transform duration-200"
                                     fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                </svg>
                            </button>
                            <ul x-show="open"
                                x-collapse
                                class="pl-4 space-y-1 mt-1 list-none p-0 m-0">
                                <li>
                                    <a href="{{ route('violation-types.index') }}"
                                       class="flex items-center gap-3 px-3 py-2 text-sm font-medium rounded-lg transition-all duration-200 no-underline
                                              {{ request()->routeIs('violation-types.*')
                                                  ? 'bg-indigo-50 text-indigo-600 font-semibold'
                                                  : 'text-slate-600 hover:bg-slate-100 hover:text-indigo-600' }}">
                                        <i class="bi bi-list-check w-5 h-5 text-lg"></i>
                                        <span>Jenis Pelanggaran</span>
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('violations.index') }}"
                                       class="flex items-center gap-3 px-3 py-2 text-sm font-medium rounded-lg transition-all duration-200 no-underline
                                              {{ request()->routeIs('violations.*')
                                                  ? 'bg-indigo-50 text-indigo-600 font-semibold'
                                                  : 'text-slate-600 hover:bg-slate-100 hover:text-indigo-600' }}">
                                        <i class="bi bi-exclamation-triangle w-5 h-5 text-lg"></i>
                                        <span>Pelanggaran</span>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>

                    <!-- ============================================
                         PKS SECTION
                         ============================================ -->
                    <div class="pt-3 mt-3 border-t border-slate-200">
                        <div x-data="{ open: false }" class="mb-1">
                            <button @click="open = !open"
                                    class="w-full flex items-center justify-between px-3 py-2 text-xs font-semibold text-slate-400 uppercase tracking-wider hover:text-slate-700 rounded-lg transition-colors duration-200">
                                <span class="flex items-center gap-2">
                                    <i class="bi bi-shield-check w-4 h-4"></i>
                                    PKS
                                </span>
                                <svg :class="open ? 'rotate-90' : ''"
                                     class="w-4 h-4 transition-transform duration-200"
                                     fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                </svg>
                            </button>
                            <ul x-show="open"
                                x-collapse
                                class="pl-4 space-y-1 mt-1 list-none p-0 m-0">
                                <li>
                                    <a href="{{ route('pks-members.index') }}"
                                       class="flex items-center gap-3 px-3 py-2 text-sm font-medium rounded-lg transition-all duration-200 no-underline
                                              {{ request()->routeIs('pks-members.*')
                                                  ? 'bg-indigo-50 text-indigo-600 font-semibold'
                                                  : 'text-slate-600 hover:bg-slate-100 hover:text-indigo-600' }}">
                                        <i class="bi bi-people w-5 h-5 text-lg"></i>
                                        <span>Anggota PKS</span>
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('pks-shifts.index') }}"
                                       class="flex items-center gap-3 px-3 py-2 text-sm font-medium rounded-lg transition-all duration-200 no-underline
                                              {{ request()->routeIs('pks-shifts.*')
                                                  ? 'bg-indigo-50 text-indigo-600 font-semibold'
                                                  : 'text-slate-600 hover:bg-slate-100 hover:text-indigo-600' }}">
                                        <i class="bi bi-clock w-5 h-5 text-lg"></i>
                                        <span>Shift Piket</span>
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('pks-duty-locations.index') }}"
                                       class="flex items-center gap-3 px-3 py-2 text-sm font-medium rounded-lg transition-all duration-200 no-underline
                                              {{ request()->routeIs('pks-duty-locations.*')
                                                  ? 'bg-indigo-50 text-indigo-600 font-semibold'
                                                  : 'text-slate-600 hover:bg-slate-100 hover:text-indigo-600' }}">
                                        <i class="bi bi-geo-alt w-5 h-5 text-lg"></i>
                                        <span>Lokasi Piket</span>
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('pks-duty-schedules.index') }}"
                                       class="flex items-center gap-3 px-3 py-2 text-sm font-medium rounded-lg transition-all duration-200 no-underline
                                              {{ request()->routeIs('pks-duty-schedules.*')
                                                  ? 'bg-indigo-50 text-indigo-600 font-semibold'
                                                  : 'text-slate-600 hover:bg-slate-100 hover:text-indigo-600' }}">
                                        <i class="bi bi-calendar-week w-5 h-5 text-lg"></i>
                                        <span>Jadwal Piket</span>
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('pks-duty-assignments.index') }}"
                                       class="flex items-center gap-3 px-3 py-2 text-sm font-medium rounded-lg transition-all duration-200 no-underline
                                              {{ request()->routeIs('pks-duty-assignments.*')
                                                  ? 'bg-indigo-50 text-indigo-600 font-semibold'
                                                  : 'text-slate-600 hover:bg-slate-100 hover:text-indigo-600' }}">
                                        <i class="bi bi-clipboard-check w-5 h-5 text-lg"></i>
                                        <span>Penugasan</span>
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('pks-duty-attendances.index') }}"
                                       class="flex items-center gap-3 px-3 py-2 text-sm font-medium rounded-lg transition-all duration-200 no-underline
                                              {{ request()->routeIs('pks-duty-attendances.*')
                                                  ? 'bg-indigo-50 text-indigo-600 font-semibold'
                                                  : 'text-slate-600 hover:bg-slate-100 hover:text-indigo-600' }}">
                                        <i class="bi bi-person-check w-5 h-5 text-lg"></i>
                                        <span>Kehadiran</span>
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('pks-field-activities.index') }}"
                                       class="flex items-center gap-3 px-3 py-2 text-sm font-medium rounded-lg transition-all duration-200 no-underline
                                              {{ request()->routeIs('pks-field-activities.*')
                                                  ? 'bg-indigo-50 text-indigo-600 font-semibold'
                                                  : 'text-slate-600 hover:bg-slate-100 hover:text-indigo-600' }}">
                                        <i class="bi bi-map w-5 h-5 text-lg"></i>
                                        <span>Aktivitas Lapangan</span>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>

                    <!-- ============================================
                         LAPORAN SECTION
                         ============================================ -->
                    <div class="pt-3 mt-3 border-t border-slate-200">
                        <div x-data="{ open: false }" class="mb-1">
                            <button @click="open = !open"
                                    class="w-full flex items-center justify-between px-3 py-2 text-xs font-semibold text-slate-400 uppercase tracking-wider hover:text-slate-700 rounded-lg transition-colors duration-200">
                                <span class="flex items-center gap-2">
                                    <i class="bi bi-file-earmark-bar-graph w-4 h-4"></i>
                                    Laporan
                                </span>
                                <svg :class="open ? 'rotate-90' : ''"
                                     class="w-4 h-4 transition-transform duration-200"
                                     fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                </svg>
                            </button>
                            <ul x-show="open"
                                x-collapse
                                class="pl-4 space-y-1 mt-1 list-none p-0 m-0">
                                <li>
                                    <a href="{{ route('reports.daily') }}"
                                       class="flex items-center gap-3 px-3 py-2 text-sm font-medium rounded-lg transition-all duration-200 no-underline
                                              {{ request()->routeIs('reports.daily*')
                                                  ? 'bg-indigo-50 text-indigo-600 font-semibold'
                                                  : 'text-slate-600 hover:bg-slate-100 hover:text-indigo-600' }}">
                                        <i class="bi bi-calendar-day w-5 h-5 text-lg"></i>
                                        <span>Laporan Harian</span>
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('reports.monthly') }}"
                                       class="flex items-center gap-3 px-3 py-2 text-sm font-medium rounded-lg transition-all duration-200 no-underline
                                              {{ request()->routeIs('reports.monthly*')
                                                  ? 'bg-indigo-50 text-indigo-600 font-semibold'
                                                  : 'text-slate-600 hover:bg-slate-100 hover:text-indigo-600' }}">
                                        <i class="bi bi-calendar-event w-5 h-5 text-lg"></i>
                                        <span>Laporan Bulanan</span>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>

                    <!-- ============================================
                         PENGATURAN SECTION - Single Link
                         ============================================ -->
                    <div class="pt-3 mt-3 border-t border-slate-200">
                        <a href="{{ route('profile.edit') }}"
                           class="flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-lg transition-all duration-200 no-underline
                                  {{ request()->routeIs('profile.edit')
                                      ? 'bg-indigo-50 text-indigo-600 font-semibold'
                                      : 'text-slate-600 hover:bg-slate-100 hover:text-indigo-600' }}">
                            <i class="bi bi-sliders w-5 h-5 text-lg"></i>
                            <span>Pengaturan</span>
                        </a>
                    </div>
                </nav>
            </div>

            <!-- Footer - School Info & User -->
            <div class="p-3 border-t border-slate-200 bg-slate-50">
                <!-- School Info -->
                @if(auth()->user()->school)
                <div class="flex items-center gap-3 px-3 py-2 mb-2 bg-white rounded-lg border border-slate-200">
                    <div class="w-8 h-8 bg-indigo-100 rounded-lg flex items-center justify-center text-indigo-600">
                        <i class="bi bi-building"></i>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-xs font-semibold text-slate-800 truncate">{{ auth()->user()->school->name }}</p>
                        <p class="text-xs text-slate-500 capitalize">{{ str_replace('_', ' ', auth()->user()->role) }}</p>
                    </div>
                </div>
                @endif

                <!-- User Menu -->
                <div class="flex items-center gap-3 px-3 py-2">
                    <div class="w-8 h-8 bg-indigo-600 rounded-full flex items-center justify-center text-white text-sm font-semibold">
                        {{ substr(auth()->user()->name, 0, 1) }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-slate-800 truncate">{{ auth()->user()->name }}</p>
                    </div>
                    <form method="POST" action="{{ route('logout') }}" class="m-0">
                        @csrf
                        <button type="submit" class="p-1.5 text-slate-400 hover:text-red-500 hover:bg-red-50 rounded-lg transition-colors duration-200" title="Keluar">
                            <i class="bi bi-box-arrow-right"></i>
                        </button>
                    </form>
                </div>
            </div>
        </aside>

        <!-- ============================================
             MAIN CONTENT AREA
             ============================================ -->
        <div class="flex-1 flex flex-col min-w-0">
            <!-- Top Header -->
            <header class="h-16 bg-white border-b border-slate-200 flex items-center justify-between px-6 flex-shrink-0">
                <div class="flex items-center gap-4">
                    <!-- Mobile Menu Toggle -->
                    <button @click="$dispatch('toggle-sidebar')"
                            class="lg:hidden p-2 text-slate-500 hover:text-slate-700 hover:bg-slate-100 rounded-lg transition-colors duration-200">
                        <i class="bi bi-list text-xl"></i>
                    </button>
                    <!-- Page Title -->
                    <h1 class="text-lg font-semibold text-slate-800">{{ $title ?? 'Dashboard' }}</h1>
                </div>

                <div class="flex items-center gap-3">
                    <!-- Notifications -->
                    <button class="relative p-2 text-slate-500 hover:text-slate-700 hover:bg-slate-100 rounded-lg transition-colors duration-200">
                        <i class="bi bi-bell text-lg"></i>
                        <span class="absolute top-1 right-1 w-2 h-2 bg-red-500 rounded-full"></span>
                    </button>

                    <!-- User Dropdown -->
                    <div x-data="{ open: false }" class="relative">
                        <button @click="open = !open"
                                class="flex items-center gap-2 px-3 py-1.5 text-slate-600 hover:bg-slate-100 rounded-lg transition-colors duration-200">
                            <div class="w-8 h-8 bg-indigo-600 rounded-full flex items-center justify-center text-white text-sm font-semibold">
                                {{ substr(auth()->user()->name, 0, 1) }}
                            </div>
                            <span class="hidden md:block text-sm font-medium">{{ auth()->user()->name }}</span>
                            <i class="bi bi-chevron-down text-xs"></i>
                        </button>

                        <!-- Dropdown Menu -->
                        <div x-show="open"
                             @click.away="open = false"
                             x-transition
                             class="absolute right-0 mt-2 w-56 bg-white rounded-xl shadow-lg border border-slate-200 py-2 z-50">
                            <div class="px-4 py-3 border-b border-slate-100">
                                <p class="text-sm font-semibold text-slate-800">{{ auth()->user()->name }}</p>
                                <p class="text-xs text-slate-500">{{ auth()->user()->email }}</p>
                            </div>
                            <div class="py-1">
                                <a href="{{ route('profile.edit') }}" class="flex items-center gap-3 px-4 py-2 text-sm text-slate-600 hover:bg-slate-50 hover:text-indigo-600 no-underline">
                                    <i class="bi bi-person w-4 h-4"></i>
                                    Profil Saya
                                </a>
                                <form method="POST" action="{{ route('logout') }}" class="m-0">
                                    @csrf
                                    <button type="submit" class="w-full flex items-center gap-3 px-4 py-2 text-sm text-red-600 hover:bg-red-50 no-underline">
                                        <i class="bi bi-box-arrow-right w-4 h-4"></i>
                                        Keluar
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Main Content -->
            <main class="flex-1 p-6 overflow-auto">
                {{ $slot }}
            </main>
        </div>
    </div>
</body>
</html>
