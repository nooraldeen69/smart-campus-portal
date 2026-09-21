@extends('layouts.app')

@section('title', 'Dashboard - Smart Campus Portal')

@section('content')
@php
    use Carbon\Carbon;
    $user = Auth::user();
    $grades = \App\Models\SisGrade::where('user_id', $user->id)->get();
    $enrollments = \App\Models\LmsEnrollment::where('user_id', $user->id)->count();
    $loans = \App\Models\LibraryLoan::with('book')->where('user_id', $user->id)->whereNull('returned_at')->get();
    $notifications = $user->unreadNotifications()->limit(5)->get();

    $gradePoints = ['A' => 4.0, 'A-' => 3.7, 'B+' => 3.3, 'B' => 3.0, 'B-' => 2.7, 'C+' => 2.3, 'C' => 2.0];
    $gpa = $grades->count() > 0
        ? round($grades->map(fn($g) => $gradePoints[$g->score] ?? 0)->average(), 2)
        : null;

    $overdue = $loans->filter(fn($l) => Carbon::parse($l->due_date)->isPast());
    $dueSoon = $loans->filter(fn($l) => !Carbon::parse($l->due_date)->isPast() && Carbon::parse($l->due_date)->diffInDays(now()) <= 3);
@endphp

<div class="container-fluid pt-2 pb-5">

    {{-- Welcome Banner --}}
    <div class="card bg-primary text-white shadow-sm border-0 rounded-3 mb-4">
        <div class="card-body py-4 px-4">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h2 class="fw-bold mb-1">Welcome back, {{ $user->name }}! 👋</h2>
                    <p class="mb-0 opacity-75">University ID: {{ $user->university_id }} · {{ now()->format('l, F j, Y') }}</p>
                </div>
                <div class="col-md-4 text-md-end mt-3 mt-md-0">
                    @if ($gpa)
                        <div class="fs-5 fw-bold">📊 GPA: {{ $gpa }} / 4.0</div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Alert: Overdue Books --}}
    @if ($overdue->count() > 0)
    <div class="alert alert-danger border-0 rounded-3 shadow-sm mb-4" role="alert">
        <i class="bi bi-exclamation-triangle-fill me-2"></i>
        <strong>Overdue:</strong> You have {{ $overdue->count() }} overdue book(s). Return them to avoid a fine.
        <a href="{{ route('library.index') }}" class="alert-link ms-2">Return now →</a>
    </div>
    @endif

    {{-- Notifications --}}
    @if ($notifications->count() > 0)
    <div class="card shadow-sm border-warning border rounded-3 mb-4">
        <div class="card-header bg-warning bg-opacity-10 border-0 py-3">
            <h5 class="mb-0 fw-bold"><i class="bi bi-bell-fill text-warning me-2"></i>Announcements ({{ $notifications->count() }})</h5>
        </div>
        <div class="card-body p-0">
            <ul class="list-group list-group-flush">
                @foreach ($notifications as $n)
                <li class="list-group-item d-flex justify-content-between align-items-start gap-3 py-3 px-4">
                    <div>
                        <strong>{{ $n->data['title'] ?? 'Notice' }}</strong>
                        <div class="text-muted small">{{ $n->data['body'] ?? '' }}</div>
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
    @endif

    {{-- Stats Row --}}
    <div class="row g-4 mb-4">
        <div class="col-6 col-md-3">
            <a href="{{ route('sis.transcript') }}" class="text-decoration-none">
                <div class="card shadow-sm border-0 rounded-3 h-100 text-center p-3 hover-card">
                    <div class="display-5 mb-2">🎓</div>
                    <div class="fs-2 fw-bold text-success">{{ $grades->count() }}</div>
                    <div class="text-muted small">Total Grades</div>
                </div>
            </a>
        </div>
        <div class="col-6 col-md-3">
            <a href="{{ route('lms.index') }}" class="text-decoration-none">
                <div class="card shadow-sm border-0 rounded-3 h-100 text-center p-3 hover-card">
                    <div class="display-5 mb-2">📚</div>
                    <div class="fs-2 fw-bold text-info">{{ $enrollments }}</div>
                    <div class="text-muted small">Enrolled Courses</div>
                </div>
            </a>
        </div>
        <div class="col-6 col-md-3">
            <a href="{{ route('library.index') }}" class="text-decoration-none">
                <div class="card shadow-sm border-0 rounded-3 h-100 text-center p-3 hover-card">
                    <div class="display-5 mb-2">📖</div>
                    <div class="fs-2 fw-bold text-warning">{{ $loans->count() }}</div>
                    <div class="text-muted small">Active Loans</div>
                </div>
            </a>
        </div>
        <div class="col-6 col-md-3">
            <a href="{{ route('sis.registration') }}" class="text-decoration-none">
                <div class="card shadow-sm border-0 rounded-3 h-100 text-center p-3 hover-card">
                    <div class="display-5 mb-2">➕</div>
                    <div class="fs-2 fw-bold text-primary">Register</div>
                    <div class="text-muted small">New Course</div>
                </div>
            </a>
        </div>
    </div>

    <div class="row g-4">
        {{-- Recent Grades --}}
        <div class="col-12 col-md-4">
            <div class="card shadow-sm border-0 rounded-3 h-100">
                <div class="card-header border-0 bg-success bg-opacity-10 py-3">
                    <h6 class="mb-0 fw-bold text-success"><i class="bi bi-journal-text me-2"></i>Recent Grades</h6>
                </div>
                <div class="card-body p-0">
                    @if ($grades->count() > 0)
                    <ul class="list-group list-group-flush">
                        @foreach ($grades->take(4) as $grade)
                        <li class="list-group-item d-flex justify-content-between align-items-center px-4">
                            <span class="text-truncate me-2">{{ $grade->course_name }}</span>
                            @php
                                $badge = in_array($grade->score, ['A', 'A-']) ? 'success' : (in_array($grade->score, ['B+', 'B', 'B-']) ? 'info' : 'warning');
                            @endphp
                            <span class="badge bg-{{ $badge }} rounded-pill">{{ $grade->score }}</span>
                        </li>
                        @endforeach
                    </ul>
                    @else
                    <p class="text-muted small p-4 mb-0">No grades recorded yet.</p>
                    @endif
                </div>
                <div class="card-footer bg-transparent border-0 text-end">
                    <a href="{{ route('sis.transcript') }}" class="btn btn-sm btn-outline-success">Full Transcript →</a>
                </div>
            </div>
        </div>

        {{-- Enrolled Courses --}}
        <div class="col-12 col-md-4">
            <div class="card shadow-sm border-0 rounded-3 h-100">
                <div class="card-header border-0 bg-info bg-opacity-10 py-3">
                    <h6 class="mb-0 fw-bold text-info"><i class="bi bi-laptop me-2"></i>My Courses</h6>
                </div>
                <div class="card-body p-0">
                    @php
                        $myCourses = \App\Models\LmsEnrollment::with('course')->where('user_id', $user->id)->get();
                    @endphp
                    @if ($myCourses->count() > 0)
                    <ul class="list-group list-group-flush">
                        @foreach ($myCourses->take(4) as $enrollment)
                        <li class="list-group-item px-4">
                            <a href="{{ route('lms.show', $enrollment->lms_course_id) }}" class="text-decoration-none text-dark">
                                <div class="fw-semibold small">{{ $enrollment->course->course_code }}</div>
                                <div class="text-muted" style="font-size:.8rem">{{ $enrollment->course->title }}</div>
                            </a>
                        </li>
                        @endforeach
                    </ul>
                    @else
                    <p class="text-muted small p-4 mb-0">Not enrolled in any courses.</p>
                    @endif
                </div>
                <div class="card-footer bg-transparent border-0 text-end">
                    <a href="{{ route('lms.index') }}" class="btn btn-sm btn-outline-info">All Courses →</a>
                </div>
            </div>
        </div>

        {{-- Library Loans --}}
        <div class="col-12 col-md-4">
            <div class="card shadow-sm border-0 rounded-3 h-100">
                <div class="card-header border-0 bg-secondary bg-opacity-10 py-3">
                    <h6 class="mb-0 fw-bold text-secondary"><i class="bi bi-book me-2"></i>Active Loans</h6>
                </div>
                <div class="card-body p-0">
                    @if ($loans->count() > 0)
                    <ul class="list-group list-group-flush">
                        @foreach ($loans as $loan)
                        @php
                            $isOverdue = Carbon::parse($loan->due_date)->isPast();
                            $isSoon = !$isOverdue && Carbon::parse($loan->due_date)->diffInDays(now()) <= 3;
                        @endphp
                        <li class="list-group-item px-4">
                            <div class="fw-semibold small text-truncate">{{ $loan->book->title }}</div>
                            <div class="small {{ $isOverdue ? 'text-danger fw-bold' : ($isSoon ? 'text-warning fw-bold' : 'text-muted') }}">
                                {{ $isOverdue ? '⚠ Overdue: ' : 'Due: ' }}{{ Carbon::parse($loan->due_date)->format('M j, Y') }}
                            </div>
                        </li>
                        @endforeach
                    </ul>
                    @else
                    <p class="text-muted small p-4 mb-0">No active book loans.</p>
                    @endif
                </div>
                <div class="card-footer bg-transparent border-0 text-end">
                    <a href="{{ route('library.index') }}" class="btn btn-sm btn-outline-secondary">Library →</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('head')
<style>
.hover-card:hover { transform: translateY(-3px); box-shadow: 0 8px 20px rgba(0,0,0,.12)!important; transition: all .2s ease; }
.hover-card { transition: all .2s ease; }
</style>
@endpush
