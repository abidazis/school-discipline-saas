<x-app-layout>
    <x-slot name="title">Tambah Penugasan Piket</x-slot>

    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('pks-duty-assignments.index') }}">Penugasan Piket</a></li>
            <li class="breadcrumb-item active">Tambah</li>
        </ol>
    </nav>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h4 mb-0">Tambah Penugasan Piket</h1>
    </div>

    <div class="row justify-content-center">
        <div class="col-12 col-lg-8 col-xl-6">
            <div class="card">
                <div class="card-body p-4">
                    <form method="POST" action="{{ route('pks-duty-assignments.store') }}">
                        @csrf

                        <div class="mb-3">
                            <label for="pks_duty_schedule_id" class="form-label">Jadwal Piket <span class="text-danger">*</span></label>
                            <select name="pks_duty_schedule_id"
                                    id="pks_duty_schedule_id"
                                    class="form-select @error('pks_duty_schedule_id') is-invalid @enderror"
                                    required>
                                <option value="">-- Pilih Jadwal Piket --</option>
                                @foreach($schedules as $schedule)
                                    <option value="{{ $schedule->id }}" {{ old('pks_duty_schedule_id') == $schedule->id ? 'selected' : '' }}>
                                        {{ $schedule->schedule_date->format('d/m/Y') }} - {{ $schedule->shift?->name ?? '-' }} ({{ $schedule->time_range ?? '-' }})
                                    </option>
                                @endforeach
                            </select>
                            @error('pks_duty_schedule_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="pks_duty_location_id" class="form-label">Lokasi Piket <span class="text-danger">*</span></label>
                            <select name="pks_duty_location_id"
                                    id="pks_duty_location_id"
                                    class="form-select @error('pks_duty_location_id') is-invalid @enderror"
                                    required
                                    {{ $selectedSchedule ? '' : 'disabled' }}>
                                <option value="">-- Pilih Lokasi Piket --</option>
                                @if($selectedSchedule)
                                    @foreach($selectedSchedule->locations as $location)
                                        <option value="{{ $location->id }}" {{ old('pks_duty_location_id') == $location->id ? 'selected' : '' }}>
                                            {{ $location->name }} ({{ $location->code }})
                                        </option>
                                    @endforeach
                                @endif
                            </select>
                            @error('pks_duty_location_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">Pilih jadwal piket terlebih dahulu</div>
                        </div>

                        <div class="mb-3">
                            <label for="pks_member_id" class="form-label">Anggota PKS <span class="text-danger">*</span></label>
                            <select name="pks_member_id"
                                    id="pks_member_id"
                                    class="form-select @error('pks_member_id') is-invalid @enderror"
                                    required>
                                <option value="">-- Pilih Anggota PKS --</option>
                                @foreach($members as $member)
                                    <option value="{{ $member->id }}" {{ old('pks_member_id') == $member->id ? 'selected' : '' }}>
                                        {{ $member->student?->name ?? '-' }} - {{ $member->student?->nis ?? '-' }} - {{ $member->position }}
                                    </option>
                                @endforeach
                            </select>
                            @error('pks_member_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">Hanya anggota PKS aktif yang ditampilkan</div>
                        </div>

                        <div class="mb-3">
                            <label for="status" class="form-label">Status</label>
                            <select name="status"
                                    id="status"
                                    class="form-select @error('status') is-invalid @enderror">
                                <option value="assigned" {{ old('status', 'assigned') == 'assigned' ? 'selected' : '' }}>
                                    Ditugaskan
                                </option>
                                <option value="replaced" {{ old('status') == 'replaced' ? 'selected' : '' }}>
                                    Digantikan
                                </option>
                                <option value="cancelled" {{ old('status') == 'cancelled' ? 'selected' : '' }}>
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
                                      placeholder="Catatan opsional...">{{ old('notes') }}</textarea>
                            @error('notes')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('pks-duty-assignments.index') }}" class="btn btn-outline-secondary">
                                <i class="bi bi-arrow-left me-1"></i>Kembali
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-lg me-1"></i>Simpan Penugasan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const scheduleSelect = document.getElementById('pks_duty_schedule_id');
            const locationSelect = document.getElementById('pks_duty_location_id');

            // Store all schedule-location mappings
            const scheduleLocationsMap = @json($schedules->mapWithKeys(fn($s) => [$s->id => $s->locations->map(fn($l) => ['id' => $l->id, 'name' => $l->name, 'code' => $l->code])]));

            scheduleSelect.addEventListener('change', function() {
                const scheduleId = parseInt(this.value);

                locationSelect.innerHTML = '<option value="">-- Pilih Lokasi Piket --</option>';

                if (!scheduleId || !scheduleLocationsMap[scheduleId]) {
                    locationSelect.disabled = true;
                    return;
                }

                const locations = scheduleLocationsMap[scheduleId];
                if (locations && locations.length > 0) {
                    locations.forEach(function(location) {
                        const option = document.createElement('option');
                        option.value = location.id;
                        option.textContent = location.name + ' (' + location.code + ')';
                        locationSelect.appendChild(option);
                    });
                    locationSelect.disabled = false;
                } else {
                    locationSelect.disabled = true;
                }
            });

            // Initialize location select if schedule is pre-selected
            if (scheduleSelect.value) {
                scheduleSelect.dispatchEvent(new Event('change'));
            }
        });
    </script>
    @endpush
</x-app-layout>
