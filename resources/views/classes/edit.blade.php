<x-app-layout>
    <x-slot name="title">Edit Kelas</x-slot>

    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('classes.index') }}">Kelas</a></li>
            <li class="breadcrumb-item"><a href="{{ route('classes.show', $schoolClass) }}">{{ $schoolClass->full_name }}</a></li>
            <li class="breadcrumb-item active">Edit</li>
        </ol>
    </nav>

    <!-- Page Header -->
    <div class="page-header">
        <h1 class="page-title">
            <div class="page-title-icon">
                <i class="bi bi-chalkboard"></i>
            </div>
            Edit Kelas
        </h1>
    </div>

    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <div class="card">
                <div class="card-header">
                    <div class="card-title">
                        <i class="bi bi-chalkboard text-primary"></i>
                        Form Edit Kelas
                    </div>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('classes.update', $schoolClass) }}">
                        @csrf
                        @method('PATCH')

                        <div class="mb-3">
                            <label for="academic_year_id" class="form-label">Tahun Ajaran <span class="text-danger">*</span></label>
                            <select class="form-select @error('academic_year_id') is-invalid @enderror"
                                    id="academic_year_id" name="academic_year_id" required>
                                <option value="">Pilih Tahun Ajaran</option>
                                @foreach($academicYears as $year)
                                    <option value="{{ $year->id }}" {{ old('academic_year_id', $schoolClass->academic_year_id) == $year->id ? 'selected' : '' }}>
                                        {{ $year->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('academic_year_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="department_id" class="form-label">Program Keahlian</label>
                                <select class="form-select @error('department_id') is-invalid @enderror"
                                        id="department_id" name="department_id">
                                    <option value="">Tidak ada (Umum)</option>
                                    @foreach($departments as $dept)
                                        <option value="{{ $dept->id }}" {{ old('department_id', $schoolClass->department_id) == $dept->id ? 'selected' : '' }}>
                                            {{ $dept->code }} - {{ $dept->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('department_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="grade_level" class="form-label">Tingkat <span class="text-danger">*</span></label>
                                <select class="form-select @error('grade_level') is-invalid @enderror"
                                        id="grade_level" name="grade_level" required>
                                    <option value="">Pilih Tingkat</option>
                                    <option value="VII" {{ old('grade_level', $schoolClass->grade_level) === 'VII' ? 'selected' : '' }}>VII</option>
                                    <option value="VIII" {{ old('grade_level', $schoolClass->grade_level) === 'VIII' ? 'selected' : '' }}>VIII</option>
                                    <option value="IX" {{ old('grade_level', $schoolClass->grade_level) === 'IX' ? 'selected' : '' }}>IX</option>
                                    <option value="X" {{ old('grade_level', $schoolClass->grade_level) === 'X' ? 'selected' : '' }}>X</option>
                                    <option value="XI" {{ old('grade_level', $schoolClass->grade_level) === 'XI' ? 'selected' : '' }}>XI</option>
                                    <option value="XII" {{ old('grade_level', $schoolClass->grade_level) === 'XII' ? 'selected' : '' }}>XII</option>
                                </select>
                                @error('grade_level')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="name" class="form-label">Nama Kelas <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror"
                                   id="name" name="name" value="{{ old('name', $schoolClass->name) }}"
                                   required placeholder="1">
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" id="is_active" name="is_active"
                                       value="1" {{ old('is_active', $schoolClass->is_active) ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_active">
                                    Aktif
                                </label>
                            </div>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-lg me-1"></i>Simpan Perubahan
                            </button>
                            <a href="{{ route('classes.show', $schoolClass) }}" class="btn btn-outline-secondary">
                                Batal
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
