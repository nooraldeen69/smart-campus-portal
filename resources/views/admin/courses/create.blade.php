@extends('admin.layout')
@section('admin_title', 'Add Course')
@section('content')
<div class="d-flex align-items-center gap-3 mb-4">
    <a href="{{ route('admin.courses.index') }}" class="btn btn-outline-secondary btn-sm"><i class="bi bi-arrow-left"></i></a>
    <h5 class="fw-bold mb-0">Add New Course</h5>
</div>
<div class="card border-0 shadow-sm rounded-3" style="max-width:700px">
    <div class="card-body p-4">
        <form method="POST" action="{{ route('admin.courses.store') }}">
            @csrf
            <div class="row mb-3">
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Course Code</label>
                    <input type="text" name="course_code" class="form-control @error('course_code') is-invalid @enderror" value="{{ old('course_code') }}" placeholder="e.g. IT301" required>
                    @error('course_code')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-8">
                    <label class="form-label fw-semibold">Title</label>
                    <input type="text" name="title" class="form-control @error('title') is-invalid @enderror" value="{{ old('title') }}" required>
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-md-8">
                    <label class="form-label fw-semibold">Instructor</label>
                    <input type="text" name="instructor" class="form-control" value="{{ old('instructor') }}" placeholder="e.g. Dr. Ahmed">
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Credits</label>
                    <input type="number" name="credits" class="form-control" value="{{ old('credits', 3) }}" min="1" max="6" required>
                </div>
            </div>
            <div class="mb-3">
                <label class="form-label fw-semibold">Description</label>
                <textarea name="description" class="form-control" rows="3">{{ old('description') }}</textarea>
            </div>
            <div class="row mb-3">
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Semester</label>
                    <input type="text" name="semester" class="form-control" value="{{ old('semester','Fall 2026') }}" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Schedule Days</label>
                    <input type="text" name="schedule_days" class="form-control" value="{{ old('schedule_days') }}" placeholder="e.g. Mon, Wed">
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Schedule Time</label>
                    <input type="text" name="schedule_time" class="form-control" value="{{ old('schedule_time') }}" placeholder="e.g. 10:00 - 11:30">
                </div>
            </div>
            <div class="mb-4">
                <label class="form-label fw-semibold">Room</label>
                <input type="text" name="room" class="form-control" value="{{ old('room') }}" placeholder="e.g. B-201">
            </div>
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary"><i class="bi bi-check-circle me-1"></i>Create Course</button>
                <a href="{{ route('admin.courses.index') }}" class="btn btn-outline-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
