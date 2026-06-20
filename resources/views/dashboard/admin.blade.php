@extends('layouts.admin')

@section('title', 'Dashboard Admin')

@section('content')
<div class="cr-dash-content cr-adm-content">

    {{-- Brand pill --}}
    <div class="cr-dash-brand-pill">
        <span class="cr-dash-brand-pill__icon">🟨</span>
        <span class="cr-dash-brand-pill__text">CampusRoom • v1.0 Admin</span>
    </div>

    {{-- ===== WELCOME BANNER ===== --}}
    <section class="cr-adm-welcome">
        <div class="cr-adm-welcome__copy">
            <h1 class="cr-adm-welcome__title">Welcome Banner Admin</h1>
            <p class="cr-adm-welcome__sub">
                Halo, {{ session('user')->nama ?? 'Admin' }}! · Hari ini ada
                <span class="cr-adm-welcome__badge">{{ $pendingBooking }}</span>
                booking menunggu persetujuan
            </p>
        </div>
        <a href="/admin/bookings" class="cr-adm-welcome__link">
            Lihat Semua Approvals →
        </a>
    </section>

    {{-- ===== STAT CARDS ===== --}}
    <div class="cr-adm-stat-grid">

        <div class="cr-adm-stat-card">
            <div class="cr-adm-stat-card__body">
                <p class="cr-adm-stat-card__label">Total Ruangan</p>
                <p class="cr-adm-stat-card__num">{{ $totalRoom }}</p>
            </div>
            <div class="cr-adm-stat-card__wave">
                <svg viewBox="0 0 80 30" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M0 20 Q10 8 20 18 Q30 28 40 15 Q50 5 60 18 Q70 28 80 15" stroke="rgba(244,180,0,0.7)" stroke-width="2.5" fill="none" stroke-linecap="round"/>
                </svg>
            </div>
        </div>

        <div class="cr-adm-stat-card">
            <div class="cr-adm-stat-card__body">
                <p class="cr-adm-stat-card__label">Booking Hari ini</p>
                <p class="cr-adm-stat-card__num">{{ $bookingHariIni }}</p>
            </div>
            <div class="cr-adm-stat-card__wave">
                <svg viewBox="0 0 80 30" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M0 22 Q15 10 25 20 Q35 28 50 12 Q60 4 80 18" stroke="rgba(244,180,0,0.7)" stroke-width="2.5" fill="none" stroke-linecap="round"/>
                </svg>
            </div>
        </div>

        <div class="cr-adm-stat-card">
            <div class="cr-adm-stat-card__body">
                <p class="cr-adm-stat-card__label">Menunggu Approval</p>
                <p class="cr-adm-stat-card__num">{{ $pendingBooking }}</p>
            </div>
            <div class="cr-adm-stat-card__wave">
                <svg viewBox="0 0 80 30" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M0 18 Q20 28 30 14 Q40 4 55 20 Q65 28 80 14" stroke="rgba(244,180,0,0.7)" stroke-width="2.5" fill="none" stroke-linecap="round"/>
                </svg>
            </div>
        </div>

        <div class="cr-adm-stat-card">
            <div class="cr-adm-stat-card__body">
                <p class="cr-adm-stat-card__label">Ruangan Aktif</p>
                <p class="cr-adm-stat-card__num">{{ $ruanganAktif }}</p>
            </div>
            <div class="cr-adm-stat-card__wave">
                <svg viewBox="0 0 80 30" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M0 15 Q10 25 25 12 Q40 2 55 18 Q65 26 80 12" stroke="rgba(244,180,0,0.7)" stroke-width="2.5" fill="none" stroke-linecap="round"/>
                </svg>
            </div>
        </div>

    </div>

    {{-- ===== MAIN GRID (3 cols) ===== --}}
    <div class="cr-adm-main-grid">

        {{-- ===== LEFT: Menunggu Persetujuan ===== --}}
        <div class="cr-adm-col-left">
            <div class="cr-adm-panel">
                <div class="cr-adm-panel__head">
                    <h2 class="cr-adm-panel__title">
                        ⌛ Menunggu Persetujuan ({{ $pendingBooking }})
                    </h2>
                    <a href="/admin/bookings" class="cr-adm-panel__link">Lihat Semua →</a>
                </div>

                <div class="cr-adm-approval-list">
                    @forelse($pendingBookings as $booking)
                    <div class="cr-adm-approval-card">
                        <div class="cr-adm-approval-card__top">
                            <div>
                                <p class="cr-adm-approval-card__name">
                                    {{ $booking->user?->nama ?? 'User' }}
                                </p>
                                <!-- <p class="cr-adm-approval-card__meta">
                                    {{ $booking->jenis === 'perkuliahan'
                                        ? 'Perkuliahan'
                                        : ($booking->kegiatan?->nama_kegiatan ?? 'Kegiatan') }},
                                    {{ $booking->rooms->pluck('nama_ruangan')->join(', ') }}
                                </p> -->
                                <p class="cr-adm-approval-card__rooms">
                                    {{ $booking->rooms->pluck('nama_ruangan')->join(', ') }}
                                    @if($booking->rooms->count() > 1)
                                        <span class="cr-adm-multi-badge">{{ $booking->rooms->count() }} ruangan</span>
                                    @endif
                                </p>
                            </div>
                            <p class="cr-adm-approval-card__date">
                                {{ \Carbon\Carbon::parse($booking->tanggal)->format('d-m-Y') }}
                            </p>
                        </div>

                        @if($booking->surat)
                        <div class="cr-adm-approval-card__surat">
                            <span class="cr-adm-surat-badge">✅ Surat Ada</span>
                        </div>
                        @endif

                        <div class="cr-adm-approval-card__actions">
                            <a href="/admin/bookings/{{ $booking->booking_id }}/approve"
                               class="cr-adm-btn cr-adm-btn--approve"
                               onclick="return confirm('Setujui booking ini?')">
                                ✓ Setujui
                            </a>
                            <a href="/admin/bookings/{{ $booking->booking_id }}/reject"
                               class="cr-adm-btn cr-adm-btn--reject"
                               onclick="return confirm('Tolak booking ini?')">
                                ✕ Tolak
                            </a>
                            <a href="/booking/detail/{{ $booking->booking_id }}"
                               class="cr-adm-btn cr-adm-btn--detail">
                                👁 Detail
                            </a>
                        </div>
                    </div>
                    @empty
                    <div class="cr-adm-empty">
                        <span>✅</span>
                        <p>Tidak ada booking yang menunggu</p>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- ===== RIGHT: Chart + Aktivitas + Room Status ===== --}}
        <div class="cr-adm-col-right">

            {{-- Chart Booking per Minggu --}}
            <div class="cr-adm-panel cr-adm-panel--chart">
                <h2 class="cr-adm-panel__title">📊 Chart: Booking per Minggu</h2>
                <div class="cr-adm-chart">
                    @php $maxVal = max(1, collect($chartData)->max('count')); @endphp
                    @foreach($chartData as $day)
                    <div class="cr-adm-chart__row">
                        <span class="cr-adm-chart__label">{{ $day['label'] }}</span>
                        <div class="cr-adm-chart__bar-wrap">
                            <div class="cr-adm-chart__bar"
                                 style="width: {{ $day['count'] > 0 ? max(8, round(($day['count'] / $maxVal) * 100)) : 0 }}%">
                            </div>
                        </div>
                        <span class="cr-adm-chart__val">{{ $day['count'] }}</span>
                    </div>
                    @endforeach
                </div>
            </div>

            {{-- Aktivitas Terbaru --}}
            <div class="cr-adm-panel cr-adm-panel--aktivitas">
                <h2 class="cr-adm-panel__title">🕐 Aktivitas Terbaru</h2>
                <div class="cr-adm-aktivitas-list">
                    @foreach($aktivitasTerbaru as $act)
                    <div class="cr-adm-aktivitas-row">
                        <div class="cr-adm-aktivitas-dot cr-adm-aktivitas-dot--{{ $act->status }}"></div>
                        <div class="cr-adm-aktivitas-info">
                            <p class="cr-adm-aktivitas-label">
                                @if($act->status === 'pending')   Diajukan
                                @elseif($act->status === 'approved') Disetujui
                                @else Ditolak
                                @endif
                            </p>
                            <p class="cr-adm-aktivitas-time">
                                {{ \Carbon\Carbon::parse($act->updated_at ?? $act->created_at)->format('d M H:i') }}
                            </p>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

        </div>

    </div>

    {{-- ===== STATUS OVERVIEW RUANGAN ===== --}}
    <div class="cr-adm-panel cr-adm-panel--rooms">
        <div class="cr-adm-panel__head">
            <h2 class="cr-adm-panel__title">🏢 Status Overview Ruangan</h2>
            <div class="cr-adm-room-legend">
                <span class="cr-adm-legend-dot cr-adm-legend-dot--available"></span><span>available</span>
                <span class="cr-adm-legend-dot cr-adm-legend-dot--inuse"></span><span>in use</span>
                <span class="cr-adm-legend-dot cr-adm-legend-dot--maintenance"></span><span>maintenance</span>
            </div>
        </div>

        <div class="cr-adm-room-grid">
            @foreach($rooms as $room)
            @php
                $isInUse = in_array($room->room_id, $roomsBookedToday);
                $isMaintenance = $room->status === 'tidak tersedia' && !$isInUse;
                $statusClass = $isInUse ? 'inuse' : ($isMaintenance ? 'maintenance' : 'available');
                $statusLabel = $isInUse ? 'In use' : ($isMaintenance ? 'Maintenance' : 'Available');
            @endphp
            <div class="cr-adm-room-dot cr-adm-room-dot--{{ $statusClass }}"
                 title="{{ $room->nama_ruangan }} — {{ $statusLabel }}">
                <div class="cr-adm-room-dot__tooltip">
                    <strong>{{ $room->nama_ruangan }}</strong>
                    <span>{{ $statusLabel }}</span>
                </div>
            </div>
            @endforeach
        </div>
    </div>

</div>
@endsection