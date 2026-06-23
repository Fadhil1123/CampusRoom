@extends('layouts.dashboard')

@section('title', 'Detail Booking #BK-' . str_pad($booking->booking_id, 4, '0', STR_PAD_LEFT))

@section('content')
<div class="cr-dash-content cr-bd-content">

    {{-- Brand pill --}}
    <div class="cr-dash-brand-pill">
        <span class="cr-dash-brand-pill__icon">🟨</span>
        <span class="cr-dash-brand-pill__text">CampusRoom • v1.0 Mahasiswa</span>
    </div>

    {{-- Breadcrumb --}}
    <nav class="cr-bd-breadcrumb">
        <a href="/booking/history">Riwayat</a>
        <span class="cr-bd-breadcrumb__sep">›</span>
        <span>Detail Booking #BK-{{ date('Y', strtotime($booking->tanggal)) }}-{{ str_pad($booking->booking_id, 3, '0', STR_PAD_LEFT) }}</span>
    </nav>

    {{-- Flash --}}
    @if(session('success'))
    <div class="cr-bd-flash cr-bd-flash--success">✅ {{ session('success') }}</div>
    @endif
    @if(session('error'))
    <div class="cr-bd-flash cr-bd-flash--error">⚠️ {{ session('error') }}</div>
    @endif

    {{-- ======== STATUS BANNER ======== --}}
    @php
        $statusClass = match($booking->status) {
            'approved' => 'approved',
            'pending'  => 'pending',
            default    => 'rejected',
        };
        $statusLabel = match($booking->status) {
            'approved' => 'Approved ✅',
            'pending'  => 'Pending ⌛',
            default    => 'Rejected ❌',
        };
        $statusHeadline = match($booking->status) {
            'approved' => 'Booking Anda telah disetujui ✅',
            'pending'  => 'Booking Anda sedang menunggu persetujuan ⌛',
            default    => 'Booking Anda ditolak ❌',
        };
        $bookingCode = '#BK-' . date('Y', strtotime($booking->tanggal)) . '-' . str_pad($booking->booking_id, 3, '0', STR_PAD_LEFT);
        $colorHex    = '#' . substr(md5($booking->booking_id), 0, 6);

        $user = session('user');
        $namaUser = $user->nama ?? 'User';

        $rooms      = $booking->rooms;
        $namaRuangan = $rooms->pluck('nama_ruangan')->implode(' · ') ?: '-';
    @endphp

    <div class="cr-bd-banner cr-bd-banner--{{ $statusClass }}">
        <div class="cr-bd-banner__top">
            <span class="cr-bd-banner__badge">{{ $statusLabel }}</span>
        </div>
        <h2 class="cr-bd-banner__headline">{{ $statusHeadline }}</h2>
        <div class="cr-bd-banner__meta">
            <span class="cr-bd-banner__code">{{ $bookingCode }}</span>
            <span class="cr-bd-banner__hex">{{ $colorHex }}</span>
        </div>
    </div>

    {{-- ======== INFO + TIMELINE ======== --}}
    <div class="cr-bd-grid">

        {{-- Info Card --}}
        <div class="cr-bd-info-card">
            <p class="cr-bd-info-card__title">Info Card <span>(Detail Booking)</span></p>
            <div class="cr-bd-info-rows">
                <div class="cr-bd-info-row">
                    <span class="cr-bd-info-row__key">Aran</span>
                    <span class="cr-bd-info-row__val">{{ $namaUser }}</span>
                </div>
                <div class="cr-bd-info-row">
                    <span class="cr-bd-info-row__key">Kunangan</span>
                    <span class="cr-bd-info-row__val">{{ $namaRuangan }}</span>
                </div>
                <div class="cr-bd-info-row">
                    <span class="cr-bd-info-row__key">Duhat</span>
                    <span class="cr-bd-info-row__val">
                        {{ substr($booking->jam_mulai,0,5) }} - {{ substr($booking->jam_selesai,0,5) }}
                    </span>
                </div>
                <div class="cr-bd-info-row">
                    <span class="cr-bd-info-row__key">Kode</span>
                    <span class="cr-bd-info-row__val cr-bd-info-row__val--code">{{ $bookingCode }}</span>
                </div>
            </div>
        </div>

        {{-- Timeline --}}
        <div class="cr-bd-timeline">
            <p class="cr-bd-timeline__title">Timeline Status</p>
            <div class="cr-bd-timeline-list">

                {{-- Diajukan --}}
                <div class="cr-bd-tl-item cr-bd-tl-item--done">
                    <div class="cr-bd-tl-dot cr-bd-tl-dot--done"></div>
                    <div class="cr-bd-tl-body">
                        <span class="cr-bd-tl-badge cr-bd-tl-badge--blue">Diajukan 📋</span>
                        <p class="cr-bd-tl-date">
                            {{ \Carbon\Carbon::parse($booking->tanggal)->format('d-m-Y') }}
                        </p>
                    </div>
                </div>
                <div class="cr-bd-tl-line"></div>

                {{-- Status saat ini --}}
                @if($booking->status === 'approved')
                <div class="cr-bd-tl-item cr-bd-tl-item--done">
                    <div class="cr-bd-tl-dot cr-bd-tl-dot--approved"></div>
                    <div class="cr-bd-tl-body">
                        <span class="cr-bd-tl-badge cr-bd-tl-badge--green">Approved ✅</span>
                        <p class="cr-bd-tl-date">
                            {{ $booking->approved_at ? \Carbon\Carbon::parse($booking->approved_at)->format('d-m-Y') : '-' }}
                        </p>
                    </div>
                </div>
                @elseif($booking->status === 'rejected')
                <div class="cr-bd-tl-item cr-bd-tl-item--done">
                    <div class="cr-bd-tl-dot cr-bd-tl-dot--rejected"></div>
                    <div class="cr-bd-tl-body">
                        <span class="cr-bd-tl-badge cr-bd-tl-badge--red">Rejected ❌</span>
                        <p class="cr-bd-tl-date">
                            {{ $booking->approved_at ? \Carbon\Carbon::parse($booking->approved_at)->format('d-m-Y') : '-' }}
                        </p>
                    </div>
                </div>
                @else
                <div class="cr-bd-tl-item">
                    <div class="cr-bd-tl-dot cr-bd-tl-dot--pending"></div>
                    <div class="cr-bd-tl-body">
                        <span class="cr-bd-tl-badge cr-bd-tl-badge--yellow">Menunggu ⌛</span>
                        <p class="cr-bd-tl-date">Perlu persetujuan admin</p>
                    </div>
                </div>
                @endif

            </div>
        </div>

    </div>

    {{-- ======== DETAIL KEGIATAN (kalau jenis kegiatan) ======== --}}
    @if($booking->jenis === 'kegiatan' && $booking->kegiatan)
    <div class="cr-bd-section">
        <div class="cr-bd-section__header">
            <span>🎯</span>
            <h3 class="cr-bd-section__title">Detail Kegiatan</h3>
        </div>
        <div class="cr-bd-detail-grid">
            <div class="cr-bd-detail-item">
                <p class="cr-bd-detail-label">Nama Kegiatan</p>
                <p class="cr-bd-detail-val">{{ $booking->kegiatan->nama_kegiatan }}</p>
            </div>
            <div class="cr-bd-detail-item">
                <p class="cr-bd-detail-label">Penyelenggara</p>
                <p class="cr-bd-detail-val">{{ $booking->kegiatan->penyelenggara ?? '-' }}</p>
            </div>
            <div class="cr-bd-detail-item">
                <p class="cr-bd-detail-label">Tanggal</p>
                <p class="cr-bd-detail-val">
                    {{ \Carbon\Carbon::parse($booking->tanggal)->locale('id')->translatedFormat('l, d F Y') }}
                    @if(!empty($booking->kegiatan->tanggal_selesai) && $booking->kegiatan->tanggal_selesai != $booking->tanggal)
                        s/d {{ \Carbon\Carbon::parse($booking->kegiatan->tanggal_selesai)->locale('id')->translatedFormat('d F Y') }}
                    @endif
                </p>
            </div>
            <div class="cr-bd-detail-item">
                <p class="cr-bd-detail-label">Perkiraan Peserta</p>
                <p class="cr-bd-detail-val">{{ $booking->kegiatan->perkiraan_peserta ?? '-' }} orang</p>
            </div>
            @if($booking->kegiatan->deskripsi)
            <div class="cr-bd-detail-item cr-bd-detail-item--full">
                <p class="cr-bd-detail-label">Deskripsi</p>
                <p class="cr-bd-detail-val">{{ $booking->kegiatan->deskripsi }}</p>
            </div>
            @endif
        </div>
    </div>
    @endif

    {{-- ======== SURAT PEMINJAMAN (hanya untuk kegiatan) ======== --}}
    @if($booking->jenis === 'kegiatan' && $booking->surat)
    <div class="cr-bd-section">
        <div class="cr-bd-section__header">
            <span>🗂️</span>
            <h3 class="cr-bd-section__title">
                Surat Peminjaman <span class="cr-bd-section__sub">(kacause this is a 🎯 Kegiatan)</span>
            </h3>
        </div>
        <div class="cr-bd-surat-card">
            {{-- Thumbnail icon --}}
            <div class="cr-bd-surat-thumb">
                @php
                    $ext = pathinfo($booking->surat, PATHINFO_EXTENSION);
                    $isPdf = strtolower($ext) === 'pdf';
                @endphp
                @if($isPdf)
                    <span class="cr-bd-surat-thumb__icon">📄</span>
                @else
                    <img src="{{ asset('storage/' . $booking->surat) }}"
                         alt="Surat" class="cr-bd-surat-thumb__img">
                @endif
            </div>

            {{-- File info --}}
            <div class="cr-bd-surat-info">
                <p class="cr-bd-surat-info__name">{{ basename($booking->surat) }}</p>
                @php
                    $filePath = storage_path('app/public/' . $booking->surat);
                    $fileSize = file_exists($filePath)
                        ? round(filesize($filePath) / 1024) . ' KB'
                        : '-';
                @endphp
                <p class="cr-bd-surat-info__size">Ukuran: {{ $fileSize }}</p>
                <div class="cr-bd-surat-info__actions">
                    <a href="{{ asset('storage/' . $booking->surat) }}" target="_blank"
                       class="cr-bd-surat-link">[ 👁️ Lihat Surat ]</a>
                    <a href="{{ asset('storage/' . $booking->surat) }}" download
                       class="cr-bd-surat-link">[ ⬇️ Download ]</a>
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- ======== AKSI ======== --}}
    <div class="cr-bd-section">
        <div class="cr-bd-section__header">
            <h3 class="cr-bd-section__title">Aksi</h3>
            <div class="cr-bd-section__line"></div>
        </div>

        <div class="cr-bd-actions">
            <button onclick="downloadPDF()" class="cr-bd-btn-download">
                📥 Download PDF Booking
            </button>

            @if($booking->status === 'pending')
            <button class="cr-bd-btn-batal" id="btnBatal"
                    onclick="konfirmasiBatal({{ $booking->booking_id }})">
                ✕ Batalkan Booking<br>
                <span class="cr-bd-btn-batal__sub">(if oodifranote or cancel, surat for HMI)</span>
            </button>
            @else
            <button class="cr-bd-btn-batal cr-bd-btn-batal--disabled" disabled>
                Batalkan Booking<br>
                <span class="cr-bd-btn-batal__sub">(hanya bisa dibatalkan saat pending)</span>
            </button>
            @endif
        </div>
    </div>

</div>

{{-- Modal konfirmasi batal --}}
<div class="cr-bd-modal" id="modalBatal" style="display:none">
    <div class="cr-bd-modal__box">
        <p class="cr-bd-modal__title">⚠️ Batalkan Booking?</p>
        <p class="cr-bd-modal__desc">Booking {{ $bookingCode }} akan dibatalkan dan tidak bisa dikembalikan.</p>
        <div class="cr-bd-modal__actions">
            <button class="cr-bd-modal__cancel" onclick="tutupModal()">Tidak, Kembali</button>
            <button class="cr-bd-modal__confirm" id="btnKonfirmasiBatal">Ya, Batalkan</button>
        </div>
    </div>
</div>
<div class="cr-bd-modal-overlay" id="modalOverlay" style="display:none" onclick="tutupModal()"></div>

<style>
/* ============================================================
   DETAIL BOOKING PAGE
   ============================================================ */
.cr-bd-content { max-width: 920px; }

/* Breadcrumb */
.cr-bd-breadcrumb {
    display: flex;
    align-items: center;
    gap: 8px;
    margin-top: 54px;
    margin-bottom: 18px;
    font-family: 'Plus Jakarta Sans', sans-serif;
    font-size: 0.813rem;
    font-weight: 600;
    color: #9AAFC8;
}
.cr-bd-breadcrumb a {
    color: #9AAFC8;
    text-decoration: none;
    transition: color .15s;
}
.cr-bd-breadcrumb a:hover { color: #1A2340; }
.cr-bd-breadcrumb__sep { color: #C5D4ED; }
.cr-bd-breadcrumb span:last-child { color: #1A2340; }

/* Flash */
.cr-bd-flash {
    padding: 12px 16px;
    border-radius: 10px;
    font-family: 'Plus Jakarta Sans', sans-serif;
    font-size: 0.875rem;
    font-weight: 600;
    margin-bottom: 16px;
}
.cr-bd-flash--success { background: #D1FAF0; color: #00C896; border: 1px solid #A0E8D8; }
.cr-bd-flash--error   { background: #FFF0F3; color: #FF4D6D; border: 1px solid #FFD0D8; }

/* ============================================================
   STATUS BANNER
   ============================================================ */
.cr-bd-banner {
    border-radius: 18px;
    padding: 24px 28px;
    margin-bottom: 20px;
    position: relative;
    overflow: hidden;
}
.cr-bd-banner--approved {
    background: linear-gradient(135deg, #00C896 0%, #4FC3F7 100%);
}
.cr-bd-banner--pending {
    background: linear-gradient(135deg, #F4B400 0%, #FFD54F 100%);
}
.cr-bd-banner--rejected {
    background: linear-gradient(135deg, #FF4D6D 0%, #FF8FA3 100%);
}

.cr-bd-banner__top { margin-bottom: 8px; }
.cr-bd-banner__badge {
    display: inline-flex;
    align-items: center;
    padding: 5px 14px;
    border-radius: 999px;
    background: rgba(255,255,255,0.25);
    font-family: 'Plus Jakarta Sans', sans-serif;
    font-size: 0.75rem;
    font-weight: 700;
    color: #fff;
    letter-spacing: 0.3px;
}
.cr-bd-banner__headline {
    font-family: 'Space Grotesk', sans-serif;
    font-size: 1.375rem;
    font-weight: 700;
    color: #fff;
    margin: 0 0 10px;
    letter-spacing: -0.3px;
}
.cr-bd-banner__meta {
    display: flex;
    align-items: center;
    gap: 12px;
}
.cr-bd-banner__code,
.cr-bd-banner__hex {
    font-family: 'DM Sans', sans-serif;
    font-size: 0.813rem;
    color: rgba(255,255,255,0.80);
}

/* ============================================================
   INFO + TIMELINE GRID
   ============================================================ */
.cr-bd-grid {
    display: grid;
    grid-template-columns: 1.2fr 1fr;
    gap: 16px;
    margin-bottom: 16px;
}

/* Info Card */
.cr-bd-info-card {
    background: #fff;
    border: 1.5px solid #EEF2FB;
    border-radius: 16px;
    padding: 20px 22px;
    box-shadow: 0 2px 10px rgba(26,35,64,0.06);
}
.cr-bd-info-card__title {
    font-family: 'Plus Jakarta Sans', sans-serif;
    font-size: 0.875rem;
    font-weight: 800;
    color: #1A2340;
    margin: 0 0 14px;
}
.cr-bd-info-card__title span {
    font-weight: 500;
    color: #9AAFC8;
    font-size: 0.75rem;
}
.cr-bd-info-rows { display: flex; flex-direction: column; gap: 10px; }
.cr-bd-info-row {
    display: flex;
    gap: 12px;
    align-items: flex-start;
    font-family: 'DM Sans', sans-serif;
    font-size: 0.875rem;
}
.cr-bd-info-row__key {
    width: 80px;
    flex-shrink: 0;
    color: #9AAFC8;
    font-size: 0.813rem;
}
.cr-bd-info-row__val {
    color: #1A2340;
    font-family: 'Plus Jakarta Sans', sans-serif;
    font-weight: 600;
    font-size: 0.875rem;
    flex: 1;
}
.cr-bd-info-row__val--code {
    font-family: 'DM Sans', monospace;
    color: #4FC3F7;
}

/* Timeline */
.cr-bd-timeline {
    background: #fff;
    border: 1.5px solid #EEF2FB;
    border-radius: 16px;
    padding: 20px 22px;
    box-shadow: 0 2px 10px rgba(26,35,64,0.06);
}
.cr-bd-timeline__title {
    font-family: 'Plus Jakarta Sans', sans-serif;
    font-size: 0.875rem;
    font-weight: 800;
    color: #1A2340;
    margin: 0 0 16px;
}
.cr-bd-timeline-list {
    display: flex;
    flex-direction: column;
}
.cr-bd-tl-item {
    display: flex;
    align-items: flex-start;
    gap: 12px;
}
.cr-bd-tl-dot {
    width: 16px;
    height: 16px;
    border-radius: 50%;
    border: 2.5px solid #E8EEF7;
    background: #fff;
    flex-shrink: 0;
    margin-top: 2px;
    position: relative;
}
.cr-bd-tl-dot--done     { border-color: #00C896; }
.cr-bd-tl-dot--approved { border-color: #00C896; background: #00C896; }
.cr-bd-tl-dot--pending  { border-color: #F4B400; background: #F4B400; }
.cr-bd-tl-dot--rejected { border-color: #FF4D6D; background: #FF4D6D; }

.cr-bd-tl-line {
    width: 2px;
    height: 28px;
    background: #EEF2FB;
    margin: 4px 0 4px 7px;
}
.cr-bd-tl-body { flex: 1; }
.cr-bd-tl-badge {
    display: inline-flex;
    align-items: center;
    padding: 4px 12px;
    border-radius: 999px;
    font-family: 'Plus Jakarta Sans', sans-serif;
    font-size: 0.688rem;
    font-weight: 700;
    margin-bottom: 4px;
}
.cr-bd-tl-badge--blue   { background: #EFF8FF; color: #4FC3F7; }
.cr-bd-tl-badge--green  { background: #D1FAF0; color: #00C896; }
.cr-bd-tl-badge--yellow { background: #FFF9E6; color: #E6820A; }
.cr-bd-tl-badge--red    { background: #FFF0F3; color: #FF4D6D; }
.cr-bd-tl-date {
    font-family: 'DM Sans', sans-serif;
    font-size: 0.75rem;
    color: #5A6A8A;
    margin: 0;
}

/* ============================================================
   SECTIONS (Kegiatan, Surat, Aksi)
   ============================================================ */
.cr-bd-section {
    background: #fff;
    border: 1.5px solid #EEF2FB;
    border-radius: 16px;
    padding: 20px 22px;
    margin-bottom: 16px;
    box-shadow: 0 2px 10px rgba(26,35,64,0.06);
}
.cr-bd-section__header {
    display: flex;
    align-items: center;
    gap: 8px;
    margin-bottom: 14px;
}
.cr-bd-section__title {
    font-family: 'Plus Jakarta Sans', sans-serif;
    font-size: 0.875rem;
    font-weight: 800;
    color: #1A2340;
    margin: 0;
}
.cr-bd-section__sub {
    font-weight: 400;
    color: #9AAFC8;
    font-size: 0.75rem;
}
.cr-bd-section__line {
    flex: 1;
    height: 1px;
    background: #EEF2FB;
}

/* Detail kegiatan grid */
.cr-bd-detail-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 14px 20px;
}
.cr-bd-detail-item--full { grid-column: span 2; }
.cr-bd-detail-label {
    font-family: 'DM Sans', sans-serif;
    font-size: 0.688rem;
    color: #9AAFC8;
    margin: 0 0 2px;
}
.cr-bd-detail-val {
    font-family: 'Plus Jakarta Sans', sans-serif;
    font-size: 0.875rem;
    font-weight: 600;
    color: #1A2340;
    margin: 0;
}

/* Surat card */
.cr-bd-surat-card {
    display: flex;
    align-items: center;
    gap: 16px;
    padding: 14px 16px;
    background: #F8FAFF;
    border: 1.5px solid #EEF2FB;
    border-radius: 12px;
}
.cr-bd-surat-thumb {
    width: 60px;
    height: 60px;
    border-radius: 10px;
    background: #E8EEF7;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    overflow: hidden;
}
.cr-bd-surat-thumb__icon { font-size: 1.75rem; }
.cr-bd-surat-thumb__img  { width: 100%; height: 100%; object-fit: cover; }
.cr-bd-surat-info { flex: 1; min-width: 0; }
.cr-bd-surat-info__name {
    font-family: 'Plus Jakarta Sans', sans-serif;
    font-size: 0.875rem;
    font-weight: 700;
    color: #1A2340;
    margin: 0 0 2px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
.cr-bd-surat-info__size {
    font-family: 'DM Sans', sans-serif;
    font-size: 0.75rem;
    color: #9AAFC8;
    margin: 0 0 6px;
}
.cr-bd-surat-info__actions { display: flex; gap: 8px; flex-wrap: wrap; }
.cr-bd-surat-link {
    font-family: 'Plus Jakarta Sans', sans-serif;
    font-size: 0.75rem;
    font-weight: 600;
    color: #4FC3F7;
    text-decoration: none;
    transition: color .15s;
}
.cr-bd-surat-link:hover { color: #0277BD; }

/* ============================================================
   ACTION BUTTONS
   ============================================================ */
.cr-bd-actions {
    display: flex;
    gap: 12px;
    flex-wrap: wrap;
}
.cr-bd-btn-download {
    flex: 1;
    min-width: 200px;
    padding: 14px 20px;
    background: linear-gradient(135deg, #F4B400 0%, #FFB020 100%);
    color: #1A2340;
    font-family: 'Plus Jakarta Sans', sans-serif;
    font-size: 0.875rem;
    font-weight: 800;
    border: none;
    border-radius: 12px;
    cursor: pointer;
    box-shadow: 0 3px 12px rgba(244,180,0,0.28);
    transition: transform .15s, box-shadow .15s;
    text-align: center;
}
.cr-bd-btn-download:hover {
    transform: scale(1.02);
    box-shadow: 0 6px 20px rgba(244,180,0,0.38);
}
.cr-bd-btn-batal {
    flex: 0 0 auto;
    min-width: 180px;
    padding: 12px 20px;
    background: #fff;
    color: #5A6A8A;
    font-family: 'Plus Jakarta Sans', sans-serif;
    font-size: 0.813rem;
    font-weight: 600;
    border: 1.5px solid #E8EEF7;
    border-radius: 12px;
    cursor: pointer;
    text-align: center;
    line-height: 1.4;
    transition: border-color .15s, color .15s;
}
.cr-bd-btn-batal:hover:not(.cr-bd-btn-batal--disabled) {
    border-color: #FF4D6D;
    color: #FF4D6D;
}
.cr-bd-btn-batal--disabled {
    opacity: 0.45;
    cursor: not-allowed;
}
.cr-bd-btn-batal__sub {
    font-size: 0.65rem;
    color: #9AAFC8;
    font-weight: 400;
}

/* ============================================================
   MODAL BATAL
   ============================================================ */
.cr-bd-modal-overlay {
    position: fixed;
    inset: 0;
    background: rgba(26,35,64,0.35);
    backdrop-filter: blur(2px);
    z-index: 200;
}
.cr-bd-modal {
    position: fixed;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    z-index: 201;
    width: 100%;
    max-width: 380px;
    padding: 0 16px;
}
.cr-bd-modal__box {
    background: #fff;
    border-radius: 20px;
    padding: 28px 24px;
    box-shadow: 0 20px 60px rgba(26,35,64,0.18);
    text-align: center;
}
.cr-bd-modal__title {
    font-family: 'Space Grotesk', sans-serif;
    font-size: 1.125rem;
    font-weight: 700;
    color: #1A2340;
    margin: 0 0 8px;
}
.cr-bd-modal__desc {
    font-family: 'DM Sans', sans-serif;
    font-size: 0.875rem;
    color: #5A6A8A;
    margin: 0 0 20px;
    line-height: 1.6;
}
.cr-bd-modal__actions { display: flex; gap: 10px; }
.cr-bd-modal__cancel {
    flex: 1;
    padding: 12px;
    background: #F8FAFF;
    border: 1.5px solid #E8EEF7;
    border-radius: 10px;
    font-family: 'Plus Jakarta Sans', sans-serif;
    font-size: 0.875rem;
    font-weight: 600;
    color: #5A6A8A;
    cursor: pointer;
    transition: background .15s;
}
.cr-bd-modal__cancel:hover { background: #EEF2FB; }
.cr-bd-modal__confirm {
    flex: 1;
    padding: 12px;
    background: #FF4D6D;
    border: none;
    border-radius: 10px;
    font-family: 'Plus Jakarta Sans', sans-serif;
    font-size: 0.875rem;
    font-weight: 700;
    color: #fff;
    cursor: pointer;
    transition: background .15s;
}
.cr-bd-modal__confirm:hover { background: #e6284a; }

/* ============================================================
   RESPONSIVE
   ============================================================ */
@media (max-width: 700px) {
    .cr-bd-grid { grid-template-columns: 1fr; }
    .cr-bd-detail-grid { grid-template-columns: 1fr; }
    .cr-bd-detail-item--full { grid-column: span 1; }
    .cr-bd-actions { flex-direction: column; }
    .cr-bd-btn-batal { min-width: unset; width: 100%; }
}
</style>

<script>
(function(){
    // ─── Modal Batalkan ──────────────────────────────────────────
    const modal        = document.getElementById('modalBatal');
    const overlay      = document.getElementById('modalOverlay');
    const btnKonfirmasi= document.getElementById('btnKonfirmasiBatal');
    let batalId        = null;

    window.konfirmasiBatal = function(id) {
        batalId = id;
        modal.style.display   = '';
        overlay.style.display = '';
    };

    window.tutupModal = function() {
        modal.style.display   = 'none';
        overlay.style.display = 'none';
        batalId = null;
    };

    if (btnKonfirmasi) {
        btnKonfirmasi.addEventListener('click', function() {
            if (!batalId) return;
            btnKonfirmasi.disabled    = true;
            btnKonfirmasi.textContent = 'Membatalkan...';

            fetch('/booking/' + batalId + '/batal', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content,
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                },
            })
            .then(res => res.json())
            .then(data => {
                tutupModal();
                if (data.success) {
                    window.location.href = '/booking/history?success=Booking+berhasil+dibatalkan';
                } else {
                    alert(data.message || 'Gagal membatalkan booking.');
                    btnKonfirmasi.disabled    = false;
                    btnKonfirmasi.textContent = 'Ya, Batalkan';
                }
            })
            .catch(() => {
                tutupModal();
                alert('Terjadi kesalahan, silakan coba lagi.');
                btnKonfirmasi.disabled    = false;
                btnKonfirmasi.textContent = 'Ya, Batalkan';
            });
        });
    }

    // ─── Download PDF Booking (print sederhana) ──────────────────
    window.downloadPDF = function() {
        window.print();
    };
})();
</script>
@endsection