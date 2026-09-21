@extends('layouts.app')

@section('title', 'Edit Profile - Smart Campus Portal')

@section('content')
<div class="container pt-2 pb-5" style="max-width: 640px;">
    <div class="d-flex align-items-center gap-3 mb-4">
        <a href="{{ route('profile.show') }}" class="btn btn-outline-secondary btn-sm"><i class="bi bi-arrow-left"></i></a>
        <h2 class="fw-bold mb-0 fs-4">Edit Profile</h2>
    </div>

    <div class="card border-0 shadow-sm rounded-3">
        <div class="card-body p-4">
            @if ($errors->any())
                <div class="alert alert-danger py-2 small"><ul class="mb-0 ps-3">@foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>
            @endif
            <form method="POST" action="{{ route('profile.update') }}">
                @csrf
                <div class="row g-3 mb-3">
                    <div class="col-12 col-md-6">
                        <label class="form-label fw-semibold">University ID</label>
                        <input class="form-control" value="{{ $user->university_id }}" disabled>
                    </div>
                    <div class="col-12 col-md-6">
                        <label class="form-label fw-semibold">E-mail</label>
                        <input class="form-control" value="{{ $user->email }}" disabled>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold" for="name">Full name</label>
                    <input id="name" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $user->name) }}" required>
                </div>
                <div class="row g-3 mb-4">
                    <div class="col-12 col-md-6">
                        <label class="form-label fw-semibold" for="phone">Phone</label>
                        <input id="phone" name="phone" class="form-control @error('phone') is-invalid @enderror" value="{{ old('phone', $user->phone) }}">
                    </div>
                    <div class="col-12 col-md-6">
                        <label class="form-label fw-semibold" for="department">Department</label>
                        <input id="department" name="department" class="form-control @error('department') is-invalid @enderror" value="{{ old('department', $user->department) }}">
                    </div>
                </div>
                <button class="btn btn-primary"><i class="bi bi-check-circle me-1"></i>Save changes</button>
                <a href="{{ route('profile.show') }}" class="btn btn-link">Cancel</a>
            </form>
        </div>
    </div>
</div>
@endsection
