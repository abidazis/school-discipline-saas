<x-app-layout>
    <x-slot name="title">Catat Aktivitas Lapangan</x-slot>

    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('pks-field-activities.index') }}">Aktivitas Lapangan</a></li>
            <li class="breadcrumb-item active">Catat</li>
        </ol>
    </nav>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h4 mb-0">Catat Aktivitas Lapangan</h1>
    </div>

    <div class="row">
        <div class="col-12 col-lg-8">
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
                                        {{ $assignment->location?->name ?? '-' }} •
                                        {{ $assignment->schedule?->schedule_date?->format('d/m/Y') ?? '-' }}
                                        {{ $assignment->schedule?->shift?->name ?? '' }}
                                    </small>
                                </div>
                            </div>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('pks-field-activities.store') }}">
                        @csrf

                        @if(!$assignment)
                            <div class="mb-3">
                                <label for="pks_duty_assignment_id" class="form-label">Penugasan <span class="text-danger">*</span></label>
                                <select name="pks_duty_assignment_id"
                                        id="pks_duty_assignment_id"
                                        class="form-select @error('pks_duty_assignment_id') is-invalid @enderror"
                                        required>
                                    <option value="">-- Pilih Penugasan --</option>
                                    @foreach($eligibleAssignments as $a)
                                        <option value="{{ $a->id }}" {{ old('pks_duty_assignment_id') == $a->id ? 'selected' : '' }}>
                                            {{ $a->schedule?->schedule_date?->format('d/m/Y') ?? '-' }} -
                                            {{ $a->member?->student?->full_name ?? '-' }} -
                                            {{ $a->location?->name ?? '-' }}
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

                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label for="activity_date" class="form-label">Tanggal Aktivitas <span class="text-danger">*</span></label>
                                <input type="date"
                                       name="activity_date"
                                       id="activity_date"
                                       class="form-control @error('activity_date') is-invalid @enderror"
                                       value="{{ old('activity_date', now()->format('Y-m-d')) }}"
                                       required>
                                @error('activity_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label for="started_at" class="form-label">Jam Mulai <span class="text-danger">*</span></label>
                                <input type="time"
                                       name="started_at"
                                       id="started_at"
                                       class="form-control @error('started_at') is-invalid @enderror"
                                       value="{{ old('started_at', now()->format('H:i')) }}"
                                       required>
                                @error('started_at')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label for="ended_at" class="form-label">Jam Selesai</label>
                                <input type="time"
                                       name="ended_at"
                                       id="ended_at"
                                       class="form-control @error('ended_at') is-invalid @enderror"
                                       value="{{ old('ended_at') }}">
                                @error('ended_at')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="activity_type" class="form-label">Jenis Aktivitas <span class="text-danger">*</span></label>
                            <select name="activity_type"
                                    id="activity_type"
                                    class="form-select @error('activity_type') is-invalid @enderror"
                                    required>
                                <option value="">-- Pilih Jenis Aktivitas --</option>
                                @foreach($activityTypes as $value => $label)
                                    <option value="{{ $value }}" {{ old('activity_type') == $value ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                            @error('activity_type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Deskripsi Aktivitas</label>
                            <textarea name="description"
                                      id="description"
                                      class="form-control @error('description') is-invalid @enderror"
                                      rows="3"
                                      placeholder="Deskripsikan aktivitas yang dilakukan...">{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="finding" class="form-label">Temuan</label>
                            <textarea name="finding"
                                      id="finding"
                                      class="form-control @error('finding') is-invalid @enderror"
                                      rows="3"
                                      placeholder="Catat temuan atau kejadian yang ditemukan...">{{ old('finding') }}</textarea>
                            @error('finding')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="action_taken" class="form-label">Tindakan yang Dilakukan</label>
                            <textarea name="action_taken"
                                      id="action_taken"
                                      class="form-control @error('action_taken') is-invalid @enderror"
                                      rows="3"
                                      placeholder="Catat tindakan atau langkah yang diambil...">{{ old('action_taken') }}</textarea>
                            @error('action_taken')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="violation_id" class="form-label">Pelanggaran Terkait</label>
                            <select name="violation_id"
                                    id="violation_id"
                                    class="form-select @error('violation_id') is-invalid @enderror">
                                <option value="">-- Tidak Ada --</option>
                                @foreach($violations as $violation)
                                    <option value="{{ $violation->id }}" {{ old('violation_id') == $violation->id ? 'selected' : '' }}>
                                        #{{ $violation->id }} - {{ $violation->student?->full_name ?? '-' }} - {{ $violation->violationType?->name ?? '-' }}
                                    </option>
                                @endforeach
                            </select>
                            @error('violation_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">Opsional: Hubungkan dengan pelanggaran yang sudah tercatat</div>
                        </div>

                        <div class="mb-3">
                            <label for="status" class="form-label">Status</label>
                            <select name="status"
                                    id="status"
                                    class="form-select @error('status') is-invalid @enderror">
                                <option value="draft" {{ old('status', 'draft') == 'draft' ? 'selected' : '' }}>Draft</option>
                                <option value="completed" {{ old('status') == 'completed' ? 'selected' : '' }}>Selesai</option>
                            </select>
                            @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="notes" class="form-label">Catatan Tambahan</label>
                            <textarea name="notes"
                                      id="notes"
                                      class="form-control @error('notes') is-invalid @enderror"
                                      rows="2"
                                      placeholder="Catatan opsional...">{{ old('notes') }}</textarea>
                            @error('notes')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex justify-content-between">
                            @if($assignment)
                                <a href="{{ route('pks-duty-schedules.show', $assignment->pks_duty_schedule_id) }}" class="btn btn-outline-secondary">
                                    <i class="bi bi-arrow-left me-1"></i>Kembali
                                </a>
                            @else
                                <a href="{{ route('pks-field-activities.index') }}" class="btn btn-outline-secondary">
                                    <i class="bi bi-arrow-left me-1"></i>Kembali
                                </a>
                            @endif
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-lg me-1"></i>Simpan Aktivitas
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
