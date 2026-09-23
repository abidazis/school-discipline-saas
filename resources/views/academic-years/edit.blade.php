<x-app-layout>
    <x-slot name="title">Edit Tahun Ajaran</x-slot>

    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('academic-years.index') }}">Tahun Ajaran</a></li>
            <li class="breadcrumb-item"><a href="{{ route('academic-years.show', $academicYear) }}">{{ $academicYear->name }}</a></li>
            <li class="breadcrumb-item active">Edit</li>
        </ol>
    </nav>

    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="bi bi-calendar3 me-2"></i>Edit Tahun Ajaran</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('academic-years.update', $academicYear) }}">
                        @csrf
                        @method('PATCH')

                        <div class="mb-3">
                            <label for="name" class="form-label">Nama Tahun Ajaran <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror"
                                   id="name" name="name" value="{{ old('name', $academicYear->name) }}"
                                   required autofocus placeholder="2026/2027">
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Format: TAHUN/TAHUN+1 (contoh: 2026/2027)</small>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="start_date" class="form-label">Tanggal Mulai <span class="text-danger">*</span></label>
                                <input type="date" class="form-control @error('start_date') is-invalid @enderror"
                                       id="start_date" name="start_date" value="{{ old('start_date', $academicYear->start_date->format('Y-m-d')) }}"
                                       required>
                                @error('start_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="end_date" class="form-label">Tanggal Selesai <span class="text-danger">*</span></label>
                                <input type="date" class="form-control @error('end_date') is-invalid @enderror"
                                       id="end_date" name="end_date" value="{{ old('end_date', $academicYear->end_date->format('Y-m-d')) }}"
                                       required>
                                @error('end_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-4">
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" id="is_active" name="is_active"
                                       value="1" {{ old('is_active', $academicYear->is_active) ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_active">
                                    Jadikan tahun ajaran aktif
                                </label>
                            </div>
                            <small class="text-muted">Hanya satu tahun ajaran yang dapat aktif pada satu waktu. Tahun ajaran lain akan dinonaktifkan.</small>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-lg me-1"></i>Simpan Perubahan
                            </button>
                            <a href="{{ route('academic-years.show', $academicYear) }}" class="btn btn-outline-secondary">
                                Batal
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
