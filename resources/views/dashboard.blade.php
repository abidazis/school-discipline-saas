<x-app-layout>
    <x-slot name="title">Dashboard</x-slot>

    <!-- Welcome Banner -->
    <div class="welcome-banner">
        <div class="welcome-banner-icon">
            <i class="bi bi-shield-check"></i>
        </div>
        <div class="welcome-banner-content">
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
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon stat-icon-primary">
                <i class="bi bi-person-badge"></i>
            </div>
            <div class="stat-content">
                <div class="stat-value">{{ $stats['activeStudents'] ?? 0 }}</div>
                <div class="stat-label">Siswa Aktif</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon stat-icon-warning">
                <i class="bi bi-exclamation-triangle"></i>
            </div>
            <div class="stat-content">
                <div class="stat-value">{{ $stats['totalViolations'] ?? 0 }}</div>
                <div class="stat-label">Total Pelanggaran</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon stat-icon-success">
                <i class="bi bi-shield-check"></i>
            </div>
            <div class="stat-content">
                <div class="stat-value">{{ $stats['totalPksMembers'] ?? 0 }}</div>
                <div class="stat-label">Anggota PKS</div>
            </div>
        </div>
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

    <div class="dashboard-grid">
        <!-- Quick Actions -->
        <div class="dashboard-card">
            <div class="dashboard-card-header">
                <div class="dashboard-card-title">
                    <i class="bi bi-lightning-fill text-primary"></i>
                    Menu Cepat
                </div>
            </div>
            <div class="dashboard-card-body">
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

        <!-- Account Info -->
        <div class="dashboard-card">
            <div class="dashboard-card-header">
                <div class="dashboard-card-title">
                    <i class="bi bi-info-circle-fill text-primary"></i>
                    Informasi Akun
                </div>
            </div>
            <div class="dashboard-card-body">
                <div class="account-info">
                    <div class="account-avatar">{{ substr(auth()->user()->name, 0, 1) }}</div>
                    <div class="account-details">
                        <h5 class="account-name">{{ auth()->user()->name }}</h5>
                        <p class="account-email">{{ auth()->user()->email }}</p>
                        <span class="badge badge-{{ auth()->user()->isSuperAdmin() ? 'danger' : (auth()->user()->isSchoolAdmin() ? 'primary' : 'secondary') }}">
                            {{ ucwords(str_replace('_', ' ', auth()->user()->role)) }}
                        </span>
                    </div>
                </div>

                <hr class="my-4">

                <dl class="info-list">
                    <div class="info-row">
                        <dt><i class="bi bi-building"></i> Sekolah</dt>
                        <dd>
                            @if(auth()->user()->school)
                                {{ auth()->user()->school->name }}
                            @else
                                <span class="text-muted">Tidak Ditetapkan</span>
                            @endif
                        </dd>
                    </div>
                </dl>

                <hr class="my-4">

                <div class="action-buttons">
                    <a href="{{ route('profile.edit') }}" class="btn btn-sm btn-outline-primary">
                        <i class="bi bi-person me-1"></i>Edit Profil
                    </a>
                    @if(auth()->user()->isSuperAdmin())
                        <a href="{{ route('schools.index') }}" class="btn btn-sm btn-danger">
                            <i class="bi bi-building me-1"></i>Kelola Sekolah
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

<style>
    /* Mobile Responsive for Dashboard */
    @media (max-width: 768px) {
        .stats-grid {
            grid-template-columns: repeat(2, 1fr) !important;
        }

        .dashboard-grid {
            grid-template-columns: 1fr !important;
        }
    }

    @media (max-width: 480px) {
        .stats-grid {
            grid-template-columns: 1fr !important;
        }

        .stat-card {
            flex-direction: column !important;
            text-align: center !important;
        }

        .stat-icon {
            margin: 0 auto !important;
        }

        .welcome-banner {
            flex-direction: column !important;
            text-align: center !important;
            padding: 20px !important;
        }

        .quick-actions {
            grid-template-columns: repeat(2, 1fr) !important;
        }
    }
</style>
