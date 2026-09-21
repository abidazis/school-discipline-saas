<x-guest-layout>
    <x-slot name="title">Login</x-slot>

    <!-- Session Status -->
    <x-auth-session-status class="mb-3" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <!-- Email Address -->
        <div class="mb-3">
            <label for="email" class="form-label">{{ __('Email') }}</label>
            <input type="email" class="form-control @error('email') is-invalid @enderror"
                   id="email" name="email" value="{{ old('email') }}"
                   required autofocus autocomplete="username"
                   placeholder="name@example.com">
            @error('email')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <!-- Password -->
        <div class="mb-3">
            <label for="password" class="form-label">{{ __('Password') }}</label>
            <input type="password" class="form-control @error('password') is-invalid @enderror"
                   id="password" name="password"
                   required autocomplete="current-password"
                   placeholder="••••••••">
            @error('password')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <!-- Remember Me -->
        <div class="mb-4">
            <div class="form-check">
                <input type="checkbox" class="form-check-input" id="remember_me" name="remember">
                <label class="form-check-label" for="remember_me">
                    {{ __('Remember me') }}
                </label>
            </div>
        </div>

        <div class="d-grid gap-2">
            <button type="submit" class="btn btn-primary">
                {{ __('Log in') }}
            </button>
        </div>

        @if (Route::has('password.request'))
            <div class="text-center mt-3">
                <a class="text-decoration-none small" href="{{ route('password.request') }}">
                    {{ __('Forgot your password?') }}
                </a>
            </div>
        @endif
    </form>

    <div class="text-center mt-4 pt-4 border-top">
        <p class="text-muted small mb-0">
            Demo Accounts (password: <code>password</code>)
        </p>
        <div class="mt-2">
            <span class="badge bg-danger me-1">Super Admin</span>
            <code class="small">super@example.com</code>
        </div>
        <div class="mt-1">
            <span class="badge bg-primary me-1">School Admin</span>
            <code class="small">admin@smk1jkt.sch.id</code>
        </div>
        <div class="mt-1">
            <span class="badge bg-secondary me-1">Operator</span>
            <code class="small">operator@smk1jkt.sch.id</code>
        </div>
        <div class="mt-1">
            <span class="badge bg-info me-1">Teacher</span>
            <code class="small">guru.tkj@smk1jkt.sch.id</code>
        </div>
    </div>
</x-guest-layout>
