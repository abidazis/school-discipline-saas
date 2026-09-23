<section class="card mb-4">
    <div class="card-header">
        <h5 class="mb-0"><i class="bi bi-key me-2"></i>Update Password</h5>
        <p class="text-muted small mb-0 mt-1">Pastikan akun Anda menggunakan password yang panjang dan acak untuk keamanan.</p>
    </div>
    <div class="card-body">
        <form method="post" action="{{ route('password.update') }}">
            @csrf
            @method('put')

            <div class="form-group">
                <label for="update_password_current_password" class="form-label">Password Saat Ini</label>
                <input type="password" class="form-control @error('current_password', 'updatePassword') is-invalid @enderror"
                       id="update_password_current_password" name="current_password"
                       autocomplete="current-password">
                @error('current_password', 'updatePassword')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="update_password_password" class="form-label">Password Baru</label>
                <input type="password" class="form-control @error('password', 'updatePassword') is-invalid @enderror"
                       id="update_password_password" name="password"
                       autocomplete="new-password">
                @error('password', 'updatePassword')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="update_password_password_confirmation" class="form-label">Konfirmasi Password Baru</label>
                <input type="password" class="form-control @error('password_confirmation', 'updatePassword') is-invalid @enderror"
                       id="update_password_password_confirmation" name="password_confirmation"
                       autocomplete="new-password">
                @error('password_confirmation', 'updatePassword')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
            </div>

            @if (session('status') === 'password-updated')
                <div class="alert alert-success" role="alert">
                    <i class="bi bi-check-circle me-2"></i>Password berhasil diperbarui.
                </div>
            @endif

            <button type="submit" class="btn btn-primary">
                <i class="bi bi-check-lg"></i>Update Password
            </button>
        </form>
    </div>
</section>
