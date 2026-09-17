<x-app-layout>
    <x-slot name="title">Edit Siswa</x-slot>

    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('students.index') }}">Siswa</a></li>
            <li class="breadcrumb-item"><a href="{{ route('students.show', $student) }}">{{ $student->full_name }}</a></li>
            <li class="breadcrumb-item active">Edit</li>
        </ol>
    </nav>

    <div class="row justify-content-center">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom">
                    <h5 class="mb-0"><i class="bi bi-mortarboard me-2"></i>Edit Siswa</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('students.update', $student) }}">
                        @csrf
                        @method('PATCH')

                        <div class="row">
                            <!-- NIS -->
                            <div class="col-md-4 mb-3">
                                <label for="nis" class="form-label">NIS <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('nis') is-invalid @enderror"
                                       id="nis" name="nis" value="{{ old('nis', $student->nis) }}"
                                       required placeholder="12345">
                                @error('nis')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- NISN -->
                            <div class="col-md-4 mb-3">
                                <label for="nisn" class="form-label">NISN</label>
                                <input type="text" class="form-control @error('nisn') is-invalid @enderror"
                                       id="nisn" name="nisn" value="{{ old('nisn', $student->nisn) }}"
                                       placeholder="1234567890">
                                @error('nisn')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Gender -->
                            <div class="col-md-4 mb-3">
                                <label for="gender" class="form-label">Jenis Kelamin <span class="text-danger">*</span></label>
                                <select class="form-select @error('gender') is-invalid @enderror"
                                        id="gender" name="gender" required>
                                    <option value="">Pilih</option>
                                    <option value="male" {{ old('gender', $student->gender) === 'male' ? 'selected' : '' }}>Laki-laki</option>
                                    <option value="female" {{ old('gender', $student->gender) === 'female' ? 'selected' : '' }}>Perempuan</option>
                                </select>
                                @error('gender')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Full Name -->
                        <div class="row">
                            <div class="col-12 mb-3">
                                <label for="full_name" class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('full_name') is-invalid @enderror"
                                       id="full_name" name="full_name" value="{{ old('full_name', $student->full_name) }}"
                                       required placeholder="Masukkan nama lengkap">
                                @error('full_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <!-- Academic Year -->
                            <div class="col-md-4 mb-3">
                                <label for="academic_year_id" class="form-label">Tahun Ajaran <span class="text-danger">*</span></label>
                                <select class="form-select @error('academic_year_id') is-invalid @enderror"
                                        id="academic_year_id" name="academic_year_id" required>
                                    <option value="">Pilih Tahun Ajaran</option>
                                    @foreach($academicYears as $year)
                                        <option value="{{ $year->id }}" {{ old('academic_year_id', $student->academic_year_id) == $year->id ? 'selected' : '' }}>
                                            {{ $year->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('academic_year_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Class -->
                            <div class="col-md-4 mb-3">
                                <label for="school_class_id" class="form-label">Kelas <span class="text-danger">*</span></label>
                                <select class="form-select @error('school_class_id') is-invalid @enderror"
                                        id="school_class_id" name="school_class_id" required>
                                    <option value="">Pilih Kelas</option>
                                    @foreach($classes as $class)
                                        <option value="{{ $class->id }}" {{ old('school_class_id', $student->school_class_id) == $class->id ? 'selected' : '' }}>
                                            {{ $class->full_name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('school_class_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Status -->
                            <div class="col-md-4 mb-3">
                                <label for="status" class="form-label">Status</label>
                                <select class="form-select @error('status') is-invalid @enderror"
                                        id="status" name="status">
                                    <option value="active" {{ old('status', $student->status) === 'active' ? 'selected' : '' }}>Aktif</option>
                                    <option value="inactive" {{ old('status', $student->status) === 'inactive' ? 'selected' : '' }}>Tidak Aktif</option>
                                    <option value="graduated" {{ old('status', $student->status) === 'graduated' ? 'selected' : '' }}>Lulus</option>
                                    <option value="transferred" {{ old('status', $student->status) === 'transferred' ? 'selected' : '' }}>Pindah</option>
                                </select>
                                @error('status')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <hr class="my-4">

                        <h6 class="mb-3">Data Tambahan</h6>

                        <div class="row">
                            <!-- Birth Place -->
                            <div class="col-md-6 mb-3">
                                <label for="birth_place" class="form-label">Tempat Lahir</label>
                                <input type="text" class="form-control @error('birth_place') is-invalid @enderror"
                                       id="birth_place" name="birth_place" value="{{ old('birth_place', $student->birth_place) }}"
                                       placeholder="Jakarta">
                                @error('birth_place')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Birth Date -->
                            <div class="col-md-6 mb-3">
                                <label for="birth_date" class="form-label">Tanggal Lahir</label>
                                <input type="date" class="form-control @error('birth_date') is-invalid @enderror"
                                       id="birth_date" name="birth_date" value="{{ old('birth_date', $student->birth_date?->format('Y-m-d')) }}">
                                @error('birth_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Address -->
                        <div class="mb-3">
                            <label for="address" class="form-label">Alamat</label>
                            <textarea class="form-control @error('address') is-invalid @enderror"
                                      id="address" name="address" rows="2"
                                      placeholder="Masukkan alamat">{{ old('address', $student->address) }}</textarea>
                            @error('address')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Phone -->
                        <div class="mb-4">
                            <label for="phone" class="form-label">Nomor Telepon</label>
                            <input type="text" class="form-control @error('phone') is-invalid @enderror"
                                   id="phone" name="phone" value="{{ old('phone', $student->phone) }}"
                                   placeholder="081234567890">
                            @error('phone')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-lg me-1"></i>Simpan Perubahan
                            </button>
                            <a href="{{ route('students.show', $student) }}" class="btn btn-outline-secondary">
                                Batal
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
