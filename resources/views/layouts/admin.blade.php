<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'TNBO Insights Admin' }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@400;500;700&family=Source+Sans+3:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://unpkg.com/htmx.org@1.9.12"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script>
    <style>
        :root {
            --admin-bg: #f4f1ea;
            --admin-panel: #fffdf8;
            --admin-ink: #16202a;
            --admin-muted: #61717f;
            --admin-accent: #c25b2d;
            --admin-accent-dark: #8b3b18;
            --admin-border: #dfd5c6;
            --admin-shadow: 0 18px 45px rgba(22, 32, 42, 0.08);
        }

        body {
            min-height: 100vh;
            font-family: "Source Sans 3", sans-serif;
            color: var(--admin-ink);
            background:
                radial-gradient(circle at top right, rgba(194, 91, 45, 0.10), transparent 28%),
                linear-gradient(180deg, #f8f4ed 0%, var(--admin-bg) 100%);
        }

        h1, h2, h3, h4, h5, .navbar-brand, .nav-link {
            font-family: "Space Grotesk", sans-serif;
        }

        .admin-shell {
            display: grid;
            grid-template-columns: 280px minmax(0, 1fr);
            min-height: 100vh;
        }

        .admin-sidebar {
            background: linear-gradient(180deg, #13212b 0%, #20384a 100%);
            color: #f4f1ea;
            padding: 2rem 1.4rem;
            position: sticky;
            top: 0;
            height: 100vh;
        }

        .admin-sidebar .brand-mark {
            width: 2.75rem;
            height: 2.75rem;
            border-radius: 0.9rem;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #f0c27b 0%, #c25b2d 100%);
            color: #13212b;
            font-weight: 700;
        }

        .admin-sidebar .nav-link {
            color: rgba(244, 241, 234, 0.82);
            border-radius: 0.9rem;
            padding: 0.75rem 0.95rem;
            margin-bottom: 0.35rem;
            transition: 150ms ease-in-out;
        }

        .admin-sidebar .nav-link.active,
        .admin-sidebar .nav-link:hover {
            background: rgba(255, 255, 255, 0.1);
            color: #fffdf8;
        }

        .admin-main {
            padding: 1.5rem;
        }

        .admin-topbar,
        .panel-card {
            background: rgba(255, 253, 248, 0.92);
            border: 1px solid var(--admin-border);
            border-radius: 1.35rem;
            box-shadow: var(--admin-shadow);
        }

        .admin-topbar {
            padding: 1rem 1.2rem;
        }

        .panel-card {
            padding: 1.2rem;
        }

        .metric-card {
            background: linear-gradient(180deg, #fffdf8 0%, #f9f2e7 100%);
            border: 1px solid var(--admin-border);
            border-radius: 1.2rem;
            padding: 1rem 1.1rem;
            height: 100%;
        }

        .metric-card .metric-label {
            color: var(--admin-muted);
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 0.06em;
        }

        .metric-card .metric-value {
            font-family: "Space Grotesk", sans-serif;
            font-size: 2rem;
            font-weight: 700;
            line-height: 1;
        }

        .page-title {
            font-size: clamp(1.7rem, 1.4rem + 1vw, 2.5rem);
            margin-bottom: 0.25rem;
        }

        .page-subtitle {
            color: var(--admin-muted);
            margin-bottom: 0;
        }

        .table > :not(caption) > * > * {
            border-bottom-color: rgba(22, 32, 42, 0.08);
        }

        .table thead th {
            color: var(--admin-muted);
            font-size: 0.82rem;
            text-transform: uppercase;
            letter-spacing: 0.04em;
        }

        .badge-soft {
            background: rgba(194, 91, 45, 0.12);
            color: var(--admin-accent-dark);
            border: 1px solid rgba(194, 91, 45, 0.2);
        }

        .form-control, .form-select {
            border-radius: 0.9rem;
            border-color: #d3c8b8;
            padding: 0.75rem 0.9rem;
        }

        .btn-primary {
            background: var(--admin-accent);
            border-color: var(--admin-accent);
        }

        .btn-primary:hover,
        .btn-primary:focus {
            background: var(--admin-accent-dark);
            border-color: var(--admin-accent-dark);
        }

        .section-label {
            font-family: "Space Grotesk", sans-serif;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            font-size: 0.78rem;
            color: var(--admin-muted);
        }

        .info-button {
            width: 1.6rem;
            height: 1.6rem;
            border-radius: 999px;
            border: 1px solid rgba(97, 113, 127, 0.35);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: rgba(255, 253, 248, 0.75);
            color: var(--admin-muted);
            font-family: "Space Grotesk", sans-serif;
            font-size: 0.82rem;
            font-weight: 700;
            line-height: 1;
        }

        .info-button:hover,
        .info-button:focus {
            border-color: var(--admin-accent);
            color: var(--admin-accent-dark);
            background: rgba(194, 91, 45, 0.08);
        }

        @media (max-width: 991.98px) {
            .admin-shell {
                grid-template-columns: 1fr;
            }

            .admin-sidebar {
                position: relative;
                height: auto;
            }
        }
    </style>
    @stack('styles')
</head>
<body>
<div class="admin-shell">
    <aside class="admin-sidebar">
        <div class="d-flex align-items-center gap-3 mb-4">
            <span class="brand-mark">I</span>
            <div>
                <div class="fw-bold">TNBO Insights</div>
                <div class="small text-white-50">Admin Console</div>
            </div>
        </div>

        <div class="small text-uppercase text-white-50 mb-2">Operations</div>
        <nav class="nav flex-column mb-4">
            <a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}">Dashboard</a>
            <a class="nav-link {{ request()->routeIs('admin.reports.*') ? 'active' : '' }}" href="{{ route('admin.reports.overview') }}">Reports</a>
            <a class="nav-link {{ request()->routeIs('admin.getting-started') ? 'active' : '' }}" href="{{ route('admin.getting-started') }}">Getting Started</a>
            <a class="nav-link {{ request()->routeIs('admin.system-log.*') ? 'active' : '' }}" href="{{ route('admin.system-log.show') }}">System Logs</a>
        </nav>

        <div class="small text-uppercase text-white-50 mb-2">Inventory</div>
        <nav class="nav flex-column mb-4">
            <a class="nav-link {{ request()->routeIs('admin.sponsors.*') ? 'active' : '' }}" href="{{ route('admin.sponsors.index') }}">Sponsors</a>
            <a class="nav-link {{ request()->routeIs('admin.placements.*') ? 'active' : '' }}" href="{{ route('admin.placements.index') }}">Placements</a>
            <a class="nav-link {{ request()->routeIs('admin.campaigns.*') ? 'active' : '' }}" href="{{ route('admin.campaigns.index') }}">Campaigns</a>
            <a class="nav-link {{ request()->routeIs('admin.creatives.*') ? 'active' : '' }}" href="{{ route('admin.creatives.index') }}">Creatives</a>
            <a class="nav-link {{ request()->routeIs('admin.targets.*') ? 'active' : '' }}" href="{{ route('admin.targets.index') }}">Targets</a>
        </nav>

        <div class="mt-auto pt-4 border-top border-white border-opacity-10">
            <div class="small text-white-50 mb-2">Signed in as</div>
            <div class="fw-semibold mb-3">{{ auth()->user()->name }}</div>
            <form method="POST" action="{{ route('admin.logout') }}">
                @csrf
                <button type="submit" class="btn btn-outline-light btn-sm w-100">Sign out</button>
            </form>
        </div>
    </aside>

    <main class="admin-main">
        <div class="admin-topbar d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3 mb-4">
            <div>
                <h1 class="page-title">{{ $heading ?? 'Admin' }}</h1>
                <p class="page-subtitle">{{ $subheading ?? 'Operational view for TNBO Insights.' }}</p>
            </div>
            @isset($topbarActions)
                <div>{!! $topbarActions !!}</div>
            @endisset
        </div>

        @if (session('status'))
            <div class="alert alert-success border-0 shadow-sm rounded-4">{{ session('status') }}</div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger border-0 shadow-sm rounded-4">
                <div class="fw-semibold mb-2">Please correct the highlighted issues.</div>
                <ul class="mb-0 ps-3">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @yield('content')
    </main>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
@stack('scripts')
</body>
</html>
