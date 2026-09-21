@extends('admin.layout')
@section('admin_title', 'Courses')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h5 class="fw-bold mb-0">Manage Courses</h5>
    <a href="{{ route('admin.courses.create') }}" class="btn btn-primary btn-sm"><i class="bi bi-plus-circle me-1"></i>Add Course</a>
</div>
<div class="card border-0 shadow-sm rounded-3">
    <div class="card-body p-0">
        <div class="table-responsive">
        <table class="table table-hover mb-0 align-middle">
            <thead class="table-light"><tr>
                <th class="ps-4">Code</th><th>Title</th><th>Instructor</th><th>Credits</th><th>Schedule</th><th>Room</th><th>Enrolled</th><th class="pe-4">Actions</th>
            </tr></thead>
            <tbody>
            @forelse($courses as $c)
            <tr>
                <td class="ps-4"><span class="badge bg-primary bg-opacity-15 text-primary fw-bold">{{ $c->course_code }}</span></td>
                <td class="fw-semibold">{{ $c->title }}</td>
                <td class="text-muted small">{{ $c->instructor ?? '—' }}</td>
                <td class="text-muted">{{ $c->credits }}</td>
                <td class="small text-muted">{{ $c->schedule_days ? $c->schedule_days.' · '.$c->schedule_time : '—' }}</td>
                <td class="text-muted small">{{ $c->room ?? '—' }}</td>
                <td><span class="badge bg-light text-dark border">{{ $c->enrollments_count }}</span></td>
                <td class="pe-4">
                    <a href="{{ route('admin.courses.edit', $c->id) }}" class="btn btn-sm btn-outline-secondary me-1"><i class="bi bi-pencil"></i></a>
                    <form method="POST" action="{{ route('admin.courses.destroy', $c->id) }}" class="d-inline" onsubmit="return confirm('Delete {{ $c->title }}?')">
                        @csrf<button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                    </form>
                </td>
            </tr>
            @empty
            <tr><td colspan="8" class="text-center text-muted py-4">No courses found.</td></tr>
            @endforelse
            </tbody>
        </table>
        </div>
    </div>
</div>
@endsection
