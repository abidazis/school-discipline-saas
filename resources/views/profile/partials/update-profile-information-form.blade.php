<section class="card mb-4">
    <div class="card-header">
        <h5 class="mb-0"><i class="bi bi-person me-2"></i>Informasi Profil</h5>
        <p class="text-muted small mb-0 mt-1">Perbarui informasi profil dan alamat email akun Anda.</p>
    </div>
    <div class="card-body">
        <form id="send-verification" method="post" action="{{ route('verification.send') }}">
            @csrf
        </form>

        <form method="post" action="{{ route('profile.update') }}">
            @csrf
            @method('patch')

            <div class="form-group">
                <label for="name" class="form-label">Nama</label>
                <input type="text" class="form-control @error('name') is-invalid @enderror"
                       id="name" name="name" value="{{ old('name', $user->name) }}"
                       required autofocus autocomplete="name">
                @error('name')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="email" class="form-label">Email</label>
                <input type="email" class="form-control @error('email') is-invalid @enderror"
                       id="email" name="email" value="{{ old('email', $user->email) }}"
                       required autocomplete="username">
                @error('email')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror

                @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                    <div class="alert alert-warning mt-2 mb-0">
                        <p class="small mb-1">Alamat email Anda belum diverifikasi.</p>
                        <button form="send-verification" class="btn btn-link p-0 text-decoration-none">
                            Klik di sini untuk mengirim ulang email verifikasi.
                        </button>
                    </div>
                @endif

                @if (session('status') === 'verification-link-sent')
                    <div class="alert alert-success mt-2 mb-0">
                        Tautan verifikasi baru telah dikirim ke alamat email Anda.
                    </div>
                @endif
            </div>

            @if(session('status') === 'profile-updated')
                <div class="alert alert-success" role="alert">
                    <i class="bi bi-check-circle me-2"></i>Profil berhasil diperbarui.
                </div>
            @endif

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-check-lg"></i>Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</section>
