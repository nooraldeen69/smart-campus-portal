@extends('admin.layout')
@section('admin_title', 'Dashboard')

@section('content')
<div class="row g-4 mb-4">
    <div class="col-6 col-lg-3">
        <div class="card border-0 shadow-sm rounded-3 h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="rounded-3 bg-primary bg-opacity-15 p-3"><i class="bi bi-people-fill fs-4 text-primary"></i></div>
                <div><div class="fs-2 fw-bold">{{ $stats['users'] }}</div><div class="text-muted small">Students</div></div>
            </div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="card border-0 shadow-sm rounded-3 h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="rounded-3 bg-info bg-opacity-15 p-3"><i class="bi bi-laptop-fill fs-4 text-info"></i></div>
                <div><div class="fs-2 fw-bold">{{ $stats['courses'] }}</div><div class="text-muted small">Courses</div></div>
            </div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="card border-0 shadow-sm rounded-3 h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="rounded-3 bg-secondary bg-opacity-15 p-3"><i class="bi bi-book-fill fs-4 text-secondary"></i></div>
                <div><div class="fs-2 fw-bold">{{ $stats['books'] }}</div><div class="text-muted small">Books</div></div>
            </div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="card border-0 shadow-sm rounded-3 h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="rounded-3 bg-warning bg-opacity-15 p-3"><i class="bi bi-bookmark-fill fs-4 text-warning"></i></div>
                <div><div class="fs-2 fw-bold">{{ $stats['active_loans'] }}</div><div class="text-muted small">Active Loans</div></div>
            </div>
        </div>
    </div>
</div>

@if($stats['overdue_loans'] > 0)
<div class="alert alert-danger d-flex align-items-center gap-2 rounded-3 mb-4">
    <i class="bi bi-exclamation-triangle-fill fs-5"></i>
    <div><strong>{{ $stats['overdue_loans'] }} overdue book(s)</strong> — students need to return them.</div>
</div>
@endif

<div class="card border-0 shadow-sm rounded-3">
    <div class="card-header bg-white border-0 py-3">
        <h6 class="fw-bold mb-0">Recent Course Enrollments</h6>
    </div>
    <div class="card-body p-0">
        @if($recentEnrollments->count() > 0)
        <table class="table table-hover mb-0 align-middle">
            <thead class="table-light">
                <tr><th class="ps-4">Student</th><th>Course</th><th>Enrolled At</th></tr>
            </thead>
            <tbody>
                @foreach($recentEnrollments as $e)
                <tr>
                    <td class="ps-4">
                        <div class="fw-semibold">{{ $e->user->name ?? 'N/A' }}</div>
                        <div class="text-muted small">{{ $e->user->university_id ?? '' }}</div>
                    </td>
                    <td>{{ $e->course->title ?? 'N/A' }} <span class="badge bg-light text-muted border small ms-1">{{ $e->course->course_code ?? '' }}</span></td>
                    <td class="text-muted small">{{ $e->created_at->diffForHumans() }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @else
        <p class="text-muted p-4 mb-0">No recent enrollments.</p>
        @endif
    </div>
</div>
@endsection
