<x-app-layout>
    <x-slot name="title">Catat Absensi</x-slot>

    <div class="pks-create-page">
        <!-- Page Header -->
        <div class="page-header-simple">
            <a href="{{ route('pks-duty-attendances.index') }}" class="back-btn">
                <i class="bi bi-arrow-left"></i>
            </a>
            <h1 class="page-title-simple">Catat Absensi Piket</h1>
        </div>

        @if(session('info'))
            <div class="alert alert-info-custom">
                <i class="bi bi-info-circle"></i>
                {{ session('info') }}
            </div>
        @endif

        @if(session('success'))
            <div class="alert alert-success-custom">
                <i class="bi bi-check-circle"></i>
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger-custom">
                <i class="bi bi-exclamation-circle"></i>
                {{ session('error') }}
            </div>
        @endif

        @if($assignment)
            <!-- Assignment Info Card -->
            <div class="assignment-card">
                <div class="assignment-header">
                    <i class="bi bi-calendar-check"></i>
                    <span>Detail Piket</span>
                </div>
                <div class="assignment-body">
                    <div class="assignment-row">
                        <span class="assignment-label">Nama</span>
                        <span class="assignment-value">{{ $assignment->member?->student?->full_name ?? '-' }}</span>
                    </div>
                    <div class="assignment-row">
                        <span class="assignment-label">NIS</span>
                        <span class="assignment-value">{{ $assignment->member?->student?->nis ?? '-' }}</span>
                    </div>
                    <div class="assignment-row">
                        <span class="assignment-label">Tanggal</span>
                        <span class="assignment-value">{{ $assignment->schedule?->schedule_date?->format('d/m/Y') ?? '-' }}</span>
                    </div>
                    <div class="assignment-row">
                        <span class="assignment-label">Shift</span>
                        <span class="assignment-value">{{ $assignment->schedule?->shift?->name ?? '-' }} ({{ $assignment->schedule?->shift?->start_time }} - {{ $assignment->schedule?->shift?->end_time }})</span>
                    </div>
                    <div class="assignment-row">
                        <span class="assignment-label">Lokasi</span>
                        <span class="assignment-value">{{ $assignment->location?->name ?? '-' }}</span>
                    </div>
                </div>
            </div>
        @elseif($eligibleAssignments && $eligibleAssignments->isNotEmpty())
            <!-- Assignment Selection -->
            <div class="card mb-3">
                <div class="card-body">
                    <h5 class="mb-3">Pilih Penugasan</h5>
                    <div class="assignment-list">
                        @foreach($eligibleAssignments as $a)
                            <a href="{{ route('pks-duty-attendances.create', ['assignment_id' => $a->id]) }}"
                               class="assignment-item">
                                <div class="assignment-item-info">
                                    <span class="shift-badge">{{ $a->schedule?->shift?->name ?? 'Shift' }}</span>
                                    <span class="assignment-date">{{ $a->schedule?->schedule_date?->format('d/m/Y') ?? '-' }}</span>
                                </div>
                                <div class="assignment-item-location">
                                    <i class="bi bi-geo-alt"></i> {{ $a->location?->name ?? '-' }}
                                </div>
                                <i class="bi bi-chevron-right"></i>
                            </a>
                        @endforeach
                    </div>
                </div>
            </div>
        @elseif(auth()->user()->isPksTeam())
            <!-- No eligible assignments -->
            <div class="empty-card">
                <div class="empty-icon">
                    <i class="bi bi-calendar-x"></i>
                </div>
                <h5>Tidak Ada Penugasan</h5>
                <p>Anda tidak memiliki penugasan piket yang eligible untuk diisi absensi saat ini.</p>
                <a href="{{ route('pks-duty-attendances.index') }}" class="btn btn-outline-secondary mt-3">
                    <i class="bi bi-arrow-left me-2"></i>Kembali
                </a>
            </div>
        @endif

        @if($assignment || ($eligibleAssignments && $eligibleAssignments->isEmpty() === false))
            <!-- Attendance Form -->
            <div class="attendance-form-card">
                <form method="POST" action="{{ route('pks-duty-attendances.store') }}" id="attendanceForm" enctype="multipart/form-data">
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
                                        {{ $a->schedule?->schedule_date?->format('d/m/Y') ?? '-' }} - {{ $a->location?->name ?? '-' }} - {{ $a->schedule?->shift?->name ?? 'Shift' }}
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

                    <!-- Camera Capture Section (for PKS Team) -->
                    @if(auth()->user()->isPksTeam())
                        <div class="camera-section mb-4">
                            <label class="form-label">
                                Ambil Foto Kehadiran <span class="text-danger">*</span>
                            </label>
                            <div class="camera-container" id="cameraContainer">
                                <div class="camera-preview" id="cameraPreview">
                                    <video id="video" autoplay playsinline></video>
                                    <canvas id="canvas" style="display: none;"></canvas>
                                </div>
                                <div class="camera-overlay" id="cameraOverlay">
                                    <div class="camera-placeholder" id="cameraPlaceholder">
                                        <i class="bi bi-camera"></i>
                                        <span>Kamera tidak tersedia</span>
                                        <small>Aktifkan kamera atau upload foto manual</small>
                                    </div>
                                </div>
                                <div class="camera-controls">
                                    <button type="button" class="btn btn-camera" id="captureBtn">
                                        <i class="bi bi-camera-fill"></i>
                                        <span>Ambil Foto</span>
                                    </button>
                                    <button type="button" class="btn btn-retake d-none" id="retakeBtn">
                                        <i class="bi bi-arrow-repeat"></i>
                                        <span>Ambil Ulang</span>
                                    </button>
                                </div>
                                <input type="file" name="photo" id="photoInput" accept="image/*" capture="environment" style="display: none;">
                                <input type="hidden" name="photo_data" id="photoData">
                                @error('photo')
                                    <div class="text-danger mt-1 small">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="camera-info">
                                <small class="text-muted">
                                    <i class="bi bi-info-circle me-1"></i>
                                    Foto akan otomatis dicapture dengan timestamp untuk verifikasi kehadiran.
                                </small>
                            </div>
                        </div>
                    @endif

                    <!-- Status Selection - Big Buttons -->
                    <div class="mb-4">
                        <label class="form-label">Status Kehadiran <span class="text-danger">*</span></label>
                        <div class="status-buttons" id="statusButtons">
                            <label class="status-btn status-present">
                                <input type="radio" name="status" value="present" {{ old('status') == 'present' ? 'checked' : '' }} required>
                                <i class="bi bi-check-circle"></i>
                                <span>Hadir</span>
                            </label>
                            <label class="status-btn status-late">
                                <input type="radio" name="status" value="late" {{ old('status') == 'late' ? 'checked' : '' }}>
                                <i class="bi bi-clock"></i>
                                <span>Terlambat</span>
                            </label>
                            <label class="status-btn status-absent">
                                <input type="radio" name="status" value="absent" {{ old('status') == 'absent' ? 'checked' : '' }}>
                                <i class="bi bi-x-circle"></i>
                                <span>Tidak Hadir</span>
                            </label>
                            <label class="status-btn status-excused">
                                <input type="radio" name="status" value="excused" {{ old('status') == 'excused' ? 'checked' : '' }}>
                                <i class="bi bi-file-text"></i>
                                <span>Izin</span>
                            </label>
                        </div>
                        @error('status')
                            <div class="text-danger mt-1 small">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Photo Required Warning -->
                    <div class="photo-warning alert-warning d-none" id="photoWarning">
                        <i class="bi bi-exclamation-triangle"></i>
                        <span>Foto diperlukan untuk status Hadir dan Terlambat</span>
                    </div>

                    <!-- Time Inputs -->
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

                    <!-- Notes -->
                    <div class="mb-4">
                        <label for="notes" class="form-label">Catatan</label>
                        <textarea name="notes"
                                  id="notes"
                                  class="form-control @error('notes') is-invalid @enderror"
                                  rows="2"
                                  placeholder="Contoh: Izin keluarga, Sakit, Terlambat karena macet">{{ old('notes') }}</textarea>
                        @error('notes')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Submit -->
                    <button type="submit" class="btn btn-success btn-lg w-100 submit-btn" id="submitBtn">
                        <i class="bi bi-check-lg me-2"></i>Simpan Absensi
                    </button>
                </form>
            </div>
        @endif
    </div>
</x-app-layout>

<style>
    .pks-create-page { width: 100%; max-width: 600px; margin: 0 auto; padding: 16px; }

    .page-header-simple { display: flex; align-items: center; gap: 12px; margin-bottom: 20px; }
    .back-btn { width: 40px; height: 40px; display: flex; align-items: center; justify-content: center; background: #f1f5f9; border: none; border-radius: 10px; color: #475569; text-decoration: none; transition: all 0.15s; }
    .back-btn:hover { background: #e2e8f0; color: #1e293b; }
    .page-title-simple { font-size: 20px; font-weight: 700; color: #1e293b; margin: 0; }

    .alert { padding: 14px 16px; border-radius: 10px; font-size: 14px; margin-bottom: 16px; display: flex; align-items: flex-start; gap: 10px; }
    .alert i { font-size: 18px; margin-top: 1px; }
    .alert-info-custom { background: #dbeafe; color: #1e40af; border: 1px solid #93c5fd; }
    .alert-success-custom { background: #dcfce7; color: #166534; border: 1px solid #86efac; }
    .alert-danger-custom { background: #fee2e2; color: #991b1b; border: 1px solid #fecaca; }
    .alert-warning { background: #fef9c3; color: #854d0e; border: 1px solid #fde047; }

    /* Assignment Card */
    .assignment-card { background: white; border: 1px solid #e2e8f0; border-radius: 12px; overflow: hidden; margin-bottom: 20px; }
    .assignment-header { background: #f8fafc; padding: 12px 16px; font-weight: 600; color: #475569; display: flex; align-items: center; gap: 8px; border-bottom: 1px solid #e2e8f0; }
    .assignment-header i { color: #16a34a; }
    .assignment-body { padding: 16px; }
    .assignment-row { display: flex; justify-content: space-between; padding: 8px 0; border-bottom: 1px solid #f1f5f9; }
    .assignment-row:last-child { border-bottom: none; }
    .assignment-label { font-size: 13px; color: #64748b; }
    .assignment-value { font-size: 13px; font-weight: 500; color: #1e293b; }

    /* Assignment List */
    .assignment-list { display: flex; flex-direction: column; gap: 8px; }
    .assignment-item { display: flex; align-items: center; padding: 14px; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 10px; text-decoration: none; color: #334155; transition: all 0.15s; }
    .assignment-item:hover { background: #f1f5f9; border-color: #cbd5e1; }
    .assignment-item-info { flex: 1; display: flex; align-items: center; gap: 8px; }
    .assignment-date { font-size: 13px; color: #64748b; }
    .assignment-item-location { font-size: 12px; color: #64748b; display: flex; align-items: center; gap: 4px; margin-right: 12px; }
    .assignment-item .bi-chevron-right { color: #94a3b8; }

    /* Empty Card */
    .empty-card { background: white; border: 1px solid #e2e8f0; border-radius: 12px; padding: 40px 24px; text-align: center; }
    .empty-icon { width: 64px; height: 64px; background: #f1f5f9; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 16px; font-size: 28px; color: #94a3b8; }
    .empty-card h5 { font-size: 16px; font-weight: 600; color: #334155; margin-bottom: 8px; }
    .empty-card p { font-size: 14px; color: #64748b; margin: 0; }

    /* Camera Section */
    .camera-section { background: #f8fafc; border: 2px dashed #e2e8f0; border-radius: 12px; padding: 16px; }
    .camera-container { position: relative; border-radius: 10px; overflow: hidden; background: #000; }
    .camera-preview { width: 100%; aspect-ratio: 4/3; position: relative; }
    .camera-preview video, .camera-preview canvas { width: 100%; height: 100%; object-fit: cover; }
    .camera-overlay { position: absolute; top: 0; left: 0; right: 0; bottom: 0; display: flex; align-items: center; justify-content: center; background: #1e293b; }
    .camera-placeholder { text-align: center; color: #94a3b8; }
    .camera-placeholder i { font-size: 48px; margin-bottom: 8px; }
    .camera-placeholder span { display: block; font-size: 14px; margin-bottom: 4px; }
    .camera-placeholder small { font-size: 12px; color: #64748b; }
    .camera-controls { padding: 12px; background: #1e293b; display: flex; justify-content: center; gap: 8px; }
    .btn-camera { background: #16a34a; color: white; border: none; padding: 10px 20px; border-radius: 8px; display: flex; align-items: center; gap: 8px; }
    .btn-camera:hover { background: #15803d; }
    .btn-retake { background: #64748b; color: white; border: none; padding: 10px 20px; border-radius: 8px; display: flex; align-items: center; gap: 8px; }
    .btn-retake:hover { background: #475569; }
    .camera-info { margin-top: 8px; text-align: center; }
    .captured-image { width: 100%; aspect-ratio: 4/3; object-fit: cover; }
    .captured-badge { position: absolute; top: 8px; left: 8px; background: #16a34a; color: white; padding: 4px 8px; border-radius: 4px; font-size: 10px; display: flex; align-items: center; gap: 4px; }
    .captured-badge i { font-size: 12px; }
    .timestamp-badge { position: absolute; bottom: 8px; right: 8px; background: rgba(0,0,0,0.7); color: white; padding: 4px 8px; border-radius: 4px; font-size: 10px; font-family: monospace; }

    /* Attendance Form Card */
    .attendance-form-card { background: white; border: 1px solid #e2e8f0; border-radius: 12px; padding: 20px; }

    .form-label { font-size: 13px; font-weight: 500; color: #475569; margin-bottom: 6px; }
    .form-control, .form-select { width: 100%; padding: 12px 14px; font-size: 14px; border: 1px solid #e2e8f0; border-radius: 8px; transition: all 0.15s; }
    .form-control:focus, .form-select:focus { outline: none; border-color: #16a34a; box-shadow: 0 0 0 3px rgba(22, 163, 74, 0.1); }
    .form-control.is-invalid, .form-select.is-invalid { border-color: #ef4444; }

    /* Status Buttons */
    .status-buttons { display: grid; grid-template-columns: repeat(4, 1fr); gap: 8px; margin-top: 8px; }
    .status-btn { display: flex; flex-direction: column; align-items: center; gap: 6px; padding: 16px 8px; background: #f8fafc; border: 2px solid #e2e8f0; border-radius: 10px; cursor: pointer; transition: all 0.15s; }
    .status-btn input { display: none; }
    .status-btn i { font-size: 24px; color: #64748b; }
    .status-btn span { font-size: 11px; font-weight: 500; color: #64748b; }

    .status-btn.status-present:hover, .status-btn.status-present input:checked ~ * { background: #dcfce7; border-color: #16a34a; color: #166534; }
    .status-btn.status-present input:checked ~ i { color: #16a34a; }
    .status-btn.status-present input:checked ~ span { color: #166534; }

    .status-btn.status-late:hover, .status-btn.status-late input:checked ~ * { background: #fef9c3; border-color: #ca8a04; color: #854d0e; }
    .status-btn.status-late input:checked ~ i { color: #ca8a04; }
    .status-btn.status-late input:checked ~ span { color: #854d0e; }

    .status-btn.status-absent:hover, .status-btn.status-absent input:checked ~ * { background: #fee2e2; border-color: #dc2626; color: #991b1b; }
    .status-btn.status-absent input:checked ~ i { color: #dc2626; }
    .status-btn.status-absent input:checked ~ span { color: #991b1b; }

    .status-btn.status-excused:hover, .status-btn.status-excused input:checked ~ * { background: #dbeafe; border-color: #2563eb; color: #1e40af; }
    .status-btn.status-excused input:checked ~ i { color: #2563eb; }
    .status-btn.status-excused input:checked ~ span { color: #1e40af; }

    .submit-btn { padding: 14px; font-size: 15px; font-weight: 600; }

    @media (max-width: 480px) {
        .pks-create-page { padding: 12px; }
        .status-buttons { grid-template-columns: repeat(2, 1fr); }
        .assignment-item-info { flex-direction: column; align-items: flex-start; gap: 4px; }
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const video = document.getElementById('video');
        const canvas = document.getElementById('canvas');
        const captureBtn = document.getElementById('captureBtn');
        const retakeBtn = document.getElementById('retakeBtn');
        const photoInput = document.getElementById('photoInput');
        const photoData = document.getElementById('photoData');
        const cameraOverlay = document.getElementById('cameraOverlay');
        const cameraPreview = document.getElementById('cameraPreview');
        const statusButtons = document.getElementById('statusButtons');
        const photoWarning = document.getElementById('photoWarning');
        const submitBtn = document.getElementById('submitBtn');
        const attendanceForm = document.getElementById('attendanceForm');

        let stream = null;
        let capturedPhoto = null;

        // Initialize camera
        async function initCamera() {
            if (!video || !cameraPreview) return;

            try {
                stream = await navigator.mediaDevices.getUserMedia({
                    video: { facingMode: 'environment' },
                    audio: false
                });
                video.srcObject = stream;
                cameraOverlay.style.display = 'none';
            } catch (err) {
                console.log('Camera not available:', err);
                cameraOverlay.style.display = 'flex';
                // Fallback: allow file upload
                photoInput.addEventListener('change', handleFileSelect);
            }
        }

        // Capture photo
        function capturePhoto() {
            if (!video || !canvas) return;

            canvas.width = video.videoWidth;
            canvas.height = video.videoHeight;
            canvas.getContext('2d').drawImage(video, 0, 0);

            // Stop camera
            if (stream) {
                stream.getTracks().forEach(track => track.stop());
                stream = null;
            }

            // Get data URL
            const dataUrl = canvas.toDataURL('image/jpeg', 0.8);
            capturedPhoto = dataUrl;

            // Show captured image
            const img = document.createElement('img');
            img.src = dataUrl;
            img.className = 'captured-image';

            // Add badges
            const badge = document.createElement('div');
            badge.className = 'captured-badge';
            badge.innerHTML = '<i class="bi bi-check-circle-fill"></i> Tersimpan';

            const timestamp = document.createElement('div');
            timestamp.className = 'timestamp-badge';
            const now = new Date();
            timestamp.textContent = now.toLocaleString('id-ID');

            // Clear and add image
            cameraPreview.innerHTML = '';
            cameraPreview.appendChild(img);
            cameraPreview.appendChild(badge);
            cameraPreview.appendChild(timestamp);
            cameraPreview.style.background = '#000';

            // Show retake button
            captureBtn.classList.add('d-none');
            retakeBtn.classList.remove('d-none');

            // Set photo data
            photoData.value = dataUrl;
        }

        // Retake photo
        function retakePhoto() {
            capturedPhoto = null;
            photoData.value = '';
            cameraPreview.innerHTML = '<video id="video" autoplay playsinline></video><canvas id="canvas" style="display: none;"></canvas>';
            initCamera();
            captureBtn.classList.remove('d-none');
            retakeBtn.classList.add('d-none');
        }

        // Handle file select fallback
        function handleFileSelect(e) {
            const file = e.target.files[0];
            if (!file) return;

            const reader = new FileReader();
            reader.onload = function(event) {
                capturedPhoto = event.target.result;
                photoData.value = capturedPhoto;

                // Show preview
                const img = document.createElement('img');
                img.src = capturedPhoto;
                img.className = 'captured-image';

                cameraPreview.innerHTML = '';
                cameraPreview.appendChild(img);
                cameraOverlay.style.display = 'none';
            };
            reader.readAsDataURL(file);
        }

        // Check if photo is required
        function checkPhotoRequired() {
            if (!photoWarning || !statusButtons) return;

            const selected = statusButtons.querySelector('input[name="status"]:checked');
            const isPresent = selected && ['present', 'late'].includes(selected.value);

            if (isPresent) {
                photoWarning.classList.remove('d-none');
                photoWarning.classList.add('d-block');
                if (!capturedPhoto) {
                    photoInput.setAttribute('required', 'required');
                }
            } else {
                photoWarning.classList.add('d-none');
                photoWarning.classList.remove('d-block');
                photoInput.removeAttribute('required');
            }
        }

        // Prevent double submission
        attendanceForm.addEventListener('submit', function(e) {
            const selected = statusButtons?.querySelector('input[name="status"]:checked');
            const needsPhoto = selected && ['present', 'late'].includes(selected.value);

            if (needsPhoto && !capturedPhoto && !photoInput.files[0]) {
                e.preventDefault();
                alert('Harap ambil foto kehadiran terlebih dahulu');
                return;
            }

            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="bi bi-hourglass-split me-2"></i>Menyimpan...';
        });

        // Event listeners
        captureBtn?.addEventListener('click', capturePhoto);
        retakeBtn?.addEventListener('click', retakePhoto);
        statusButtons?.addEventListener('change', checkPhotoRequired);

        // Initialize
        initCamera();
        checkPhotoRequired();

        // Auto-calculate checkout time
        const checkInInput = document.getElementById('check_in_at');
        const checkOutInput = document.getElementById('check_out_at');

        checkInInput?.addEventListener('change', function() {
            if (checkInInput.value && !checkOutInput.value) {
                const inDate = new Date(checkInInput.value);
                inDate.setHours(inDate.getHours() + 3);
                const year = inDate.getFullYear();
                const month = String(inDate.getMonth() + 1).padStart(2, '0');
                const day = String(inDate.getDate()).padStart(2, '0');
                const hours = String(inDate.getHours()).padStart(2, '0');
                const minutes = String(inDate.getMinutes()).padStart(2, '0');
                checkOutInput.value = `${year}-${month}-${day}T${hours}:${minutes}`;
            }
        });
    });
</script>
