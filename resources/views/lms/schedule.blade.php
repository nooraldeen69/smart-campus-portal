@extends('layouts.app')

@section('title', 'My Schedule - Smart Campus Portal')

@section('content')
@php
    $week = ['Sun' => 'Sunday', 'Mon' => 'Monday', 'Tue' => 'Tuesday', 'Wed' => 'Wednesday', 'Thu' => 'Thursday', 'Fri' => 'Friday', 'Sat' => 'Saturday'];
    $byDay = array_fill_keys(array_keys($week), []);
    foreach ($enrollments as $enrollment) {
        $c = $enrollment->course;
        foreach (preg_split('/[,\s\/]+/', (string) $c->schedule_days, -1, PREG_SPLIT_NO_EMPTY) as $d) {
            $key = ucfirst(strtolower(substr($d, 0, 3)));
            if (isset($byDay[$key])) {
                $byDay[$key][] = $c;
            }
        }
    }
    foreach ($byDay as $k => $list) {
        usort($list, fn ($a, $b) => strcmp((string) $a->schedule_time, (string) $b->schedule_time));
        $byDay[$k] = $list;
    }
@endphp
<div class="container-fluid pt-2 pb-5">
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-4">
        <div>
            <h2 class="fw-bold mb-0"><i class="bi bi-calendar-week text-primary me-2"></i>Weekly Schedule</h2>
            <p class="text-muted mb-0">{{ $enrollments->count() }} registered course(s)</p>
        </div>
        <a href="{{ route('sis.registration') }}" class="btn btn-outline-primary btn-sm"><i class="bi bi-plus-circle me-1"></i>Add Course</a>
    </div>

    @if ($enrollments->isEmpty())
        <div class="card shadow-sm border-0 rounded-3 text-center py-5">
            <div class="display-1 mb-3">📅</div>
            <h5>Nothing to show yet.</h5>
            <p class="text-muted">Register for courses to build your weekly timetable.</p>
        </div>
    @else
        <div class="row g-3">
            @foreach ($week as $short => $full)
                <div class="col-12 col-md-6 col-xl-4">
                    <div class="card border-0 shadow-sm rounded-3 h-100">
                        <div class="card-header bg-white fw-semibold">{{ $full }}</div>
                        <div class="list-group list-group-flush">
                            @forelse ($byDay[$short] as $c)
                                <a href="{{ route('lms.show', $c->id) }}" class="list-group-item list-group-item-action">
                                    <div class="d-flex justify-content-between gap-2">
                                        <strong class="text-break">{{ $c->course_code }} — {{ $c->title }}</strong>
                                        <span class="badge bg-primary bg-opacity-10 text-primary text-nowrap align-self-start">{{ $c->schedule_time ?: 'TBA' }}</span>
                                    </div>
                                    <div class="small text-muted">{{ $c->room ? 'Room '.$c->room : 'Room TBA' }}@if($c->instructor) · {{ $c->instructor }}@endif</div>
                                </a>
                            @empty
                                <div class="list-group-item text-muted small">No classes</div>
                            @endforelse
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection
