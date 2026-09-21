@extends('layouts.app')

@section('title', 'My Transcript - SIS')

@section('content')
@php
    $gradePoints = ['A' => 4.0, 'A-' => 3.7, 'B+' => 3.3, 'B' => 3.0, 'B-' => 2.7, 'C+' => 2.3, 'C' => 2.0, 'D' => 1.0];
    $gpa = $grades->count() > 0
        ? round($grades->map(fn($g) => $gradePoints[$g->score] ?? 0)->average(), 2)
        : null;
    $totalCredits = $grades->count() * 3;
@endphp

<div class="container-fluid pt-2 pb-5">

    <div class="d-flex align-items-center mb-4 gap-3">
        <div>
            <h2 class="fw-bold mb-0"><i class="bi bi-journal-bookmark-fill text-success me-2"></i>Academic Transcript</h2>
            <p class="text-muted mb-0">Official grade record for {{ Auth::user()->name }}</p>
        </div>
        @if($gpa)
        <div class="ms-auto">
            <div class="card border-0 bg-success text-white px-4 py-3 text-center shadow-sm rounded-3">
                <div class="fs-4 fw-bold">{{ $gpa }}</div>
                <div class="small opacity-75">Cumulative GPA</div>
            </div>
        </div>
        @endif
    </div>

    @if($grades->count() > 0)
    <div class="card shadow-sm border-0 rounded-3">
        <div class="card-header bg-white border-0 pt-3">
            <div class="row text-muted small fw-bold text-uppercase px-2">
                <div class="col-1">#</div>
                <div class="col-7">Course</div>
                <div class="col-2 text-center">Grade</div>
                <div class="col-2 text-center">Points</div>
            </div>
        </div>
        <div class="card-body p-0">
            <ul class="list-group list-group-flush">
                @foreach ($grades as $i => $grade)
                @php
                    $pts = $gradePoints[$grade->score] ?? 0;
                    $badgeColor = $pts >= 3.7 ? 'success' : ($pts >= 2.7 ? 'info' : ($pts >= 2.0 ? 'warning' : 'danger'));
                @endphp
                <li class="list-group-item px-4 py-3">
                    <div class="row align-items-center">
                        <div class="col-1 text-muted small">{{ $i + 1 }}</div>
                        <div class="col-7">
                            <div class="fw-semibold">{{ $grade->course_name }}</div>
                        </div>
                        <div class="col-2 text-center">
                            <span class="badge bg-{{ $badgeColor }} fs-6 px-3">{{ $grade->score }}</span>
                        </div>
                        <div class="col-2 text-center text-muted">{{ number_format($pts, 1) }}</div>
                    </div>
                </li>
                @endforeach
            </ul>
        </div>
        <div class="card-footer bg-light border-0 px-4 py-3">
            <div class="row text-muted small">
                <div class="col">Total Courses: <strong class="text-dark">{{ $grades->count() }}</strong></div>
                @if($gpa)
                <div class="col text-end">Cumulative GPA: <strong class="text-dark">{{ $gpa }} / 4.0</strong></div>
                @endif
            </div>
        </div>
    </div>
    @else
    <div class="card shadow-sm border-0 rounded-3 text-center py-5">
        <div class="display-1 mb-3">📋</div>
        <h5 class="text-muted">No grades recorded yet.</h5>
        <p class="text-muted small">Your academic record will appear here once grades are posted.</p>
        <a href="{{ route('sis.registration') }}" class="btn btn-success mx-auto" style="width:fit-content">Register for a Course</a>
    </div>
    @endif
</div>
@endsection
