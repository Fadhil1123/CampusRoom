@extends('layouts.dashboard')

@section('title', 'Riwayat Booking Saya')

@section('content')
<div class="cr-dash-content cr-hist-content">

    <div class="cr-dash-brand-pill">
        <span class="cr-dash-brand-pill__icon">🟨</span>
        <span class="cr-dash-brand-pill__text">CampusRoom • v1.0 Mahasiswa</span>
    </div>

    {{-- ===== HEADER ===== --}}
    <div class="cr-hist-header">
        <div>
            <h1 class="cr-hist-title">🗒️ Riwayat Booking Saya</h1>
            <p class="cr-hist-sub">Total: {{ $bookings->count() }} booking</p>
        </div>
    </div>

    @if(session('success'))
    <div class="cr-hist-flash cr-hist-flash--success">✅ {{ session('success') }}</div>
    @endif
    @if(session('error'))
    <div class="cr-hist-flash cr-hist-flash--error">⚠️ {{ session('error') }}</div>
    @endif

    {{-- ===== STATUS TABS ===== --}}
    <div class="cr-hist-tabs">
        <button class="cr-hist-tab is-active" data-filter="semua">
            Semua [{{ $bookings->count() }}]
        </button>
        <button class="cr-hist-tab cr-hist-tab--approved" data-filter="approved">
            Approved [{{ $bookings->where('status','approved')->count() }}]
        </button>
        <button class="cr-hist-tab cr-hist-tab--pending" data-filter="pending">
            Pending [{{ $bookings->where('status','pending')->count() }}]
        </button>
        <button class="cr-hist-tab cr-hist-tab--rejected" data-filter="rejected">
            Rejected [{{ $bookings->where('status','rejected')->count() }}]
        </button>
    </div>

    {{-- ===== FILTER BAR ===== --}}
    <div class="cr-hist-filters">
        <div class="cr-hist-filter-group">
            <label class="cr-hist-filter-label">[📅 Filter Tanggal]</label>
            <div class="cr-hist-input-wrap">
                <input type="month" id="filterBulan" class="cr-hist-input" placeholder="e.g. Juni 2025">
                <span class="cr-hist-input-icon">📅</span>
            </div>
        </div>
        <div class="cr-hist-filter-group">
            <label class="cr-hist-filter-label">[Jenis ▼]</label>
            <div class="cr-hist-input-wrap">
                <select id="filterJenis" class="cr-hist-select">
                    <option value="">Semua Jenis</option>
                    <option value="perkuliahan">Perkuliahan</option>
                    <option value="kegiatan">Kegiatan</option>
                </select>
                <span class="cr-hist-input-icon" style="pointer-events:none">▼</span>
            </div>
        </div>
        <div class="cr-hist-filter-group">
            <label class="cr-hist-filter-label">[Cari 🔍]</label>
            <div class="cr-hist-input-wrap">
                <input type="text" id="filterCari" class="cr-hist-input" placeholder="Cari">
                <span class="cr-hist-input-icon">🔍</span>
            </div>
        </div>
    </div>

    {{-- ===== BOOKING LIST grouped per bulan ===== --}}
    <div id="bookingContainer">
    @php
        $grouped = $bookings->groupBy(function($b) {
            return \Carbon\Carbon::parse($b->tanggal)->locale('id')->translatedFormat('F Y');
        });
    @endphp

    @forelse($grouped as $bulan => $group)
    <div class="cr-hist-month-group"
         data-month="{{ \Carbon\Carbon::parse($group->first()->tanggal)->format('Y-m') }}">

        <div class="cr-hist-month-header">
            <span class="cr-hist-month-label">{{ strtoupper($bulan) }} ({{ $group->count() }} booking)</span>
            <div class="cr-hist-month-line"></div>
        </div>

        <div class="cr-hist-grid">
            @foreach($group as $booking)
            <div class="cr-hist-card"
                 data-status="{{ $booking->status }}"
                 data-jenis="{{ $booking->jenis }}"
                 data-tanggal="{{ $booking->tanggal }}"
                 data-search="{{ strtolower(
                     ($booking->jenis === 'perkuliahan'
                         ? ($booking->rooms->first()?->nama_ruangan ?? '')
                         : ($booking->kegiatan?->nama_kegiatan ?? ''))
                     . ' ' . $booking->rooms->pluck('nama_ruangan')->implode(' ')
                 ) }}">

                {{-- Top: status badge + tanggal --}}
                <div class="cr-hist-card__top">
                    @if($booking->status === 'approved')
                        <span class="cr-hist-badge cr-hist-badge--approved">Approved ✅</span>
                    @elseif($booking->status === 'pending')
                        <span class="cr-hist-badge cr-hist-badge--pending">Pending ⌛</span>
                    @else
                        <span class="cr-hist-badge cr-hist-badge--rejected">Rejected ❌</span>
                    @endif
                    <span class="cr-hist-card__date">
                        {{ \Carbon\Carbon::parse($booking->tanggal)->format('d-m-Y') }}
                    </span>
                </div>

                {{-- Tanggal selesai jika multi-hari --}}
                @if($booking->jenis === 'kegiatan' && !empty($booking->kegiatan?->tanggal_selesai) && $booking->kegiatan->tanggal_selesai != $booking->tanggal)
                <p class="cr-hist-card__date2">
                    s/d {{ \Carbon\Carbon::parse($booking->kegiatan->tanggal_selesai)->format('d-m-Y') }}
                </p>
                @endif

                {{-- Jenis tag --}}
                <div class="cr-hist-card__jenis">
                    @if($booking->jenis === 'perkuliahan')
                        <span class="cr-hist-tag cr-hist-tag--kuliah">🎓 Kuliah</span>
                    @else
                        <span class="cr-hist-tag cr-hist-tag--kegiatan">🎯 Kegiatan</span>
                    @endif
                </div>

                {{-- Judul --}}
                <h3 class="cr-hist-card__title">
                    @if($booking->jenis === 'perkuliahan')
                        {{ $booking->rooms->first()?->nama_ruangan ?? 'Perkuliahan' }}
                    @else
                        {{ $booking->kegiatan?->nama_kegiatan ?? 'Kegiatan' }}
                    @endif
                </h3>

                {{-- Ruangan --}}
                <p class="cr-hist-card__room">
                    🏢 {{ $booking->rooms->pluck('nama_ruangan')->implode(' · ') ?: '-' }}
                </p>

                {{-- Waktu --}}
                <p class="cr-hist-card__time">
                    🕐 {{ substr($booking->jam_mulai,0,5) }} – {{ substr($booking->jam_selesai,0,5) }}
                </p>

                <div class="cr-hist-card__divider"></div>

                {{-- Action buttons --}}
                <div class="cr-hist-card__actions">
                    <a href="/booking/detail/{{ $booking->booking_id }}"
                       class="cr-hist-btn cr-hist-btn--detail">Lihat Detail</a>

                    @if($booking->status === 'approved')
                        <button class="cr-hist-btn cr-hist-btn--rebook"
                            onclick="window.location.href='/booking/{{ $booking->jenis }}?room_id={{ $booking->rooms->first()?->room_id }}'">
                            Re-book
                        </button>
                    @elseif($booking->status === 'pending')
                        <button class="cr-hist-btn cr-hist-btn--batal"
                            onclick="batalBooking({{ $booking->booking_id }})">
                            Batalkan
                        </button>
                    @endif
                </div>

            </div>
            @endforeach
        </div>

    </div>
    @empty
    <div class="cr-hist-empty">
        <span>📋</span>
        <p>Belum ada riwayat booking</p>
        <a href="/booking" class="cr-hist-empty-btn">Booking Sekarang</a>
    </div>
    @endforelse
    </div>

    {{-- ===== PAGINATION ===== --}}
    <div class="cr-hist-pagination">
        <button class="cr-hist-page-btn" id="pagePrev" disabled>‹</button>
        <span class="cr-hist-page-num" id="pageNum">1</span>
        <button class="cr-hist-page-btn" id="pageNext">›</button>
    </div>

</div>

<style>
.cr-hist-content { max-width: 900px; }

.cr-hist-header { margin-top: 56px; margin-bottom: 20px; }
.cr-hist-title {
    font-family: 'Space Grotesk', sans-serif; font-size: 1.625rem; font-weight: 700;
    color: #1A2340; margin: 0 0 4px; letter-spacing: -0.3px;
}
.cr-hist-sub { font-family: 'DM Sans', sans-serif; font-size: 0.875rem; color: #5A6A8A; margin: 0; }

.cr-hist-flash {
    padding: 12px 16px; border-radius: 10px; font-family: 'Plus Jakarta Sans', sans-serif;
    font-size: 0.875rem; font-weight: 600; margin-bottom: 16px;
}
.cr-hist-flash--success { background: #D1FAF0; border: 1px solid #A0E8D8; color: #00C896; }
.cr-hist-flash--error   { background: #FFF0F3; border: 1px solid #FFD0D8; color: #FF4D6D; }

/* TABS */
.cr-hist-tabs { display: flex; gap: 8px; flex-wrap: wrap; margin-bottom: 16px; }
.cr-hist-tab {
    padding: 7px 16px; border-radius: 999px; border: 1.5px solid #E8EEF7; background: #fff;
    font-family: 'Plus Jakarta Sans', sans-serif; font-size: 0.813rem; font-weight: 600; color: #5A6A8A;
    cursor: pointer; transition: all .15s;
}
.cr-hist-tab:hover { border-color: #C8D5EE; color: #1A2340; }
.cr-hist-tab.is-active          { background: #1A2340; border-color: #1A2340; color: #fff; }
.cr-hist-tab--approved.is-active{ background: #00C896; border-color: #00C896; color: #fff; }
.cr-hist-tab--pending.is-active { background: #F4B400; border-color: #F4B400; color: #1A2340; }
.cr-hist-tab--rejected.is-active{ background: #FF4D6D; border-color: #FF4D6D; color: #fff; }

/* FILTERS */
.cr-hist-filters { display: flex; gap: 12px; flex-wrap: wrap; margin-bottom: 24px; align-items: flex-end; }
.cr-hist-filter-group { display: flex; flex-direction: column; gap: 5px; flex: 1; min-width: 140px; }
.cr-hist-filter-label {
    font-family: 'Plus Jakarta Sans', sans-serif; font-size: 0.75rem; font-weight: 700; color: #5A6A8A;
}
.cr-hist-input-wrap { position: relative; }
.cr-hist-input,
.cr-hist-select {
    width: 100%; padding: 10px 34px 10px 12px; border: 1.5px solid #E8EEF7; border-radius: 10px;
    font-family: 'DM Sans', sans-serif; font-size: 0.838rem; color: #1A2340; background: #fff;
    outline: none; appearance: none; -webkit-appearance: none; transition: border-color .2s;
}
.cr-hist-input:focus,
.cr-hist-select:focus { border-color: #F4B400; box-shadow: 0 0 0 3px rgba(244,180,0,.10); }
.cr-hist-input-icon {
    position: absolute; right: 10px; top: 50%; transform: translateY(-50%);
    font-size: 0.75rem; color: #9AAFC8;
}

/* MONTH GROUP */
.cr-hist-month-group { margin-bottom: 28px; }
.cr-hist-month-header { display: flex; align-items: center; gap: 14px; margin-bottom: 14px; }
.cr-hist-month-label {
    font-family: 'Plus Jakarta Sans', sans-serif; font-size: 0.875rem; font-weight: 800;
    color: #1A2340; white-space: nowrap;
}
.cr-hist-month-line { flex: 1; height: 1px; background: #E8EEF7; }

/* GRID */
.cr-hist-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 14px; }

/* CARD */
.cr-hist-card {
    background: #fff; border: 1.5px solid #EEF2FB; border-radius: 16px; padding: 16px;
    display: flex; flex-direction: column; gap: 6px;
    box-shadow: 0 2px 8px rgba(26,35,64,0.05); transition: transform .2s, box-shadow .2s;
}
.cr-hist-card:hover { transform: translateY(-2px); box-shadow: 0 6px 20px rgba(26,35,64,0.09); }
.cr-hist-card.hidden { display: none !important; }
.cr-hist-month-group.all-hidden { display: none !important; }

.cr-hist-card__top { display: flex; align-items: center; justify-content: space-between; }
.cr-hist-card__date { font-family: 'DM Sans', sans-serif; font-size: 0.75rem; color: #9AAFC8; }
.cr-hist-card__date2 { font-family: 'DM Sans', sans-serif; font-size: 0.7rem; color: #9AAFC8; margin: -2px 0 0; }

.cr-hist-badge {
    font-family: 'Plus Jakarta Sans', sans-serif; font-size: 0.72rem; font-weight: 800;
    padding: 3px 12px; border-radius: 999px; display: inline-flex; align-items: center; gap: 4px;
}
.cr-hist-badge--approved { background: #D1FAF0; color: #00C896; }
.cr-hist-badge--pending  { background: #FFF3CD; color: #E6820A; }
.cr-hist-badge--rejected { background: #FFE4E9; color: #FF4D6D; }

.cr-hist-card__jenis { margin-top: 2px; }
.cr-hist-tag {
    display: inline-flex; align-items: center; gap: 4px;
    font-family: 'Plus Jakarta Sans', sans-serif; font-size: 0.7rem; font-weight: 700;
    padding: 3px 10px; border-radius: 999px;
}
.cr-hist-tag--kuliah   { background: rgba(79,195,247,0.12); color: #0277BD; }
.cr-hist-tag--kegiatan { background: rgba(244,180,0,0.12); color: #E6820A; }

.cr-hist-card__title {
    font-family: 'Plus Jakarta Sans', sans-serif; font-size: 0.938rem; font-weight: 800;
    color: #1A2340; margin: 4px 0 0; line-height: 1.3;
}
.cr-hist-card__room,
.cr-hist-card__time {
    font-family: 'DM Sans', sans-serif; font-size: 0.788rem; color: #5A6A8A; margin: 0;
}
.cr-hist-card__divider { height: 1px; background: #EEF2FB; margin: 8px 0 4px; }

.cr-hist-card__actions { display: flex; gap: 8px; }
.cr-hist-btn {
    flex: 1; padding: 8px 10px; border-radius: 8px; border: none; cursor: pointer;
    font-family: 'Plus Jakarta Sans', sans-serif; font-size: 0.75rem; font-weight: 700;
    text-align: center; text-decoration: none; display: inline-flex; align-items: center;
    justify-content: center; transition: transform .15s, box-shadow .15s;
}
.cr-hist-btn--detail {
    background: linear-gradient(135deg, #F4B400 0%, #FFB020 100%); color: #1A2340;
    box-shadow: 0 2px 8px rgba(244,180,0,.22);
}
.cr-hist-btn--detail:hover { transform: scale(1.02); box-shadow: 0 4px 14px rgba(244,180,0,.32); }
.cr-hist-btn--rebook { background: #F0F4FF; color: #1A2340; border: 1.5px solid #E8EEF7; }
.cr-hist-btn--rebook:hover { background: #E8EEF7; }
.cr-hist-btn--batal  { background: #fff; color: #FF4D6D; border: 1.5px solid #FFD0D8; }
.cr-hist-btn--batal:hover { background: #FFF0F3; }

/* EMPTY */
.cr-hist-empty {
    display: flex; flex-direction: column; align-items: center; gap: 10px;
    padding: 60px 20px; background: #fff; border-radius: 16px;
    border: 1.5px solid #EEF2FB; box-shadow: 0 2px 8px rgba(26,35,64,0.05);
}
.cr-hist-empty span { font-size: 2.5rem; }
.cr-hist-empty p { font-family: 'Plus Jakarta Sans', sans-serif; font-size: 0.938rem; font-weight: 600; color: #9AAFC8; margin: 0; }
.cr-hist-empty-btn {
    margin-top: 6px; padding: 10px 24px; background: linear-gradient(135deg, #F4B400 0%, #FFB020 100%);
    color: #1A2340; font-family: 'Plus Jakarta Sans', sans-serif; font-size: 0.875rem; font-weight: 700;
    border-radius: 10px; text-decoration: none;
}

/* PAGINATION */
.cr-hist-pagination { display: flex; justify-content: center; align-items: center; gap: 8px; margin-top: 28px; padding-bottom: 32px; }
.cr-hist-page-btn {
    width: 32px; height: 32px; border-radius: 8px; border: 1.5px solid #E8EEF7;
    background: #fff; font-size: 1rem; color: #5A6A8A; cursor: pointer;
    display: flex; align-items: center; justify-content: center; transition: all .15s;
}
.cr-hist-page-btn:hover:not(:disabled) { border-color: #F4B400; color: #1A2340; }
.cr-hist-page-btn:disabled { opacity: 0.35; cursor: default; }
.cr-hist-page-num {
    min-width: 32px; height: 32px; border-radius: 8px; background: #1A2340; color: #fff;
    font-family: 'Plus Jakarta Sans', sans-serif; font-size: 0.813rem; font-weight: 700;
    display: flex; align-items: center; justify-content: center; padding: 0 8px;
}

@media (max-width: 640px) {
    .cr-hist-grid { grid-template-columns: 1fr; }
    .cr-hist-tabs { gap: 6px; }
    .cr-hist-tab { padding: 6px 12px; font-size: 0.75rem; }
    .cr-hist-filters { flex-direction: column; }
}
</style>

<script>
(function() {
    const cards   = Array.from(document.querySelectorAll('.cr-hist-card'));
    const groups  = Array.from(document.querySelectorAll('.cr-hist-month-group'));
    const tabs    = Array.from(document.querySelectorAll('.cr-hist-tab'));
    const fBulan  = document.getElementById('filterBulan');
    const fJenis  = document.getElementById('filterJenis');
    const fCari   = document.getElementById('filterCari');

    const PER_PAGE = 10;
    let page = 1;
    let activeFilter = 'semua';

    function applyFilters() {
        const bulan = fBulan?.value || '';
        const jenis = fJenis?.value || '';
        const cari  = (fCari?.value || '').toLowerCase().trim();

        cards.forEach(card => {
            const ok =
                (activeFilter === 'semua' || card.dataset.status === activeFilter) &&
                (!bulan || (card.dataset.tanggal || '').startsWith(bulan))        &&
                (!jenis || card.dataset.jenis === jenis)                          &&
                (!cari  || (card.dataset.search || '').includes(cari));
            card.classList.toggle('hidden', !ok);
        });

        groups.forEach(g => {
            const hasVisible = Array.from(g.querySelectorAll('.cr-hist-card'))
                .some(c => !c.classList.contains('hidden'));
            g.classList.toggle('all-hidden', !hasVisible);
        });

        page = 1;
        renderPage();
    }

    // Tabs
    tabs.forEach(tab => tab.addEventListener('click', () => {
        tabs.forEach(t => t.classList.remove('is-active'));
        tab.classList.add('is-active');
        activeFilter = tab.dataset.filter;
        applyFilters();
    }));

    // Filters
    let timer;
    [fBulan, fJenis, fCari].forEach(el => {
        if (!el) return;
        el.addEventListener('input', () => { clearTimeout(timer); timer = setTimeout(applyFilters, 250); });
    });

    // Pagination
    function renderPage() {
        const visible = cards.filter(c => !c.classList.contains('hidden'));
        const total   = Math.max(1, Math.ceil(visible.length / PER_PAGE));
        document.getElementById('pageNum').textContent = page;
        document.getElementById('pagePrev').disabled = page <= 1;
        document.getElementById('pageNext').disabled = page >= total;
    }

    document.getElementById('pagePrev')?.addEventListener('click', () => { page--; renderPage(); });
    document.getElementById('pageNext')?.addEventListener('click', () => { page++; renderPage(); });

    renderPage();

    // Batal booking via AJAX
    window.batalBooking = function(id) {
        if (!confirm('Yakin ingin membatalkan booking ini?')) return;
        const token = document.querySelector('meta[name="csrf-token"]')?.content;
        fetch('/booking/' + id + '/batal', {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': token, 'Accept': 'application/json' }
        })
        .then(r => r.json())
        .then(d => { if (d.success) location.reload(); else alert(d.message || 'Gagal membatalkan.'); })
        .catch(() => alert('Terjadi kesalahan.'));
    };
})();
</script>

@endsection