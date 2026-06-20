<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin') — CampusRoom</title>
    @vite(['resources/css/app.css', 'resources/css/landing.css', 'resources/css/login.css', 'resources/css/dashboard.css', 'resources/css/admin.css', 'resources/js/app.js'])
</head>
<body class="cr-dashboard-body">

<div class="cr-dash-wrap">

    {{-- ===================== SIDEBAR ADMIN ===================== --}}
    <aside class="cr-sidebar cr-sidebar--admin" id="sidebar">

        {{-- Logo --}}
        <div class="cr-sidebar__logo">
            <span class="cr-logo-campus">Campus</span><span class="cr-logo-room">Room</span>
            <span class="cr-sidebar__version">v1.0</span>
        </div>

        {{-- Profile Admin --}}
        @php $admin = session('user'); @endphp
        <div class="cr-sidebar__profile">
            <div class="cr-sidebar__avatar-wrap">
                @if(!empty($admin->foto))
                    <img src="{{ asset('storage/' . $admin->foto) }}" alt="{{ $admin->nama }}" class="cr-sidebar__avatar-img">
                @else
                    <div class="cr-sidebar__avatar-initials cr-sidebar__avatar-initials--admin">
                        {{ strtoupper(substr($admin->nama ?? 'A', 0, 1)) }}
                    </div>
                @endif
                {{-- verified badge --}}
                <span class="cr-sidebar__verified">✓</span>
            </div>
            <div class="cr-sidebar__profile-info">
                <p class="cr-sidebar__name">{{ $admin->nama ?? 'Admin' }}</p>
                <p class="cr-sidebar__role">(ADMIN role)</p>
                <span class="cr-sidebar__admin-badge">ADMIN</span>
            </div>
        </div>

        {{-- Notifikasi --}}
        <!-- <div class="cr-sidebar__notif">
            <span class="cr-sidebar__notif-icon">🔔</span>
            <span class="cr-sidebar__notif-text">Notifikasi</span>
            <span class="cr-sidebar__notif-badge">3</span>
            <span class="cr-sidebar__notif-dot"></span>
        </div> -->

        <div class="cr-sidebar__divider"></div>

        {{-- Nav Admin --}}
        <nav class="cr-sidebar__nav">
            <a href="/admin/dashboard"
               class="cr-sidebar__nav-item {{ request()->is('admin/dashboard') ? 'cr-sidebar__nav-item--active' : '' }}">
                <span class="cr-nav-icon">⊞</span>
                <span class="cr-nav-label">Dashboard</span>
            </a>
            <a href="/rooms"
               class="cr-sidebar__nav-item {{ request()->is('rooms*') ? 'cr-sidebar__nav-item--active' : '' }}">
                <span class="cr-nav-icon">🏢</span>
                <span class="cr-nav-label">Ruangan</span>
            </a>
            <a href="/schedules"
               class="cr-sidebar__nav-item {{ request()->is('schedules*') ? 'cr-sidebar__nav-item--active' : '' }}">
                <span class="cr-nav-icon">📅</span>
                <span class="cr-nav-label">Jadwal</span>
            </a>
            <a href="/admin/all-bookings"
               class="cr-sidebar__nav-item {{ request()->is('admin/all-bookings') ? 'cr-sidebar__nav-item--active' : '' }}">
                <span class="cr-nav-icon">📋</span>
                <span class="cr-nav-label">Data Booking</span>
            </a>
            <a href="/admin/bookings"
               class="cr-sidebar__nav-item {{ request()->is('admin/bookings') ? 'cr-sidebar__nav-item--active' : '' }}">
                <span class="cr-nav-icon">✅</span>
                <span class="cr-nav-label">
                    Approval
                    @php $pendingCount = \App\Models\Booking::where('status','pending')->count(); @endphp
                    @if($pendingCount > 0)
                        <span class="cr-nav-badge">{{ $pendingCount }}</span>
                    @endif
                </span>
            </a>
            <!-- <a href="/admin/kegiatan"
               class="cr-sidebar__nav-item {{ request()->is('admin/kegiatan*') ? 'cr-sidebar__nav-item--active' : '' }}">
                <span class="cr-nav-icon">🎯</span>
                <span class="cr-nav-label">Kegiatan</span>
            </a> -->
            <a href="/admin/notifikasi"
               class="cr-sidebar__nav-item {{ request()->is('admin/notifikasi') ? 'cr-sidebar__nav-item--active' : '' }}">
                <span class="cr-nav-icon">🔔</span>
                <span class="cr-nav-label">Notifikasi</span>
            </a>
        </nav>

        <div class="cr-sidebar__spacer"></div>

        {{-- Profil di bawah (seperti user) --}}
        <div class="cr-sidebar__bottom-profile">
            <div class="cr-sidebar__bottom-avatar">
                @if(!empty($admin->foto))
                    <img src="{{ asset('storage/' . $admin->foto) }}" alt="{{ $admin->nama }}" class="cr-sidebar__avatar-img cr-sidebar__avatar-img--sm">
                @else
                    <div class="cr-sidebar__avatar-initials cr-sidebar__avatar-initials--admin cr-sidebar__avatar-initials--sm">
                        {{ strtoupper(substr($admin->nama ?? 'A', 0, 1)) }}
                    </div>
                @endif
            </div>
            <div class="cr-sidebar__bottom-info">
                <p class="cr-sidebar__bottom-name">{{ $admin->nama ?? 'Admin' }}</p>
                <p class="cr-sidebar__bottom-nim">{{ $admin->nim_nip ?? '' }}</p>
            </div>
            <a href="/logout" class="cr-sidebar__bottom-logout" title="Keluar">↪</a>
        </div>

    </aside>

    {{-- ===================== MAIN CONTENT ===================== --}}
    <main class="cr-dash-main">
        @yield('content')
    </main>

</div>

</body>
</html>