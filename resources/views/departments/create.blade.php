<x-app-layout>
    <x-slot name="title">Tambah Program Keahlian</x-slot>

    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('departments.index') }}">Program Keahlian</a></li>
            <li class="breadcrumb-item active">Tambah</li>
        </ol>
    </nav>

    <div class="row justify-content-center">
        <div class="col-12 col-lg-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="bi bi-book me-2"></i>Tambah Program Keahlian</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('departments.store') }}">
                        @csrf

                        <div class="mb-3">
                            <label for="code" class="form-label">Kode <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('code') is-invalid @enderror"
                                   id="code" name="code" value="{{ old('code') }}"
                                   required autofocus placeholder="TKJ" maxlength="20">
                            @error('code')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Kode singkat untuk program keahlian (maksimal 20 karakter).</small>
                        </div>

                        <div class="mb-3">
                            <label for="name" class="form-label">Nama Program Keahlian <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror"
                                   id="name" name="name" value="{{ old('name') }}"
                                   required placeholder="Teknik Komputer dan Jaringan">
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" id="is_active" name="is_active"
                                       value="1" {{ old('is_active', true) ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_active">
                                    Aktif
                                </label>
                            </div>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-lg me-1"></i>Simpan
                            </button>
                            <a href="{{ route('departments.index') }}" class="btn btn-outline-secondary">
                                Batal
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
