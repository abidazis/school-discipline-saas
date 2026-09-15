<section class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-white border-bottom">
        <h5 class="mb-0"><i class="bi bi-person me-2"></i>{{ __('Profile Information') }}</h5>
        <p class="text-muted small mb-0 mt-1">{{ __("Update your account's profile information and email address.") }}</p>
    </div>
    <div class="card-body">
        <form id="send-verification" method="post" action="{{ route('verification.send') }}">
            @csrf
        </form>

        <form method="post" action="{{ route('profile.update') }}">
            @csrf
            @method('patch')

            <div class="mb-3">
                <label for="name" class="form-label">{{ __('Name') }}</label>
                <input type="text" class="form-control @error('name') is-invalid @enderror"
                       id="name" name="name" value="{{ old('name', $user->name) }}"
                       required autofocus autocomplete="name">
                @error('name')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="email" class="form-label">{{ __('Email') }}</label>
                <input type="email" class="form-control @error('email') is-invalid @enderror"
                       id="email" name="email" value="{{ old('email', $user->email) }}"
                       required autocomplete="username">
                @error('email')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror

                @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                    <div class="alert alert-warning mt-2 mb-0 py-2">
                        <p class="small mb-1">{{ __('Your email address is unverified.') }}</p>
                        <button form="send-verification" class="btn btn-link p-0 text-decoration-none">
                            {{ __('Click here to re-send the verification email.') }}
                        </button>
                    </div>
                @endif

                @if (session('status') === 'verification-link-sent')
                    <div class="alert alert-success mt-2 mb-0 py-2">
                        {{ __('A new verification link has been sent to your email address.') }}
                    </div>
                @endif
            </div>

            @if(session('status') === 'profile-updated')
                <div class="alert alert-success py-2" role="alert">
                    {{ __('Saved.') }}
                </div>
            @endif

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-check-lg me-1"></i>{{ __('Save Changes') }}
                </button>
            </div>
        </form>
    </div>
</section>
