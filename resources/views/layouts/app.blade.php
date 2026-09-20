<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    {{-- Mobile-first: required for AC-03 / AT-06 --}}
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Smart Campus Portal')</title>

    {{-- For production, self-host these (npm) or add SRI hashes. --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    @stack('head')
</head>
<body class="bg-light">
    <main class="px-2 px-md-4">
        @if (session('error'))
            <div class="alert alert-danger mt-3 mb-0" role="alert">{{ session('error') }}</div>
        @endif
        @if (session('status'))
            <div class="alert alert-success mt-3 mb-0" role="status">{{ session('status') }}</div>
        @endif

        @yield('content')
    </main>
</body>
</html>
