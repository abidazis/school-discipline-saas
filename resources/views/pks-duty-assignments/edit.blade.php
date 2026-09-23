<x-app-layout>
    <x-slot name="title">Edit Penugasan Piket</x-slot>

    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('pks-duty-assignments.index') }}">Penugasan Piket</a></li>
            <li class="breadcrumb-item"><a href="{{ route('pks-duty-assignments.show', $pksDutyAssignment) }}">Detail</a></li>
            <li class="breadcrumb-item active">Edit</li>
        </ol>
    </nav>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h4 mb-0">Edit Penugasan Piket</h1>
    </div>

    <div class="row justify-content-center">
        <div class="col-12 col-lg-8 col-xl-6">
            <div class="card">
                <div class="card-body p-4">
                    <div class="alert alert-info mb-4">
                        <i class="bi bi-info-circle me-2"></i>
                        <strong>Informasi:</strong> Perubahan jadwal piket dan anggota PKS tidak diizinkan untuk menjaga integritas data.
                        Anda hanya dapat mengubah status penugasan.
                    </div>

                    <form method="POST" action="{{ route('pks-duty-assignments.update', $pksDutyAssignment) }}">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label class="form-label">Jadwal Piket</label>
                            <input type="text" class="form-control" value="{{ $pksDutyAssignment->schedule?->schedule_date?->format('d/m/Y') }} - {{ $pksDutyAssignment->schedule?->shift?->name ?? '-' }}" readonly>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Lokasi Piket</label>
                            <input type="text" class="form-control" value="{{ $pksDutyAssignment->location?->name ?? '-' }} ({{ $pksDutyAssignment->location?->code ?? '-' }})" readonly>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Anggota PKS</label>
                            <input type="text" class="form-control" value="{{ $pksDutyAssignment->member?->student?->name ?? '-' }} - {{ $pksDutyAssignment->member?->student?->nis ?? '-' }} - {{ $pksDutyAssignment->member?->position ?? '-' }}" readonly>
                        </div>

                        <div class="mb-3">
                            <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                            <select name="status"
                                    id="status"
                                    class="form-select @error('status') is-invalid @enderror"
                                    required>
                                <option value="assigned" {{ old('status', $pksDutyAssignment->status) == 'assigned' ? 'selected' : '' }}>
                                    Ditugaskan
                                </option>
                                <option value="replaced" {{ old('status', $pksDutyAssignment->status) == 'replaced' ? 'selected' : '' }}>
                                    Digantikan
                                </option>
                                <option value="cancelled" {{ old('status', $pksDutyAssignment->status) == 'cancelled' ? 'selected' : '' }}>
                                    Dibatalkan
                                </option>
                            </select>
                            @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="notes" class="form-label">Catatan</label>
                            <textarea name="notes"
                                      id="notes"
                                      class="form-control @error('notes') is-invalid @enderror"
                                      rows="3"
                                      placeholder="Catatan opsional...">{{ old('notes', $pksDutyAssignment->notes) }}</textarea>
                            @error('notes')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('pks-duty-assignments.show', $pksDutyAssignment) }}" class="btn btn-outline-secondary">
                                <i class="bi bi-arrow-left me-1"></i>Batal
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-lg me-1"></i>Simpan Perubahan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
