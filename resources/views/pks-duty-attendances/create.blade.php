<x-app-layout>
    <x-slot name="title">Catat Kehadiran</x-slot>

    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb">
            @if($assignment)
                <li class="breadcrumb-item"><a href="{{ route('pks-duty-schedules.show', $assignment->pks_duty_schedule_id) }}">Jadwal Piket</a></li>
            @endif
            <li class="breadcrumb-item active">Catat Kehadiran</li>
        </ol>
    </nav>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h4 mb-0">Catat Kehadiran</h1>
    </div>

    <div class="row justify-content-center">
        <div class="col-12 col-lg-8 col-xl-6">
            <div class="card">
                <div class="card-body p-4">
                    @if($assignment)
                        <div class="alert alert-info mb-4">
                            <div class="d-flex">
                                <i class="bi bi-info-circle me-2 mt-1"></i>
                                <div>
                                    <strong>{{ $assignment->member?->student?->full_name ?? '-' }}</strong>
                                    <br>
                                    <small>
                                        NIS: {{ $assignment->member?->student?->nis ?? '-' }} •
                                        {{ $assignment->location?->name ?? '-' }} •
                                        {{ $assignment->schedule?->schedule_date?->format('d/m/Y') ?? '-' }} {{ $assignment->schedule?->shift?->name ?? '' }}
                                    </small>
                                </div>
                            </div>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('pks-duty-attendances.store') }}">
                        @csrf

                        @if(!$assignment)
                            <div class="mb-3">
                                <label for="pks_duty_assignment_id" class="form-label">Penugasan <span class="text-danger">*</span></label>
                                <select name="pks_duty_assignment_id"
                                        id="pks_duty_assignment_id"
                                        class="form-select @error('pks_duty_assignment_id') is-invalid @enderror"
                                        required>
                                    <option value="">-- Pilih Penugasan --</option>
                                    @foreach($eligibleAssignments ?? [] as $a)
                                        <option value="{{ $a->id }}" {{ old('pks_duty_assignment_id') == $a->id ? 'selected' : '' }}>
                                            {{ $a->member?->student?->full_name ?? '-' }} -
                                            {{ $a->location?->name ?? '-' }} -
                                            {{ $a->schedule?->schedule_date?->format('d/m/Y') ?? '-' }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('pks_duty_assignment_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        @else
                            <input type="hidden" name="pks_duty_assignment_id" value="{{ $assignment->id }}">
                        @endif

                        <div class="mb-3">
                            <label for="status" class="form-label">Status Kehadiran <span class="text-danger">*</span></label>
                            <select name="status"
                                    id="status"
                                    class="form-select @error('status') is-invalid @enderror"
                                    required>
                                <option value="">-- Pilih Status --</option>
                                <option value="present" {{ old('status') == 'present' ? 'selected' : '' }}>Hadir</option>
                                <option value="late" {{ old('status') == 'late' ? 'selected' : '' }}>Terlambat</option>
                                <option value="absent" {{ old('status') == 'absent' ? 'selected' : '' }}>Tidak Hadir</option>
                                <option value="excused" {{ old('status') == 'excused' ? 'selected' : '' }}>Izin</option>
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
                                       value="{{ old('check_in_at', now()->format('Y-m-d\TH:i')) }}">
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
                                       value="{{ old('check_out_at') }}">
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
                                      placeholder="Catatan opsional...">{{ old('notes') }}</textarea>
                            @error('notes')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">Contoh: Izin keluarga, Sakit, Terlambat karena kendaraan</div>
                        </div>

                        <div class="d-flex justify-content-between">
                            @if($assignment)
                                <a href="{{ route('pks-duty-schedules.show', $assignment->pks_duty_schedule_id) }}" class="btn btn-outline-secondary">
                                    <i class="bi bi-arrow-left me-1"></i>Kembali
                                </a>
                            @else
                                <a href="{{ route('pks-duty-attendances.index') }}" class="btn btn-outline-secondary">
                                    <i class="bi bi-arrow-left me-1"></i>Kembali
                                </a>
                            @endif
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-lg me-1"></i>Simpan Kehadiran
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
