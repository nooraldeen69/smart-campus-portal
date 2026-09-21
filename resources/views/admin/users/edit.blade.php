@extends('admin.layout')
@section('admin_title', 'Edit User')
@section('content')
<div class="d-flex align-items-center gap-3 mb-4">
    <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary btn-sm"><i class="bi bi-arrow-left"></i></a>
    <h5 class="fw-bold mb-0">Edit User — {{ $user->name }}</h5>
</div>
<div class="card border-0 shadow-sm rounded-3" style="max-width:600px">
    <div class="card-body p-4">
        <form method="POST" action="{{ route('admin.users.update', $user->id) }}">
            @csrf
            <div class="mb-3">
                <label class="form-label fw-semibold">Full Name</label>
                <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $user->name) }}" required>
                @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="mb-3">
                <label class="form-label fw-semibold">Email</label>
                <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $user->email) }}" required>
                @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="row mb-3">
                <div class="col">
                    <label class="form-label fw-semibold">University ID</label>
                    <input type="text" name="university_id" class="form-control" value="{{ old('university_id', $user->university_id) }}">
                </div>
                <div class="col">
                    <label class="form-label fw-semibold">Role</label>
                    <select name="role" class="form-select">
                        <option value="student" {{ old('role',$user->role)=='student'?'selected':'' }}>Student</option>
                        <option value="admin" {{ old('role',$user->role)=='admin'?'selected':'' }}>Admin</option>
                    </select>
                </div>
            </div>
            <div class="row mb-3">
                <div class="col">
                    <label class="form-label fw-semibold">Department</label>
                    <input type="text" name="department" class="form-control" value="{{ old('department', $user->department) }}">
                </div>
                <div class="col">
                    <label class="form-label fw-semibold">Phone</label>
                    <input type="text" name="phone" class="form-control" value="{{ old('phone', $user->phone) }}">
                </div>
            </div>
            <div class="mb-4">
                <label class="form-label fw-semibold">New Password <span class="text-muted small fw-normal">(leave blank to keep current)</span></label>
                <input type="password" name="password" class="form-control">
            </div>
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary"><i class="bi bi-check-circle me-1"></i>Save Changes</button>
                <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
