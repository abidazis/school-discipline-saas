<x-app-layout>
    <x-slot name="title">Tambah Anggota PKS</x-slot>

    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('pks-members.index') }}">Anggota PKS</a></li>
            <li class="breadcrumb-item active">Tambah</li>
        </ol>
    </nav>

    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="bi bi-person-plus me-2"></i>Tambah Anggota PKS</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('pks-members.store') }}">
                        @csrf

                        <div class="mb-3">
                            <label for="student_id" class="form-label">Siswa <span class="text-danger">*</span></label>
                            <select class="form-select @error('student_id') is-invalid @enderror"
                                    id="student_id" name="student_id" required>
                                <option value="">Pilih Siswa...</option>
                                @foreach(\App\Models\Student::active()
                                    ->when(auth()->user()->school_id, fn($q) => $q->where('school_id', auth()->user()->school_id))
                                    ->with('schoolClass.department')
                                    ->orderBy('full_name')
                                    ->get() as $student)
                                    <option value="{{ $student->id }}"
                                            {{ old('student_id') == $student->id ? 'selected' : '' }}
                                            data-class="{{ $student->schoolClass?->full_name }}"
                                            data-nis="{{ $student->nis }}">
                                        {{ $student->full_name }} ({{ $student->nis }}) - {{ $student->schoolClass?->full_name ?? 'Tanpa Kelas' }}
                                    </option>
                                @endforeach
                            </select>
                            @error('student_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="position" class="form-label">Jabatan <span class="text-danger">*</span></label>
                                <select class="form-select @error('position') is-invalid @enderror"
                                        id="position" name="position" required>
                                    <option value="">Pilih Jabatan...</option>
                                    @foreach($positions as $position)
                                        <option value="{{ $position }}" {{ old('position') === $position ? 'selected' : '' }}>
                                            {{ $position }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('position')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="status" class="form-label">Status</label>
                                <select class="form-select @error('status') is-invalid @enderror"
                                        id="status" name="status">
                                    <option value="active" {{ old('status', 'active') === 'active' ? 'selected' : '' }}>Aktif</option>
                                    <option value="inactive" {{ old('status') === 'inactive' ? 'selected' : '' }}>Tidak Aktif</option>
                                    <option value="graduated" {{ old('status') === 'graduated' ? 'selected' : '' }}>Lulus</option>
                                    <option value="resigned" {{ old('status') === 'resigned' ? 'selected' : '' }}>Mengundurkan Diri</option>
                                </select>
                                @error('status')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="joined_at" class="form-label">Tanggal Bergabung <span class="text-danger">*</span></label>
                                <input type="date" class="form-control @error('joined_at') is-invalid @enderror"
                                       id="joined_at" name="joined_at"
                                       value="{{ old('joined_at', now()->format('Y-m-d')) }}" required>
                                @error('joined_at')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="ended_at" class="form-label">Tanggal Berakhir</label>
                                <input type="date" class="form-control @error('ended_at') is-invalid @enderror"
                                       id="ended_at" name="ended_at" value="{{ old('ended_at') }}">
                                @error('ended_at')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-4">
                            <label for="notes" class="form-label">Catatan</label>
                            <textarea class="form-control @error('notes') is-invalid @enderror"
                                      id="notes" name="notes" rows="3"
                                      placeholder="Catatan tambahan...">{{ old('notes') }}</textarea>
                            @error('notes')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg me-1"></i>Simpan</button>
                            <a href="{{ route('pks-members.index') }}" class="btn btn-outline-secondary">Batal</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
