<x-app-layout>
    <x-slot name="title">Dashboard</x-slot>

    <div class="row g-4 mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="bg-primary bg-opacity-10 rounded-circle p-3">
                                <i class="bi bi-shield-check text-primary fs-4"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h5 class="mb-1">Selamat Datang, {{ auth()->user()->name }}!</h5>
                            <p class="mb-0 text-muted small">
                                @if(auth()->user()->isSuperAdmin())
                                    Anda login sebagai Super Admin. Anda memiliki akses ke semua sekolah.
                                @elseif(auth()->user()->school)
                                    Mengelola <strong>{{ auth()->user()->school->name }}</strong>
                                @else
                                    Silakan hubungi administrator untuk menetapkan sekolah Anda.
                                @endif
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center">
                    <div class="bg-primary bg-opacity-10 rounded-circle d-inline-flex p-3 mb-3">
                        <i class="bi bi-mortarboard text-primary fs-4"></i>
                    </div>
                    <h3 class="mb-1">{{ $stats['activeStudents'] ?? 0 }}</h3>
                    <p class="text-muted mb-0 small">Siswa Aktif</p>
                </div>
            </div>
        </div>

        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center">
                    <div class="bg-warning bg-opacity-10 rounded-circle d-inline-flex p-3 mb-3">
                        <i class="bi bi-exclamation-triangle text-warning fs-4"></i>
                    </div>
                    <h3 class="mb-1">{{ $stats['totalViolations'] ?? 0 }}</h3>
                    <p class="text-muted mb-0 small">Pelanggaran</p>
                </div>
            </div>
        </div>

        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center">
                    <div class="bg-success bg-opacity-10 rounded-circle d-inline-flex p-3 mb-3">
                        <i class="bi bi-people text-success fs-4"></i>
                    </div>
                    <h3 class="mb-1">{{ $stats['totalPksMembers'] ?? 0 }}</h3>
                    <p class="text-muted mb-0 small">Anggota PKS</p>
                </div>
            </div>
        </div>

        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center">
                    <div class="bg-info bg-opacity-10 rounded-circle d-inline-flex p-3 mb-3">
                        <i class="bi bi-calendar-check text-info fs-4"></i>
                    </div>
                    <h3 class="mb-1">{{ $stats['totalSchedules'] ?? 0 }}</h3>
                    <p class="text-muted mb-0 small">Jadwal Piket</p>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4 mt-2">
        <div class="col-12 col-lg-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-0">
                    <h5 class="mb-0"><i class="bi bi-lightning me-2"></i>Menu Cepat</h5>
                </div>
                <div class="card-body">
                    <div class="d-flex flex-column gap-2">
                        <a href="{{ route('students.index') }}" class="btn btn-outline-primary text-start">
                            <i class="bi bi-mortarboard me-2"></i>Daftar Siswa
                        </a>
                        @if(auth()->user()->isSuperAdmin() || auth()->user()->isSchoolAdmin())
                            <a href="{{ route('violations.index') }}" class="btn btn-outline-warning text-start">
                                <i class="bi bi-exclamation-triangle me-2"></i>Pelanggaran
                            </a>
                            <a href="{{ route('violation-types.index') }}" class="btn btn-outline-secondary text-start">
                                <i class="bi bi-list-ul me-2"></i>Jenis Pelanggaran
                            </a>
                        @endif
                        <a href="{{ route('pks-members.index') }}" class="btn btn-outline-success text-start">
                            <i class="bi bi-people me-2"></i>Anggota PKS
                        </a>
                        @if(auth()->user()->isSuperAdmin() || auth()->user()->isSchoolAdmin())
                            <a href="{{ route('pks-shifts.index') }}" class="btn btn-outline-info text-start">
                                <i class="bi bi-clock me-2"></i>Shift Piket
                            </a>
                            <a href="{{ route('pks-duty-locations.index') }}" class="btn btn-outline-info text-start">
                                <i class="bi bi-geo-alt me-2"></i>Lokasi Piket
                            </a>
                            <a href="{{ route('pks-duty-schedules.index') }}" class="btn btn-outline-info text-start">
                                <i class="bi bi-calendar-check me-2"></i>Jadwal Piket
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-lg-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-0">
                    <h5 class="mb-0"><i class="bi bi-info-circle me-2"></i>Informasi Akun</h5>
                </div>
                <div class="card-body">
                    <dl class="row mb-0">
                        <dt class="col-4 text-muted small">Nama</dt>
                        <dd class="col-8">{{ auth()->user()->name }}</dd>
                        <dt class="col-4 text-muted small">Email</dt>
                        <dd class="col-8">{{ auth()->user()->email }}</dd>
                        <dt class="col-4 text-muted small">Role</dt>
                        <dd class="col-8">
                            <span class="badge bg-{{ auth()->user()->isSuperAdmin() ? 'danger' : (auth()->user()->isSchoolAdmin() ? 'primary' : 'secondary') }}">
                                {{ ucwords(str_replace('_', ' ', auth()->user()->role)) }}
                            </span>
                        </dd>
                        <dt class="col-4 text-muted small">Sekolah</dt>
                        <dd class="col-8">
                            @if(auth()->user()->school)
                                {{ auth()->user()->school->name }}
                            @else
                                <span class="text-muted">Tidak Ditetapkan</span>
                            @endif
                        </dd>
                    </dl>
                    <hr>
                    <div class="d-flex gap-2 flex-wrap">
                        <a href="{{ route('profile.edit') }}" class="btn btn-sm btn-outline-primary">
                            <i class="bi bi-person me-1"></i>Edit Profil
                        </a>
                        @if(auth()->user()->isSuperAdmin())
                            <a href="{{ route('schools.index') }}" class="btn btn-sm btn-outline-danger">
                                <i class="bi bi-building me-1"></i>Kelola Sekolah
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
