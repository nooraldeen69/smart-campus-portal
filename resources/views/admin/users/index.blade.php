@extends('admin.layout')
@section('admin_title', 'Users')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h5 class="fw-bold mb-0">Manage Users</h5>
    <a href="{{ route('admin.users.create') }}" class="btn btn-primary btn-sm"><i class="bi bi-person-plus me-1"></i>Add User</a>
</div>
<div class="card border-0 shadow-sm rounded-3">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0 align-middle">
                <thead class="table-light"><tr>
                    <th class="ps-4">Name</th><th>University ID</th><th>Email</th><th>Role</th><th>Department</th><th class="pe-4">Actions</th>
                </tr></thead>
                <tbody>
                @forelse($users as $u)
                <tr>
                    <td class="ps-4 fw-semibold">{{ $u->name }}</td>
                    <td class="text-muted">{{ $u->university_id }}</td>
                    <td class="text-muted small">{{ $u->email }}</td>
                    <td><span class="badge {{ $u->role === 'admin' ? 'bg-danger' : 'bg-primary' }}">{{ ucfirst($u->role) }}</span></td>
                    <td class="text-muted small">{{ $u->department ?? '—' }}</td>
                    <td class="pe-4">
                        <a href="{{ route('admin.users.edit', $u->id) }}" class="btn btn-sm btn-outline-secondary me-1"><i class="bi bi-pencil"></i></a>
                        <form method="POST" action="{{ route('admin.users.destroy', $u->id) }}" class="d-inline" onsubmit="return confirm('Delete {{ $u->name }}?')">
                            @csrf
                            <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" class="text-center text-muted py-4">No users found.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($users->hasPages())
    <div class="card-footer bg-white border-0 py-3">{{ $users->links() }}</div>
    @endif
</div>
@endsection
