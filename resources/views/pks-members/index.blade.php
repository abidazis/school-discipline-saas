<x-app-layout>
    <x-slot name="title">Anggota PKS</x-slot>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h4 mb-0"><i class="bi bi-people me-2"></i>Anggota PKS</h1>
        @if(auth()->user()->isSuperAdmin() || auth()->user()->isSchoolAdmin())
            <a href="{{ route('pks-members.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-lg me-1"></i>Tambah Anggota
            </a>
        @endif
    </div>

    <!-- Filters -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" class="row g-3">
                <div class="col-12 col-md-3">
                    <div class="input-group">
                        <span class="input-group-text bg-white"><i class="bi bi-search"></i></span>
                        <input type="text" class="form-control" name="search"
                               placeholder="NIS atau nama..." value="{{ request('search') }}">
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
                    <button type="submit" class="btn btn-outline-primary me-2">Filter</button>
                    <a href="{{ route('pks-members.index') }}" class="btn btn-outline-secondary">Reset</a>
                </div>
            </form>
        </div>
    </div>

    <!-- List -->
    <div class="card border-0 shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
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
                            <td class="small">{{ $member->joined_at->format('d/m/Y') }}</td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('pks-members.show', $member) }}" class="btn btn-outline-primary"><i class="bi bi-eye"></i></a>
                                    @if(auth()->user()->isSuperAdmin() || auth()->user()->isSchoolAdmin())
                                        <a href="{{ route('pks-members.edit', $member) }}" class="btn btn-outline-secondary"><i class="bi bi-pencil"></i></a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-4 text-muted">
                                <i class="bi bi-people fs-1 d-block mb-2"></i>
                                Belum ada anggota PKS.
                                @if(auth()->user()->isSuperAdmin() || auth()->user()->isSchoolAdmin())
                                    <a href="{{ route('pks-members.create') }}" class="text-decoration-none">Tambah Anggota</a>
                                @endif
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($pksMembers->hasPages())
            <div class="card-footer bg-white">{{ $pksMembers->links() }}</div>
        @endif
    </div>
</x-app-layout>
