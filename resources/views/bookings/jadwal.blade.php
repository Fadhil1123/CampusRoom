@extends('layouts.dashboard')

@section('title', 'Jadwal Saya')

@section('content')
<div class="cr-dash-content">

    <div class="cr-dash-brand-pill">
        <span class="cr-dash-brand-pill__icon">🟨</span>
        <span class="cr-dash-brand-pill__text">CampusRoom • v1.0 Admin</span>
    </div>

    <section class="cr-jadwal-hero">
        <div class="cr-jadwal-hero__copy">
            <p class="cr-jadwal-kicker">Jadwal mendatang</p>
            <h1 class="cr-jadwal-title">Semua agenda aktif kamu di satu tempat</h1>
            <p class="cr-jadwal-desc">
                Pantau jadwal perkuliahan dan kegiatan yang sudah disetujui atau masih menunggu persetujuan, lengkap dengan detail waktu, ruangan, dan keterangan penting.
            </p>
        </div>

        <div class="cr-jadwal-hero__stats">
            <div class="cr-jadwal-stat-card">
                <span class="cr-jadwal-stat-card__label">Total jadwal</span>
                <span class="cr-jadwal-stat-card__value">{{ $jumlahJadwal }}</span>
            </div>
            <div class="cr-jadwal-stat-card cr-jadwal-stat-card--approved">
                <span class="cr-jadwal-stat-card__label">Disetujui</span>
                <span class="cr-jadwal-stat-card__value">{{ $jumlahDisetujui }}</span>
            </div>
            <div class="cr-jadwal-stat-card cr-jadwal-stat-card--pending">
                <span class="cr-jadwal-stat-card__label">Menunggu</span>
                <span class="cr-jadwal-stat-card__value">{{ $jumlahMenunggu }}</span>
            </div>
        </div>
    </section>

    @if($jadwalTerdekat)
    <section class="cr-jadwal-highlight cr-dash-section">
        <h2 class="cr-dash-section__title">⚡ Jadwal Terdekat</h2>
        <div class="cr-jadwal-highlight__card">
            <div class="cr-jadwal-highlight__badge {{ $jadwalTerdekat->status === 'approved' ? 'cr-jadwal-highlight__badge--approved' : 'cr-jadwal-highlight__badge--pending' }}">
                {{ $jadwalTerdekat->status === 'approved' ? 'Disetujui' : 'Menunggu' }}
            </div>
            <div class="cr-jadwal-highlight__main">
                <div>
                    <p class="cr-jadwal-highlight__name">
                        @if($jadwalTerdekat->jenis === 'perkuliahan')
                            {{ $jadwalTerdekat->rooms->first()?->nama_ruangan ?? 'Ruangan' }}
                        @else
                            {{ $jadwalTerdekat->kegiatan?->nama_kegiatan ?? 'Kegiatan' }}
                        @endif
                    </p>
                    <p class="cr-jadwal-highlight__meta">
                        {{ $jadwalTerdekat->jenis === 'perkuliahan' ? 'Perkuliahan' : 'Kegiatan' }}
                        @if($jadwalTerdekat->jenis === 'kegiatan' && $jadwalTerdekat->kegiatan?->penyelenggara)
                            • {{ $jadwalTerdekat->kegiatan->penyelenggara }}
                        @endif
                    </p>
                </div>
                <div class="cr-jadwal-highlight__time">
                    <span class="cr-jadwal-highlight__date">{{ \Carbon\Carbon::parse($jadwalTerdekat->tanggal)->locale('id')->translatedFormat('l, d F Y') }}</span>
                    <span class="cr-jadwal-highlight__clock">🕐 {{ substr($jadwalTerdekat->jam_mulai, 0, 5) }} - {{ substr($jadwalTerdekat->jam_selesai, 0, 5) }}</span>
                </div>
            </div>
            <div class="cr-jadwal-highlight__details">
                <div class="cr-jadwal-highlight__detail">
                    <span class="cr-jadwal-highlight__detail-label">Ruangan</span>
                    <span class="cr-jadwal-highlight__detail-value">
                        {{ $jadwalTerdekat->rooms->pluck('nama_ruangan')->join(', ') ?: 'Tidak tersedia' }}
                    </span>
                </div>
                <div class="cr-jadwal-highlight__detail">
                    <span class="cr-jadwal-highlight__detail-label">Jenis</span>
                    <span class="cr-jadwal-highlight__detail-value">{{ $jadwalTerdekat->jenis === 'perkuliahan' ? 'Perkuliahan' : 'Kegiatan' }}</span>
                </div>
            </div>
        </div>
    </section>
    @endif

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
                                • {{ $booking->kegiatan->penyelenggara }}
                            @endif
                        </p>
                    </div>
                </div>

                <div class="cr-jadwal-card__meta-grid">
                    <div class="cr-jadwal-card__meta-item">
                        <span class="cr-jadwal-card__meta-label">Tanggal</span>
                        <span class="cr-jadwal-card__meta-value">{{ \Carbon\Carbon::parse($booking->tanggal)->locale('id')->translatedFormat('l, d F Y') }}</span>
                    </div>
                    <div class="cr-jadwal-card__meta-item">
                        <span class="cr-jadwal-card__meta-label">Waktu</span>
                        <span class="cr-jadwal-card__meta-value">{{ substr($booking->jam_mulai, 0, 5) }} - {{ substr($booking->jam_selesai, 0, 5) }}</span>
                    </div>
                    <div class="cr-jadwal-card__meta-item">
                        <span class="cr-jadwal-card__meta-label">Ruangan</span>
                        <span class="cr-jadwal-card__meta-value">{{ $booking->rooms->pluck('nama_ruangan')->join(', ') ?: 'Tidak tersedia' }}</span>
                    </div>
                    <div class="cr-jadwal-card__meta-item">
                        <span class="cr-jadwal-card__meta-label">Status</span>
                        <span class="cr-jadwal-card__meta-value">{{ $booking->status === 'approved' ? 'Siap digunakan' : 'Menunggu persetujuan' }}</span>
                    </div>
                </div>

                <div class="cr-jadwal-card__extra">
                    <div class="cr-jadwal-card__extra-item">
                        <span class="cr-jadwal-card__extra-label">Informasi tambahan</span>
                        <span class="cr-jadwal-card__extra-value">
                            @if($booking->jenis === 'kegiatan')
                                {{ $booking->kegiatan?->deskripsi ?: 'Tidak ada deskripsi kegiatan.' }}
                            @else
                                Jadwal rutin perkuliahan yang sudah terdaftar pada sistem.
                            @endif
                        </span>
                    </div>
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
.cr-jadwal-hero {
    display: grid;
    grid-template-columns: minmax(0, 1.35fr) minmax(320px, 0.9fr);
    gap: 16px;
    align-items: stretch;
    margin-top: 56px;
    margin-bottom: 28px;
}
.cr-jadwal-kicker {
    margin: 0 0 8px;
    font-family: 'Plus Jakarta Sans', sans-serif;
    font-size: 0.75rem;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: 0.12em;
    color: #E6820A;
}
.cr-jadwal-title {
    margin: 0;
    font-family: 'Space Grotesk', sans-serif;
    font-size: clamp(1.7rem, 3vw, 2.5rem);
    line-height: 1.1;
    letter-spacing: -0.04em;
    color: #1A2340;
}
.cr-jadwal-desc {
    margin: 12px 0 0;
    max-width: 56ch;
    font-family: 'DM Sans', sans-serif;
    font-size: 0.95rem;
    line-height: 1.65;
    color: #5A6A8A;
}
.cr-jadwal-hero__stats {
    display: grid;
    grid-template-columns: repeat(3, minmax(0, 1fr));
    gap: 12px;
}
.cr-jadwal-stat-card {
    padding: 18px 16px;
    border-radius: 18px;
    background: linear-gradient(180deg, #FFFFFF 0%, #FBFCFF 100%);
    border: 1.5px solid #EEF2FB;
    box-shadow: 0 2px 8px rgba(26, 35, 64, 0.06);
    display: flex;
    flex-direction: column;
    gap: 6px;
}
.cr-jadwal-stat-card--approved { border-color: rgba(0, 200, 150, 0.18); }
.cr-jadwal-stat-card--pending { border-color: rgba(230, 130, 10, 0.18); }
.cr-jadwal-stat-card__label {
    font-family: 'DM Sans', sans-serif;
    font-size: 0.8rem;
    color: #5A6A8A;
}
.cr-jadwal-stat-card__value {
    font-family: 'Space Grotesk', sans-serif;
    font-size: 2rem;
    font-weight: 700;
    line-height: 1;
    color: #1A2340;
}
.cr-jadwal-highlight__card {
    display: grid;
    grid-template-columns: auto minmax(0, 1fr) minmax(220px, 0.8fr);
    gap: 16px;
    padding: 20px;
    border-radius: 20px;
    background: linear-gradient(135deg, rgba(244, 180, 0, 0.10) 0%, rgba(255, 255, 255, 1) 60%);
    border: 1.5px solid rgba(244, 180, 0, 0.18);
    box-shadow: 0 4px 18px rgba(26, 35, 64, 0.08);
}
.cr-jadwal-highlight__badge {
    align-self: flex-start;
    padding: 6px 12px;
    border-radius: 999px;
    font-family: 'Plus Jakarta Sans', sans-serif;
    font-size: 0.72rem;
    font-weight: 700;
}
.cr-jadwal-highlight__badge--approved { background: #D1FAF0; color: #00C896; }
.cr-jadwal-highlight__badge--pending { background: #FFF3CD; color: #E6820A; }
.cr-jadwal-highlight__main {
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    gap: 12px;
}
.cr-jadwal-highlight__name {
    margin: 0;
    font-family: 'Plus Jakarta Sans', sans-serif;
    font-size: 1.1rem;
    font-weight: 800;
    color: #1A2340;
}
.cr-jadwal-highlight__meta {
    margin: 4px 0 0;
    font-family: 'DM Sans', sans-serif;
    font-size: 0.88rem;
    color: #5A6A8A;
}
.cr-jadwal-highlight__time {
    display: flex;
    flex-direction: column;
    gap: 6px;
    align-items: flex-start;
    justify-content: center;
}
.cr-jadwal-highlight__date {
    font-family: 'Plus Jakarta Sans', sans-serif;
    font-size: 0.95rem;
    font-weight: 700;
    color: #1A2340;
}
.cr-jadwal-highlight__clock {
    font-family: 'DM Sans', sans-serif;
    font-size: 0.85rem;
    color: #5A6A8A;
}
.cr-jadwal-highlight__details {
    display: flex;
    flex-direction: column;
    gap: 12px;
    justify-content: center;
}
.cr-jadwal-highlight__detail {
    display: flex;
    flex-direction: column;
    gap: 4px;
}
.cr-jadwal-highlight__detail-label,
.cr-jadwal-card__meta-label,
.cr-jadwal-card__extra-label {
    font-family: 'DM Sans', sans-serif;
    font-size: 0.75rem;
    color: #9AAFC8;
}
.cr-jadwal-highlight__detail-value,
.cr-jadwal-card__meta-value,
.cr-jadwal-card__extra-value {
    font-family: 'Plus Jakarta Sans', sans-serif;
    font-size: 0.9rem;
    font-weight: 600;
    color: #1A2340;
}
.cr-jadwal-grid {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 14px;
}
.cr-jadwal-card {
    padding: 18px;
    border-radius: 18px;
    background: #FFFFFF;
    border: 1.5px solid #EEF2FB;
    box-shadow: 0 2px 8px rgba(26, 35, 64, 0.06);
}
.cr-jadwal-card__top {
    display: flex;
    gap: 12px;
    align-items: flex-start;
    margin-bottom: 14px;
}
.cr-jadwal-card__icon {
    width: 48px;
    height: 48px;
    border-radius: 14px;
    background: rgba(79, 195, 247, 0.1);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.25rem;
    flex-shrink: 0;
}
.cr-jadwal-card__head {
    flex: 1;
    min-width: 0;
}
.cr-jadwal-card__title-row {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    gap: 12px;
}
.cr-jadwal-card__title {
    margin: 0;
    font-family: 'Plus Jakarta Sans', sans-serif;
    font-size: 1rem;
    font-weight: 800;
    color: #1A2340;
}
.cr-jadwal-card__sub {
    margin: 4px 0 0;
    font-family: 'DM Sans', sans-serif;
    font-size: 0.8rem;
    color: #5A6A8A;
}
.cr-jadwal-card__meta-grid {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 12px;
}
.cr-jadwal-card__meta-item {
    padding: 12px;
    border-radius: 14px;
    background: #FAFBFF;
    border: 1px solid #EEF2FB;
    display: flex;
    flex-direction: column;
    gap: 3px;
}
.cr-jadwal-card__extra {
    margin-top: 12px;
    padding-top: 12px;
    border-top: 1px dashed #EEF2FB;
}
.cr-jadwal-card__extra-value {
    display: block;
    margin-top: 4px;
    font-size: 0.86rem;
    font-weight: 500;
    line-height: 1.55;
    color: #5A6A8A;
}
@media (max-width: 1080px) {
    .cr-jadwal-hero,
    .cr-jadwal-highlight__card {
        grid-template-columns: 1fr;
    }
    .cr-jadwal-hero__stats,
    .cr-jadwal-grid {
        grid-template-columns: 1fr;
    }
}
@media (max-width: 720px) {
    .cr-jadwal-card__title-row,
    .cr-jadwal-card__top {
        flex-direction: column;
    }
    .cr-jadwal-card__meta-grid {
        grid-template-columns: 1fr;
    }
}
</style>
@endsection