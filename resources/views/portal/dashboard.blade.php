@extends('layouts.app')

@section('title', 'Dashboard - Smart Campus Portal')

@section('content')
@php
    $sourceNames = ['sis' => 'Student Information System', 'lms' => 'Learning Management System', 'library' => 'Library'];
@endphp

<div class="container-fluid pt-3 pb-5">
    <!-- Header Section -->
    <div class="row mb-4">
        <div class="col-12 d-flex justify-content-between align-items-center">
            <h2 class="h4 mb-0 text-primary fw-bold">Smart Campus Hub</h2>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="btn btn-sm btn-outline-danger shadow-sm">Logout</button>
            </form>
        </div>
        <div class="col-12 mt-2">
            <p class="text-muted mb-0 text-break">Welcome back, <strong>{{ $user->name }}</strong> ({{ $user->university_id }})</p>
        </div>
    </div>

    <!-- Degraded-service notices (a back-end is slow/down, portal keeps working) -->
    @foreach ($degraded as $source => $state)
        <div class="alert alert-warning py-2 small" role="alert">
            @if ($state === 'stale')
                {{ $sourceNames[$source] ?? $source }}: showing the last known data, which may be out of date.
            @else
                {{ $sourceNames[$source] ?? $source }} is temporarily unavailable.
            @endif
        </div>
    @endforeach

    <div class="row g-3">
        <!-- Notifications -->
        @if ($notifications->count() > 0)
            <div class="col-12">
                <div class="card shadow-sm border-warning h-100">
                    <div class="card-header bg-white border-0 pt-3 pb-0">
                        <h5 class="card-title text-warning-emphasis"><i class="bi bi-bell me-2"></i>Notifications</h5>
                    </div>
                    <div class="card-body pt-2">
                        <ul class="list-group list-group-flush">
                            @foreach ($notifications as $n)
                                <li class="list-group-item d-flex justify-content-between align-items-start gap-2 px-0">
                                    <div class="text-break">
                                        <strong>{{ $n->data['title'] ?? 'Notice' }}</strong>
                                        <div class="small text-muted">{{ $n->data['body'] ?? '' }}</div>
                                    </div>
                                    <form method="POST" action="{{ route('notifications.read', $n->id) }}">
                                        @csrf
                                        <button class="btn btn-sm btn-outline-secondary">Dismiss</button>
                                    </form>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        @endif

        <!-- SIS Integration Module -->
        <div class="col-12 col-md-6">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-white border-0 pt-3 pb-0">
                    <h5 class="card-title text-success"><i class="bi bi-journal-text me-2"></i>SIS: Latest Grades</h5>
                </div>
                <div class="card-body">
                    @if (count($grades) > 0)
                        <ul class="list-group list-group-flush">
                            @foreach ($grades as $grade)
                                <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                    <span class="text-break">{{ $grade['course_name'] ?? '-' }}</span>
                                    <span class="badge bg-success rounded-pill">{{ $grade['score'] ?? '-' }}</span>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <p class="text-muted small mb-0">No grades synchronized yet.</p>
                    @endif
                </div>
            </div>
        </div>

        <!-- LMS Integration Module -->
        <div class="col-12 col-md-6">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-white border-0 pt-3 pb-0">
                    <h5 class="card-title text-info"><i class="bi bi-laptop me-2"></i>LMS: Active Courses</h5>
                </div>
                <div class="card-body">
                    @if (count($courses) > 0)
                        <div class="d-grid gap-2">
                            @foreach ($courses as $course)
                                <a href="{{ $course['lms_link'] }}" class="btn btn-outline-primary text-start text-break"
                                   rel="noopener noreferrer">
                                    {{ $course['course_code'] ?? '' }} - {{ $course['title'] ?? '' }}
                                </a>
                            @endforeach
                        </div>
                    @else
                        <p class="text-muted small mb-0">No active courses found.</p>
                    @endif
                </div>
            </div>
        </div>

        <!-- Library Integration Module -->
        <div class="col-12 col-md-6">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-white border-0 pt-3 pb-0">
                    <h5 class="card-title text-secondary"><i class="bi bi-book me-2"></i>Library: Current Loans</h5>
                </div>
                <div class="card-body">
                    @if (count($loans) > 0)
                        <ul class="list-group list-group-flush">
                            @foreach ($loans as $loan)
                                <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                    <span class="text-break">{{ $loan['title'] ?? '-' }}</span>
                                    <span class="small text-muted ms-2 text-nowrap">Due {{ $loan['due_date'] ?? '-' }}</span>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <p class="text-muted small mb-0">No books on loan.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
