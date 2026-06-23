@extends('layouts.dashboard')

@section('title', 'Jadwal Saya')

@section('content')
<div class="cr-dash-content cr-schedule-page">

    <div class="cr-dash-brand-pill">
        <span class="cr-dash-brand-pill__icon">🟨</span>
        <span class="cr-dash-brand-pill__text">CampusRoom • v1.0 Mahasiswa</span>
    </div>

    <section class="cr-schedule-hero">
        <div class="cr-schedule-hero__intro">
            <div class="cr-schedule-hero__eyebrow-wrap">
                <p class="cr-schedule-hero__eyebrow">📅 JADWAL SAYA</p>
                <span class="cr-schedule-hero__live">LIVE</span>
            </div>
            <h1 class="cr-schedule-hero__title">Agenda booking yang rapi, cepat dibaca, dan tetap elegan.</h1>
            <p class="cr-schedule-hero__desc">
                Semua booking aktif ditampilkan dalam satu alur visual yang konsisten dengan dashboard CampusRoom.
                Fokusnya ada di jadwal berikutnya, status persetujuan, dan agenda yang paling dekat.
            </p>

            <div class="cr-schedule-hero__chips">
                <span class="cr-schedule-chip cr-schedule-chip--gold">{{ $jadwalSaya->count() }} agenda aktif</span>
                <span class="cr-schedule-chip cr-schedule-chip--green">{{ $jadwalApproved }} disetujui</span>
                <span class="cr-schedule-chip cr-schedule-chip--muted">{{ $jadwalPending }} menunggu</span>
            </div>
        </div>

        <div class="cr-schedule-hero__card">
            <div class="cr-schedule-hero__card-head">
                <span class="cr-schedule-hero__card-label">Jadwal Berikutnya</span>
                <span class="cr-schedule-hero__card-accent">NEXT UP</span>
            </div>

            @if($jadwalBerikutnya)
                <div class="cr-schedule-hero__orbit">
                    <div class="cr-schedule-hero__orbit-ring cr-schedule-hero__orbit-ring--one"></div>
                    <div class="cr-schedule-hero__orbit-ring cr-schedule-hero__orbit-ring--two"></div>
                    <div class="cr-schedule-hero__orbit-dot"></div>
                </div>

                <p class="cr-schedule-hero__card-title">
                    {{ $jadwalBerikutnya->jenis === 'perkuliahan' ? 'Perkuliahan' : ($jadwalBerikutnya->kegiatan?->nama_kegiatan ?? 'Kegiatan') }}
                </p>
                <p class="cr-schedule-hero__card-meta">
                    {{ $jadwalBerikutnya->rooms->pluck('nama_ruangan')->implode(', ') ?: 'Ruangan' }}
                </p>

                <div class="cr-schedule-hero__card-timebox">
                    <span class="cr-schedule-hero__card-date">
                        {{ \Carbon\Carbon::parse($jadwalBerikutnya->tanggal)->locale('id')->translatedFormat('l, d F Y') }}
                    </span>
                    <span class="cr-schedule-hero__card-time">
                        {{ substr($jadwalBerikutnya->jam_mulai, 0, 5) }} - {{ substr($jadwalBerikutnya->jam_selesai, 0, 5) }}
                    </span>
                </div>

                <span class="cr-schedule-status cr-schedule-status--{{ $jadwalBerikutnya->status }}">
                    {{ strtoupper($jadwalBerikutnya->status) }}
                </span>
            @else
                <p class="cr-schedule-hero__card-title">Belum ada jadwal</p>
                <p class="cr-schedule-hero__card-meta">Silakan buat booking baru untuk melihat agenda di sini.</p>
                <a href="/booking" class="cr-schedule-hero__card-cta">Booking Sekarang</a>
            @endif
        </div>
    </section>

    <section class="cr-dash-section">
        <h2 class="cr-dash-section__title">📊 Ringkasan Jadwal</h2>

        <div class="cr-stat-grid">
            <div class="cr-stat-card cr-stat-card--aktif">
                <div class="cr-stat-card__icon">📅</div>
                <div class="cr-stat-card__body">
                    <p class="cr-stat-card__label">Total Jadwal</p>
                    <p class="cr-stat-card__num">{{ $jadwalSaya->count() }}</p>
                </div>
            </div>

            <div class="cr-stat-card cr-stat-card--approved">
                <div class="cr-stat-card__icon">✅</div>
                <div class="cr-stat-card__body">
                    <p class="cr-stat-card__label">Disetujui</p>
                    <p class="cr-stat-card__num">{{ $jadwalApproved }}</p>
                </div>
            </div>

            <div class="cr-stat-card cr-stat-card--pending">
                <div class="cr-stat-card__icon">⌛</div>
                <div class="cr-stat-card__body">
                    <p class="cr-stat-card__label">Menunggu</p>
                    <p class="cr-stat-card__num">{{ $jadwalPending }}</p>
                </div>
            </div>

            <div class="cr-stat-card cr-stat-card--rejected">
                <div class="cr-stat-card__icon">📆</div>
                <div class="cr-stat-card__body">
                    <p class="cr-stat-card__label">Hari Ini</p>
                    <p class="cr-stat-card__num">{{ $jadwalHariIni->count() }}</p>
                </div>
            </div>
        </div>
    </section>

    <section class="cr-dash-section">
        <div class="cr-schedule-section-head">
            <h2 class="cr-dash-section__title">🗓️ Agenda Minggu Ini</h2>
            <div class="cr-schedule-legend">
                <span class="cr-schedule-legend__item"><i class="cr-schedule-legend__dot cr-schedule-legend__dot--approved"></i> Disetujui</span>
                <span class="cr-schedule-legend__item"><i class="cr-schedule-legend__dot cr-schedule-legend__dot--pending"></i> Menunggu</span>
            </div>
        </div>

        <div class="cr-schedule-strip">
            <div class="cr-schedule-strip__item">
                <span class="cr-schedule-strip__num">{{ $jadwalHariIni->count() }}</span>
                <span class="cr-schedule-strip__label">Hari Ini</span>
            </div>
            <div class="cr-schedule-strip__divider"></div>
            <div class="cr-schedule-strip__item">
                <span class="cr-schedule-strip__num">{{ $jadwalMingguIni->count() }}</span>
                <span class="cr-schedule-strip__label">Minggu Ini</span>
            </div>
            <div class="cr-schedule-strip__divider"></div>
            <div class="cr-schedule-strip__item">
                <span class="cr-schedule-strip__num">{{ $jadwalSaya->count() - $jadwalMingguIni->count() }}</span>
                <span class="cr-schedule-strip__label">Setelah Ini</span>
            </div>
        </div>
    </section>

    <section class="cr-dash-section">
        <h2 class="cr-dash-section__title">📋 Daftar Agenda</h2>

        <div class="cr-jadwal-list cr-schedule-list">
            @forelse($jadwalSaya as $booking)
                <div class="cr-schedule-card">
                    <div class="cr-schedule-card__rail">
                        <div class="cr-jadwal-item__dot {{ $booking->status === 'approved' ? 'cr-jadwal-item__dot--approved' : 'cr-jadwal-item__dot--pending' }}"></div>
                        <div class="cr-jadwal-item__icon">
                            {{ $booking->jenis === 'perkuliahan' ? '🎓' : '🎯' }}
                        </div>
                    </div>

                    <div class="cr-schedule-card__body">
                        <div class="cr-schedule-card__topline">
                            <div>
                                <p class="cr-jadwal-item__room">
                                    {{ $booking->jenis === 'perkuliahan' ? 'Perkuliahan' : ($booking->kegiatan?->nama_kegiatan ?? 'Kegiatan') }}
                                </p>
                                <p class="cr-jadwal-item__type">
                                    {{ $booking->rooms->pluck('nama_ruangan')->implode(', ') ?: 'Ruangan belum tersedia' }}
                                </p>
                            </div>
                            <span class="cr-schedule-status cr-schedule-status--{{ $booking->status }}">
                                {{ $booking->status === 'approved' ? 'Disetujui' : 'Menunggu' }}
                            </span>
                        </div>

                        <div class="cr-schedule-card__meta">
                            <span>📆 {{ \Carbon\Carbon::parse($booking->tanggal)->locale('id')->translatedFormat('l, d F Y') }}</span>
                            <span>🕐 {{ substr($booking->jam_mulai, 0, 5) }} - {{ substr($booking->jam_selesai, 0, 5) }}</span>
                        </div>

                        <div class="cr-schedule-card__footer">
                            <div class="cr-schedule-card__tag-group">
                                <span class="cr-schedule-card__tag">{{ strtoupper($booking->jenis) }}</span>
                                @if($booking->jenis === 'kegiatan')
                                    <span class="cr-schedule-card__tag cr-schedule-card__tag--muted">Surat terlampir</span>
                                @else
                                    <span class="cr-schedule-card__tag cr-schedule-card__tag--muted">Tanpa surat</span>
                                @endif
                            </div>
                            <a href="/booking/history" class="cr-schedule-card__link">Lihat riwayat</a>
                        </div>
                    </div>
                </div>
            @empty
                <div class="cr-jadwal-empty">
                    <div class="cr-jadwal-empty__orb">
                        <span>📋</span>
                    </div>
                    <p>Belum ada jadwal aktif</p>
                    <span class="cr-jadwal-empty__sub">Buat booking pertama agar agenda muncul di sini.</span>
                    <a href="/booking" class="cr-btn-booking-empty">Buat Booking</a>
                </div>
            @endforelse
        </div>
    </section>

</div>

<style>
.cr-schedule-page {
    max-width: 1080px;
}

.cr-schedule-hero {
    display: grid;
    grid-template-columns: minmax(0, 1.6fr) minmax(320px, 0.95fr);
    gap: 18px;
    margin: 56px 0 26px;
}

.cr-schedule-hero__intro,
.cr-schedule-hero__card {
    position: relative;
    overflow: hidden;
    background: linear-gradient(145deg, rgba(255,255,255,0.96), rgba(248,250,255,0.98));
    border: 1.5px solid #E8EEF7;
    border-radius: 24px;
    box-shadow: 0 10px 30px rgba(26, 35, 64, 0.08);
}

.cr-schedule-hero__intro {
    padding: 28px 28px 24px;
    background:
        radial-gradient(circle at top right, rgba(244, 180, 0, 0.18), transparent 26%),
        radial-gradient(circle at bottom left, rgba(79, 195, 247, 0.14), transparent 30%),
        linear-gradient(145deg, rgba(255,255,255,0.96), rgba(248,250,255,0.98));
}

.cr-schedule-hero__intro::after {
    content: '';
    position: absolute;
    inset: auto -60px -70px auto;
    width: 210px;
    height: 210px;
    border-radius: 50%;
    border: 22px solid rgba(244, 180, 0, 0.10);
    pointer-events: none;
}

.cr-schedule-hero__eyebrow-wrap {
    display: flex;
    align-items: center;
    gap: 10px;
    margin-bottom: 10px;
}

.cr-schedule-hero__eyebrow {
    font-family: 'Plus Jakarta Sans', sans-serif;
    font-size: 0.75rem;
    font-weight: 800;
    letter-spacing: 0.08em;
    color: #F4B400;
    margin: 0;
}

.cr-schedule-hero__live {
    display: inline-flex;
    align-items: center;
    padding: 4px 9px;
    border-radius: 999px;
    background: rgba(0, 200, 150, 0.12);
    color: #00A97E;
    font-family: 'Plus Jakarta Sans', sans-serif;
    font-size: 0.68rem;
    font-weight: 800;
    letter-spacing: 0.06em;
}

.cr-schedule-hero__title {
    font-family: 'Space Grotesk', sans-serif;
    font-size: clamp(1.9rem, 3.4vw, 2.9rem);
    font-weight: 700;
    line-height: 1.08;
    letter-spacing: -0.6px;
    color: #1A2340;
    margin: 0 0 12px;
    max-width: 14ch;
}

.cr-schedule-hero__desc {
    font-family: 'DM Sans', sans-serif;
    font-size: 0.98rem;
    line-height: 1.8;
    color: #5A6A8A;
    margin: 0;
    max-width: 60ch;
    position: relative;
    z-index: 1;
}

.cr-schedule-hero__chips {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
    margin-top: 18px;
}

.cr-schedule-chip {
    display: inline-flex;
    align-items: center;
    padding: 8px 14px;
    border-radius: 999px;
    font-family: 'Plus Jakarta Sans', sans-serif;
    font-size: 0.79rem;
    font-weight: 800;
    letter-spacing: 0.01em;
}

.cr-schedule-chip--gold {
    background: rgba(244, 180, 0, 0.14);
    color: #A96E00;
}

.cr-schedule-chip--green {
    background: rgba(0, 200, 150, 0.12);
    color: #008E6A;
}

.cr-schedule-chip--muted {
    background: rgba(26, 35, 64, 0.06);
    color: #5A6A8A;
}

.cr-schedule-hero__card {
    padding: 22px 22px 20px;
    display: flex;
    flex-direction: column;
    justify-content: center;
    gap: 8px;
    background:
        radial-gradient(circle at top right, rgba(79, 195, 247, 0.18), transparent 28%),
        radial-gradient(circle at bottom left, rgba(244, 180, 0, 0.14), transparent 26%),
        linear-gradient(145deg, rgba(255,255,255,0.98), rgba(247,250,255,0.98));
}

.cr-schedule-hero__card-head {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 8px;
}

.cr-schedule-hero__card-label {
    font-family: 'DM Sans', sans-serif;
    font-size: 0.78rem;
    color: #9AAFC8;
    margin: 0;
}

.cr-schedule-hero__card-accent {
    display: inline-flex;
    align-items: center;
    padding: 4px 9px;
    border-radius: 999px;
    font-family: 'Plus Jakarta Sans', sans-serif;
    font-size: 0.66rem;
    font-weight: 800;
    letter-spacing: 0.08em;
    color: #1A2340;
    background: rgba(244, 180, 0, 0.12);
}

.cr-schedule-hero__orbit {
    position: relative;
    width: 114px;
    height: 114px;
    margin: 8px 0 2px;
}

.cr-schedule-hero__orbit-ring,
.cr-schedule-hero__orbit-dot {
    position: absolute;
    inset: 50% auto auto 50%;
    transform: translate(-50%, -50%);
    border-radius: 50%;
}

.cr-schedule-hero__orbit-ring--one {
    width: 114px;
    height: 114px;
    border: 1.5px solid rgba(79, 195, 247, 0.18);
}

.cr-schedule-hero__orbit-ring--two {
    width: 76px;
    height: 76px;
    border: 1.5px solid rgba(244, 180, 0, 0.16);
}

.cr-schedule-hero__orbit-dot {
    width: 14px;
    height: 14px;
    background: linear-gradient(135deg, #F4B400 0%, #4FC3F7 100%);
    box-shadow: 0 0 0 7px rgba(244, 180, 0, 0.10);
}

.cr-schedule-hero__card-title {
    font-family: 'Plus Jakarta Sans', sans-serif;
    font-size: 1.12rem;
    font-weight: 800;
    color: #1A2340;
    margin: 0;
}

.cr-schedule-hero__card-meta {
    font-family: 'DM Sans', sans-serif;
    font-size: 0.88rem;
    color: #5A6A8A;
    margin: 0;
    line-height: 1.6;
}

.cr-schedule-hero__card-timebox {
    display: flex;
    flex-direction: column;
    gap: 4px;
    padding: 14px 14px 12px;
    margin: 6px 0 2px;
    background: rgba(26, 35, 64, 0.03);
    border: 1px solid rgba(26, 35, 64, 0.06);
    border-radius: 14px;
}

.cr-schedule-hero__card-date {
    font-family: 'Plus Jakarta Sans', sans-serif;
    font-size: 0.85rem;
    font-weight: 700;
    color: #1A2340;
}

.cr-schedule-hero__card-time {
    font-family: 'Space Grotesk', sans-serif;
    font-size: 1rem;
    font-weight: 700;
    color: #4FC3F7;
}

.cr-schedule-hero__card-cta {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: fit-content;
    margin-top: 6px;
    padding: 10px 16px;
    border-radius: 10px;
    background: linear-gradient(135deg, #F4B400 0%, #FFB020 100%);
    color: #1A2340;
    font-family: 'Plus Jakarta Sans', sans-serif;
    font-size: 0.875rem;
    font-weight: 800;
    text-decoration: none;
    box-shadow: 0 4px 16px rgba(244, 180, 0, 0.25);
}

.cr-schedule-status {
    display: inline-flex;
    width: fit-content;
    align-items: center;
    justify-content: center;
    padding: 4px 12px;
    border-radius: 999px;
    font-family: 'Plus Jakarta Sans', sans-serif;
    font-size: 0.7rem;
    font-weight: 800;
    letter-spacing: 0.04em;
}

.cr-schedule-status--approved {
    background: #D1FAF0;
    color: #00A97E;
}

.cr-schedule-status--pending {
    background: #FFF3CD;
    color: #E6820A;
}

.cr-schedule-section-head {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 16px;
    margin-bottom: 14px;
}

.cr-schedule-legend {
    display: flex;
    gap: 10px;
    flex-wrap: wrap;
}

.cr-schedule-legend__item {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    font-family: 'DM Sans', sans-serif;
    font-size: 0.8rem;
    color: #5A6A8A;
    background: #fff;
    border: 1px solid #E8EEF7;
    border-radius: 999px;
    padding: 6px 12px;
}

.cr-schedule-legend__dot {
    width: 8px;
    height: 8px;
    border-radius: 999px;
    display: inline-block;
}

.cr-schedule-legend__dot--approved {
    background: #00C896;
}

.cr-schedule-legend__dot--pending {
    background: #E6820A;
}

.cr-schedule-strip {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 18px 18px;
    background: linear-gradient(145deg, #FFFFFF, #F8FAFF);
    border-radius: 18px;
    border: 1.5px solid #E8EEF7;
    box-shadow: 0 10px 30px rgba(26, 35, 64, 0.06);
}

.cr-schedule-strip__item {
    display: flex;
    flex-direction: column;
    gap: 2px;
    flex: 1;
}

.cr-schedule-strip__num {
    font-family: 'Space Grotesk', sans-serif;
    font-size: 1.45rem;
    font-weight: 700;
    color: #1A2340;
    line-height: 1;
}

.cr-schedule-strip__label {
    font-family: 'DM Sans', sans-serif;
    font-size: 0.8rem;
    color: #5A6A8A;
}

.cr-schedule-strip__divider {
    width: 1px;
    height: 34px;
    background: #EEF2FB;
    flex-shrink: 0;
}

.cr-schedule-list {
    background: transparent;
    box-shadow: none;
}

.cr-schedule-card {
    display: flex;
    align-items: flex-start;
    gap: 14px;
    background: linear-gradient(145deg, #FFFFFF, #FCFDFF);
    border: 1.5px solid #E8EEF7;
    border-radius: 20px;
    padding: 16px 18px;
    box-shadow: 0 2px 10px rgba(26, 35, 64, 0.06);
    transition: transform 0.18s ease, box-shadow 0.18s ease, border-color 0.18s ease;
    position: relative;
}

.cr-schedule-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 12px 28px rgba(26, 35, 64, 0.10);
    border-color: rgba(244, 180, 0, 0.20);
}

.cr-schedule-card + .cr-schedule-card {
    margin-top: 12px;
}

.cr-schedule-card__rail {
    display: flex;
    align-items: flex-start;
    gap: 10px;
    padding-top: 3px;
}

.cr-schedule-card__body {
    flex: 1;
    min-width: 0;
}

.cr-schedule-card__topline {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    gap: 14px;
    margin-bottom: 8px;
}

.cr-schedule-card__meta {
    display: flex;
    gap: 14px;
    flex-wrap: wrap;
    font-family: 'DM Sans', sans-serif;
    font-size: 0.82rem;
    color: #5A6A8A;
}

.cr-schedule-card__footer {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 12px;
    margin-top: 14px;
    padding-top: 12px;
    border-top: 1px solid #EEF2FB;
}

.cr-schedule-card__tag-group {
    display: flex;
    gap: 8px;
    flex-wrap: wrap;
}

.cr-schedule-card__tag {
    display: inline-flex;
    align-items: center;
    padding: 5px 10px;
    border-radius: 999px;
    background: rgba(244, 180, 0, 0.12);
    color: #A96E00;
    font-family: 'Plus Jakarta Sans', sans-serif;
    font-size: 0.68rem;
    font-weight: 800;
    letter-spacing: 0.05em;
}

.cr-schedule-card__tag--muted {
    background: rgba(26, 35, 64, 0.05);
    color: #5A6A8A;
    letter-spacing: 0.02em;
}

.cr-schedule-card__link {
    display: inline-flex;
    align-items: center;
    text-decoration: none;
    color: #4FC3F7;
    font-family: 'Plus Jakarta Sans', sans-serif;
    font-size: 0.8rem;
    font-weight: 800;
    white-space: nowrap;
}

.cr-schedule-card__link:hover {
    color: #1A2340;
}

.cr-jadwal-empty {
    background: linear-gradient(145deg, #FFFFFF, #F8FAFF);
    border: 1.5px dashed #DCE5F4;
    border-radius: 20px;
    box-shadow: 0 8px 24px rgba(26, 35, 64, 0.05);
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    gap: 8px;
    padding: 40px 20px;
    color: #9AAFC8;
}

.cr-jadwal-empty__orb {
    width: 72px;
    height: 72px;
    border-radius: 22px;
    background: linear-gradient(135deg, rgba(244, 180, 0, 0.14), rgba(79, 195, 247, 0.14));
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: inset 0 0 0 1px rgba(255,255,255,0.6);
}

.cr-jadwal-empty__orb span {
    font-size: 1.9rem;
}

.cr-jadwal-empty p {
    font-family: 'Plus Jakarta Sans', sans-serif;
    font-size: 0.875rem;
    font-weight: 700;
    color: #1A2340;
    margin: 0;
}

.cr-jadwal-empty__sub {
    font-family: 'DM Sans', sans-serif;
    font-size: 0.84rem;
    color: #9AAFC8;
    margin: 0;
}

.cr-btn-booking-empty {
    margin-top: 8px;
    padding: 8px 20px;
    background: linear-gradient(135deg, #F4B400 0%, #FFB020 100%);
    color: #1A2340;
    font-family: 'Plus Jakarta Sans', sans-serif;
    font-size: 0.813rem;
    font-weight: 700;
    border-radius: 10px;
    text-decoration: none;
    transition: transform 0.15s ease;
}

.cr-btn-booking-empty:hover {
    transform: scale(1.03);
}

@media (max-width: 1024px) {
    .cr-schedule-page {
        max-width: 860px;
    }
}

@media (max-width: 768px) {
    .cr-schedule-hero {
        grid-template-columns: 1fr;
        margin-top: 16px;
    }

    .cr-schedule-hero__intro {
        padding: 22px 20px 20px;
    }

    .cr-schedule-hero__title {
        max-width: none;
    }

    .cr-schedule-section-head {
        flex-direction: column;
        align-items: flex-start;
    }

    .cr-schedule-strip {
        flex-direction: column;
        align-items: stretch;
    }

    .cr-schedule-strip__divider {
        width: 100%;
        height: 1px;
    }

    .cr-schedule-card {
        padding: 14px;
    }

    .cr-schedule-card__topline {
        flex-direction: column;
    }

    .cr-schedule-card__footer {
        flex-direction: column;
        align-items: flex-start;
    }
}
</style>
@endsection
