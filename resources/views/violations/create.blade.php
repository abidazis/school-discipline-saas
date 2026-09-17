<x-app-layout>
    <x-slot name="title>Catat Pelanggaran</x-slot>

    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('violations.index') }}">Pelanggaran</a></li>
            <li class="breadcrumb-item active">Catat</li>
        </ol>
    </nav>

    <div class="row justify-content-center">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom"><h5 class="mb-0"><i class="bi bi-plus-circle me-2"></i>Catat Pelanggaran Baru</h5></div>
                <div class="card-body">
                    <form method="POST" action="{{ route('violations.store') }}" enctype="multipart/form-data">
                        @csrf
                        <div class="row">
                            <div class="col-12 col-md-6 mb-3">
                                <label for="student_id" class="form-label">Siswa <span class="text-danger">*</span></label>
                                <select class="form-select @error('student_id') is-invalid @enderror select2-student" id="student_id" name="student_id" required>
                                    <option value="">Ketik NIS atau nama...</option>
                                </select>
                                @error('student_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                <div id="studentInfo" class="small text-muted mt-1"></div>
                            </div>
                            <div class="col-12 col-md-6 mb-3">
                                <label for="violation_type_id" class="form-label">Jenis Pelanggaran <span class="text-danger">*</span></label>
                                <select class="form-select @error('violation_type_id') is-invalid @enderror" id="violation_type_id" name="violation_type_id" required onchange="updatePoints()">
                                    <option value="">Pilih pelanggaran...</option>
                                    @foreach($violationTypes->groupBy('category') as $category => $types)
                                        <optgroup label="{{ $category }}">
                                            @foreach($types as $type)
                                                <option value="{{ $type->id }}" data-points="{{ $type->points }}" data-severity="{{ $type->severity }}" data-category="{{ $type->category }}">{{ $type->code }} - {{ $type->name }}</option>
                                            @endforeach
                                        </optgroup>
                                    @endforeach
                                </select>
                                @error('violation_type_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12 col-md-4 mb-3">
                                <label for="occurred_at" class="form-label">Tanggal & Waktu <span class="text-danger">*</span></label>
                                <input type="datetime-local" class="form-control @error('occurred_at') is-invalid @enderror" id="occurred_at" name="occurred_at" value="{{ old('occurred_at', now()->format('Y-m-d\TH:i')) }}" required>
                                @error('occurred_at')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-12 col-md-4 mb-3">
                                <label for="location" class="form-label">Lokasi</label>
                                <input type="text" class="form-control" id="location" name="location" value="{{ old('location') }}" placeholder="Gerbang Depan, Kelas X TKJ 1, dll">
                            </div>
                            <div class="col-12 col-md-4 mb-3">
                                <label class="form-label">Point Pelanggaran</label>
                                <div class="form-control-plaintext"><span id="pointsDisplay" class="badge bg-primary fs-6">-</span></div>
                                <input type="hidden" id="points" name="points">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="description" class="form-label">Keterangan</label>
                            <textarea class="form-control" id="description" name="description" rows="2" placeholder="Kronologi singkat...">{{ old('description') }}</textarea>
                        </div>
                        <div class="mb-4">
                            <label for="evidences" class="form-label">Bukti Foto (maks 5MB per file, JPG/PNG/WebP)</label>
                            <input type="file" class="form-control @error('evidences.*') is-invalid @enderror" id="evidences" name="evidences[]" accept="image/jpeg,image/png,image/webp" multiple>
                            @error('evidences.*')<div class="text-danger small">{{ $message }}</div>@enderror
                        </div>
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg me-1"></i>Simpan Pelanggaran</button>
                            <a href="{{ route('violations.index') }}" class="btn btn-outline-secondary">Batal</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
    function updatePoints() {
        const select = document.getElementById('violation_type_id');
        const option = select.options[select.selectedIndex];
        const pointsDisplay = document.getElementById('pointsDisplay');
        const pointsInput = document.getElementById('points');
        if (option && option.dataset.points) {
            pointsDisplay.textContent = option.dataset.points + ' point';
            pointsInput.value = option.dataset.points;
        } else {
            pointsDisplay.textContent = '-';
            pointsInput.value = '';
        }
    }
    </script>
</x-app-layout>