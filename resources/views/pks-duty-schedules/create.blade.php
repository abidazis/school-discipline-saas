<x-app-layout>
    <x-slot name="title">Tambah Jadwal Piket</x-slot>

    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('pks-duty-schedules.index') }}">Jadwal Piket</a></li>
            <li class="breadcrumb-item active">Tambah</li>
        </ol>
    </nav>

    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="bi bi-calendar-plus me-2"></i>Tambah Jadwal Piket</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('pks-duty-schedules.store') }}">
                        @csrf

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="schedule_date" class="form-label">Tanggal <span class="text-danger">*</span></label>
                                <input type="date" class="form-control @error('schedule_date') is-invalid @enderror"
                                       id="schedule_date" name="schedule_date"
                                       value="{{ old('schedule_date', now()->format('Y-m-d')) }}" required>
                                @error('schedule_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="pks_shift_id" class="form-label">Shift <span class="text-danger">*</span></label>
                                <select class="form-select @error('pks_shift_id') is-invalid @enderror"
                                        id="pks_shift_id" name="pks_shift_id" required>
                                    <option value="">Pilih Shift...</option>
                                    @foreach($shifts as $shift)
                                        <option value="{{ $shift->id }}"
                                                data-time-start="{{ $shift->start_time->format('H:i') }}"
                                                data-time-end="{{ $shift->end_time->format('H:i') }}"
                                                {{ old('pks_shift_id') == $shift->id ? 'selected' : '' }}>
                                            {{ $shift->name }} ({{ $shift->time_range }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('pks_shift_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Lokasi Piket <span class="text-danger">*</span></label>
                            <div class="border rounded p-3" style="max-height: 200px; overflow-y: auto;">
                                @error('location_ids')
                                    <div class="text-danger small mb-2">{{ $message }}</div>
                                @enderror
                                @foreach($locations as $location)
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox"
                                               name="location_ids[]"
                                               id="location_{{ $location->id }}"
                                               value="{{ $location->id }}"
                                               {{ in_array($location->id, old('location_ids', []) ? old('location_ids') : []) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="location_{{ $location->id }}">
                                            <span class="badge badge-secondary me-1">{{ $location->code }}</span>
                                            {{ $location->name }}
                                        </label>
                                    </div>
                                @endforeach
                                @if($locations->isEmpty())
                                    <p class="text-muted mb-0">Belum ada lokasi aktif. <a href="{{ route('pks-duty-locations.create') }}">Tambah Lokasi</a></p>
                                @endif
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="status" class="form-label">Status</label>
                                <select class="form-select @error('status') is-invalid @enderror" id="status" name="status">
                                    <option value="scheduled" {{ old('status', 'scheduled') === 'scheduled' ? 'selected' : '' }}>Terjadwal</option>
                                    <option value="completed" {{ old('status') === 'completed' ? 'selected' : '' }}>Selesai</option>
                                    <option value="cancelled" {{ old('status') === 'cancelled' ? 'selected' : '' }}>Dibatalkan</option>
                                </select>
                                @error('status')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="notes" class="form-label">Catatan</label>
                                <input type="text" class="form-control @error('notes') is-invalid @enderror"
                                       id="notes" name="notes" value="{{ old('notes') }}"
                                       placeholder="Catatan opsional">
                                @error('notes')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg me-1"></i>Simpan</button>
                            <a href="{{ route('pks-duty-schedules.index') }}" class="btn btn-outline-secondary">Batal</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
