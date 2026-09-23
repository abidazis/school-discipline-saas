<x-app-layout>
    <x-slot name="title">Tambah Tahun Ajaran</x-slot>

    <!-- Breadcrumb -->
    <nav class="breadcrumb-nav mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('academic-years.index') }}"><i class="bi bi-calendar-range"></i> Tahun Ajaran</a></li>
            <li class="breadcrumb-item active"><i class="bi bi-plus-circle"></i> Tambah</li>
        </ol>
    </nav>

    <!-- Form Card -->
    <div class="form-card">
        <div class="form-card-header">
            <div class="form-card-icon">
                <i class="bi bi-calendar-plus"></i>
            </div>
            <div class="form-card-title">
                <h5>Tambah Tahun Ajaran</h5>
                <p>Buat tahun ajaran baru untuk sekolah</p>
            </div>
        </div>
        <div class="form-card-body">
            <form method="POST" action="{{ route('academic-years.store') }}">
                @csrf

                <!-- Name Field -->
                <div class="form-group">
                    <label for="name" class="form-label">
                        <i class="bi bi-tag"></i>
                        Nama Tahun Ajaran <span class="required">*</span>
                    </label>
                    <input type="text" class="form-control @error('name') is-invalid @enderror"
                           id="name" name="name" value="{{ old('name') }}"
                           required autofocus placeholder="2026/2027">
                    @error('name')
                        <div class="error-message"><i class="bi bi-exclamation-circle"></i> {{ $message }}</div>
                    @enderror
                    <small class="form-hint"><i class="bi bi-info-circle"></i> Format: TAHUN/TAHUN+1 (contoh: 2026/2027)</small>
                </div>

                <!-- Date Fields -->
                <div class="form-row">
                    <div class="form-group">
                        <label for="start_date" class="form-label">
                            <i class="bi bi-calendar-event"></i>
                            Tanggal Mulai <span class="required">*</span>
                        </label>
                        <input type="date" class="form-control @error('start_date') is-invalid @enderror"
                               id="start_date" name="start_date" value="{{ old('start_date') }}"
                               required>
                        @error('start_date')
                            <div class="error-message"><i class="bi bi-exclamation-circle"></i> {{ $message }}</div>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label for="end_date" class="form-label">
                            <i class="bi bi-calendar-check"></i>
                            Tanggal Selesai <span class="required">*</span>
                        </label>
                        <input type="date" class="form-control @error('end_date') is-invalid @enderror"
                               id="end_date" name="end_date" value="{{ old('end_date') }}"
                               required>
                        @error('end_date')
                            <div class="error-message"><i class="bi bi-exclamation-circle"></i> {{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- Active Toggle -->
                <div class="form-group">
                    <div class="toggle-card">
                        <div class="toggle-content">
                            <label for="is_active" class="toggle-label">
                                <div class="toggle-switch">
                                    <input type="checkbox" id="is_active" name="is_active" value="1"
                                           {{ old('is_active') ? 'checked' : '' }}>
                                    <span class="toggle-slider"></span>
                                </div>
                                <div class="toggle-text">
                                    <span class="toggle-title">Jadikan tahun ajaran aktif</span>
                                    <span class="toggle-desc">Hanya satu tahun ajaran yang dapat aktif pada satu waktu</span>
                                </div>
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-lg"></i>Simpan
                    </button>
                    <a href="{{ route('academic-years.index') }}" class="btn btn-secondary">
                        <i class="bi bi-arrow-left"></i>Kembali
                    </a>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>

<style>
    /* Breadcrumb */
    .breadcrumb-nav {
        font-size: 14px;
    }

    .breadcrumb {
        display: flex;
        align-items: center;
        flex-wrap: wrap;
        gap: 6px;
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .breadcrumb-item {
        display: flex;
        align-items: center;
        gap: 6px;
        color: var(--gray-500);
    }

    .breadcrumb-item::before {
        content: '›';
        color: var(--gray-400);
    }

    .breadcrumb-item:first-child::before {
        display: none;
    }

    .breadcrumb-item a {
        display: flex;
        align-items: center;
        gap: 6px;
        color: var(--primary);
        text-decoration: none;
        transition: all 0.2s;
    }

    .breadcrumb-item a:hover {
        color: var(--primary-dark);
    }

    .breadcrumb-item a i {
        font-size: 14px;
    }

    .breadcrumb-item.active {
        color: var(--gray-600);
        font-weight: 500;
    }

    .breadcrumb-item.active::before {
        color: var(--gray-400);
    }

    /* Form Card */
    .form-card {
        background: #fff;
        border: 1px solid var(--gray-200);
        border-radius: 16px;
        overflow: hidden;
        max-width: 700px;
    }

    .form-card-header {
        display: flex;
        align-items: center;
        gap: 16px;
        padding: 24px;
        background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
        color: white;
    }

    .form-card-icon {
        width: 56px;
        height: 56px;
        background: rgba(255, 255, 255, 0.2);
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 26px;
        flex-shrink: 0;
    }

    .form-card-title h5 {
        font-size: 18px;
        font-weight: 700;
        margin: 0 0 4px 0;
        color: white;
    }

    .form-card-title p {
        font-size: 13px;
        margin: 0;
        opacity: 0.9;
        color: rgba(255, 255, 255, 0.9);
    }

    .form-card-body {
        padding: 28px;
    }

    /* Form Group */
    .form-group {
        margin-bottom: 24px;
    }

    .form-label {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 14px;
        font-weight: 600;
        color: var(--gray-700);
        margin-bottom: 10px;
    }

    .form-label i {
        font-size: 16px;
        color: var(--primary);
    }

    .required {
        color: var(--danger);
    }

    .form-control {
        width: 100%;
        padding: 12px 16px;
        font-size: 14px;
        border: 2px solid var(--gray-200);
        border-radius: 10px;
        transition: all 0.2s;
        background: #fff;
    }

    .form-control:focus {
        outline: none;
        border-color: var(--primary);
        box-shadow: 0 0 0 4px rgba(79, 70, 229, 0.1);
    }

    .form-control.is-invalid {
        border-color: var(--danger);
    }

    .form-control::placeholder {
        color: var(--gray-400);
    }

    .form-hint {
        display: flex;
        align-items: center;
        gap: 6px;
        font-size: 12px;
        color: var(--gray-500);
        margin-top: 8px;
    }

    .form-hint i {
        color: var(--info);
    }

    .error-message {
        display: flex;
        align-items: center;
        gap: 6px;
        font-size: 13px;
        color: var(--danger);
        margin-top: 8px;
    }

    /* Form Row */
    .form-row {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 20px;
    }

    /* Toggle Card */
    .toggle-card {
        background: var(--gray-50);
        border: 1px solid var(--gray-200);
        border-radius: 12px;
        padding: 16px;
    }

    .toggle-label {
        display: flex;
        align-items: center;
        gap: 16px;
        cursor: pointer;
        margin: 0;
    }

    .toggle-switch {
        position: relative;
        width: 52px;
        height: 28px;
        flex-shrink: 0;
    }

    .toggle-switch input {
        opacity: 0;
        width: 0;
        height: 0;
    }

    .toggle-slider {
        position: absolute;
        cursor: pointer;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: var(--gray-300);
        border-radius: 28px;
        transition: 0.3s;
    }

    .toggle-slider::before {
        position: absolute;
        content: "";
        height: 22px;
        width: 22px;
        left: 3px;
        bottom: 3px;
        background-color: white;
        border-radius: 50%;
        transition: 0.3s;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
    }

    .toggle-switch input:checked + .toggle-slider {
        background-color: var(--primary);
    }

    .toggle-switch input:checked + .toggle-slider::before {
        transform: translateX(24px);
    }

    .toggle-text {
        display: flex;
        flex-direction: column;
        gap: 2px;
    }

    .toggle-title {
        font-size: 14px;
        font-weight: 600;
        color: var(--gray-700);
    }

    .toggle-desc {
        font-size: 12px;
        color: var(--gray-500);
    }

    /* Form Actions */
    .form-actions {
        display: flex;
        gap: 12px;
        margin-top: 32px;
        padding-top: 24px;
        border-top: 1px solid var(--gray-100);
    }

    .btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        font-size: 14px;
        font-weight: 600;
        padding: 12px 24px;
        border-radius: 10px;
        border: none;
        cursor: pointer;
        transition: all 0.2s;
        text-decoration: none;
    }

    .btn i {
        font-size: 16px;
    }

    .btn-primary {
        background: var(--primary);
        color: white;
    }

    .btn-primary:hover {
        background: var(--primary-dark);
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(79, 70, 229, 0.3);
    }

    .btn-secondary {
        background: var(--gray-100);
        color: var(--gray-600);
    }

    .btn-secondary:hover {
        background: var(--gray-200);
        transform: translateY(-2px);
    }

    /* Responsive */
    @media (max-width: 768px) {
        .form-card {
            border-radius: 12px;
        }

        .form-card-header {
            flex-direction: column;
            text-align: center;
            padding: 20px;
        }

        .form-card-icon {
            width: 48px;
            height: 48px;
            font-size: 22px;
        }

        .form-card-body {
            padding: 20px;
        }

        .form-row {
            grid-template-columns: 1fr;
            gap: 16px;
        }

        .form-actions {
            flex-direction: column;
        }

        .form-actions .btn {
            width: 100%;
        }
    }

    @media (max-width: 480px) {
        .breadcrumb {
            font-size: 12px;
        }

        .form-card-header {
            padding: 16px;
        }

        .form-card-body {
            padding: 16px;
        }

        .form-label {
            font-size: 13px;
        }

        .form-control {
            padding: 10px 14px;
            font-size: 14px;
        }

        .toggle-card {
            padding: 12px;
        }
    }
</style>
