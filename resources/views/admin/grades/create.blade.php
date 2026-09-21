@extends('admin.layout')
@section('admin_title', 'Post Grade')
@section('content')
<div class="d-flex align-items-center gap-3 mb-4">
    <a href="{{ route('admin.grades.index') }}" class="btn btn-outline-secondary btn-sm"><i class="bi bi-arrow-left"></i></a>
    <h5 class="fw-bold mb-0">Post New Grade</h5>
</div>
<div class="card border-0 shadow-sm rounded-3" style="max-width:550px">
    <div class="card-body p-4">
        <form method="POST" action="{{ route('admin.grades.store') }}">
            @csrf
            <div class="mb-3">
                <label class="form-label fw-semibold">Student</label>
                <select name="user_id" class="form-select @error('user_id') is-invalid @enderror" required>
                    <option value="">Select student…</option>
                    @foreach($students as $s)
                    <option value="{{ $s->id }}" {{ old('user_id')==$s->id?'selected':'' }}>{{ $s->name }} ({{ $s->university_id }})</option>
                    @endforeach
                </select>
                @error('user_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="mb-3">
                <label class="form-label fw-semibold">Course Name</label>
                <input type="text" name="course_name" class="form-control @error('course_name') is-invalid @enderror" value="{{ old('course_name') }}" placeholder="e.g. Database Systems" required>
                @error('course_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="row mb-3">
                <div class="col">
                    <label class="form-label fw-semibold">Grade</label>
                    <select name="score" class="form-select @error('score') is-invalid @enderror" required>
                        <option value="">Select…</option>
                        @foreach(['A','A-','B+','B','B-','C+','C','D','F'] as $g)
                        <option value="{{ $g }}" {{ old('score')==$g?'selected':'' }}>{{ $g }}</option>
                        @endforeach
                    </select>
                    @error('score')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col">
                    <label class="form-label fw-semibold">Semester</label>
                    <input type="text" name="semester" class="form-control" value="{{ old('semester','Fall 2026') }}" required>
                </div>
            </div>
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary"><i class="bi bi-check-circle me-1"></i>Post Grade</button>
                <a href="{{ route('admin.grades.index') }}" class="btn btn-outline-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
