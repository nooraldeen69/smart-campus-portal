@extends('layouts.app')

@section('title', 'My Courses - LMS')

@section('content')
<div class="container-fluid pt-2 pb-5">

    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h2 class="fw-bold mb-0"><i class="bi bi-laptop text-info me-2"></i>My Courses</h2>
            <p class="text-muted mb-0">{{ $enrollments->count() }} courses enrolled this semester</p>
        </div>
        <a href="{{ route('sis.registration') }}" class="btn btn-outline-primary btn-sm">
            <i class="bi bi-plus-circle me-1"></i>Add Course
        </a>
    </div>

    @if($enrollments->count() > 0)
    <div class="row g-4">
        @foreach($enrollments as $enrollment)
        @php $course = $enrollment->course; @endphp
        <div class="col-12 col-md-6 col-lg-4">
            <div class="card h-100 shadow-sm border-0 rounded-3">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <span class="badge bg-info bg-opacity-15 text-info fw-bold px-3 py-2 rounded-pill">{{ $course->course_code }}</span>
                        <span class="badge bg-light text-muted border">{{ $course->credits }} Credits</span>
                    </div>
                    <h5 class="fw-bold mb-2">{{ $course->title }}</h5>
                    @if($course->instructor)
                        <p class="text-muted small mb-2"><i class="bi bi-person-fill me-1"></i>{{ $course->instructor }}</p>
                    @endif
                    @if($course->description)
                        <p class="text-muted small mb-3 lh-sm">{{ Str::limit($course->description, 90) }}</p>
                    @endif
                </div>
                <div class="card-footer bg-transparent border-top pt-2 pb-3 px-3 d-flex gap-2">
                    <a href="{{ route('lms.show', $course->id) }}" class="btn btn-info text-white flex-grow-1">
                        <i class="bi bi-arrow-right-circle me-2"></i>Open Course
                    </a>
                    <form method="POST" action="{{ route('sis.drop') }}" onsubmit="return confirm('Drop {{ $course->course_code }}?')">
                        @csrf
                        <input type="hidden" name="course_id" value="{{ $course->id }}">
                        <button class="btn btn-outline-danger" title="Drop course"><i class="bi bi-x-circle"></i></button>
                    </form>
                </div>
            </div>
        </div>
        @endforeach
    </div>
    @else
    <div class="card shadow-sm border-0 rounded-3 text-center py-5">
        <div class="display-1 mb-3">📚</div>
        <h5>You're not enrolled in any courses yet.</h5>
        <a href="{{ route('sis.registration') }}" class="btn btn-primary mx-auto mt-2" style="width:fit-content">Browse & Register</a>
    </div>
    @endif
</div>
@endsection
