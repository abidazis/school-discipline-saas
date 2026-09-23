<x-app-layout>
    <x-slot name="title>Edit {{ $violationType->code }}</x-slot>

    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('violation-types.index') }}">Jenis Pelanggaran</a></li>
            <li class="breadcrumb-item"><a href="{{ route('violation-types.show', $violationType) }}">{{ $violationType->code }}</a></li>
            <li class="breadcrumb-item active">Edit</li>
        </ol>
    </nav>

    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <div class="card">
                <div class="card-header"><h5 class="mb-0"><i class="bi bi-pencil me-2"></i>Edit Jenis Pelanggaran</h5></div>
                <div class="card-body">
                    <form method="POST" action="{{ route('violation-types.update', $violationType) }}">
                        @csrf @method('PATCH')
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="code" class="form-label">Kode <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('code') is-invalid @enderror" id="code" name="code" value="{{ old('code', $violationType->code) }}" required>
                                @error('code')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="points" class="form-label">Point <span class="text-danger">*</span></label>
                                <input type="number" class="form-control @error('points') is-invalid @enderror" id="points" name="points" value="{{ old('points', $violationType->points) }}" required min="0" max="100">
                                @error('points')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="name" class="form-label">Nama <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $violationType->name) }}" required>
                            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="category" class="form-label">Kategori <span class="text-danger">*</span></label>
                                <select class="form-select @error('category') is-invalid @enderror" id="category" name="category" required>
                                    @foreach(['Attendance','Uniform','Behavior','Safety','Academic','Technology','Leaving School','Other'] as $cat)
                                        <option value="{{ $cat }}" {{ old('category', $violationType->category) === $cat ? 'selected' : '' }}>{{ $cat }}</option>
                                    @endforeach
                                </select>
                                @error('category')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="severity" class="form-label">Tingkat <span class="text-danger">*</span></label>
                                <select class="form-select @error('severity') is-invalid @enderror" id="severity" name="severity" required>
                                    @foreach(['low','medium','high','critical'] as $sev)
                                        <option value="{{ $sev }}" {{ old('severity', $violationType->severity) === $sev ? 'selected' : '' }}>{{ ucfirst($sev) }}</option>
                                    @endforeach
                                </select>
                                @error('severity')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="description" class="form-label">Deskripsi</label>
                            <textarea class="form-control" id="description" name="description" rows="3">{{ old('description', $violationType->description) }}</textarea>
                        </div>
                        <div class="mb-4">
                            <div class="form-check"><input type="checkbox" class="form-check-input" id="is_active" name="is_active" value="1" {{ old('is_active', $violationType->is_active) ? 'checked' : '' }}><label class="form-check-label" for="is_active">Aktif</label></div>
                        </div>
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg me-1"></i>Simpan</button>
                            <a href="{{ route('violation-types.show', $violationType) }}" class="btn btn-outline-secondary">Batal</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>