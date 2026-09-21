<x-app-layout>
    <x-slot name="title">Edit Kehadiran</x-slot>

    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('pks-duty-attendances.index') }}">Kehadiran Piket</a></li>
            <li class="breadcrumb-item"><a href="{{ route('pks-duty-attendances.show', $pksDutyAttendance) }}">Detail</a></li>
            <li class="breadcrumb-item active">Edit</li>
        </ol>
    </nav>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h4 mb-0">Edit Kehadiran</h1>
    </div>

    <div class="row justify-content-center">
        <div class="col-12 col-lg-8 col-xl-6">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <div class="alert alert-info mb-4">
                        <div class="d-flex">
                            <i class="bi bi-info-circle me-2 mt-1"></i>
                            <div>
                                <strong>{{ $pksDutyAttendance->assignment?->member?->student?->full_name ?? '-' }}</strong>
                                <br>
                                <small>
                                    NIS: {{ $pksDutyAttendance->assignment?->member?->student?->nis ?? '-' }} •
                                    {{ $pksDutyAttendance->assignment?->location?->name ?? '-' }} •
                                    {{ $pksDutyAttendance->assignment?->schedule?->schedule_date?->format('d/m/Y') ?? '-' }}
                                </small>
                            </div>
                        </div>
                    </div>

                    <form method="POST" action="{{ route('pks-duty-attendances.update', $pksDutyAttendance) }}">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label for="status" class="form-label">Status Kehadiran <span class="text-danger">*</span></label>
                            <select name="status"
                                    id="status"
                                    class="form-select @error('status') is-invalid @enderror"
                                    required>
                                <option value="">-- Pilih Status --</option>
                                <option value="present" {{ old('status', $pksDutyAttendance->status) == 'present' ? 'selected' : '' }}>Hadir</option>
                                <option value="late" {{ old('status', $pksDutyAttendance->status) == 'late' ? 'selected' : '' }}>Terlambat</option>
                                <option value="absent" {{ old('status', $pksDutyAttendance->status) == 'absent' ? 'selected' : '' }}>Tidak Hadir</option>
                                <option value="excused" {{ old('status', $pksDutyAttendance->status) == 'excused' ? 'selected' : '' }}>Izin</option>
                            </select>
                            @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row mb-3">
                            <div class="col-6">
                                <label for="check_in_at" class="form-label">Jam Masuk</label>
                                <input type="datetime-local"
                                       name="check_in_at"
                                       id="check_in_at"
                                       class="form-control @error('check_in_at') is-invalid @enderror"
                                       value="{{ old('check_in_at', $pksDutyAttendance->check_in_at?->format('Y-m-d\TH:i')) }}">
                                @error('check_in_at')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-6">
                                <label for="check_out_at" class="form-label">Jam Pulang</label>
                                <input type="datetime-local"
                                       name="check_out_at"
                                       id="check_out_at"
                                       class="form-control @error('check_out_at') is-invalid @enderror"
                                       value="{{ old('check_out_at', $pksDutyAttendance->check_out_at?->format('Y-m-d\TH:i')) }}">
                                @error('check_out_at')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-4">
                            <label for="notes" class="form-label">Catatan</label>
                            <textarea name="notes"
                                      id="notes"
                                      class="form-control @error('notes') is-invalid @enderror"
                                      rows="3"
                                      placeholder="Catatan opsional...">{{ old('notes', $pksDutyAttendance->notes) }}</textarea>
                            @error('notes')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('pks-duty-attendances.show', $pksDutyAttendance) }}" class="btn btn-outline-secondary">
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
