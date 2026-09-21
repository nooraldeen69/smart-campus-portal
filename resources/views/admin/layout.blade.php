<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('admin_title', 'Admin') — Smart Campus Portal</title>
    <link href="{{ asset('vendor/bootstrap/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('vendor/bootstrap-icons/bootstrap-icons.min.css') }}" rel="stylesheet">
    <style>
        body { font-family: system-ui, -apple-system, sans-serif; }
        .sidebar { width: 250px; min-height: 100vh; background: #1a1d23; flex-shrink: 0; }
        .sidebar .brand { padding: 1.5rem 1.2rem 1rem; border-bottom: 1px solid rgba(255,255,255,.08); }
        .sidebar .nav-link { color: rgba(255,255,255,.65); padding: .6rem 1.2rem; border-radius: 8px; margin: 2px 10px; font-size: .9rem; transition: all .2s; }
        .sidebar .nav-link:hover, .sidebar .nav-link.active { color: #fff; background: rgba(255,255,255,.1); }
        .sidebar .nav-link i { width: 20px; }
        .sidebar-section { padding: .5rem 1rem; font-size: .7rem; text-transform: uppercase; letter-spacing: .1em; color: rgba(255,255,255,.3); margin-top: .5rem; }
        .topbar { background: #fff; border-bottom: 1px solid #e9ecef; padding: .75rem 1.5rem; }
        .main-content { flex: 1; overflow-x: hidden; background: #f5f6f8; min-height: 100vh; }
        .content-area { padding: 1.5rem; }
    </style>
</head>
<body>
<div class="d-flex">
    {{-- Sidebar --}}
    <nav class="sidebar">
        <div class="brand">
            <div class="text-white fw-bold fs-6"><i class="bi bi-mortarboard-fill me-2 text-primary"></i>Smart Campus</div>
            <div class="text-white-50 small mt-1">Admin Panel</div>
        </div>

        <div class="mt-2">
            <div class="sidebar-section">Overview</div>
            <a href="{{ route('admin.dashboard') }}" class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                <i class="bi bi-speedometer2 me-2"></i>Dashboard
            </a>

            <div class="sidebar-section">Management</div>
            <a href="{{ route('admin.users.index') }}" class="nav-link {{ request()->routeIs('admin.users*') ? 'active' : '' }}">
                <i class="bi bi-people me-2"></i>Users
            </a>
            <a href="{{ route('admin.grades.index') }}" class="nav-link {{ request()->routeIs('admin.grades*') ? 'active' : '' }}">
                <i class="bi bi-journal-text me-2"></i>Grades
            </a>
            <a href="{{ route('admin.courses.index') }}" class="nav-link {{ request()->routeIs('admin.courses*') ? 'active' : '' }}">
                <i class="bi bi-laptop me-2"></i>Courses
            </a>
            <a href="{{ route('admin.books.index') }}" class="nav-link {{ request()->routeIs('admin.books*') ? 'active' : '' }}">
                <i class="bi bi-book me-2"></i>Library Books
            </a>

            <div class="sidebar-section">Portal</div>
            <a href="{{ route('dashboard') }}" class="nav-link">
                <i class="bi bi-arrow-left-circle me-2"></i>Student Portal
            </a>
        </div>
    </nav>

    {{-- Main Area --}}
    <div class="main-content d-flex flex-column">
        {{-- Top Bar --}}
        <div class="topbar d-flex align-items-center justify-content-between">
            <h6 class="mb-0 fw-bold text-muted">@yield('admin_title', 'Admin Dashboard')</h6>
            <div class="d-flex align-items-center gap-3">
                <span class="small text-muted"><i class="bi bi-shield-fill-check text-success me-1"></i>{{ Auth::user()->name }}</span>
                <form method="POST" action="{{ route('logout') }}" class="m-0">
                    @csrf
                    <button type="submit" class="btn btn-sm btn-outline-danger">Logout</button>
                </form>
            </div>
        </div>

        {{-- Flash Messages --}}
        <div class="px-4 pt-3">
            @if(session('status'))
                <div class="alert alert-success alert-dismissible fade show py-2" role="alert">
                    <i class="bi bi-check-circle me-2"></i>{{ session('status') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show py-2" role="alert">
                    <i class="bi bi-exclamation-circle me-2"></i>{{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
        </div>

        <div class="content-area flex-grow-1">
            @yield('content')
        </div>
    </div>
</div>
<script src="{{ asset('vendor/bootstrap/bootstrap.bundle.min.js') }}"></script>
</body>
</html>
