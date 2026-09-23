<x-app-layout>
    <x-slot name="title">Tambah Siswa</x-slot>

    <!-- Breadcrumb -->
    <nav class="breadcrumb-nav mb-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('students.index') }}">Siswa</a></li>
            <li class="breadcrumb-item active">Tambah</li>
        </ol>
    </nav>

    <!-- Form Card -->
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0"><i class="bi bi-mortarboard me-2"></i>Tambah Siswa</h5>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('students.store') }}">
                @csrf

                <div class="form-grid">
                    <!-- NIS -->
                    <div class="form-group">
                        <label for="nis" class="form-label">NIS <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('nis') is-invalid @enderror"
                               id="nis" name="nis" value="{{ old('nis') }}"
                               required placeholder="12345">
                        @error('nis')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- NISN -->
                    <div class="form-group">
                        <label for="nisn" class="form-label">NISN</label>
                        <input type="text" class="form-control @error('nisn') is-invalid @enderror"
                               id="nisn" name="nisn" value="{{ old('nisn') }}"
                               placeholder="1234567890">
                        @error('nisn')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Gender -->
                    <div class="form-group">
                        <label for="gender" class="form-label">Jenis Kelamin <span class="text-danger">*</span></label>
                        <select class="form-select @error('gender') is-invalid @enderror"
                                id="gender" name="gender" required>
                            <option value="">Pilih</option>
                            <option value="male" {{ old('gender') === 'male' ? 'selected' : '' }}>Laki-laki</option>
                            <option value="female" {{ old('gender') === 'female' ? 'selected' : '' }}>Perempuan</option>
                        </select>
                        @error('gender')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- Full Name -->
                <div class="form-group">
                    <label for="full_name" class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                    <input type="text" class="form-control @error('full_name') is-invalid @enderror"
                           id="full_name" name="full_name" value="{{ old('full_name') }}"
                           required placeholder="Masukkan nama lengkap">
                    @error('full_name')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-grid form-grid-3">
                    <!-- Academic Year -->
                    <div class="form-group">
                        <label for="academic_year_id" class="form-label">Tahun Ajaran <span class="text-danger">*</span></label>
                        <select class="form-select @error('academic_year_id') is-invalid @enderror"
                                id="academic_year_id" name="academic_year_id" required>
                            <option value="">Pilih Tahun Ajaran</option>
                            @foreach($academicYears as $year)
                                <option value="{{ $year->id }}" {{ old('academic_year_id', $selectedAcademicYear?->id) == $year->id ? 'selected' : '' }}>
                                    {{ $year->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('academic_year_id')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Class -->
                    <div class="form-group">
                        <label for="school_class_id" class="form-label">Kelas <span class="text-danger">*</span></label>
                        <select class="form-select @error('school_class_id') is-invalid @enderror"
                                id="school_class_id" name="school_class_id" required>
                            <option value="">Pilih Kelas</option>
                            @foreach($classes as $class)
                                <option value="{{ $class->id }}" {{ old('school_class_id') == $class->id ? 'selected' : '' }}>
                                    {{ $class->full_name }}
                                </option>
                            @endforeach
                        </select>
                        @error('school_class_id')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Status -->
                    <div class="form-group">
                        <label for="status" class="form-label">Status</label>
                        <select class="form-select @error('status') is-invalid @enderror"
                                id="status" name="status">
                            <option value="active" {{ old('status', 'active') === 'active' ? 'selected' : '' }}>Aktif</option>
                            <option value="inactive" {{ old('status') === 'inactive' ? 'selected' : '' }}>Tidak Aktif</option>
                            <option value="graduated" {{ old('status') === 'graduated' ? 'selected' : '' }}>Lulus</option>
                            <option value="transferred" {{ old('status') === 'transferred' ? 'selected' : '' }}>Pindah</option>
                        </select>
                        @error('status')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <hr class="my-4">

                <h6 class="mb-3">Data Tambahan</h6>

                <div class="form-grid">
                    <!-- Birth Place -->
                    <div class="form-group">
                        <label for="birth_place" class="form-label">Tempat Lahir</label>
                        <input type="text" class="form-control @error('birth_place') is-invalid @enderror"
                               id="birth_place" name="birth_place" value="{{ old('birth_place') }}"
                               placeholder="Jakarta">
                        @error('birth_place')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Birth Date -->
                    <div class="form-group">
                        <label for="birth_date" class="form-label">Tanggal Lahir</label>
                        <input type="date" class="form-control @error('birth_date') is-invalid @enderror"
                               id="birth_date" name="birth_date" value="{{ old('birth_date') }}">
                        @error('birth_date')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- Address -->
                <div class="form-group">
                    <label for="address" class="form-label">Alamat</label>
                    <textarea class="form-control @error('address') is-invalid @enderror"
                              id="address" name="address" rows="2"
                              placeholder="Masukkan alamat">{{ old('address') }}</textarea>
                    @error('address')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Phone -->
                <div class="form-group">
                    <label for="phone" class="form-label">Nomor Telepon</label>
                    <input type="text" class="form-control @error('phone') is-invalid @enderror"
                           id="phone" name="phone" value="{{ old('phone') }}"
                           placeholder="081234567890">
                    @error('phone')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-lg"></i>Simpan
                    </button>
                    <a href="{{ route('students.index') }}" class="btn btn-outline-secondary">
                        Batal
                    </a>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>

<style>
    /* Breadcrumb */
    .breadcrumb-nav {
        font-size: 14px;
    }

    .breadcrumb {
        display: flex;
        align-items: center;
        gap: 8px;
        list-style: none;
        padding: 0;
        margin: 0;
        flex-wrap: wrap;
    }

    .breadcrumb-item {
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .breadcrumb-item::before {
        content: '/';
        color: var(--gray-400);
    }

    .breadcrumb-item:first-child::before {
        content: '';
    }

    .breadcrumb-item a {
        color: var(--primary);
        text-decoration: none;
    }

    .breadcrumb-item a:hover {
        text-decoration: underline;
    }

    .breadcrumb-item.active {
        color: var(--gray-500);
    }

    /* Form Grid */
    .form-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 16px;
        margin-bottom: 16px;
    }

    .form-grid-3 {
        grid-template-columns: repeat(3, 1fr);
    }

    @media (max-width: 768px) {
        .form-grid,
        .form-grid-3 {
            grid-template-columns: 1fr;
        }
    }

    /* Form Actions */
    .form-actions {
        display: flex;
        gap: 12px;
        flex-wrap: wrap;
        margin-top: 24px;
    }

    @media (max-width: 640px) {
        .form-actions {
            flex-direction: column;
        }

        .form-actions .btn {
            width: 100%;
            justify-content: center;
        }
    }
</style>
