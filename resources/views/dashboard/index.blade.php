@extends('layouts.dashboard')

@section('title', 'Dashboard Mahasiswa')

@section('content')

<div class="cr-dash-content">

    {{-- ===================== BRAND PILL (top right) ===================== --}}
    <div class="cr-dash-brand-pill">
        <span class="cr-dash-brand-pill__icon">🟨</span>
        <span class="cr-dash-brand-pill__text">CampusRoom • v1.0 Mahasiswa</span>
    </div>

    {{-- ===================== RINGKASAN STATUS ===================== --}}
    <section class="cr-dash-section">
        <h2 class="cr-dash-section__title">📋 Ringkasan Status</h2>

        <div class="cr-stat-grid">

            {{-- Booking Aktif --}}
            <div class="cr-stat-card cr-stat-card--aktif">
                <div class="cr-stat-card__icon">📅</div>
                <div class="cr-stat-card__body">
                    <p class="cr-stat-card__label">Booking Aktif</p>
                    <p class="cr-stat-card__num">{{ $bookingAktif }}</p>
                </div>
            </div>

            {{-- Disetujui --}}
            <div class="cr-stat-card cr-stat-card--approved">
                <div class="cr-stat-card__icon">✅</div>
                <div class="cr-stat-card__body">
                    <p class="cr-stat-card__label">Disetujui</p>
                    <p class="cr-stat-card__num">{{ $bookingDisetujui }}</p>
                </div>
            </div>

            {{-- Menunggu --}}
            <div class="cr-stat-card cr-stat-card--pending">
                <div class="cr-stat-card__icon">⌛</div>
                <div class="cr-stat-card__body">
                    <p class="cr-stat-card__label">Menunggu</p>
                    <p class="cr-stat-card__num">{{ $bookingMenunggu }}</p>
                </div>
            </div>

            {{-- Ditolak --}}
            <div class="cr-stat-card cr-stat-card--rejected">
                <div class="cr-stat-card__icon">❌</div>
                <div class="cr-stat-card__body">
                    <p class="cr-stat-card__label">Ditolak</p>
                    <p class="cr-stat-card__num">{{ $bookingDitolak }}</p>
                </div>
            </div>

        </div>
    </section>

    {{-- ===================== JADWAL MENDATANG ===================== --}}
    <section class="cr-dash-section">
        <h2 class="cr-dash-section__title">📅 Jadwal Mendatang</h2>

        <div class="cr-jadwal-list">
            @forelse($jadwalMendatang as $booking)
            <div class="cr-jadwal-item">
                <div class="cr-jadwal-item__dot
                    {{ $booking->status === 'approved' ? 'cr-jadwal-item__dot--approved' : 'cr-jadwal-item__dot--pending' }}">
                </div>
                <div class="cr-jadwal-item__icon">
                    {{ $booking->jenis === 'perkuliahan' ? '🎓' : '🎯' }}
                </div>
                <div class="cr-jadwal-item__info">
                    <span class="cr-jadwal-item__room">
                        {{ $booking->rooms->first()?->nama_ruangan ?? 'Ruangan' }}
                    </span>
                    <span class="cr-jadwal-item__type">
                        {{ $booking->jenis === 'perkuliahan' ? 'Perkuliahan' : ($booking->kegiatan?->nama_kegiatan ?? 'Kegiatan') }}
                    </span>
                </div>
                <div class="cr-jadwal-item__time">
                    {{ \Carbon\Carbon::parse($booking->tanggal)->locale('id')->isoFormat('ddd, DD-MM') }}, {{ substr($booking->jam_mulai, 0, 5) }}
                </div>
                <div class="cr-jadwal-item__status">
                    @if($booking->status === 'approved')
                        <span class="cr-badge cr-badge--approved">Approved</span>
                    @else
                        <span class="cr-badge cr-badge--pending">Pending</span>
                    @endif
                </div>
            </div>
            @empty
            <div class="cr-jadwal-empty">
                <span>📋</span>
                <p>Belum ada jadwal mendatang</p>
                <a href="/booking" class="cr-btn-booking-empty">Booking Sekarang</a>
            </div>
            @endforelse
        </div>
    </section>

    {{-- ===================== REKOMENDASI RUANGAN ===================== --}}
    <section class="cr-dash-section">
        <h2 class="cr-dash-section__title">🏫 Rekomendasi Ruangan</h2>

        <div class="cr-room-grid">
            @forelse($rekomendasiRuangan as $room)
            <div class="cr-room-card">
                <div class="cr-room-card__img">
                    <div class="cr-room-card__img-placeholder">
                        <svg viewBox="0 0 80 60" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <!-- Classroom illustration -->
                            <rect width="80" height="60" fill="#E8F4F8" rx="4"/>
                            <!-- Whiteboard -->
                            <rect x="8" y="8" width="50" height="22" rx="2" fill="#F0F8FF" stroke="#B0D4E8" stroke-width="1"/>
                            <!-- Desk rows -->
                            <rect x="10" y="36" width="14" height="6" rx="1.5" fill="#C8DDE8"/>
                            <rect x="28" y="36" width="14" height="6" rx="1.5" fill="#C8DDE8"/>
                            <rect x="46" y="36" width="14" height="6" rx="1.5" fill="#C8DDE8"/>
                            <rect x="10" y="46" width="14" height="6" rx="1.5" fill="#C8DDE8"/>
                            <rect x="28" y="46" width="14" height="6" rx="1.5" fill="#C8DDE8"/>
                            <rect x="46" y="46" width="14" height="6" rx="1.5" fill="#C8DDE8"/>
                            <!-- Teacher desk -->
                            <rect x="62" y="36" width="12" height="8" rx="1.5" fill="#A8C8D8"/>
                        </svg>
                    </div>
                </div>
                <div class="cr-room-card__body">
                    <p class="cr-room-card__name">{{ $room->nama_ruangan }}</p>
                    <p class="cr-room-card__meta">{{ $room->kapasitas }} Capacity &nbsp; AMS</p>
                    <a href="/booking?room={{ $room->room_id }}" class="cr-room-card__btn">
                        Booking
                    </a>
                </div>
            </div>
            @empty
            <div class="cr-rooms-empty">
                <p>Tidak ada ruangan tersedia saat ini</p>
            </div>
            @endforelse
        </div>
    </section>

</div>

@endsection