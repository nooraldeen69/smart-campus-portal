<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Smart Campus Portal')</title>

    <link href="{{ asset('vendor/bootstrap/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('vendor/bootstrap-icons/bootstrap-icons.min.css') }}" rel="stylesheet">
    @stack('head')
</head>
<body class="bg-light">

    @auth
    <nav class="navbar navbar-expand-md navbar-dark bg-primary shadow-sm">
        <div class="container-fluid px-md-4">
            <a class="navbar-brand fw-bold" href="{{ route('dashboard') }}">
                <i class="bi bi-mortarboard-fill me-2"></i>Smart Campus
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarCollapse" aria-controls="navbarCollapse" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarCollapse">
                <ul class="navbar-nav me-auto mb-2 mb-md-0">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">Dashboard</a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle {{ request()->is('sis*') ? 'active' : '' }}" href="#" id="sisDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            SIS
                        </a>
                        <ul class="dropdown-menu shadow-sm" aria-labelledby="sisDropdown">
                            <li><a class="dropdown-item" href="{{ route('sis.transcript') }}">Transcript & Grades</a></li>
                            <li><a class="dropdown-item" href="{{ route('sis.registration') }}">Course Registration</a></li>
                        </ul>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle {{ request()->is('lms*') ? 'active' : '' }}" href="#" id="lmsDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            LMS
                        </a>
                        <ul class="dropdown-menu shadow-sm" aria-labelledby="lmsDropdown">
                            <li><a class="dropdown-item" href="{{ route('lms.index') }}">My Courses</a></li>
                            <li><a class="dropdown-item" href="{{ route('lms.schedule') }}">Weekly Schedule</a></li>
                        </ul>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle {{ request()->is('library*') ? 'active' : '' }}" href="#" id="libDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            Library
                        </a>
                        <ul class="dropdown-menu shadow-sm" aria-labelledby="libDropdown">
                            <li><a class="dropdown-item" href="{{ route('library.index') }}">Catalog & Loans</a></li>
                        </ul>
                    </li>
                    @if (Auth::user()->isAdmin())
                        <li class="nav-item"><a class="nav-link {{ request()->is('admin*') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}"><i class="bi bi-shield-lock me-1"></i>Admin</a></li>
                    @endif
                </ul>
                <div class="d-flex align-items-center">
                    <a href="{{ route('profile.show') }}" class="text-white-50 me-3 small text-decoration-none" title="My profile"><i class="bi bi-person-circle me-1"></i>{{ Auth::user()->name }} ({{ Auth::user()->university_id }})</a>
                    <form method="POST" action="{{ route('logout') }}" class="m-0">
                        @csrf
                        <button type="submit" class="btn btn-sm btn-outline-light">Logout</button>
                    </form>
                </div>
            </div>
        </div>
    </nav>
    @endauth

    <main class="px-2 px-md-4 py-3">
        @if (session('error'))
            <div class="alert alert-danger mb-4" role="alert">{{ session('error') }}</div>
        @endif
        @if (session('status'))
            <div class="alert alert-success mb-4" role="status">{{ session('status') }}</div>
        @endif

        @yield('content')
    </main>

    <script src="{{ asset('vendor/bootstrap/bootstrap.bundle.min.js') }}"></script>
</body>
</html>
