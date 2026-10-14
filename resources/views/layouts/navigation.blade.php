<nav x-data="{ open: false }" class="bg-white border-b border-gray-100">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}">
                        <x-application-logo class="block h-9 w-auto fill-current text-gray-800" />
                    </a>
                </div>

                <!-- Navigation Links - Simplified for PKS Team -->
                <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                        <i class="bi bi-house-door me-1"></i>{{ __('Dashboard') }}
                    </x-nav-link>

                    @if(auth()->user()->isPksTeam())
                        {{-- PKS TEAM: Only these menus --}}
                        <x-nav-link :href="route('violations.index')" :active="request()->routeIs('violations.*')">
                            <i class="bi bi-exclamation-triangle me-1"></i>{{ __('Pelanggaran') }}
                        </x-nav-link>

                        <div class="hidden sm:flex sm:items-center sm:ms-6">
                            <x-dropdown align="left" width="48">
                                <x-slot name="trigger">
                                    <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition ease-in-out duration-150 {{ request()->routeIs('pks-duty-schedules.*', 'pks-duty-attendances.*') ? 'text-gray-700' : '' }}">
                                        <i class="bi bi-calendar-week me-1"></i>
                                        {{ __('Piket PKS') }}
                                        <div class="ms-1">
                                            <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                            </svg>
                                        </div>
                                    </button>
                                </x-slot>
                                <x-slot name="content">
                                    <x-dropdown-link :href="route('pks-duty-schedules.index')">
                                        <i class="bi bi-calendar3 me-2"></i>{{ __('Jadwal Piket') }}
                                    </x-dropdown-link>
                                    <x-dropdown-link :href="route('pks-duty-attendances.index')">
                                        <i class="bi bi-clipboard-check me-2"></i>{{ __('Absensi Piket') }}
                                    </x-dropdown-link>
                                </x-slot>
                            </x-dropdown>
                        </div>
                    @else
                        {{-- OTHER ROLES: Full menu (Admin Sekolah, Pembina PKS, Guru) --}}
                        @can('manage-master-data')
                            <x-nav-link :href="route('schools.index')" :active="request()->routeIs('schools.*')">
                                {{ __('Schools') }}
                            </x-nav-link>
                            <x-nav-link :href="route('users.index')" :active="request()->routeIs('users.*')">
                                {{ __('Users') }}
                            </x-nav-link>
                            <x-nav-link :href="route('academic-years.index')" :active="request()->routeIs('academic-years.*')">
                                {{ __('Tahun Ajaran') }}
                            </x-nav-link>
                            <x-nav-link :href="route('departments.index')" :active="request()->routeIs('departments.*')">
                                {{ __('Program Keahlian') }}
                            </x-nav-link>
                            <x-nav-link :href="route('classes.index')" :active="request()->routeIs('classes.*')">
                                {{ __('Kelas') }}
                            </x-nav-link>
                            <x-nav-link :href="route('students.index')" :active="request()->routeIs('students.*')">
                                {{ __('Daftar Siswa') }}
                            </x-nav-link>
                            <x-nav-link :href="route('violation-types.index')" :active="request()->routeIs('violation-types.*')">
                                {{ __('Jenis Pelanggaran') }}
                            </x-nav-link>
                        @endcan

                        @can('manage-pks-team')
                            <x-nav-link :href="route('pks-members.index')" :active="request()->routeIs('pks-members.*')">
                                {{ __('Anggota PKS') }}
                            </x-nav-link>
                        @endcan

                        @can('view-violations')
                            <x-nav-link :href="route('violations.index')" :active="request()->routeIs('violations.*')">
                                {{ __('Pelanggaran') }}
                            </x-nav-link>
                        @endcan

                        @can('view-pks-operational')
                            <div class="hidden sm:flex sm:items-center sm:ms-6">
                                <x-dropdown align="left" width="48">
                                    <x-slot name="trigger">
                                        <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition ease-in-out duration-150 {{ request()->routeIs('pks-shifts.*', 'pks-duty-locations.*', 'pks-duty-schedules.*', 'pks-duty-assignments.*', 'pks-duty-attendances.*', 'pks-field-activities.*', 'reports.*') ? 'text-gray-700' : '' }}">
                                            {{ __('Operasional PKS') }}
                                            <div class="ms-1">
                                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                                </svg>
                                            </div>
                                        </button>
                                    </x-slot>
                                    <x-slot name="content">
                                        <x-dropdown-link :href="route('pks-shifts.index')">
                                            {{ __('Shift PKS') }}
                                        </x-dropdown-link>
                                        <x-dropdown-link :href="route('pks-duty-locations.index')">
                                            {{ __('Lokasi Duty') }}
                                        </x-dropdown-link>
                                        <x-dropdown-link :href="route('pks-duty-schedules.index')">
                                            {{ __('Jadwal Duty') }}
                                        </x-dropdown-link>
                                        <x-dropdown-link :href="route('pks-duty-assignments.index')">
                                            {{ __('Penugasan Duty') }}
                                        </x-dropdown-link>
                                        <x-dropdown-link :href="route('pks-duty-attendances.index')">
                                            {{ __('Absensi Duty') }}
                                        </x-dropdown-link>
                                        <x-dropdown-link :href="route('pks-field-activities.index')">
                                            {{ __('Aktivitas Lapangan') }}
                                        </x-dropdown-link>
                                        <hr class="my-1 border-gray-200">
                                        <x-dropdown-link :href="route('reports.daily')">
                                            {{ __('Laporan Harian') }}
                                        </x-dropdown-link>
                                        <x-dropdown-link :href="route('reports.monthly')">
                                            {{ __('Laporan Bulanan') }}
                                        </x-dropdown-link>
                                    </x-slot>
                                </x-dropdown>
                            </div>
                        @endcan
                    @endif
                </div>
            </div>

            <!-- Settings Dropdown -->
            <div class="hidden sm:flex sm:items-center sm:ms-6">
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition ease-in-out duration-150">
                            <div>{{ Auth::user()->name }}</div>
                            <span class="ms-2 text-xs px-2 py-0.5 rounded-full
                                {{ Auth::user()->isPksTeam() ? 'bg-warning text-dark' : (Auth::user()->isSuperAdmin() ? 'bg-danger text-white' : (Auth::user()->isSchoolAdmin() ? 'bg-primary text-white' : (Auth::user()->isPembinaPks() ? 'bg-success text-white' : 'bg-secondary text-white'))) }}">
                                {{ Auth::user()->role === 'super_admin' ? 'Super Admin' : (Auth::user()->role === 'school_admin' ? 'Admin Sekolah' : (Auth::user()->role === 'pembina_pks' ? 'Pembina PKS' : (Auth::user()->role === 'pks_team' ? 'Tim PKS' : 'Guru'))) }}
                            </span>

                            <div class="ms-1">
                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <x-dropdown-link :href="route('profile.edit')">
                            {{ __('Profile') }}
                        </x-dropdown-link>

                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <x-dropdown-link :href="route('logout')"
                                    onclick="event.preventDefault(); this.closest('form').submit();">
                                {{ __('Log Out') }}
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            <!-- Hamburger -->
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu (Mobile) -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                <i class="bi bi-house me-2"></i>{{ __('Dashboard') }}
            </x-responsive-nav-link>

            @if(auth()->user()->isPksTeam())
                {{-- PKS TEAM: Only these menus --}}
                <x-responsive-nav-link :href="route('violations.index')" :active="request()->routeIs('violations.*')">
                    <i class="bi bi-exclamation-triangle me-2"></i>{{ __('Pelanggaran') }}
                </x-responsive-nav-link>

                <div class="px-4 py-2 text-xs font-semibold text-gray-500 uppercase tracking-wider">
                    <i class="bi bi-calendar-week me-1"></i>{{ __('Piket PKS') }}
                </div>
                <x-responsive-nav-link :href="route('pks-duty-schedules.index')" :active="request()->routeIs('pks-duty-schedules.*')">
                    <i class="bi bi-calendar3 me-2"></i>{{ __('Jadwal Piket') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('pks-duty-attendances.index')" :active="request()->routeIs('pks-duty-attendances.*')">
                    <i class="bi bi-clipboard-check me-2"></i>{{ __('Absensi Piket') }}
                </x-responsive-nav-link>
            @else
                {{-- OTHER ROLES: Full menu --}}
                @can('manage-master-data')
                    <x-responsive-nav-link :href="route('schools.index')" :active="request()->routeIs('schools.*')">
                        {{ __('Schools') }}
                    </x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('users.index')" :active="request()->routeIs('users.*')">
                        {{ __('Users') }}
                    </x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('academic-years.index')" :active="request()->routeIs('academic-years.*')">
                        {{ __('Tahun Ajaran') }}
                    </x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('departments.index')" :active="request()->routeIs('departments.*')">
                        {{ __('Program Keahlian') }}
                    </x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('classes.index')" :active="request()->routeIs('classes.*')">
                        {{ __('Kelas') }}
                    </x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('students.index')" :active="request()->routeIs('students.*')">
                        {{ __('Daftar Siswa') }}
                    </x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('violation-types.index')" :active="request()->routeIs('violation-types.*')">
                        {{ __('Jenis Pelanggaran') }}
                    </x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('pks-members.index')" :active="request()->routeIs('pks-members.*')">
                        {{ __('Anggota PKS') }}
                    </x-responsive-nav-link>
                @endcan

                @can('view-violations')
                    <x-responsive-nav-link :href="route('violations.index')" :active="request()->routeIs('violations.*')">
                        <i class="bi bi-exclamation-triangle me-2"></i>{{ __('Pelanggaran') }}
                    </x-responsive-nav-link>
                @endcan

                @can('view-pks-operational')
                    <div class="px-4 py-2 text-xs font-semibold text-gray-500 uppercase tracking-wider">
                        {{ __('Operasional PKS') }}
                    </div>
                    <x-responsive-nav-link :href="route('pks-shifts.index')" :active="request()->routeIs('pks-shifts.*')">
                        {{ __('Shift PKS') }}
                    </x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('pks-duty-locations.index')" :active="request()->routeIs('pks-duty-locations.*')">
                        {{ __('Lokasi Duty') }}
                    </x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('pks-duty-schedules.index')" :active="request()->routeIs('pks-duty-schedules.*')">
                        {{ __('Jadwal Duty') }}
                    </x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('pks-duty-assignments.index')" :active="request()->routeIs('pks-duty-assignments.*')">
                        {{ __('Penugasan Duty') }}
                    </x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('pks-duty-attendances.index')" :active="request()->routeIs('pks-duty-attendances.*')">
                        {{ __('Absensi Duty') }}
                    </x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('pks-field-activities.index')" :active="request()->routeIs('pks-field-activities.*')">
                        {{ __('Aktivitas Lapangan') }}
                    </x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('reports.daily')" :active="request()->routeIs('reports.daily')">
                        {{ __('Laporan Harian') }}
                    </x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('reports.monthly')" :active="request()->routeIs('reports.monthly')">
                        {{ __('Laporan Bulanan') }}
                    </x-responsive-nav-link>
                @endcan
            @endif
        </div>

        <!-- Responsive Settings Options -->
        <div class="pt-4 pb-1 border-t border-gray-200">
            <div class="px-4">
                <div class="font-medium text-base text-gray-800">{{ Auth::user()->name }}</div>
                <div class="font-medium text-sm text-gray-500">{{ Auth::user()->email }}</div>
                <span class="mt-1 inline-block text-xs px-2 py-0.5 rounded-full
                    {{ Auth::user()->isPksTeam() ? 'bg-warning text-dark' : (Auth::user()->isSuperAdmin() ? 'bg-danger text-white' : (Auth::user()->isSchoolAdmin() ? 'bg-primary text-white' : (Auth::user()->isPembinaPks() ? 'bg-success text-white' : 'bg-secondary text-white'))) }}">
                    {{ Auth::user()->role === 'super_admin' ? 'Super Admin' : (Auth::user()->role === 'school_admin' ? 'Admin Sekolah' : (Auth::user()->role === 'pembina_pks' ? 'Pembina PKS' : (Auth::user()->role === 'pks_team' ? 'Tim PKS' : 'Guru'))) }}
                </span>
            </div>

            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('profile.edit')">
                    {{ __('Profile') }}
                </x-responsive-nav-link>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <x-responsive-nav-link :href="route('logout')"
                            onclick="event.preventDefault(); this.closest('form').submit();">
                        {{ __('Log Out') }}
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>
