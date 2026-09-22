<x-app-layout>
    <x-slot name="title">Anggota PKS</x-slot>

    <!-- Page Header -->
    <div class="page-header">
        <h1 class="page-title">
            <div class="page-title-icon" style="background: var(--color-success-light); color: var(--color-success);">
                <i class="bi bi-shield-check"></i>
            </div>
            Anggota PKS
        </h1>
        @if(auth()->user()->isSuperAdmin() || auth()->user()->isSchoolAdmin())
            <a href="{{ route('pks-members.create') }}" class="btn btn-primary">
                <i class="bi bi-person-plus me-1"></i>Tambah Anggota
            </a>
        @endif
    </div>

    <!-- Filters Card -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" class="row g-3">
                <div class="col-12 col-md-3">
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-search"></i></span>
                        <input type="text" class="form-control" name="search" placeholder="NIS atau nama..." value="{{ request('search') }}">
                    </div>
                </div>
                <div class="col-12 col-md-2">
                    <select class="form-select" name="status">
                        <option value="">Semua Status</option>
                        <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Aktif</option>
                        <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Tidak Aktif</option>
                        <option value="graduated" {{ request('status') === 'graduated' ? 'selected' : '' }}>Lulus</option>
                        <option value="resigned" {{ request('status') === 'resigned' ? 'selected' : '' }}>Mengundurkan Diri</option>
                    </select>
                </div>
                <div class="col-12 col-md-2">
                    <select class="form-select" name="position">
                        <option value="">Semua Jabatan</option>
                        @foreach($positions as $position)
                            <option value="{{ $position }}" {{ request('position') === $position ? 'selected' : '' }}>{{ $position }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-12 col-md-2">
                    <select class="form-select" name="school_class_id">
                        <option value="">Semua Kelas</option>
                        @foreach($classes as $class)
                            <option value="{{ $class->id }}" {{ request('school_class_id') == $class->id ? 'selected' : '' }}>
                                {{ $class->full_name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-12 col-md-3">
                    <button type="submit" class="btn btn-outline-primary">
                        <i class="bi bi-filter me-1"></i>Filter
                    </button>
                    <a href="{{ route('pks-members.index') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-counterclockwise"></i>
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- List Card -->
    <div class="card">
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>NIS</th>
                        <th>Nama</th>
                        <th>Kelas</th>
                        <th>Jabatan</th>
                        <th>Status</th>
                        <th>Bergabung</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($pksMembers as $member)
                        <tr>
                            <td>
                                <a href="{{ route('pks-members.show', $member) }}" class="text-decoration-none fw-medium">
                                    {{ $member->student->nis }}
                                </a>
                            </td>
                            <td>{{ $member->student->full_name }}</td>
                            <td>
                                @if($member->student->schoolClass)
                                    {{ $member->student->schoolClass->full_name }}
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>{{ $member->position }}</td>
                            <td>
                                @if($member->status === 'active')
                                    <span class="badge bg-success">Aktif</span>
                                @elseif($member->status === 'inactive')
                                    <span class="badge bg-secondary">Tidak Aktif</span>
                                @elseif($member->status === 'graduated')
                                    <span class="badge bg-primary">Lulus</span>
                                @else
                                    <span class="badge bg-warning">Mengundurkan Diri</span>
                                @endif
                            </td>
                            <td class="text-nowrap">{{ $member->joined_at->format('d/m/Y') }}</td>
                            <td>
                                <div class="table-actions">
                                    <a href="{{ route('pks-members.show', $member) }}" class="btn btn-sm btn-outline-primary" title="Lihat">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    @if(auth()->user()->isSuperAdmin() || auth()->user()->isSchoolAdmin())
                                        <a href="{{ route('pks-members.edit', $member) }}" class="btn btn-sm btn-outline-secondary" title="Edit">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7">
                                <div class="empty-state">
                                    <div class="empty-state-icon">
                                        <i class="bi bi-shield-check"></i>
                                    </div>
                                    <div class="empty-state-title">Belum ada anggota PKS</div>
                                    <div class="empty-state-text">Tambahkan anggota PKS untuk mulai mengelola piket.</div>
                                    @if(auth()->user()->isSuperAdmin() || auth()->user()->isSchoolAdmin())
                                        <a href="{{ route('pks-members.create') }}" class="btn btn-primary btn-sm">
                                            <i class="bi bi-person-plus me-1"></i>Tambah Anggota
                                        </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($pksMembers->hasPages())
            <div class="card-footer">
                {{ $pksMembers->links() }}
            </div>
        @endif
    </div>
</x-app-layout>
