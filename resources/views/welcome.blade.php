@extends('layouts.app')

@section('title', 'Sign in - Smart Campus Portal')

@section('content')
<div class="min-vh-100 d-flex align-items-center justify-content-center py-5" style="background: linear-gradient(135deg, #0d6efd 0%, #0a58ca 100%);">
    <div class="container" style="max-width: 440px;">
        @auth
            <div class="card shadow-lg border-0 rounded-4 text-center p-4">
                <div class="display-2 mb-3">🎓</div>
                <h3 class="fw-bold mb-1">Welcome back, {{ Auth::user()->name }}!</h3>
                <p class="text-muted mb-4">You are already signed in.</p>
                <a href="{{ route('dashboard') }}" class="btn btn-primary btn-lg w-100">Go to Dashboard</a>
            </div>
        @else
        <div class="text-center mb-4">
            <div class="display-3 mb-2">🎓</div>
            <h1 class="text-white fw-bold fs-3 mb-0">Smart Campus Portal</h1>
            <p class="text-white-50">Al-Rasheed Smart University</p>
        </div>

        <div class="card shadow-lg border-0 rounded-4">
            <div class="card-body p-4">
                <h4 class="fw-bold mb-1 text-center">Sign In</h4>
                <p class="text-muted small text-center mb-4">One login for SIS, LMS & Library</p>

                <form method="POST" action="{{ route('login.post') }}">
                    @csrf

                    @if ($errors->any())
                        <div class="alert alert-danger py-2 small rounded-3">
                            <ul class="mb-0 ps-3">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <div class="mb-3">
                        <label for="login" class="form-label fw-semibold">University ID or e-mail</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0"><i class="bi bi-person-badge text-muted"></i></span>
                            <input type="text" class="form-control border-start-0 ps-0" id="login" name="login" value="{{ old('login') }}" placeholder="e.g. 20260001" autocomplete="username" required autofocus>
                        </div>
                    </div>
                    <div class="mb-4">
                        <label for="password" class="form-label fw-semibold">Password</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0"><i class="bi bi-lock text-muted"></i></span>
                            <input type="password" class="form-control border-start-0 ps-0" id="password" name="password" placeholder="••••••••" autocomplete="current-password" required>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary btn-lg w-100 fw-semibold rounded-3">
                        <i class="bi bi-box-arrow-in-right me-2"></i>Sign In
                    </button>
                </form>

                @if (config('services.campus.demo_mode'))
                <hr class="my-4">
                <div class="text-center">
                    <p class="text-muted small fw-semibold mb-2">Demo accounts <span class="fw-normal">(click to fill in)</span></p>
                    <div class="d-grid gap-2">
                        <button type="button" class="btn btn-outline-secondary btn-sm demo-login" data-login="20260001"><i class="bi bi-person me-1"></i> Layla Hassan (20260001)</button>
                        <button type="button" class="btn btn-outline-secondary btn-sm demo-login" data-login="20260002"><i class="bi bi-person me-1"></i> Omar Khalid (20260002)</button>
                        <button type="button" class="btn btn-outline-secondary btn-sm demo-login" data-login="20260003"><i class="bi bi-person me-1"></i> Sara Ahmed (20260003)</button>
                    </div>
                    <p class="text-muted" style="font-size:.75rem;margin-top:.4rem">Student password: <code>password</code></p>
                </div>
                @endif
            </div>
        </div>
        @endauth
    </div>
</div>
@endsection

@push('head')
<style>
body { background: transparent !important; }
.input-group-text { border-color: #dee2e6; }
</style>
<script>
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.demo-login').forEach(function (btn) {
        btn.addEventListener('click', function () {
            document.getElementById('login').value = this.dataset.login;
            document.getElementById('password').value = 'password';
        });
    });
});
</script>
@endpush
