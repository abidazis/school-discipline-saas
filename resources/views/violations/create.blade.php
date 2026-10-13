<x-app-layout>
    <x-slot name="title">Catat Pelanggaran</x-slot>

    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active">Catat Pelanggaran</li>
        </ol>
    </nav>

    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <div class="card">
                <div class="card-header bg-warning text-dark">
                    <h5 class="mb-0"><i class="bi bi-clipboard-plus me-2"></i>Catat Pelanggaran Baru</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('violations.store') }}" enctype="multipart/form-data" id="violationForm">
                        @csrf

                        <!-- Section: Siswa -->
                        <div class="mb-4">
                            <label for="student_id" class="form-label fw-bold">
                                <i class="bi bi-person me-1"></i>Siswa <span class="text-danger">*</span>
                            </label>
                            <div class="position-relative">
                                <input type="text" class="form-control" id="studentSearch" placeholder="Ketik nama atau NIS siswa..." autocomplete="off">
                                <input type="hidden" name="student_id" id="student_id" value="{{ old('student_id') }}" required>
                                <div id="studentDropdown" class="dropdown-menu w-100 show" style="display: none; max-height: 200px; overflow-y: auto;"></div>
                            </div>
                            @error('student_id')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                            <div id="selectedStudentInfo" class="small text-muted mt-1">
                                @if(old('student_id'))
                                    <span class="text-success">Siswa terpilih: {{ old('student_id') }}</span>
                                @endif
                            </div>
                        </div>

                        <!-- Section: Jenis Pelanggaran -->
                        <div class="mb-4">
                            <label for="violation_type_id" class="form-label fw-bold">
                                <i class="bi bi-exclamation-triangle me-1"></i>Jenis Pelanggaran <span class="text-danger">*</span>
                            </label>
                            <select class="form-select form-select-lg @error('violation_type_id') is-invalid @enderror"
                                    id="violation_type_id" name="violation_type_id" required
                                    onchange="updatePoints()">
                                <option value="">-- Pilih Jenis Pelanggaran --</option>
                                @foreach($violationTypes->groupBy('category') as $category => $types)
                                    <optgroup label="{{ $category }}">
                                        @foreach($types as $type)
                                            <option value="{{ $type->id }}"
                                                    data-points="{{ $type->points }}"
                                                    data-severity="{{ $type->severity }}"
                                                    data-category="{{ $type->category }}"
                                                    {{ old('violation_type_id') == $type->id ? 'selected' : '' }}>
                                                {{ $type->code }} - {{ $type->name }} ({{ $type->points }} poin)
                                            </option>
                                        @endforeach
                                    </optgroup>
                                @endforeach
                            </select>
                            @error('violation_type_id')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Section: Waktu & Lokasi -->
                        <div class="row mb-4">
                            <div class="col-12 col-md-6">
                                <label for="occurred_at" class="form-label fw-bold">
                                    <i class="bi bi-calendar-event me-1"></i>Tanggal & Waktu <span class="text-danger">*</span>
                                </label>
                                <input type="datetime-local"
                                       class="form-control @error('occurred_at') is-invalid @enderror"
                                       id="occurred_at"
                                       name="occurred_at"
                                       value="{{ old('occurred_at', now()->format('Y-m-d\TH:i')) }}"
                                       required>
                                @error('occurred_at')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-12 col-md-6">
                                <label for="location" class="form-label fw-bold">
                                    <i class="bi bi-geo-alt me-1"></i>Lokasi
                                </label>
                                <input type="text"
                                       class="form-control"
                                       id="location"
                                       name="location"
                                       value="{{ old('location') }}"
                                       placeholder="Contoh: Gerbang Utama">
                            </div>
                        </div>

                        <!-- Section: Points Display -->
                        <div class="alert alert-warning d-flex align-items-center mb-4" role="alert">
                            <i class="bi bi-info-circle-fill me-2"></i>
                            <div>
                                <strong>Point Pelanggaran:</strong>
                                <span id="pointsDisplay" class="badge bg-warning text-dark fs-6 ms-2">-</span>
                            </div>
                        </div>

                        <!-- Section: Keterangan -->
                        <div class="mb-4">
                            <label for="description" class="form-label fw-bold">
                                <i class="bi bi-card-text me-1"></i>Keterangan / Kronologi
                            </label>
                            <textarea class="form-control"
                                      id="description"
                                      name="description"
                                      rows="3"
                                      placeholder="Jelaskan kronologi singkat kejadian...">{{ old('description') }}</textarea>
                        </div>

                        <!-- Section: Upload Foto -->
                        <div class="mb-4">
                            <label class="form-label fw-bold">
                                <i class="bi bi-camera me-1"></i>Bukti Foto
                            </label>
                            <div class="border rounded p-3 bg-light">
                                <input type="file"
                                       class="form-control @error('evidences.*') is-invalid @enderror"
                                       id="evidences"
                                       name="evidences[]"
                                       accept="image/jpeg,image/png,image/webp"
                                       multiple
                                       onchange="previewImages(this)">
                                <div class="form-text">
                                    <i class="bi bi-info-circle me-1"></i>Maks 5MB per file. Format: JPG, PNG, WebP. Maks 5 foto.
                                </div>
                                @error('evidences.*')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            <div id="imagePreview" class="d-flex flex-wrap gap-2 mt-3"></div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="d-flex flex-column flex-sm-row gap-2">
                            <button type="submit" class="btn btn-warning btn-lg flex-grow-1" id="submitBtn">
                                <i class="bi bi-check-lg me-2"></i>Simpan Pelanggaran
                            </button>
                            <a href="{{ route('violations.index') }}" class="btn btn-outline-secondary btn-lg">
                                <i class="bi bi-arrow-left me-2"></i>Kembali
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
    // Students data for search
    const students = @json($students);

    // Search functionality
    const studentSearch = document.getElementById('studentSearch');
    const studentDropdown = document.getElementById('studentDropdown');
    const studentIdInput = document.getElementById('student_id');
    const selectedStudentInfo = document.getElementById('selectedStudentInfo');

    studentSearch.addEventListener('input', function() {
        const query = this.value.toLowerCase().trim();

        if (query.length < 2) {
            studentDropdown.style.display = 'none';
            return;
        }

        const filtered = students.filter(s =>
            s.full_name.toLowerCase().includes(query) ||
            s.nis.toLowerCase().includes(query)
        ).slice(0, 10);

        if (filtered.length === 0) {
            studentDropdown.innerHTML = '<div class="dropdown-item text-muted">Tidak ditemukan</div>';
        } else {
            studentDropdown.innerHTML = filtered.map(s => `
                <button type="button" class="dropdown-item" onclick="selectStudent(${s.id}, '${s.full_name}', '${s.nis}', '${s.school_class?.full_name || 'Tanpa Kelas'}')">
                    <strong>${s.full_name}</strong>
                    <br><small class="text-muted">NIS: ${s.nis} - ${s.school_class?.full_name || 'Tanpa Kelas'}</small>
                </button>
            `).join('');
        }

        studentDropdown.style.display = 'block';
    });

    function selectStudent(id, name, nis, className) {
        studentIdInput.value = id;
        studentSearch.value = `${name} (${nis})`;
        selectedStudentInfo.innerHTML = `<span class="text-success"><i class="bi bi-check-circle me-1"></i>Terpilih: ${name} - Kelas ${className}</span>`;
        studentDropdown.style.display = 'none';

        // Trigger validation
        studentSearch.classList.remove('is-invalid');
    }

    // Close dropdown when clicking outside
    document.addEventListener('click', function(e) {
        if (!studentSearch.contains(e.target) && !studentDropdown.contains(e.target)) {
            studentDropdown.style.display = 'none';
        }
    });

    // Points display
    function updatePoints() {
        const select = document.getElementById('violation_type_id');
        const option = select.options[select.selectedIndex];
        const pointsDisplay = document.getElementById('pointsDisplay');

        if (option && option.dataset.points) {
            pointsDisplay.textContent = option.dataset.points + ' Poin';
        } else {
            pointsDisplay.textContent = '-';
        }
    }

    // Initialize points on load
    updatePoints();

    // Image preview
    function previewImages(input) {
        const preview = document.getElementById('imagePreview');
        preview.innerHTML = '';

        if (input.files) {
            const maxFiles = 5;
            const maxSize = 5 * 1024 * 1024; // 5MB

            Array.from(input.files).slice(0, maxFiles).forEach((file, index) => {
                if (file.size > maxSize) {
                    alert(`File "${file.name}" terlalu besar. Maks 5MB.`);
                    return;
                }

                const reader = new FileReader();
                reader.onload = function(e) {
                    const div = document.createElement('div');
                    div.className = 'position-relative';
                    div.style.width = '100px';
                    div.style.height = '100px';
                    div.innerHTML = `
                        <img src="${e.target.result}" class="img-thumbnail" style="width: 100px; height: 100px; object-fit: cover;">
                        <button type="button" class="btn btn-sm btn-danger position-absolute top-0 end-0" onclick="this.parentElement.remove()">
                            <i class="bi bi-x"></i>
                        </button>
                    `;
                    preview.appendChild(div);
                };
                reader.readAsDataURL(file);
            });
        }
    }

    // Form submission - prevent double submit
    document.getElementById('violationForm').addEventListener('submit', function() {
        const btn = document.getElementById('submitBtn');
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Menyimpan...';
    });
    </script>
</x-app-layout>
