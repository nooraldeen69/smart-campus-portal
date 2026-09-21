@extends('layouts.app')

@section('title', $course->title . ' - LMS')

@section('content')
<div class="container-fluid pt-2 pb-5">

    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('lms.index') }}" class="text-decoration-none">My Courses</a></li>
            <li class="breadcrumb-item active" aria-current="page">{{ $course->title }}</li>
        </ol>
    </nav>

    {{-- Course Header --}}
    <div class="card bg-info text-white border-0 shadow-sm rounded-3 mb-4">
        <div class="card-body p-4">
            <div class="row align-items-center">
                <div class="col">
                    <span class="badge bg-white text-info fw-bold mb-2">{{ $course->course_code }}</span>
                    <h2 class="fw-bold mb-1">{{ $course->title }}</h2>
                    <p class="opacity-75 mb-0">
                        <i class="bi bi-person-fill me-1"></i>{{ $course->instructor ?? 'TBA' }} &nbsp;·&nbsp;
                        <i class="bi bi-bookmark-fill me-1"></i>{{ $course->credits }} Credits
                    </p>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        {{-- Course Details --}}
        <div class="col-12 col-lg-8">
            {{-- Description --}}
            <div class="card shadow-sm border-0 rounded-3 mb-4">
                <div class="card-header border-0 bg-white pt-3">
                    <h5 class="fw-bold mb-0"><i class="bi bi-info-circle me-2 text-primary"></i>Course Description</h5>
                </div>
                <div class="card-body">
                    <p class="text-muted">{{ $course->description ?? 'No description available for this course.' }}</p>
                </div>
            </div>

            {{-- Lectures --}}
            <div class="card shadow-sm border-0 rounded-3 mb-4">
                <div class="card-header border-0 bg-white pt-3">
                    <h5 class="fw-bold mb-0"><i class="bi bi-camera-video me-2 text-danger"></i>Lectures & Materials</h5>
                </div>
                <div class="card-body p-0">
                    <ul class="list-group list-group-flush">
                        @foreach(['Week 1 – Introduction & Overview', 'Week 2 – Core Concepts', 'Week 3 – Lab Session', 'Week 4 – Advanced Topics', 'Week 5 – Case Study', 'Week 6 – Midterm Review'] as $i => $lecture)
                        <li class="list-group-item d-flex justify-content-between align-items-center px-4 py-3">
                            <div class="d-flex align-items-center gap-3">
                                <span class="rounded-circle bg-info bg-opacity-15 text-info fw-bold d-flex align-items-center justify-content-center" style="width:36px;height:36px;font-size:.85rem">{{ $i+1 }}</span>
                                <span class="fw-semibold">{{ $lecture }}</span>
                            </div>
                            <span class="badge bg-light text-muted border small">PDF + Video</span>
                        </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>

        {{-- Sidebar --}}
        <div class="col-12 col-lg-4">
            {{-- Instructor --}}
            <div class="card shadow-sm border-0 rounded-3 mb-4">
                <div class="card-body text-center py-4">
                    <div class="rounded-circle bg-info text-white d-flex align-items-center justify-content-center mx-auto mb-3" style="width:60px;height:60px;font-size:1.5rem">
                        <i class="bi bi-person-fill"></i>
                    </div>
                    <h6 class="fw-bold mb-0">{{ $course->instructor ?? 'To Be Announced' }}</h6>
                    <p class="text-muted small mb-3">Course Instructor</p>
                    <a href="mailto:instructor@university.edu" class="btn btn-outline-info btn-sm w-100">
                        <i class="bi bi-envelope me-1"></i>Send Email
                    </a>
                </div>
            </div>

            {{-- Quick Stats --}}
            <div class="card shadow-sm border-0 rounded-3">
                <div class="card-header border-0 bg-white pt-3">
                    <h6 class="fw-bold mb-0">Course Info</h6>
                </div>
                <ul class="list-group list-group-flush">
                    <li class="list-group-item d-flex justify-content-between">
                        <span class="text-muted">Credits</span>
                        <strong>{{ $course->credits }}</strong>
                    </li>
                    <li class="list-group-item d-flex justify-content-between">
                        <span class="text-muted">Total Lectures</span>
                        <strong>6</strong>
                    </li>
                    <li class="list-group-item d-flex justify-content-between">
                        <span class="text-muted">Status</span>
                        <span class="badge bg-success">Active</span>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection
