<section class="card mb-4 border-danger">
    <div class="card-header">
        <h5 class="mb-0 text-danger"><i class="bi bi-exclamation-triangle me-2"></i>Hapus Akun</h5>
        <p class="text-muted small mb-0 mt-1">
            Setelah akun Anda dihapus, semua sumber daya dan datanya akan dihapus secara permanen. Sebelum menghapus akun Anda, silakan unduh data atau informasi yang ingin Anda pertahankan.
        </p>
    </div>
    <div class="card-body">
        <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deleteAccountModal">
            <i class="bi bi-trash"></i>Hapus Akun
        </button>
    </div>
</section>

<!-- Delete Account Modal -->
<div class="modal fade" id="deleteAccountModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Hapus Akun</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="post" action="{{ route('profile.destroy') }}">
                @csrf
                @method('delete')
                <div class="modal-body">
                    <p>Apakah Anda yakin ingin menghapus akun Anda?</p>
                    <p class="text-muted small">Setelah akun Anda dihapus, semua sumber daya dan datanya akan dihapus secara permanen. Silakan masukkan password Anda untuk mengonfirmasi.</p>

                    <div class="form-group">
                        <label for="delete_password" class="form-label">Password</label>
                        <input type="password" class="form-control @error('password', 'userDeletion') is-invalid @enderror"
                               id="delete_password" name="password"
                               autocomplete="current-password">
                        @error('password', 'userDeletion')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger">Hapus Akun</button>
                </div>
            </form>
        </div>
    </div>
</div>
