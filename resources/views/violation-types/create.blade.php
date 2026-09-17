<x-app-layout>
    <x-slot name="title">Tambah Jenis Pelanggaran</x-slot>

    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('violation-types.index') }}">Jenis Pelanggaran</a></li>
            <li class="breadcrumb-item active">Tambah</li>
        </ol>
    </nav>

    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom">
                    <h5 class="mb-0"><i class="bi bi-exclamation-triangle me-2"></i>Tambah Jenis Pelanggaran</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('violation-types.store') }}">
                        @csrf

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="code" class="form-label">Kode <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('code') is-invalid @enderror"
                                       id="code" name="code" value="{{ old('code') }}" required placeholder="LATE">
                                @error('code')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="points" class="form-label">Point <span class="text-danger">*</span></label>
                                <input type="number" class="form-control @error('points') is-invalid @enderror"
                                       id="points" name="points" value="{{ old('points', 1) }}" required min="0" max="100">
                                @error('points')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="name" class="form-label">Nama Pelanggaran <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror"
                                   id="name" name="name" value="{{ old('name') }}" required placeholder="Terlambat Masuk Sekolah">
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="category" class="form-label">Kategori <span class="text-danger">*</span></label>
                                <select class="form-select @error('category') is-invalid @enderror" id="category" name="category" required>
                                    <option value="">Pilih Kategori</option>
                                    <option value="Attendance" {{ old('category') === 'Attendance' ? 'selected' : '' }}>Attendance</option>
                                    <option value="Uniform" {{ old('category') === 'Uniform' ? 'selected' : '' }}>Uniform</option>
                                    <option value="Behavior" {{ old('category') === 'Behavior' ? 'selected' : '' }}>Behavior</option>
                                    <option value="Safety" {{ old('category') === 'Safety' ? 'selected' : '' }}>Safety</option>
                                    <option value="Academic" {{ old('category') === 'Academic' ? 'selected' : '' }}>Academic</option>
                                    <option value="Technology" {{ old('category') === 'Technology' ? 'selected' : '' }}>Technology</option>
                                    <option value="Leaving School" {{ old('category') === 'Leaving School' ? 'selected' : '' }}>Leaving School</option>
                                    <option value="Other" {{ old('category') === 'Other' ? 'selected' : '' }}>Other</option>
                                </select>
                                @error('category')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="severity" class="form-label">Tingkat <span class="text-danger">*</span></label>
                                <select class="form-select @error('severity') is-invalid @enderror" id="severity" name="severity" required>
                                    <option value="">Pilih Tingkat</option>
                                    <option value="low" {{ old('severity') === 'low' ? 'selected' : '' }}>Ringan (low)</option>
                                    <option value="medium" {{ old('severity') === 'medium' ? 'selected' : '' }}>Sedang (medium)</option>
                                    <option value="high" {{ old('severity') === 'high' ? 'selected' : '' }}>Berat (high)</option>
                                    <option value="critical" {{ old('severity') === 'critical' ? 'selected' : '' }}>Sangat Berat (critical)</option>
                                </select>
                                @error('severity')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-4">
                            <label for="description" class="form-label">Deskripsi</label>
                            <textarea class="form-control @error('description') is-invalid @enderror"
                                      id="description" name="description" rows="3" placeholder="Deskripsi opsional">{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" id="is_active" name="is_active"
                                       value="1" {{ old('is_active', true) ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_active">Aktif</label>
                            </div>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg me-1"></i>Simpan</button>
                            <a href="{{ route('violation-types.index') }}" class="btn btn-outline-secondary">Batal</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>