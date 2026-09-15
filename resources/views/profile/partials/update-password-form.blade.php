<section class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-white border-bottom">
        <h5 class="mb-0"><i class="bi bi-key me-2"></i>{{ __('Update Password') }}</h5>
        <p class="text-muted small mb-0 mt-1">{{ __('Ensure your account is using a long, random password to stay secure.') }}</p>
    </div>
    <div class="card-body">
        <form method="post" action="{{ route('password.update') }}">
            @csrf
            @method('put')

            <div class="mb-3">
                <label for="update_password_current_password" class="form-label">{{ __('Current Password') }}</label>
                <input type="password" class="form-control @error('current_password', 'updatePassword') is-invalid @enderror"
                       id="update_password_current_password" name="current_password"
                       autocomplete="current-password">
                @error('current_password', 'updatePassword')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="update_password_password" class="form-label">{{ __('New Password') }}</label>
                <input type="password" class="form-control @error('password', 'updatePassword') is-invalid @enderror"
                       id="update_password_password" name="password"
                       autocomplete="new-password">
                @error('password', 'updatePassword')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="update_password_password_confirmation" class="form-label">{{ __('Confirm Password') }}</label>
                <input type="password" class="form-control @error('password_confirmation', 'updatePassword') is-invalid @enderror"
                       id="update_password_password_confirmation" name="password_confirmation"
                       autocomplete="new-password">
                @error('password_confirmation', 'updatePassword')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            @if (session('status') === 'password-updated')
                <div class="alert alert-success py-2" role="alert">
                    {{ __('Password updated successfully.') }}
                </div>
            @endif

            <button type="submit" class="btn btn-primary">
                <i class="bi bi-check-lg me-1"></i>{{ __('Update Password') }}
            </button>
        </form>
    </div>
</section>
