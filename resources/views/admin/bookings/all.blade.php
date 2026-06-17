@extends('layouts.admin')

@section('title', 'Data Booking Seluruh User')

@section('content')
<div class="cr-adm-content cr-dba-content">

    {{-- Brand pill --}}
    <div class="cr-dash-brand-pill">
        <span class="cr-dash-brand-pill__icon">🟨</span>
        <span class="cr-dash-brand-pill__text">CampusRoom • v1.0 Admin</span>
    </div>

    {{-- Flash --}}
    @if(session('success'))
    <div class="cr-dba-flash cr-dba-flash--success">✅ {{ session('success') }}</div>
    @endif
    @if(session('error'))
    <div class="cr-dba-flash cr-dba-flash--error">⚠️ {{ session('error') }}</div>
    @endif

    {{-- ======== PAGE HEADER ======== --}}
    <div class="cr-dba-header">
        <div>
            <p class="cr-dba-eyebrow">PAGE HEADER</p>
            <h1 class="cr-dba-title">
                <span class="cr-dba-title-icon">🗂️</span>
                Data Booking Seluruh User
            </h1>
            <p class="cr-dba-subtitle">
                Total: <strong>{{ $totalBooking }} booking</strong>
                <a href="#" class="cr-dba-export-link" onclick="exportExcel(event)">
                    [Export Excel 📊]
                </a>
            </p>
        </div>
    </div>

    {{-- ======== FILTER & SEARCH ======== --}}
    <form method="GET" action="/admin/all-bookings" class="cr-dba-filterbox" id="filterForm">
        <div class="cr-dba-filter-row cr-dba-filter-row--1">
            {{-- Search kode/nama --}}
            <div class="cr-dba-search cr-dba-search--wide">
                <span class="cr-dba-search__icon">🔍</span>
                <input type="text" name="search" value="{{ request('search') }}"
                       class="cr-dba-input" placeholder="Cari nama/kode booking"
                       oninput="debounceSubmit()">
            </div>

            {{-- Status --}}
            <select name="status" class="cr-dba-select" onchange="this.form.submit()">
                <option value="">Status ▼</option>
                <option value="approved" {{ request('status')==='approved' ? 'selected' : '' }}>Approved</option>
                <option value="pending"  {{ request('status')==='pending'  ? 'selected' : '' }}>Pending</option>
                <option value="rejected" {{ request('status')==='rejected' ? 'selected' : '' }}>Rejected</option>
            </select>

            {{-- Jenis --}}
            <select name="jenis" class="cr-dba-select" onchange="this.form.submit()">
                <option value="">Jenis ▼</option>
                <option value="perkuliahan" {{ request('jenis')==='perkuliahan' ? 'selected' : '' }}>Perkuliahan</option>
                <option value="kegiatan"    {{ request('jenis')==='kegiatan'    ? 'selected' : '' }}>Kegiatan</option>
            </select>
        </div>

        <div class="cr-dba-filter-row cr-dba-filter-row--2">
            {{-- Search nama/BK --}}
            <div class="cr-dba-search">
                <span class="cr-dba-search__icon">🔍</span>
                <input type="text" name="user_search" value="{{ request('user_search') }}"
                       class="cr-dba-input" placeholder="Ahmad/BK..."
                       oninput="debounceSubmit()">
            </div>

            {{-- Dari --}}
            <div class="cr-dba-date-wrap">
                <span class="cr-dba-date__icon">📅</span>
                <input type="date" name="dari" value="{{ request('dari') }}"
                       class="cr-dba-input cr-dba-input--date" placeholder="Dari"
                       onchange="this.form.submit()">
            </div>

            {{-- Hingga --}}
            <div class="cr-dba-date-wrap">
                <span class="cr-dba-date__icon">📅</span>
                <input type="date" name="hingga" value="{{ request('hingga') }}"
                       class="cr-dba-input cr-dba-input--date" placeholder="Hingga"
                       onchange="this.form.submit()">
            </div>

            {{-- Ruangan --}}
            <select name="room_id" class="cr-dba-select" onchange="this.form.submit()">
                <option value="">Ruangan ▼</option>
                @foreach($rooms as $r)
                <option value="{{ $r->room_id }}" {{ request('room_id')==$r->room_id ? 'selected' : '' }}>
                    {{ $r->nama_ruangan }}
                </option>
                @endforeach
            </select>
        </div>

        {{-- Reset filter --}}
        @if(request()->hasAny(['search','status','jenis','dari','hingga','room_id','user_search']))
        <div class="cr-dba-filter-reset">
            <a href="/admin/all-bookings" class="cr-dba-reset-btn">✕ Reset Filter</a>
        </div>
        @endif
    </form>

    {{-- ======== TABEL BOOKING ======== --}}
    <div class="cr-dba-table-wrap">
        <p class="cr-dba-table-label">TABEL BOOKING</p>

        <form method="POST" id="bulkForm" action="/admin/bookings/bulk-delete">
            @csrf
            <table class="cr-dba-table">
                <thead>
                    <tr>
                        <th class="cr-dba-th-check">
                            <input type="checkbox" id="checkAll" class="cr-dba-checkbox"
                                   onchange="toggleCheckAll(this)">
                        </th>
                        <th>Kode Booking</th>
                        <th>Nama Peminjam</th>
                        <th>Jenis</th>
                        <th>Ruangan</th>
                        <th>Tanggal &amp; Waktu</th>
                        <th>Status</th>
                        <th>Aksi</th>
                        <th><span class="cr-dba-th-gear">⚙</span></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($bookings as $bk)
                    @php
                        $kode    = '#BK-' . date('Y', strtotime($bk->tanggal)) . '-' . str_pad($bk->booking_id, 3, '0', STR_PAD_LEFT);
                        $namaUser= $bk->user->nama ?? 'Unknown';
                        $ruangan = $bk->rooms->pluck('nama_ruangan')->implode(' · ') ?: '-';
                        $isFirst = $loop->first;
                    @endphp
                    <tr class="cr-dba-row {{ $isFirst ? 'cr-dba-row--highlight' : '' }}"
                        data-id="{{ $bk->booking_id }}">

                        {{-- Checkbox --}}
                        <td class="cr-dba-td-check">
                            <input type="checkbox" name="booking_ids[]"
                                   value="{{ $bk->booking_id }}"
                                   class="cr-dba-checkbox row-check"
                                   onchange="updateBulkBar()">
                        </td>

                        {{-- Kode --}}
                        <td class="cr-dba-td-kode">{{ $kode }}</td>

                        {{-- Nama peminjam --}}
                        <td class="cr-dba-td-user">
                            <div class="cr-dba-user">
                                <div class="cr-dba-user__avatar">
                                    {{ strtoupper(substr($namaUser, 0, 1)) }}
                                </div>
                                <div class="cr-dba-user__info">
                                    <p class="cr-dba-user__name">{{ Str::limit($namaUser, 14) }}</p>
                                    <p class="cr-dba-user__role">Panitia</p>
                                </div>
                            </div>
                        </td>

                        {{-- Jenis --}}
                        <td class="cr-dba-td-jenis">
                            <div class="cr-dba-jenis-wrap">
                                <span class="cr-dba-badge cr-dba-badge--kuliah">Kuliah</span>
                                @if($bk->jenis === 'kegiatan')
                                <span class="cr-dba-badge cr-dba-badge--kegiatan">Kegiatan</span>
                                @endif
                            </div>
                        </td>

                        {{-- Ruangan --}}
                        <td class="cr-dba-td-room">
                            <p class="cr-dba-room-name">{{ $ruangan }}</p>
                            @if($bk->rooms->count() > 1)
                            <p class="cr-dba-room-sub">+{{ $bk->rooms->count()-1 }} ruangan</p>
                            @endif
                        </td>

                        {{-- Tanggal & Waktu --}}
                        <td class="cr-dba-td-date">
                            <p class="cr-dba-date-val">
                                {{ \Carbon\Carbon::parse($bk->tanggal)->format('d-m-Y') }}
                            </p>
                            <p class="cr-dba-date-time">
                                {{ substr($bk->jam_mulai,0,5) }} - {{ substr($bk->jam_selesai,0,5) }}
                            </p>
                        </td>

                        {{-- Status --}}
                        <td class="cr-dba-td-status">
                            @if($bk->status === 'approved')
                                <span class="cr-dba-status cr-dba-status--approved">Approved ✓</span>
                            @elseif($bk->status === 'pending')
                                <span class="cr-dba-status cr-dba-status--pending">Pending ⌛</span>
                            @else
                                <span class="cr-dba-status cr-dba-status--rejected">Rejected ✕</span>
                            @endif
                        </td>

                        {{-- Aksi --}}
                        <td class="cr-dba-td-aksi">
                            <a href="/admin/booking/detail/{{ $bk->booking_id }}"
                               class="cr-dba-aksi-btn cr-dba-aksi-btn--lihat">
                                👁 {{ $isFirst ? 'Lihat Detail' : 'Lihat' }}
                            </a>
                            <a href="/admin/booking/export/{{ $bk->booking_id }}"
                               class="cr-dba-aksi-btn cr-dba-aksi-btn--export">
                                ⬇ Export
                            </a>
                        </td>

                        {{-- Checkbox kanan --}}
                        <td class="cr-dba-td-check-r">
                            <input type="checkbox" name="booking_ids_r[]"
                                   value="{{ $bk->booking_id }}"
                                   class="cr-dba-checkbox row-check-r"
                                   onchange="syncCheck(this)">
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="cr-dba-empty">
                            <span>📋</span>
                            <p>Belum ada data booking.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </form>
    </div>

    {{-- ======== BULK ACTION BAR + PAGINATION ======== --}}
    <div class="cr-dba-footer">
        {{-- Bulk bar --}}
        <div class="cr-dba-bulk" id="bulkBar" style="display:none">
            <span class="cr-dba-bulk__count" id="bulkCount">*0 dipilih —</span>
            <button type="button" class="cr-dba-bulk-btn cr-dba-bulk-btn--export"
                    onclick="bulkExport()">[Export]</button>
            <button type="button" class="cr-dba-bulk-btn cr-dba-bulk-btn--hapus"
                    onclick="bulkDelete()">[Hapus]</button>
        </div>

        {{-- Pagination --}}
        @if($bookings->hasPages())
        <div class="cr-dba-pagination">
            @if($bookings->onFirstPage())
                <span class="cr-dba-page-btn cr-dba-page-btn--disabled">‹</span>
            @else
                <a href="{{ $bookings->previousPageUrl() }}" class="cr-dba-page-btn">‹</a>
            @endif

            @foreach($bookings->getUrlRange(1, $bookings->lastPage()) as $page => $url)
                <a href="{{ $url }}"
                   class="cr-dba-page-btn {{ $page == $bookings->currentPage() ? 'cr-dba-page-btn--active' : '' }}">
                    {{ $page }}
                </a>
            @endforeach

            @if($bookings->hasMorePages())
                <a href="{{ $bookings->nextPageUrl() }}" class="cr-dba-page-btn">›</a>
            @else
                <span class="cr-dba-page-btn cr-dba-page-btn--disabled">›</span>
            @endif
        </div>
        @endif
    </div>

</div>

<style>
/* ============================================================
   DATA BOOKING ADMIN
   ============================================================ */
.cr-dba-content { 
    max-width: 1100px;
    margin: 0 auto; 
    padding: 0 10px }

.cr-dba-flash { padding:12px 16px; border-radius:10px; margin:56px 0 16px; font-family:'Plus Jakarta Sans',sans-serif; font-size:.875rem; font-weight:600; }
.cr-dba-flash--success { background:#D1FAF0; color:#00C896; border:1px solid #A0E8D8; }
.cr-dba-flash--error   { background:#FFF0F3; color:#FF4D6D; border:1px solid #FFD0D8; }

/* Header */
.cr-dba-header { margin-top:56px; margin-bottom:18px; }
.cr-dba-eyebrow { font-family:'DM Sans',sans-serif; font-size:.688rem; font-weight:700; letter-spacing:1.5px; text-transform:uppercase; color:#9AAFC8; margin:0 0 6px; }
.cr-dba-title {
    display:flex; align-items:center; gap:10px;
    font-family:'Space Grotesk',sans-serif; font-size:1.5rem; font-weight:700; color:#1A2340; margin:0 0 6px; letter-spacing:-.3px;
}
.cr-dba-title-icon { font-size:1.25rem; }
.cr-dba-subtitle { font-family:'DM Sans',sans-serif; font-size:.813rem; color:#5A6A8A; margin:0; }
.cr-dba-export-link { font-family:'Plus Jakarta Sans',sans-serif; font-size:.75rem; font-weight:600; color:#4FC3F7; text-decoration:none; margin-left:6px; }
.cr-dba-export-link:hover { color:#0277BD; }

/* Filter box */
.cr-dba-filterbox {
    background:#fff; border:1.5px solid #EEF2FB; border-radius:16px;
    padding:16px 20px; margin-bottom:16px; box-shadow:0 2px 10px rgba(26,35,64,.06);
    display:flex; flex-direction:column; gap:10px;
}
.cr-dba-filter-row { display:flex; gap:10px; align-items:center; flex-wrap:wrap; }

.cr-dba-search { position:relative; display:flex; align-items:center; flex:1; min-width:140px; }
.cr-dba-search--wide { flex:2; }
.cr-dba-search__icon { position:absolute; left:11px; font-size:.75rem; pointer-events:none; }
.cr-dba-input {
    width:100%; padding:9px 12px 9px 32px;
    background:#F8FAFF; border:1.5px solid #E8EEF7; border-radius:10px;
    font-family:'DM Sans',sans-serif; font-size:.813rem; color:#1A2340; outline:none;
    transition:border-color .2s, box-shadow .2s;
}
.cr-dba-input:focus { border-color:#F4B400; box-shadow:0 0 0 3px rgba(244,180,0,.10); }
.cr-dba-input::placeholder { color:#9AAFC8; }
.cr-dba-input--date { padding-left:36px; }

.cr-dba-date-wrap { position:relative; display:flex; align-items:center; }
.cr-dba-date__icon { position:absolute; left:10px; font-size:.75rem; pointer-events:none; z-index:1; }

.cr-dba-select {
    padding:9px 28px 9px 12px; appearance:none; -webkit-appearance:none;
    background:#F8FAFF url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='10' height='6'%3E%3Cpath d='M0 0l5 6 5-6z' fill='%239AAFC8'/%3E%3C/svg%3E") no-repeat right 10px center;
    border:1.5px solid #E8EEF7; border-radius:10px;
    font-family:'Plus Jakarta Sans',sans-serif; font-size:.813rem; font-weight:600; color:#1A2340;
    cursor:pointer; outline:none; white-space:nowrap;
    transition:border-color .15s;
}
.cr-dba-select:focus { border-color:#F4B400; }

.cr-dba-filter-reset { display:flex; justify-content:flex-end; }
.cr-dba-reset-btn {
    font-family:'Plus Jakarta Sans',sans-serif; font-size:.75rem; font-weight:600;
    color:#FF4D6D; text-decoration:none;
    padding:4px 12px; border-radius:6px; background:#FFF0F3; border:1px solid #FFD0D8;
    transition:background .15s;
}
.cr-dba-reset-btn:hover { background:#FFE4E9; }

/* Table */
.cr-dba-table-wrap {
    background:#fff; border:1.5px solid #EEF2FB; border-radius:16px;
    padding:16px 20px; box-shadow:0 2px 10px rgba(26,35,64,.06); overflow-x:auto;
}
.cr-dba-table-label { font-family:'DM Sans',sans-serif; font-size:.688rem; font-weight:700; letter-spacing:1.5px; color:#9AAFC8; text-transform:uppercase; margin:0 0 14px; }
.cr-dba-table { width:100%; border-collapse:collapse; min-width:800px; }
.cr-dba-table thead th {
    font-family:'Plus Jakarta Sans',sans-serif; font-size:.75rem; font-weight:800;
    color:#1A2340; padding:10px 10px; text-align:left; border-bottom:2px solid #EEF2FB; white-space:nowrap;
}
.cr-dba-th-check { width:36px; }
.cr-dba-th-gear  { font-size:.875rem; }

.cr-dba-row td { padding:11px 10px; vertical-align:middle; border-bottom:1px solid #F0F4FF; }
.cr-dba-row:last-child td { border-bottom:none; }
.cr-dba-row:hover td { background:#FFFBF0; }
.cr-dba-row--highlight { background:#FFFCF0; }

/* Checkbox */
.cr-dba-checkbox { width:15px; height:15px; accent-color:#F4B400; cursor:pointer; }
.cr-dba-td-check, .cr-dba-td-check-r { width:36px; text-align:center; }

/* Kode */
.cr-dba-td-kode { font-family:'DM Sans',monospace; font-size:.75rem; color:#5A6A8A; white-space:nowrap; }

/* User */
.cr-dba-user { display:flex; align-items:center; gap:8px; }
.cr-dba-user__avatar {
    width:28px; height:28px; border-radius:50%; flex-shrink:0;
    background:linear-gradient(135deg,#F4B400,#FFB020);
    display:flex; align-items:center; justify-content:center;
    font-family:'Plus Jakarta Sans',sans-serif; font-size:.688rem; font-weight:800; color:#1A2340;
}
.cr-dba-user__name { font-family:'Plus Jakarta Sans',sans-serif; font-size:.75rem; font-weight:700; color:#1A2340; margin:0 0 1px; }
.cr-dba-user__role { font-family:'DM Sans',sans-serif; font-size:.625rem; color:#9AAFC8; margin:0; }

/* Jenis badge */
.cr-dba-jenis-wrap { display:flex; flex-direction:column; gap:3px; }
.cr-dba-badge { font-family:'Plus Jakarta Sans',sans-serif; font-size:.6rem; font-weight:700; padding:2px 8px; border-radius:999px; white-space:nowrap; width:fit-content; }
.cr-dba-badge--kuliah   { background:#EFF8FF; color:#4FC3F7; }
.cr-dba-badge--kegiatan { background:#FFF3CD; color:#E6820A; }

/* Ruangan */
.cr-dba-room-name { font-family:'Plus Jakarta Sans',sans-serif; font-size:.75rem; font-weight:600; color:#1A2340; margin:0 0 1px; line-height:1.3; }
.cr-dba-room-sub  { font-family:'DM Sans',sans-serif; font-size:.625rem; color:#9AAFC8; margin:0; }

/* Date */
.cr-dba-date-val  { font-family:'DM Sans',sans-serif; font-size:.75rem; color:#5A6A8A; margin:0 0 1px; white-space:nowrap; }
.cr-dba-date-time { font-family:'DM Sans',sans-serif; font-size:.625rem; color:#9AAFC8; margin:0; }

/* Status */
.cr-dba-status { font-family:'Plus Jakarta Sans',sans-serif; font-size:.688rem; font-weight:700; padding:4px 10px; border-radius:999px; white-space:nowrap; }
.cr-dba-status--approved { background:#D1FAF0; color:#00C896; }
.cr-dba-status--pending  { background:#FFF3CD; color:#E6820A; }
.cr-dba-status--rejected { background:#FFF0F3; color:#FF4D6D; }

/* Aksi */
.cr-dba-td-aksi { display:flex; flex-direction:column; gap:4px; }
.cr-dba-aksi-btn {
    display:inline-flex; align-items:center; gap:4px;
    padding:4px 10px; border-radius:7px;
    font-family:'Plus Jakarta Sans',sans-serif; font-size:.625rem; font-weight:700;
    text-decoration:none; white-space:nowrap; transition:transform .12s;
}
.cr-dba-aksi-btn:hover { transform:scale(1.04); }
.cr-dba-aksi-btn--lihat  { background:#EFF8FF; color:#4FC3F7; border:1px solid #C5E8F8; }
.cr-dba-aksi-btn--export { background:#F8FAFF; color:#5A6A8A; border:1px solid #E8EEF7; }

/* Empty */
.cr-dba-empty { text-align:center; padding:48px; color:#9AAFC8; }
.cr-dba-empty span { font-size:2.5rem; display:block; margin-bottom:8px; }
.cr-dba-empty p { font-family:'Plus Jakarta Sans',sans-serif; font-size:.938rem; font-weight:600; margin:0; }

/* ── Footer: bulk bar + pagination ── */
.cr-dba-footer { display:flex; align-items:center; justify-content:space-between; margin-top:16px; flex-wrap:wrap; gap:10px; }

.cr-dba-bulk {
    display:flex; align-items:center; gap:8px;
    padding:10px 16px; background:linear-gradient(135deg,#F4B400,#FFB020);
    border-radius:12px; box-shadow:0 3px 10px rgba(244,180,0,.25);
}
.cr-dba-bulk__count { font-family:'Plus Jakarta Sans',sans-serif; font-size:.813rem; font-weight:700; color:#1A2340; white-space:nowrap; }
.cr-dba-bulk-btn {
    padding:6px 14px; border-radius:8px; border:none; cursor:pointer;
    font-family:'Plus Jakarta Sans',sans-serif; font-size:.75rem; font-weight:800;
    transition:transform .12s;
}
.cr-dba-bulk-btn:hover { transform:scale(1.04); }
.cr-dba-bulk-btn--export { background:#1A2340; color:#fff; }
.cr-dba-bulk-btn--hapus  { background:#FF4D6D; color:#fff; }

.cr-dba-pagination { display:flex; align-items:center; gap:6px; flex-wrap:wrap; margin-left:auto; }
.cr-dba-page-btn {
    display:inline-flex; align-items:center; justify-content:center;
    min-width:34px; height:34px; padding:0 8px;
    background:#fff; border:1.5px solid #E8EEF7; border-radius:8px;
    font-family:'Plus Jakarta Sans',sans-serif; font-size:.813rem; font-weight:700;
    color:#5A6A8A; text-decoration:none; cursor:pointer; transition:all .15s;
}
.cr-dba-page-btn:hover:not(.cr-dba-page-btn--disabled):not(.cr-dba-page-btn--active) { border-color:#F4B400; color:#1A2340; }
.cr-dba-page-btn--active   { background:#F4B400; border-color:#F4B400; color:#1A2340; }
.cr-dba-page-btn--disabled { opacity:.40; cursor:not-allowed; pointer-events:none; }

/* Responsive */
@media (max-width:800px) {
    .cr-dba-filter-row { flex-direction:column; }
    .cr-dba-search, .cr-dba-search--wide { min-width:100%; }
    .cr-dba-footer { flex-direction:column; align-items:flex-start; }
    .cr-dba-pagination { margin-left:0; }
}
</style>

<script>
(function(){
    // Debounce untuk input search
    let debounceTimer;
    window.debounceSubmit = function() {
        clearTimeout(debounceTimer);
        debounceTimer = setTimeout(() => {
            document.getElementById('filterForm').submit();
        }, 500);
    };

    // Check all
    window.toggleCheckAll = function(master) {
        document.querySelectorAll('.row-check').forEach(cb => {
            cb.checked = master.checked;
        });
        updateBulkBar();
    };

    // Sync kanan-kiri checkbox
    window.syncCheck = function(cb) {
        updateBulkBar();
    };

    // Update bulk bar
    window.updateBulkBar = function() {
        const checked = document.querySelectorAll('.row-check:checked');
        const bulkBar = document.getElementById('bulkBar');
        const bulkCount = document.getElementById('bulkCount');
        if (checked.length > 0) {
            bulkBar.style.display = 'flex';
            bulkCount.textContent = '*' + checked.length + ' dipilih —';
        } else {
            bulkBar.style.display = 'none';
        }
    };

    // Bulk export
    window.bulkExport = function() {
        const ids = [...document.querySelectorAll('.row-check:checked')].map(cb => cb.value);
        if (ids.length === 0) return;
        alert('Export ' + ids.length + ' booking: ' + ids.join(', '));
    };

    // Bulk delete
    window.bulkDelete = function() {
        const ids = [...document.querySelectorAll('.row-check:checked')].map(cb => cb.value);
        if (ids.length === 0) return;
        if (!confirm('Hapus ' + ids.length + ' booking yang dipilih? Tindakan ini tidak dapat dibatalkan.')) return;
        document.getElementById('bulkForm').submit();
    };

    // Export excel semua
    window.exportExcel = function(e) {
        e.preventDefault();
        window.location.href = '/admin/all-bookings/export';
    };
})();
</script>
@endsection