@extends('admin.layout')
@section('admin_title', 'Edit Course')
@section('content')
<div class="d-flex align-items-center gap-3 mb-4">
    <a href="{{ route('admin.courses.index') }}" class="btn btn-outline-secondary btn-sm"><i class="bi bi-arrow-left"></i></a>
    <h5 class="fw-bold mb-0">Edit Course — {{ $course->title }}</h5>
</div>
<div class="card border-0 shadow-sm rounded-3" style="max-width:700px">
    <div class="card-body p-4">
        <form method="POST" action="{{ route('admin.courses.update', $course->id) }}">
            @csrf
            <div class="row mb-3">
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Course Code</label>
                    <input type="text" name="course_code" class="form-control @error('course_code') is-invalid @enderror" value="{{ old('course_code', $course->course_code) }}" required>
                    @error('course_code')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-8">
                    <label class="form-label fw-semibold">Title</label>
                    <input type="text" name="title" class="form-control" value="{{ old('title', $course->title) }}" required>
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-md-8">
                    <label class="form-label fw-semibold">Instructor</label>
                    <input type="text" name="instructor" class="form-control" value="{{ old('instructor', $course->instructor) }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Credits</label>
                    <input type="number" name="credits" class="form-control" value="{{ old('credits', $course->credits) }}" min="1" max="6" required>
                </div>
            </div>
            <div class="mb-3">
                <label class="form-label fw-semibold">Description</label>
                <textarea name="description" class="form-control" rows="3">{{ old('description', $course->description) }}</textarea>
            </div>
            <div class="row mb-3">
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Semester</label>
                    <input type="text" name="semester" class="form-control" value="{{ old('semester', $course->semester) }}" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Schedule Days</label>
                    <input type="text" name="schedule_days" class="form-control" value="{{ old('schedule_days', $course->schedule_days) }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Schedule Time</label>
                    <input type="text" name="schedule_time" class="form-control" value="{{ old('schedule_time', $course->schedule_time) }}">
                </div>
            </div>
            <div class="mb-4">
                <label class="form-label fw-semibold">Room</label>
                <input type="text" name="room" class="form-control" value="{{ old('room', $course->room) }}">
            </div>
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary"><i class="bi bi-check-circle me-1"></i>Save Changes</button>
                <a href="{{ route('admin.courses.index') }}" class="btn btn-outline-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
