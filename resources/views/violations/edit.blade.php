<x-app-layout>
    <x-slot name="title">Edit Pelanggaran</x-slot>

    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('violations.index') }}">Pelanggaran</a></li>
            <li class="breadcrumb-item"><a href="{{ route('violations.show', $violation) }}">{{ $violation->student->nis }}</a></li>
            <li class="breadcrumb-item active">Edit</li>
        </ol>
    </nav>

    <div class="row justify-content-center">
        <div class="col-12">
            <div class="card">
                <div class="card-header"><h5 class="mb-0"><i class="bi bi-pencil me-2"></i>Edit Pelanggaran</h5></div>
                <div class="card-body">
                    <form method="POST" action="{{ route('violations.update', $violation) }}" enctype="multipart/form-data">
                        @csrf @method('PATCH')
                        <div class="row">
                            <div class="col-12 col-md-6 mb-3">
                                <label class="form-label">Siswa</label>
                                <input type="text" class="form-control" value="{{ $violation->student->full_name }} ({{ $violation->student->nis }})" disabled>
                            </div>
                            <div class="col-12 col-md-6 mb-3">
                                <label class="form-label">Jenis Pelanggaran</label>
                                <input type="text" class="form-control" value="{{ $violation->violationType->code }} - {{ $violation->violationType->name }}" disabled>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12 col-md-4 mb-3">
                                <label for="occurred_at" class="form-label">Tanggal & Waktu</label>
                                <input type="datetime-local" class="form-control @error('occurred_at') is-invalid @enderror" id="occurred_at" name="occurred_at" value="{{ old('occurred_at', $violation->occurred_at->format('Y-m-d\TH:i')) }}">
                                @error('occurred_at')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-12 col-md-4 mb-3">
                                <label for="location" class="form-label">Lokasi</label>
                                <input type="text" class="form-control" id="location" name="location" value="{{ old('location', $violation->location) }}" placeholder="Gerbang Depan, Kelas X TKJ 1, dll">
                            </div>
                            <div class="col-12 col-md-4 mb-3">
                                <label class="form-label">Point Pelanggaran</label>
                                <div class="form-control-plaintext"><span class="badge badge-primary fs-6">{{ $violation->points }} point</span></div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="description" class="form-label">Keterangan</label>
                            <textarea class="form-control" id="description" name="description" rows="2" placeholder="Kronologi singkat...">{{ old('description', $violation->description) }}</textarea>
                        </div>

                        @if($violation->evidences->isNotEmpty())
                            <div class="mb-3">
                                <label class="form-label">Bukti Foto Tersedia</label>
                                <div class="row g-2">
                                    @foreach($violation->evidences as $evidence)
                                        <div class="col-6 col-md-3 col-lg-2">
                                            <div class="position-relative">
                                                <img src="{{ route('evidences.show', $evidence) }}" class="img-thumbnail" alt="Bukti">
                                                <form method="POST" action="{{ route('violations.evidences.destroy', $evidence) }}" class="position-absolute top-0 end-0 m-1">
                                                    @csrf @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Hapus bukti ini?')"><i class="bi bi-trash"></i></button>
                                                </form>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        <div class="mb-4">
                            <label for="evidences" class="form-label">Tambah Bukti Foto (maks 5MB per file, JPG/PNG/WebP)</label>
                            <input type="file" class="form-control @error('evidences.*') is-invalid @enderror" id="evidences" name="evidences[]" accept="image/jpeg,image/png,image/webp" multiple>
                            @error('evidences.*')<div class="text-danger small">{{ $message }}</div>@enderror
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg me-1"></i>Simpan</button>
                            <a href="{{ route('violations.show', $violation) }}" class="btn btn-outline-secondary">Batal</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
