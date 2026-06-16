@extends('layouts.dashboard')

@section('title', 'Jadwal Saya')

@section('content')
<div class="cr-dash-content">

    <div class="cr-dash-brand-pill">
        <span class="cr-dash-brand-pill__icon">🟨</span>
        <span class="cr-dash-brand-pill__text">CampusRoom • v1.0 Admin</span>
    </div>

    {{-- ===== HERO SECTION ===== --}}
    <section class="cr-jadwal-hero">
        <div class="cr-jadwal-hero__copy">
            <p class="cr-jadwal-kicker">Jadwal mendatang</p>
            <h1 class="cr-jadwal-title">Semua agenda aktif<br>di satu tempat</h1>
            <p class="cr-jadwal-desc">
                Pantau jadwal perkuliahan dan kegiatan yang sudah disetujui atau masih menunggu persetujuan, lengkap dengan detail waktu dan ruangan.
            </p>
        </div>

        <div class="cr-jadwal-hero__stats">
            <div class="cr-jadwal-stat-card">
                <span class="cr-jadwal-stat-card__num">{{ $jumlahJadwal }}</span>
                <span class="cr-jadwal-stat-card__label">Total jadwal</span>
            </div>
            <div class="cr-jadwal-stat-card cr-jadwal-stat-card--approved">
                <span class="cr-jadwal-stat-card__num">{{ $jumlahDisetujui }}</span>
                <span class="cr-jadwal-stat-card__label">Disetujui</span>
            </div>
            <div class="cr-jadwal-stat-card cr-jadwal-stat-card--pending">
                <span class="cr-jadwal-stat-card__num">{{ $jumlahMenunggu }}</span>
                <span class="cr-jadwal-stat-card__label">Menunggu</span>
            </div>
        </div>
    </section>

    {{-- ===== JADWAL TERDEKAT ===== --}}
    @if($jadwalTerdekat)
    <section class="cr-dash-section">
        <h2 class="cr-dash-section__title">⚡ Jadwal Terdekat</h2>

        <div class="cr-jadwal-nearest">
            <div class="cr-jadwal-nearest__left">
                <span class="cr-badge {{ $jadwalTerdekat->status === 'approved' ? 'cr-badge--approved' : 'cr-badge--pending' }}">
                    {{ $jadwalTerdekat->status === 'approved' ? 'Disetujui ✅' : 'Menunggu ⌛' }}
                </span>
                <h3 class="cr-jadwal-nearest__name">
                    @if($jadwalTerdekat->jenis === 'perkuliahan')
                        {{ $jadwalTerdekat->rooms->first()?->nama_ruangan ?? 'Ruangan' }}
                    @else
                        {{ $jadwalTerdekat->kegiatan?->nama_kegiatan ?? 'Kegiatan' }}
                    @endif
                </h3>
                <p class="cr-jadwal-nearest__type">
                    {{ $jadwalTerdekat->jenis === 'perkuliahan' ? '🎓 Perkuliahan' : '🎯 Kegiatan' }}
                    @if($jadwalTerdekat->jenis === 'kegiatan' && $jadwalTerdekat->kegiatan?->penyelenggara)
                        · {{ $jadwalTerdekat->kegiatan->penyelenggara }}
                    @endif
                </p>
            </div>
            <div class="cr-jadwal-nearest__mid">
                <div class="cr-jadwal-nearest__item">
                    <span class="cr-jadwal-nearest__label">Tanggal</span>
                    <span class="cr-jadwal-nearest__val">
                        {{ \Carbon\Carbon::parse($jadwalTerdekat->tanggal)->locale('id')->translatedFormat('l, d F Y') }}
                    </span>
                </div>
                <div class="cr-jadwal-nearest__item">
                    <span class="cr-jadwal-nearest__label">Waktu</span>
                    <span class="cr-jadwal-nearest__val">
                        🕐 {{ substr($jadwalTerdekat->jam_mulai,0,5) }} – {{ substr($jadwalTerdekat->jam_selesai,0,5) }}
                    </span>
                </div>
            </div>
            <div class="cr-jadwal-nearest__right">
                <div class="cr-jadwal-nearest__item">
                    <span class="cr-jadwal-nearest__label">Ruangan</span>
                    <span class="cr-jadwal-nearest__val">
                        {{ $jadwalTerdekat->rooms->pluck('nama_ruangan')->join(', ') ?: '-' }}
                    </span>
                </div>
                <div class="cr-jadwal-nearest__item">
                    <span class="cr-jadwal-nearest__label">Jenis</span>
                    <span class="cr-jadwal-nearest__val">
                        {{ $jadwalTerdekat->jenis === 'perkuliahan' ? 'Perkuliahan' : 'Kegiatan' }}
                    </span>
                </div>
            </div>
        </div>
    </section>
    @endif

    {{-- ===== SEMUA JADWAL ===== --}}
    <section class="cr-dash-section">
        <h2 class="cr-dash-section__title">📅 Jadwal Saya</h2>

        <div class="cr-jadwal-grid">
            @forelse($jadwalSaya as $booking)
            <article class="cr-jadwal-card">
                <div class="cr-jadwal-card__top">
                    <div class="cr-jadwal-card__icon">
                        {{ $booking->jenis === 'perkuliahan' ? '🎓' : '🎯' }}
                    </div>
                    <div class="cr-jadwal-card__head">
                        <div class="cr-jadwal-card__title-row">
                            <h3 class="cr-jadwal-card__title">
                                @if($booking->jenis === 'perkuliahan')
                                    {{ $booking->rooms->first()?->nama_ruangan ?? 'Ruangan' }}
                                @else
                                    {{ $booking->kegiatan?->nama_kegiatan ?? 'Kegiatan' }}
                                @endif
                            </h3>
                            <span class="cr-badge {{ $booking->status === 'approved' ? 'cr-badge--approved' : 'cr-badge--pending' }}">
                                {{ $booking->status === 'approved' ? 'Approved' : 'Pending' }}
                            </span>
                        </div>
                        <p class="cr-jadwal-card__sub">
                            {{ $booking->jenis === 'perkuliahan' ? 'Perkuliahan' : 'Kegiatan' }}
                            @if($booking->jenis === 'kegiatan' && $booking->kegiatan?->penyelenggara)
                                · {{ $booking->kegiatan->penyelenggara }}
                            @endif
                        </p>
                    </div>
                </div>

                <div class="cr-jadwal-card__meta-grid">
                    <div class="cr-jadwal-card__meta-item">
                        <span class="cr-jadwal-card__meta-label">Tanggal</span>
                        <span class="cr-jadwal-card__meta-value">
                            {{ \Carbon\Carbon::parse($booking->tanggal)->locale('id')->translatedFormat('l, d F Y') }}
                        </span>
                    </div>
                    <div class="cr-jadwal-card__meta-item">
                        <span class="cr-jadwal-card__meta-label">Waktu</span>
                        <span class="cr-jadwal-card__meta-value">
                            {{ substr($booking->jam_mulai,0,5) }} – {{ substr($booking->jam_selesai,0,5) }}
                        </span>
                    </div>
                    <div class="cr-jadwal-card__meta-item">
                        <span class="cr-jadwal-card__meta-label">Ruangan</span>
                        <span class="cr-jadwal-card__meta-value">
                            {{ $booking->rooms->pluck('nama_ruangan')->join(', ') ?: '-' }}
                        </span>
                    </div>
                    <div class="cr-jadwal-card__meta-item">
                        <span class="cr-jadwal-card__meta-label">Status</span>
                        <span class="cr-jadwal-card__meta-value">
                            {{ $booking->status === 'approved' ? '✅ Siap digunakan' : '⏳ Menunggu persetujuan' }}
                        </span>
                    </div>
                </div>

                <div class="cr-jadwal-card__extra">
                    <span class="cr-jadwal-card__extra-label">Informasi tambahan</span>
                    <span class="cr-jadwal-card__extra-value">
                        @if($booking->jenis === 'kegiatan')
                            {{ $booking->kegiatan?->deskripsi ?: 'Tidak ada deskripsi kegiatan.' }}
                        @else
                            Jadwal rutin perkuliahan yang sudah terdaftar pada sistem.
                        @endif
                    </span>
                </div>
            </article>
            @empty
            <div class="cr-jadwal-empty">
                <span>📋</span>
                <p>Belum ada jadwal mendatang</p>
                <a href="/booking" class="cr-btn-booking-empty">Booking Sekarang</a>
            </div>
            @endforelse
        </div>
    </section>

</div>

<style>
/* ============================================================
   JADWAL SAYA PAGE
   ============================================================ */

/* Hero */
.cr-jadwal-hero {
    display: grid;
    grid-template-columns: 1fr auto;
    gap: 24px;
    align-items: center;
    margin-top: 56px;
    margin-bottom: 28px;
    padding: 28px;
    background: #fff;
    border: 1.5px solid #EEF2FB;
    border-radius: 20px;
    box-shadow: 0 2px 10px rgba(26,35,64,0.06);
}
.cr-jadwal-kicker {
    font-family: 'Plus Jakarta Sans', sans-serif; font-size: 0.75rem; font-weight: 800;
    text-transform: uppercase; letter-spacing: 0.1em; color: #E6820A; margin: 0 0 8px;
}
.cr-jadwal-title {
    font-family: 'Space Grotesk', sans-serif; font-size: 1.5rem; font-weight: 700;
    color: #1A2340; margin: 0 0 10px; line-height: 1.2; letter-spacing: -0.03em;
}
.cr-jadwal-desc {
    font-family: 'DM Sans', sans-serif; font-size: 0.875rem; color: #5A6A8A; margin: 0; line-height: 1.6;
    max-width: 48ch;
}
.cr-jadwal-hero__stats {
    display: flex; gap: 12px;
}
.cr-jadwal-stat-card {
    padding: 16px 20px; border-radius: 14px; background: #F8FAFF;
    border: 1.5px solid #EEF2FB; display: flex; flex-direction: column; gap: 4px; min-width: 80px; text-align: center;
}
.cr-jadwal-stat-card--approved { border-color: rgba(0,200,150,0.2); background: #F0FFFA; }
.cr-jadwal-stat-card--pending  { border-color: rgba(244,180,0,0.2); background: #FFFBF0; }
.cr-jadwal-stat-card__num {
    font-family: 'Space Grotesk', sans-serif; font-size: 1.75rem; font-weight: 700; color: #1A2340; line-height: 1;
}
.cr-jadwal-stat-card__label {
    font-family: 'DM Sans', sans-serif; font-size: 0.75rem; color: #5A6A8A;
}

/* Nearest card */
.cr-jadwal-nearest {
    display: grid; grid-template-columns: 1.2fr 1fr 1fr; gap: 20px; align-items: center;
    background: linear-gradient(135deg, rgba(244,180,0,0.08) 0%, #fff 60%);
    border: 1.5px solid rgba(244,180,0,0.2); border-radius: 16px; padding: 20px 24px;
    box-shadow: 0 2px 10px rgba(26,35,64,0.06);
}
.cr-jadwal-nearest__left { display: flex; flex-direction: column; gap: 6px; }
.cr-jadwal-nearest__name {
    font-family: 'Plus Jakarta Sans', sans-serif; font-size: 1.063rem; font-weight: 800; color: #1A2340; margin: 4px 0 0;
}
.cr-jadwal-nearest__type {
    font-family: 'DM Sans', sans-serif; font-size: 0.838rem; color: #5A6A8A; margin: 0;
}
.cr-jadwal-nearest__mid,
.cr-jadwal-nearest__right { display: flex; flex-direction: column; gap: 14px; }
.cr-jadwal-nearest__item { display: flex; flex-direction: column; gap: 2px; }
.cr-jadwal-nearest__label {
    font-family: 'DM Sans', sans-serif; font-size: 0.7rem; color: #9AAFC8;
}
.cr-jadwal-nearest__val {
    font-family: 'Plus Jakarta Sans', sans-serif; font-size: 0.875rem; font-weight: 700; color: #1A2340;
}

/* Badge (shared) */
.cr-badge {
    display: inline-flex; align-items: center; padding: 3px 12px; border-radius: 999px;
    font-family: 'Plus Jakarta Sans', sans-serif; font-size: 0.72rem; font-weight: 700;
}
.cr-badge--approved { background: #D1FAF0; color: #00C896; }
.cr-badge--pending  { background: #FFF3CD; color: #E6820A; }
.cr-badge--rejected { background: #FFE4E9; color: #FF4D6D; }

/* Jadwal grid */
.cr-jadwal-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 14px; }
.cr-jadwal-card {
    padding: 18px; border-radius: 16px; background: #fff;
    border: 1.5px solid #EEF2FB; box-shadow: 0 2px 8px rgba(26,35,64,0.05);
    transition: transform .2s, box-shadow .2s;
}
.cr-jadwal-card:hover { transform: translateY(-2px); box-shadow: 0 6px 20px rgba(26,35,64,0.09); }

.cr-jadwal-card__top { display: flex; gap: 12px; align-items: flex-start; margin-bottom: 14px; }
.cr-jadwal-card__icon {
    width: 44px; height: 44px; border-radius: 12px; background: rgba(79,195,247,0.1);
    display: flex; align-items: center; justify-content: center; font-size: 1.2rem; flex-shrink: 0;
}
.cr-jadwal-card__head { flex: 1; min-width: 0; }
.cr-jadwal-card__title-row {
    display: flex; align-items: flex-start; justify-content: space-between; gap: 8px;
}
.cr-jadwal-card__title {
    font-family: 'Plus Jakarta Sans', sans-serif; font-size: 0.938rem; font-weight: 800; color: #1A2340; margin: 0;
}
.cr-jadwal-card__sub {
    font-family: 'DM Sans', sans-serif; font-size: 0.775rem; color: #5A6A8A; margin: 4px 0 0;
}
.cr-jadwal-card__meta-grid {
    display: grid; grid-template-columns: repeat(2, 1fr); gap: 10px; margin-bottom: 12px;
}
.cr-jadwal-card__meta-item {
    padding: 10px 12px; border-radius: 10px; background: #F8FAFF;
    border: 1px solid #EEF2FB; display: flex; flex-direction: column; gap: 2px;
}
.cr-jadwal-card__meta-label {
    font-family: 'DM Sans', sans-serif; font-size: 0.688rem; color: #9AAFC8;
}
.cr-jadwal-card__meta-value {
    font-family: 'Plus Jakarta Sans', sans-serif; font-size: 0.8rem; font-weight: 700; color: #1A2340;
}
.cr-jadwal-card__extra {
    padding-top: 12px; border-top: 1px dashed #EEF2FB;
    display: flex; flex-direction: column; gap: 3px;
}
.cr-jadwal-card__extra-label {
    font-family: 'DM Sans', sans-serif; font-size: 0.7rem; color: #9AAFC8;
}
.cr-jadwal-card__extra-value {
    font-family: 'DM Sans', sans-serif; font-size: 0.813rem; color: #5A6A8A; line-height: 1.55;
}

/* Empty */
.cr-jadwal-empty {
    grid-column: 1 / -1; display: flex; flex-direction: column; align-items: center;
    gap: 10px; padding: 60px 20px; background: #fff; border-radius: 16px;
    border: 1.5px solid #EEF2FB;
}
.cr-jadwal-empty span { font-size: 2.5rem; }
.cr-jadwal-empty p { font-family: 'Plus Jakarta Sans', sans-serif; font-size: 0.938rem; font-weight: 600; color: #9AAFC8; margin: 0; }
.cr-btn-booking-empty {
    margin-top: 6px; padding: 10px 24px; background: linear-gradient(135deg, #F4B400 0%, #FFB020 100%);
    color: #1A2340; font-family: 'Plus Jakarta Sans', sans-serif; font-size: 0.875rem; font-weight: 700;
    border-radius: 10px; text-decoration: none;
}

@media (max-width: 900px) {
    .cr-jadwal-hero { grid-template-columns: 1fr; }
    .cr-jadwal-hero__stats { flex-wrap: wrap; }
    .cr-jadwal-nearest { grid-template-columns: 1fr; gap: 14px; }
    .cr-jadwal-grid { grid-template-columns: 1fr; }
}
</style>

@endsection