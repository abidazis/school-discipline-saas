<x-app-layout>
    <x-slot name="title">Create User</x-slot>

    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('users.index') }}">Users</a></li>
            <li class="breadcrumb-item active">Create</li>
        </ol>
    </nav>

    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="bi bi-person-plus me-2"></i>Create New User</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('users.store') }}">
                        @csrf

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="name" class="form-label">Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror"
                                       id="name" name="name" value="{{ old('name') }}"
                                       required autofocus placeholder="Enter full name">
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                                <input type="email" class="form-control @error('email') is-invalid @enderror"
                                       id="email" name="email" value="{{ old('email') }}"
                                       required placeholder="user@example.com">
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="password" class="form-label">Password <span class="text-danger">*</span></label>
                                <input type="password" class="form-control @error('password') is-invalid @enderror"
                                       id="password" name="password" required
                                       placeholder="Min. 8 characters">
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="password_confirmation" class="form-label">Confirm Password <span class="text-danger">*</span></label>
                                <input type="password" class="form-control"
                                       id="password_confirmation" name="password_confirmation" required
                                       placeholder="Confirm password">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="role" class="form-label">Role <span class="text-danger">*</span></label>
                                <select class="form-select @error('role') is-invalid @enderror" id="role" name="role" required>
                                    <option value="">Select Role</option>
                                    <option value="super_admin" {{ old('role') === 'super_admin' ? 'selected' : '' }}>
                                        Super Admin (Full System Access)
                                    </option>
                                    <option value="school_admin" {{ old('role') === 'school_admin' ? 'selected' : '' }}>
                                        School Admin (School Level Access)
                                    </option>
                                    <option value="operator" {{ old('role') === 'operator' ? 'selected' : '' }}>
                                        Operator (Data Entry)
                                    </option>
                                    <option value="teacher" {{ old('role') === 'teacher' ? 'selected' : '' }}>
                                        Teacher
                                    </option>
                                </select>
                                @error('role')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="school_id" class="form-label">School</label>
                                <select class="form-select @error('school_id') is-invalid @enderror" id="school_id" name="school_id">
                                    <option value="">No School (for Super Admin)</option>
                                    @foreach($schools as $school)
                                        <option value="{{ $school->id }}" {{ old('school_id') == $school->id ? 'selected' : '' }}>
                                            {{ $school->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('school_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Required for non-Super Admin roles</small>
                            </div>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-lg me-1"></i>Create User
                            </button>
                            <a href="{{ route('users.index') }}" class="btn btn-outline-secondary">
                                Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
