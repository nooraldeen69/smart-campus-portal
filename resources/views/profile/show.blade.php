@extends('layouts.app')

@section('title', 'My Profile - Smart Campus Portal')

@section('content')
<div class="container-fluid pt-2 pb-5">
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-4">
        <h2 class="fw-bold mb-0"><i class="bi bi-person-circle text-primary me-2"></i>My Profile</h2>
        <a href="{{ route('profile.edit') }}" class="btn btn-outline-primary btn-sm"><i class="bi bi-pencil me-1"></i>Edit profile</a>
    </div>

    <div class="row g-4">
        <div class="col-12 col-lg-4">
            <div class="card border-0 shadow-sm rounded-3 h-100">
                <div class="card-body text-center p-4">
                    <div class="rounded-circle bg-primary text-white d-inline-flex align-items-center justify-content-center fw-bold mb-3" style="width:84px;height:84px;font-size:2rem">
                        {{ mb_strtoupper(mb_substr($user->name, 0, 1)) }}
                    </div>
                    <h4 class="fw-bold mb-0 text-break">{{ $user->name }}</h4>
                    <div class="text-muted mb-3">{{ ucfirst($user->role ?? 'student') }} &middot; {{ $user->university_id }}</div>
                    <dl class="text-start small mb-0">
                        <dt class="text-muted">E-mail</dt><dd class="text-break">{{ $user->email }}</dd>
                        <dt class="text-muted">Phone</dt><dd>{{ $user->phone ?: '—' }}</dd>
                        <dt class="text-muted">Department</dt><dd>{{ $user->department ?: '—' }}</dd>
                    </dl>
                </div>
            </div>
        </div>

        <div class="col-12 col-lg-8">
            <div class="row g-3 mb-3">
                <div class="col-4"><div class="card border-0 shadow-sm rounded-3 text-center p-3">
                    <div class="fs-2 fw-bold text-success">{{ $gpa !== null ? number_format($gpa, 2) : '—' }}</div><div class="small text-muted">GPA (4.0)</div></div></div>
                <div class="col-4"><div class="card border-0 shadow-sm rounded-3 text-center p-3">
                    <div class="fs-2 fw-bold text-info">{{ $enrollments->count() }}</div><div class="small text-muted">Courses</div></div></div>
                <div class="col-4"><div class="card border-0 shadow-sm rounded-3 text-center p-3">
                    <div class="fs-2 fw-bold text-secondary">{{ $loans->count() }}</div><div class="small text-muted">Books on loan</div></div></div>
            </div>

            <div class="card border-0 shadow-sm rounded-3 mb-3">
                <div class="card-header bg-white fw-semibold"><i class="bi bi-journal-text text-success me-2"></i>Grades</div>
                <ul class="list-group list-group-flush">
                    @forelse ($grades as $g)
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <span class="text-break">{{ $g->course_name }}@if($g->semester ?? null) <span class="small text-muted">· {{ $g->semester }}</span>@endif</span>
                            <span class="badge bg-success rounded-pill">{{ $g->score }}</span>
                        </li>
                    @empty
                        <li class="list-group-item text-muted small">No grades yet.</li>
                    @endforelse
                </ul>
            </div>

            <div class="row g-3">
                <div class="col-12 col-md-6">
                    <div class="card border-0 shadow-sm rounded-3 h-100">
                        <div class="card-header bg-white fw-semibold"><i class="bi bi-laptop text-info me-2"></i>My courses</div>
                        <ul class="list-group list-group-flush">
                            @forelse ($enrollments as $e)
                                <li class="list-group-item"><a class="text-decoration-none" href="{{ route('lms.show', $e->lms_course_id) }}">{{ $e->course->course_code }} — {{ $e->course->title }}</a></li>
                            @empty
                                <li class="list-group-item text-muted small">Not registered for any course.</li>
                            @endforelse
                        </ul>
                    </div>
                </div>
                <div class="col-12 col-md-6">
                    <div class="card border-0 shadow-sm rounded-3 h-100">
                        <div class="card-header bg-white fw-semibold"><i class="bi bi-book text-secondary me-2"></i>Library loans</div>
                        <ul class="list-group list-group-flush">
                            @forelse ($loans as $l)
                                <li class="list-group-item d-flex justify-content-between gap-2"><span class="text-break">{{ $l->book->title }}</span><span class="small text-muted text-nowrap">due {{ $l->due_date }}</span></li>
                            @empty
                                <li class="list-group-item text-muted small">No books on loan.</li>
                            @endforelse
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
