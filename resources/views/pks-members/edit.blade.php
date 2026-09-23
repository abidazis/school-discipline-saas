<x-app-layout>
    <x-slot name="title">Edit Anggota PKS</x-slot>

    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('pks-members.index') }}">Anggota PKS</a></li>
            <li class="breadcrumb-item"><a href="{{ route('pks-members.show', $pksMember) }}">{{ $pksMember->student->full_name }}</a></li>
            <li class="breadcrumb-item active">Edit</li>
        </ol>
    </nav>

    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="bi bi-pencil me-2"></i>Edit Anggota PKS</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('pks-members.update', $pksMember) }}">
                        @csrf
                        @method('PATCH')

                        <div class="alert alert-info">
                            <i class="bi bi-info-circle me-2"></i>
                            <strong>{{ $pksMember->student->full_name }}</strong> ({{ $pksMember->student->nis }})
                            - {{ $pksMember->student->schoolClass?->full_name ?? 'Tanpa Kelas' }}
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="position" class="form-label">Jabatan <span class="text-danger">*</span></label>
                                <select class="form-select @error('position') is-invalid @enderror"
                                        id="position" name="position" required>
                                    <option value="">Pilih Jabatan...</option>
                                    @foreach($positions as $position)
                                        <option value="{{ $position }}" {{ old('position', $pksMember->position) === $position ? 'selected' : '' }}>
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
                                    <option value="active" {{ old('status', $pksMember->status) === 'active' ? 'selected' : '' }}>Aktif</option>
                                    <option value="inactive" {{ old('status', $pksMember->status) === 'inactive' ? 'selected' : '' }}>Tidak Aktif</option>
                                    <option value="graduated" {{ old('status', $pksMember->status) === 'graduated' ? 'selected' : '' }}>Lulus</option>
                                    <option value="resigned" {{ old('status', $pksMember->status) === 'resigned' ? 'selected' : '' }}>Mengundurkan Diri</option>
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
                                       value="{{ old('joined_at', $pksMember->joined_at->format('Y-m-d')) }}" required>
                                @error('joined_at')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="ended_at" class="form-label">Tanggal Berakhir</label>
                                <input type="date" class="form-control @error('ended_at') is-invalid @enderror"
                                       id="ended_at" name="ended_at" value="{{ old('ended_at', $pksMember->ended_at?->format('Y-m-d')) }}">
                                @error('ended_at')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">Kosongkan jika status masih aktif.</div>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label for="notes" class="form-label">Catatan</label>
                            <textarea class="form-control @error('notes') is-invalid @enderror"
                                      id="notes" name="notes" rows="3"
                                      placeholder="Catatan tambahan...">{{ old('notes', $pksMember->notes) }}</textarea>
                            @error('notes')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg me-1"></i>Simpan</button>
                            <a href="{{ route('pks-members.show', $pksMember) }}" class="btn btn-outline-secondary">Batal</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
