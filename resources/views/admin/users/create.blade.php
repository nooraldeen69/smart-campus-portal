@extends('admin.layout')
@section('admin_title', 'Add User')
@section('content')
<div class="d-flex align-items-center gap-3 mb-4">
    <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary btn-sm"><i class="bi bi-arrow-left"></i></a>
    <h5 class="fw-bold mb-0">Add New User</h5>
</div>
<div class="card border-0 shadow-sm rounded-3" style="max-width:600px">
    <div class="card-body p-4">
        <form method="POST" action="{{ route('admin.users.store') }}">
            @csrf
            <div class="mb-3">
                <label class="form-label fw-semibold">Full Name</label>
                <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" required>
                @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="mb-3">
                <label class="form-label fw-semibold">Email</label>
                <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}" required>
                @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="row mb-3">
                <div class="col">
                    <label class="form-label fw-semibold">University ID</label>
                    <input type="text" name="university_id" class="form-control @error('university_id') is-invalid @enderror" value="{{ old('university_id') }}" required>
                    @error('university_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col">
                    <label class="form-label fw-semibold">Role</label>
                    <select name="role" class="form-select">
                        <option value="student" {{ old('role')=='student'?'selected':'' }}>Student</option>
                        <option value="admin" {{ old('role')=='admin'?'selected':'' }}>Admin</option>
                    </select>
                </div>
            </div>
            <div class="mb-3">
                <label class="form-label fw-semibold">Department</label>
                <input type="text" name="department" class="form-control" value="{{ old('department') }}" placeholder="e.g. Information Technology">
            </div>
            <div class="mb-4">
                <label class="form-label fw-semibold">Password</label>
                <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" required>
                @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary"><i class="bi bi-check-circle me-1"></i>Create User</button>
                <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
