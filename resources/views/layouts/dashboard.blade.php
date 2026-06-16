<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') — CampusRoom</title>
    @vite(['resources/css/app.css', 'resources/css/landing.css', 'resources/css/login.css', 'resources/css/dashboard.css', 'resources/js/app.js'])
</head>
<body class="cr-dashboard-body">

<div class="cr-dash-wrap">

    {{-- ===================== SIDEBAR ===================== --}}
    <aside class="cr-sidebar" id="sidebar">

        {{-- Logo --}}
        <div class="cr-sidebar__logo">
            <span class="cr-logo-campus">Campus</span><span class="cr-logo-room">Room</span>
            <span class="cr-sidebar__version">v1.0</span>
        </div>

        {{-- Profile --}}
        <div class="cr-sidebar__profile">
            @php $user = session('user'); @endphp
            <div class="cr-sidebar__avatar-wrap">
                @if(!empty($user->foto))
                    <img src="{{ asset('storage/' . $user->foto) }}" alt="{{ $user->nama }}" class="cr-sidebar__avatar-img">
                @else
                    <div class="cr-sidebar__avatar-initials">
                        {{ strtoupper(substr($user->nama ?? 'U', 0, 1)) }}
                    </div>
                @endif
            </div>
            <div class="cr-sidebar__profile-info">
                <p class="cr-sidebar__name">{{ $user->nama ?? 'User' }}! 👋</p>
                <p class="cr-sidebar__role">Informatika</p>
                <p class="cr-sidebar__nim">{{ $user->nim_nip ?? '' }}</p>
            </div>
        </div>

        {{-- Notifikasi --}}
        <div class="cr-sidebar__notif">
            <span class="cr-sidebar__notif-icon">🔔</span>
            <span class="cr-sidebar__notif-text">Notifikasi</span>
            <span class="cr-sidebar__notif-badge">3</span>
            <span class="cr-sidebar__notif-dot"></span>
        </div>

        <div class="cr-sidebar__divider"></div>

        {{-- Nav --}}
        <nav class="cr-sidebar__nav">
            <a href="/dashboard"
               class="cr-sidebar__nav-item {{ request()->is('dashboard') ? 'cr-sidebar__nav-item--active' : '' }}">
                <span class="cr-nav-icon">🏠</span>
                <span class="cr-nav-label">Beranda</span>
            </a>
            <a href="/rooms"
               class="cr-sidebar__nav-item {{ request()->is('rooms*') ? 'cr-sidebar__nav-item--active' : '' }}">
                <span class="cr-nav-icon">≡</span>
                <span class="cr-nav-label">Daftar Ruangan</span>
            </a>
            <a href="/booking/history"
               class="cr-sidebar__nav-item {{ request()->is('booking/history') ? 'cr-sidebar__nav-item--active' : '' }}">
                <span class="cr-nav-icon">🕐</span>
                <span class="cr-nav-label">Booking Saya</span>
            </a>
            <a href="/jadwal-saya"
                    class="cr-sidebar__nav-item {{ request()->is('jadwal-saya*') ? 'cr-sidebar__nav-item--active' : '' }}">
                <span class="cr-nav-icon">📅</span>
                <span class="cr-nav-label">Jadwal Saya</span>
            </a>
            <a href="/profile"
               class="cr-sidebar__nav-item {{ request()->is('profile') ? 'cr-sidebar__nav-item--active' : '' }}">
                <span class="cr-nav-icon">👤</span>
                <span class="cr-nav-label">Profil</span>
            </a>
        </nav>

        <div class="cr-sidebar__spacer"></div>

        {{-- Logout --}}
        <a href="/logout" class="cr-sidebar__logout">
            <span class="cr-nav-icon">↪</span>
            <span class="cr-nav-label">Keluar</span>
        </a>

    </aside>

    {{-- ===================== MAIN CONTENT ===================== --}}
    <main class="cr-dash-main">
        @yield('content')
    </main>

</div>

</body>
</html>