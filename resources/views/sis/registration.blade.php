@extends('layouts.app')

@section('title', 'Course Registration - SIS')

@section('content')
<div class="container-fluid pt-2 pb-5">

    <div class="mb-4">
        <h2 class="fw-bold mb-0"><i class="bi bi-pencil-square text-primary me-2"></i>Course Registration</h2>
        <p class="text-muted mb-0">Browse and register for courses available this semester</p>
    </div>

    @if($availableCourses->count() > 0)
    <div class="row g-4">
        @foreach($availableCourses as $course)
        <div class="col-12 col-md-6 col-lg-4">
            <div class="card h-100 shadow-sm border-0 rounded-3">
                <div class="card-body pb-2">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <span class="badge bg-primary bg-opacity-10 text-primary fw-bold px-3 py-2 rounded-pill">{{ $course->course_code }}</span>
                        <span class="badge bg-light text-muted border">{{ $course->credits }} Credits</span>
                    </div>
                    <h5 class="fw-bold mb-1">{{ $course->title }}</h5>
                    @if($course->instructor)
                        <p class="text-muted small mb-2"><i class="bi bi-person-fill me-1"></i>{{ $course->instructor }}</p>
                    @endif
                    @if($course->description)
                        <p class="text-muted small mb-0">{{ $course->description }}</p>
                    @endif
                </div>
                <div class="card-footer bg-transparent border-0 pt-0 pb-3 px-3">
                    <form method="POST" action="{{ route('sis.enroll') }}">
                        @csrf
                        <input type="hidden" name="course_id" value="{{ $course->id }}">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="bi bi-plus-circle me-2"></i>Enroll in Course
                        </button>
                    </form>
                </div>
            </div>
        </div>
        @endforeach
    </div>
    @else
    <div class="card shadow-sm border-0 rounded-3 text-center py-5">
        <div class="display-1 mb-3">🎉</div>
        <h5>You're enrolled in all available courses!</h5>
        <p class="text-muted small">Check back at the start of next semester for new courses.</p>
        <a href="{{ route('lms.index') }}" class="btn btn-outline-primary mx-auto" style="width:fit-content">View My Courses</a>
    </div>
    @endif
</div>
@endsection
