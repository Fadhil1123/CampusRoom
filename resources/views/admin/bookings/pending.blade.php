@extends('layouts.admin')

@section('title', 'Approval Booking Kegiatan')

@section('content')
<div class="cr-adm-content cr-apv-content">

    {{-- Brand pill --}}
    <div class="cr-dash-brand-pill">
        <span class="cr-dash-brand-pill__icon">🟨</span>
        <span class="cr-dash-brand-pill__text">CampusRoom • v1.0 Admin</span>
    </div>

    {{-- Flash --}}
    @if(session('success'))
    <div class="cr-apv-flash cr-apv-flash--success">✅ {{ session('success') }}</div>
    @endif
    @if(session('error'))
    <div class="cr-apv-flash cr-apv-flash--error">⚠️ {{ session('error') }}</div>
    @endif

    {{-- ======== PAGE HEADER ======== --}}
    <div class="cr-apv-header">
        <p class="cr-apv-eyebrow">PAGE HEADER</p>
        <div class="cr-apv-header__title-row">
            <h1 class="cr-apv-title">
                <span>✅</span> Approval Booking Kegiatan
            </h1>
            <span class="cr-apv-pending-badge">[{{ $totalPending }} pending]</span>
        </div>
    </div>

    {{-- ======== FILTER ======== --}}
    <form method="GET" action="/admin/bookings" class="cr-apv-filterbox" id="filterForm">
        <div class="cr-apv-filter-row">
            <div class="cr-apv-search cr-apv-search--wide">
                <span class="cr-apv-search__icon">🔍</span>
                <input type="text" name="search" value="{{ request('search') }}"
                       class="cr-apv-input" placeholder="Cari nama/kode"
                       oninput="debounceSubmit()">
            </div>
            <select name="status" class="cr-apv-select" onchange="this.form.submit()">
                <option value="">Status ▼</option>
                <option value="approved" {{ request('status')==='approved' ? 'selected' : '' }}>Approved</option>
                <option value="pending"  {{ request('status')==='pending' || !request()->has('status') ? 'selected' : '' }}>Pending</option>
                <option value="rejected" {{ request('status')==='rejected' ? 'selected' : '' }}>Rejected</option>
            </select>
            <select name="jenis" class="cr-apv-select" onchange="this.form.submit()">
                <option value="">Jenis ▼</option>
                <option value="perkuliahan" {{ request('jenis')==='perkuliahan' ? 'selected' : '' }}>Perkuliahan</option>
                <option value="kegiatan"    {{ request('jenis')==='kegiatan'    ? 'selected' : '' }}>Kegiatan</option>
            </select>
        </div>
        <div class="cr-apv-filter-row">
            <div class="cr-apv-date-wrap">
                <span class="cr-apv-date__icon">📅</span>
                <input type="date" name="dari" value="{{ request('dari') }}"
                       class="cr-apv-input cr-apv-input--date"
                       onchange="this.form.submit()">
            </div>
            <div class="cr-apv-date-wrap">
                <span class="cr-apv-date__icon">📅</span>
                <input type="date" name="hingga" value="{{ request('hingga') }}"
                       class="cr-apv-input cr-apv-input--date"
                       onchange="this.form.submit()">
            </div>
            <select name="room_id" class="cr-apv-select" onchange="this.form.submit()">
                <option value="">Ruangan ▼</option>
                @foreach($rooms as $r)
                <option value="{{ $r->room_id }}" {{ request('room_id')==$r->room_id ? 'selected' : '' }}>
                    {{ $r->nama_ruangan }}
                </option>
                @endforeach
            </select>
        </div>
    </form>

    {{-- ======== QUEUE LIST ======== --}}
    <div class="cr-apv-queue-wrap">
        <p class="cr-apv-queue-label">QUEUE LIST</p>

        @forelse($bookings as $i => $bk)
        @php
            $isFirst     = $i === 0;
            $namaUser    = $bk->user->nama ?? 'Unknown';
            $nimNip      = $bk->user->nim_nip ?? '';
            $namaKegiatan= $bk->kegiatan?->nama_kegiatan ?? 'Perkuliahan';
            $penyelenggara = $bk->kegiatan?->penyelenggara ?? '';
            $ruangan     = $bk->rooms->pluck('nama_ruangan')->implode(', ') ?: '-';
            $gedung      = 'Gedung ' . chr(64 + (($bk->rooms->first()?->room_id ?? 1) % 3 + 1));
            $hariMap     = ['Monday'=>'Senin','Tuesday'=>'Selasa','Wednesday'=>'Rabu','Thursday'=>'Kamis','Friday'=>'Jumat','Saturday'=>'Sabtu','Sunday'=>'Minggu'];
            $hari        = $hariMap[date('l', strtotime($bk->tanggal))] ?? '-';
            $tanggalFmt  = $hari . ', ' . \Carbon\Carbon::parse($bk->tanggal)->format('d') . ' ' .
                           ['','Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'][(int)\Carbon\Carbon::parse($bk->tanggal)->format('n')] . ' ' .
                           \Carbon\Carbon::parse($bk->tanggal)->format('Y');
            $daysAgo     = \Carbon\Carbon::parse($bk->tanggal)->diffInDays(now());
            $isUrgent    = $daysAgo <= 2;
            $peserta     = $bk->kegiatan?->perkiraan_peserta ?? '-';
        @endphp

        <div class="cr-apv-card {{ $isFirst ? 'cr-apv-card--expanded' : '' }} {{ $isUrgent ? 'cr-apv-card--urgent' : '' }}"
             id="card{{ $bk->booking_id }}">

            {{-- ── Card Header (selalu tampil) ── --}}
            <div class="cr-apv-card__header" onclick="toggleCard({{ $bk->booking_id }})">
                <div class="cr-apv-card__header-left">
                    @if($isFirst)
                    <div class="cr-apv-urgent-bar">
                        <span class="cr-apv-urgent-bar__icon">⌛</span>
                        <span class="cr-apv-urgent-bar__text">MENUNGGU APPROVAL</span>
                        <span class="cr-apv-urgent-bar__divider">|</span>
                        <span class="cr-apv-urgent-bar__days">Diajukan: {{ $daysAgo }} hari lalu</span>
                        @if($isUrgent)
                        <span class="cr-apv-urgent-label">URGENT</span>
                        @endif
                    </div>
                    @endif

                    <p class="cr-apv-card__name">
                        👤 {{ $namaUser }}
                        @if($nimNip) <span class="cr-apv-card__nim">(NIM: {{ $nimNip }})</span>@endif
                    </p>
                    <p class="cr-apv-card__detail">
                        📌 {{ $namaKegiatan }}
                        @if($penyelenggara) — {{ $penyelenggara }}@endif
                    </p>
                    <p class="cr-apv-card__meta">
                        🏢 {{ $ruangan }}, {{ $gedung }}
                        • 📅 {{ $tanggalFmt }}
                    </p>
                </div>
                <div class="cr-apv-card__header-right">
                    <span class="cr-apv-card__toggle-icon" id="toggleIcon{{ $bk->booking_id }}">
                        {{ $isFirst ? '▲' : '▼' }}
                    </span>
                </div>
            </div>

            {{-- ── Card Body (detail, collapsed by default kecuali card pertama) ── --}}
            <div class="cr-apv-card__body" id="body{{ $bk->booking_id }}"
                 style="{{ $isFirst ? '' : 'display:none' }}">

                {{-- Info row --}}
                <div class="cr-apv-card__info-row">
                    <span>🕐 {{ substr($bk->jam_mulai,0,5) }} – {{ substr($bk->jam_selesai,0,5) }}</span>
                    @if($peserta !== '-')
                    <span>👥 {{ $peserta }} peserta</span>
                    @endif
                    @if($bk->kegiatan?->deskripsi)
                    <span>📝 {{ Str::limit($bk->kegiatan->deskripsi, 60) }}</span>
                    @endif
                </div>

                {{-- Multi-room list --}}
                @if($bk->rooms->count() > 1)
                <div class="cr-apv-card__rooms">
                    @foreach($bk->rooms as $r)
                    <span class="cr-apv-room-tag">🏢 {{ $r->nama_ruangan }}</span>
                    @endforeach
                </div>
                @endif

                {{-- Surat (kalau kegiatan) --}}
                @if($bk->surat)
                <div class="cr-apv-card__surat">
                    <p class="cr-apv-card__surat-label">
                        📄 Surat Peminjaman: {{ basename($bk->surat) }}
                        <a href="{{ asset('storage/' . $bk->surat) }}" target="_blank"
                           class="cr-apv-card__surat-link">[👁 Lihat Surat]</a>
                    </p>
                    <div class="cr-apv-surat-row">
                        {{-- Thumbnail --}}
                        <div class="cr-apv-surat-thumb">
                            @php $ext = strtolower(pathinfo($bk->surat, PATHINFO_EXTENSION)); @endphp
                            @if(in_array($ext, ['jpg','jpeg','png','webp']))
                                <img src="{{ asset('storage/' . $bk->surat) }}" alt="Surat">
                            @else
                                <div class="cr-apv-surat-thumb__pdf">
                                    <span>📄</span>
                                    <p>PDF</p>
                                </div>
                            @endif
                        </div>

                        {{-- Catatan admin --}}
                        <form method="POST" action="/admin/bookings/{{ $bk->booking_id }}/catatan"
                              class="cr-apv-catatan-form">
                            @csrf
                            <textarea name="catatan"
                                      class="cr-apv-catatan"
                                      placeholder="Catatan admin (opsional)..."
                                      rows="3">{{ $bk->catatan ?? '' }}</textarea>
                        </form>
                    </div>
                </div>
                @endif

                {{-- Tombol aksi --}}
                <div class="cr-apv-card__actions">
                    <form action="/admin/bookings/{{ $bk->booking_id }}/reject" method="POST" style="display: inline-block; flex: 1; margin: 0;" onsubmit="return confirm('Tolak booking ini?')">
                        @csrf
                        <button type="submit" class="cr-apv-btn cr-apv-btn--tolak" style="width: 100%; border: none; cursor: pointer;">
                            ✕ Tolak Booking
                        </button>
                    </form>
                    <form action="/admin/bookings/{{ $bk->booking_id }}/approve" method="POST" style="display: inline-block; flex: 1; margin: 0;" onsubmit="return confirm('Setujui booking ini?')">
                        @csrf
                        <button type="submit" class="cr-apv-btn cr-apv-btn--setujui" style="width: 100%; border: none; cursor: pointer;">
                            ✓ Setujui Booking
                        </button>
                    </form>
                </div>

            </div>{{-- end card body --}}

        </div>{{-- end card --}}
        @empty
        <div class="cr-apv-empty">
            <span>🎉</span>
            <p>Tidak ada booking yang menunggu persetujuan!</p>
        </div>
        @endforelse

    </div>

</div>

<style>
/* ============================================================
   APPROVAL PAGE
   ============================================================ */
.cr-apv-content { 
    max-width: 960px; 
    margin: 0 auto;
    padding: 0 20px;}

.cr-apv-flash { padding:12px 16px; border-radius:10px; margin:56px 0 16px;
    font-family:'Plus Jakarta Sans',sans-serif; font-size:.875rem; font-weight:600; }
.cr-apv-flash--success { background:#D1FAF0; color:#00C896; border:1px solid #A0E8D8; }
.cr-apv-flash--error   { background:#FFF0F3; color:#FF4D6D; border:1px solid #FFD0D8; }

/* Header */
.cr-apv-header { margin-top:56px; margin-bottom:18px; }
.cr-apv-eyebrow { font-family:'DM Sans',sans-serif; font-size:.688rem; font-weight:700;
    letter-spacing:1.5px; text-transform:uppercase; color:#9AAFC8; margin:0 0 6px; }
.cr-apv-header__title-row { display:flex; align-items:center; gap:14px; flex-wrap:wrap; }
.cr-apv-title { font-family:'Space Grotesk',sans-serif; font-size:1.5rem; font-weight:700;
    color:#1A2340; margin:0; letter-spacing:-.3px; display:flex; align-items:center; gap:8px; }
.cr-apv-pending-badge {
    display:inline-flex; align-items:center; padding:5px 14px;
    background:linear-gradient(135deg,#F4B400,#FFB020); color:#1A2340;
    border-radius:10px; font-family:'Plus Jakarta Sans',sans-serif;
    font-size:.875rem; font-weight:800; box-shadow:0 2px 8px rgba(244,180,0,.25);
}

/* Filter */
.cr-apv-filterbox {
    background:#fff; border:1.5px solid #EEF2FB; border-radius:16px;
    padding:16px 20px; margin-bottom:18px; box-shadow:0 2px 10px rgba(26,35,64,.06);
    display:flex; flex-direction:column; gap:10px;
}
.cr-apv-filter-row { display:flex; gap:10px; align-items:center; flex-wrap:wrap; }
.cr-apv-search { position:relative; display:flex; align-items:center; flex:1; min-width:130px; }
.cr-apv-search--wide { flex:2; }
.cr-apv-search__icon { position:absolute; left:11px; font-size:.75rem; pointer-events:none; }
.cr-apv-input {
    width:100%; padding:9px 12px 9px 32px;
    background:#F8FAFF; border:1.5px solid #E8EEF7; border-radius:10px;
    font-family:'DM Sans',sans-serif; font-size:.813rem; color:#1A2340; outline:none;
    transition:border-color .2s, box-shadow .2s;
}
.cr-apv-input:focus { border-color:#F4B400; box-shadow:0 0 0 3px rgba(244,180,0,.10); }
.cr-apv-input::placeholder { color:#9AAFC8; }
.cr-apv-input--date { padding-left:36px; }
.cr-apv-date-wrap { position:relative; display:flex; align-items:center; }
.cr-apv-date__icon { position:absolute; left:10px; font-size:.75rem; pointer-events:none; z-index:1; }
.cr-apv-select {
    padding:9px 28px 9px 12px; appearance:none; -webkit-appearance:none;
    background:#F8FAFF url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='10' height='6'%3E%3Cpath d='M0 0l5 6 5-6z' fill='%239AAFC8'/%3E%3C/svg%3E") no-repeat right 10px center;
    border:1.5px solid #E8EEF7; border-radius:10px;
    font-family:'Plus Jakarta Sans',sans-serif; font-size:.813rem; font-weight:600; color:#1A2340;
    cursor:pointer; outline:none; white-space:nowrap; transition:border-color .15s;
}
.cr-apv-select:focus { border-color:#F4B400; }

/* Queue label */
.cr-apv-queue-label { font-family:'DM Sans',sans-serif; font-size:.688rem; font-weight:700;
    letter-spacing:1.5px; color:#9AAFC8; text-transform:uppercase; margin:0 0 12px; }
.cr-apv-queue-wrap { display:flex; flex-direction:column; gap:14px; }

/* ============================================================
   QUEUE CARD
   ============================================================ */
.cr-apv-card {
    background:#fff; border:1.5px solid #EEF2FB; border-radius:16px;
    box-shadow:0 2px 10px rgba(26,35,64,.06); overflow:hidden;
    transition:box-shadow .2s;
}
.cr-apv-card:hover { box-shadow:0 4px 18px rgba(26,35,64,.10); }

/* Expanded card — kuning border kiri */
.cr-apv-card--expanded {
    border-left:4px solid #F4B400;
    box-shadow:0 4px 20px rgba(244,180,0,.15);
}

/* Urgent card */
.cr-apv-card--urgent.cr-apv-card--expanded {
    border-left-color:#FF4D6D;
}

/* Card header (clickable) */
.cr-apv-card__header {
    padding:16px 20px; cursor:pointer;
    display:flex; justify-content:space-between; align-items:flex-start; gap:12px;
}
.cr-apv-card__header:hover { background:#FAFBFF; }
.cr-apv-card__header-left { flex:1; display:flex; flex-direction:column; gap:4px; }

/* Urgent bar */
.cr-apv-urgent-bar {
    display:inline-flex; align-items:center; gap:8px;
    padding:5px 12px; margin-bottom:8px;
    background:linear-gradient(135deg,#F4B400,#FFB020);
    border-radius:8px; width:fit-content;
}
.cr-apv-urgent-bar__icon { font-size:.75rem; }
.cr-apv-urgent-bar__text {
    font-family:'Plus Jakarta Sans',sans-serif; font-size:.688rem; font-weight:800;
    color:#1A2340; letter-spacing:.5px; text-transform:uppercase;
}
.cr-apv-urgent-bar__divider { color:rgba(26,35,64,.40); font-size:.75rem; }
.cr-apv-urgent-bar__days {
    font-family:'DM Sans',sans-serif; font-size:.688rem; color:rgba(26,35,64,.65);
}
.cr-apv-urgent-label {
    margin-left:4px; padding:2px 8px; background:#FF4D6D; color:#fff;
    border-radius:5px; font-family:'Plus Jakarta Sans',sans-serif;
    font-size:.6rem; font-weight:800; letter-spacing:.5px;
}

.cr-apv-card__name {
    font-family:'Plus Jakarta Sans',sans-serif; font-size:.875rem; font-weight:700;
    color:#1A2340; margin:0;
}
.cr-apv-card__nim { font-weight:400; color:#5A6A8A; font-size:.813rem; }
.cr-apv-card__detail {
    font-family:'DM Sans',sans-serif; font-size:.813rem; color:#1A2340; margin:0;
}
.cr-apv-card__meta {
    font-family:'DM Sans',sans-serif; font-size:.75rem; color:#5A6A8A; margin:0;
}
.cr-apv-card__toggle-icon {
    font-size:.75rem; color:#9AAFC8; flex-shrink:0;
    margin-top:4px; transition:transform .2s;
}

/* Card body */
.cr-apv-card__body {
    padding:0 20px 20px; display:flex; flex-direction:column; gap:14px;
}
.cr-apv-card__body::before {
    content:''; display:block; height:1px; background:#EEF2FB; margin-bottom:2px;
}

.cr-apv-card__info-row {
    display:flex; flex-wrap:wrap; gap:16px;
    font-family:'DM Sans',sans-serif; font-size:.813rem; color:#5A6A8A;
}

/* Room tags */
.cr-apv-card__rooms { display:flex; flex-wrap:wrap; gap:8px; }
.cr-apv-room-tag {
    padding:4px 12px; background:#F0F4FF; border:1px solid #E0E8F5;
    border-radius:8px; font-family:'Plus Jakarta Sans',sans-serif;
    font-size:.75rem; font-weight:600; color:#1A2340;
}

/* Surat section */
.cr-apv-card__surat-label {
    font-family:'DM Sans',sans-serif; font-size:.813rem; color:#5A6A8A; margin:0 0 10px;
}
.cr-apv-card__surat-link {
    color:#4FC3F7; text-decoration:none; font-family:'Plus Jakarta Sans',sans-serif;
    font-size:.75rem; font-weight:600; margin-left:8px;
}
.cr-apv-card__surat-link:hover { color:#0277BD; }
.cr-apv-surat-row { display:flex; gap:16px; align-items:flex-start; }

.cr-apv-surat-thumb {
    width:90px; height:80px; border-radius:10px; overflow:hidden; flex-shrink:0;
    border:1.5px solid #EEF2FB; background:#F8FAFF;
    display:flex; align-items:center; justify-content:center;
}
.cr-apv-surat-thumb img { width:100%; height:100%; object-fit:cover; }
.cr-apv-surat-thumb__pdf {
    display:flex; flex-direction:column; align-items:center; gap:4px;
    color:#5A6A8A;
}
.cr-apv-surat-thumb__pdf span { font-size:1.75rem; }
.cr-apv-surat-thumb__pdf p { font-family:'Plus Jakarta Sans',sans-serif; font-size:.625rem; font-weight:700; margin:0; }

.cr-apv-catatan-form { flex:1; }
.cr-apv-catatan {
    width:100%; padding:10px 12px;
    background:#F8FAFF; border:1.5px solid #E8EEF7; border-radius:10px;
    font-family:'DM Sans',sans-serif; font-size:.813rem; color:#1A2340;
    resize:vertical; min-height:72px; outline:none; transition:border-color .2s;
}
.cr-apv-catatan:focus { border-color:#F4B400; box-shadow:0 0 0 3px rgba(244,180,0,.10); }

/* Action buttons */
.cr-apv-card__actions { display:flex; gap:12px; flex-wrap:wrap; }
.cr-apv-btn {
    flex:1; min-width:140px; padding:13px 20px;
    border-radius:12px; border:none; cursor:pointer; text-decoration:none;
    display:flex; align-items:center; justify-content:center; gap:8px;
    font-family:'Plus Jakarta Sans',sans-serif; font-size:.875rem; font-weight:800;
    letter-spacing:.3px; transition:transform .15s, box-shadow .15s;
    text-align:center;
}
.cr-apv-btn:hover { transform:scale(1.02); }
.cr-apv-btn--tolak {
    background:#FFF0F3; color:#FF4D6D; border:2px solid #FF4D6D;
}
.cr-apv-btn--tolak:hover { background:#FF4D6D; color:#fff; box-shadow:0 4px 14px rgba(255,77,109,.30); }
.cr-apv-btn--setujui {
    background:linear-gradient(135deg,#00C896,#00A87E); color:#fff;
    box-shadow:0 3px 12px rgba(0,200,150,.28);
}
.cr-apv-btn--setujui:hover { box-shadow:0 6px 20px rgba(0,200,150,.40); }

/* Empty */
.cr-apv-empty {
    display:flex; flex-direction:column; align-items:center;
    padding:60px 20px; gap:10px; color:#9AAFC8;
    background:#fff; border-radius:16px; border:1.5px solid #EEF2FB;
}
.cr-apv-empty span { font-size:3rem; }
.cr-apv-empty p { font-family:'Plus Jakarta Sans',sans-serif; font-size:1rem; font-weight:600; margin:0; }

@media (max-width:700px) {
    .cr-apv-filter-row { flex-direction:column; }
    .cr-apv-header__title-row { flex-direction:column; align-items:flex-start; }
    .cr-apv-btn { min-width:100%; }
    .cr-apv-surat-row { flex-direction:column; }
}
</style>

<script>
(function(){
    // Debounce search
    let debounceTimer;
    window.debounceSubmit = function() {
        clearTimeout(debounceTimer);
        debounceTimer = setTimeout(() => document.getElementById('filterForm').submit(), 500);
    };

    // Toggle card expand/collapse
    window.toggleCard = function(id) {
        const body      = document.getElementById('body' + id);
        const icon      = document.getElementById('toggleIcon' + id);
        const card      = document.getElementById('card' + id);
        const isOpen    = body.style.display !== 'none';

        body.style.display = isOpen ? 'none' : '';
        icon.textContent   = isOpen ? '▼' : '▲';

        if (!isOpen) {
            card.classList.add('cr-apv-card--expanded');
        } else {
            card.classList.remove('cr-apv-card--expanded');
        }
    };
})();
</script>
@endsection