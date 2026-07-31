@extends('admin.layouts.app')
@section('title','New User')
@section('page-title','New User')

@section('content')
<div class="page-head">
    <div>
        <h4>Create User</h4>
        <div class="sub">Add a new member to the admin panel</div>
    </div>
    <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left me-1"></i>Back
    </a>
</div>

<div class="row justify-content-center">
    <div class="col-lg-6">
        <form action="{{ route('admin.users.store') }}" method="POST">
            @csrf

            <div class="panel mb-3">
                <div class="panel-body">
                    <div class="form-section-title"><i class="bi bi-person"></i>Account</div>

                    <div class="mb-3">
                        <label class="form-label">Full Name <span class="req">*</span></label>
                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                               value="{{ old('name') }}" required>
                        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-0">
                        <label class="form-label">Email Address <span class="req">*</span></label>
                        <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                               value="{{ old('email') }}" required>
                        @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
            </div>

            <div class="panel mb-3">
                <div class="panel-body">
                    <div class="form-section-title"><i class="bi bi-shield-lock"></i>Password</div>

                    <div class="mb-3">
                        <label class="form-label">Password <span class="req">*</span></label>
                        <input type="password" name="password" class="form-control @error('password') is-invalid @enderror"
                               autocomplete="new-password" required>
                        @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-0">
                        <label class="form-label">Confirm Password <span class="req">*</span></label>
                        <input type="password" name="password_confirmation" class="form-control"
                               autocomplete="new-password" required>
                    </div>
                </div>
            </div>

            <div class="panel mb-3">
                <div class="panel-body">
                    <div class="form-section-title"><i class="bi bi-key"></i>Permissions</div>

                    <div class="mb-3">
                        <label class="form-label">Role <span class="req">*</span></label>
                        <select name="role" class="form-select @error('role') is-invalid @enderror" required>
                            <option value="author" {{ old('role')==='author' ? 'selected' : '' }}>Author</option>
                            <option value="editor" {{ old('role')==='editor' ? 'selected' : '' }}>Editor</option>
                            <option value="admin" {{ old('role')==='admin' ? 'selected' : '' }}>Admin</option>
                        </select>
                        @error('role')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="form-check form-switch mb-0">
                        <input class="form-check-input" type="checkbox" role="switch" name="is_active" id="is_active" value="1"
                               {{ old('is_active', true) ? 'checked' : '' }}>
                        <label class="form-check-label" for="is_active">Account Active</label>
                    </div>
                </div>
            </div>

            <div class="d-flex gap-2 justify-content-end">
                <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-person-plus me-1"></i>Create User
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
