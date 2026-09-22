<x-app-layout>
    <x-slot name="title">Dashboard</x-slot>

    <!-- Welcome Banner -->
    <div class="welcome-banner">
        <div class="welcome-banner-icon">
            <i class="bi bi-shield-check"></i>
        </div>
        <div>
            <h2 class="welcome-banner-title">Selamat Datang, {{ auth()->user()->name }}!</h2>
            <p class="welcome-banner-text">
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

    <!-- Stats Cards -->
    <div class="row g-3 mb-4">
        <div class="col-6 col-lg-3">
            <div class="stat-card">
                <div class="stat-icon stat-icon-primary">
                    <i class="bi bi-person-badge"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-value">{{ $stats['activeStudents'] ?? 0 }}</div>
                    <div class="stat-label">Siswa Aktif</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="stat-card">
                <div class="stat-icon stat-icon-warning">
                    <i class="bi bi-exclamation-triangle"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-value">{{ $stats['totalViolations'] ?? 0 }}</div>
                    <div class="stat-label">Total Pelanggaran</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="stat-card">
                <div class="stat-icon stat-icon-success">
                    <i class="bi bi-shield-check"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-value">{{ $stats['totalPksMembers'] ?? 0 }}</div>
                    <div class="stat-label">Anggota PKS</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="stat-card">
                <div class="stat-icon stat-icon-info">
                    <i class="bi bi-calendar-week"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-value">{{ $stats['totalSchedules'] ?? 0 }}</div>
                    <div class="stat-label">Jadwal Piket</div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <!-- Quick Actions -->
        <div class="col-12 col-lg-6">
            <div class="app-card">
                <div class="app-card-header">
                    <div class="app-card-title">
                        <i class="bi bi-lightning text-primary"></i>
                        Menu Cepat
                    </div>
                </div>
                <div class="app-card-body">
                    <div class="quick-actions">
                        <a href="{{ route('students.index') }}" class="quick-action">
                            <div class="quick-action-icon" style="background: #e0e7ff; color: #4f46e5;">
                                <i class="bi bi-person-badge"></i>
                            </div>
                            <span class="quick-action-label">Daftar Siswa</span>
                        </a>
                        @if(auth()->user()->isSuperAdmin() || auth()->user()->isSchoolAdmin())
                            <a href="{{ route('violations.index') }}" class="quick-action">
                                <div class="quick-action-icon" style="background: #fef3c7; color: #d97706;">
                                    <i class="bi bi-exclamation-triangle"></i>
                                </div>
                                <span class="quick-action-label">Pelanggaran</span>
                            </a>
                            <a href="{{ route('violation-types.index') }}" class="quick-action">
                                <div class="quick-action-icon" style="background: #fee2e2; color: #dc2626;">
                                    <i class="bi bi-clipboard-check"></i>
                                </div>
                                <span class="quick-action-label">Jenis Pelanggaran</span>
                            </a>
                        @endif
                        <a href="{{ route('pks-members.index') }}" class="quick-action">
                            <div class="quick-action-icon" style="background: #d1fae5; color: #059669;">
                                <i class="bi bi-shield-check"></i>
                            </div>
                            <span class="quick-action-label">Anggota PKS</span>
                        </a>
                        @if(auth()->user()->isSuperAdmin() || auth()->user()->isSchoolAdmin())
                            <a href="{{ route('pks-shifts.index') }}" class="quick-action">
                                <div class="quick-action-icon" style="background: #cffafe; color: #0891b2;">
                                    <i class="bi bi-clock"></i>
                                </div>
                                <span class="quick-action-label">Shift Piket</span>
                            </a>
                            <a href="{{ route('pks-duty-schedules.index') }}" class="quick-action">
                                <div class="quick-action-icon" style="background: #f1f5f9; color: #64748b;">
                                    <i class="bi bi-calendar-week"></i>
                                </div>
                                <span class="quick-action-label">Jadwal Piket</span>
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Account Info -->
        <div class="col-12 col-lg-6">
            <div class="app-card">
                <div class="app-card-header">
                    <div class="app-card-title">
                        <i class="bi bi-info-circle text-primary"></i>
                        Informasi Akun
                    </div>
                </div>
                <div class="app-card-body">
                    <div class="d-flex align-items-start gap-3 mb-3">
                        <div class="avatar avatar-lg">{{ substr(auth()->user()->name, 0, 1) }}</div>
                        <div>
                            <h5 class="mb-1">{{ auth()->user()->name }}</h5>
                            <p class="text-muted small mb-1">{{ auth()->user()->email }}</p>
                            <span class="badge-app badge-{{ auth()->user()->isSuperAdmin() ? 'danger' : (auth()->user()->isSchoolAdmin() ? 'primary' : 'secondary') }}">
                                {{ ucwords(str_replace('_', ' ', auth()->user()->role)) }}
                            </span>
                        </div>
                    </div>

                    <hr>

                    <dl class="row mb-0">
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
                        <a href="{{ route('profile.edit') }}" class="btn-app btn-app-sm btn-app-outline-primary">
                            <i class="bi bi-person me-1"></i>Edit Profil
                        </a>
                        @if(auth()->user()->isSuperAdmin())
                            <a href="{{ route('schools.index') }}" class="btn-app btn-app-sm btn-app-danger">
                                <i class="bi bi-building me-1"></i>Kelola Sekolah
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
