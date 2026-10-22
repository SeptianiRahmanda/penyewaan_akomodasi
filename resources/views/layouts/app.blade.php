<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') · Penyewaan Akomodasi</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .stat-card { border-left: 4px solid var(--bs-primary); }
        .table td, .table th { vertical-align: middle; }
        .bar { height: 8px; border-radius: 4px; background: var(--bs-primary); }
    </style>
</head>
<body class="bg-light">
<nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4">
    <div class="container">
        <a class="navbar-brand fw-semibold" href="{{ route('dashboard') }}">Penyewaan Akomodasi</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#nav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="nav">
            <ul class="navbar-nav me-auto">
                @foreach ([
                    'dashboard' => ['Dashboard', 'dashboard'],
                    'customers.index' => ['Customer', 'customers.*'],
                    'rooms.index' => ['Kamar', 'rooms.*'],
                    'bookings.index' => ['Booking', 'bookings.*'],
                    'payments.index' => ['Pembayaran', 'payments.*'],
                ] as $route => [$label, $pattern])
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs($pattern) ? 'active fw-semibold' : '' }}" href="{{ route($route) }}">{{ $label }}</a>
                    </li>
                @endforeach
            </ul>
            <form action="{{ route('logout') }}" method="POST" class="d-flex align-items-center gap-2 m-0">
                @csrf
                <span class="text-white-50 small">{{ auth()->user()->name }}</span>
                <button class="btn btn-sm btn-outline-light">Keluar</button>
            </form>
        </div>
    </div>
</nav>

<main class="container pb-5">
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show">{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show">{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
    @endif
    @if ($errors->any())
        <div class="alert alert-danger">
            <div class="fw-semibold mb-1">Data belum bisa disimpan:</div>
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @yield('content')
</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
@stack('scripts')
</body>
</html>
