@extends('layouts.admin')

@section('title', 'Data Kegiatan Seluruh User')

@section('content')
<div class="cr-adm-content cr-dak-content">

    {{-- Brand pill --}}
    <div class="cr-dash-brand-pill">
        <span class="cr-dash-brand-pill__icon">🟨</span>
        <span class="cr-dash-brand-pill__text">CampusRoom • v1.0 Admin</span>
    </div>

    {{-- Flash --}}
    @if(session('success'))
    <div class="cr-dak-flash cr-dak-flash--success">✅ {{ session('success') }}</div>
    @endif
    @if(session('error'))
    <div class="cr-dak-flash cr-dak-flash--error">⚠️ {{ session('error') }}</div>
    @endif

    {{-- ======== PAGE HEADER ======== --}}
    <div class="cr-dak-header">
        <p class="cr-dak-eyebrow">PAGE HEADER</p>
        <h1 class="cr-dak-title">
            <span>📋</span> Data Kegiatan Seluruh User
        </h1>
    </div>

    {{-- ======== FILTER BAR KOMPREHENSIF ======== --}}
    <form method="GET" action="/admin/kegiatan" class="cr-dak-filterbox" id="filterForm">
        <p class="cr-dak-filter-label">FILTER BAR KOMPREHENSIF</p>

        <div class="cr-dak-filter-row">
            <div class="cr-dak-search cr-dak-search--wide">
                <span class="cr-dak-search__icon">🔍</span>
                <input type="text" name="search" value="{{ request('search') }}"
                       class="cr-dak-input" placeholder="Cari nama kegiatan/penyelenggara"
                       oninput="debounceSubmit()">
            </div>
            <select name="status" class="cr-dak-select" onchange="this.form.submit()">
                <option value="">Status ▼</option>
                <option value="approved" {{ request('status')==='approved' ? 'selected':'' }}>Approved</option>
                <option value="pending"  {{ request('status')==='pending'  ? 'selected':'' }}>Pending</option>
                <option value="rejected" {{ request('status')==='rejected' ? 'selected':'' }}>Rejected</option>
            </select>
            <select name="jenis" class="cr-dak-select" onchange="this.form.submit()">
                <option value="">Jenis ▼</option>
                <option value="kegiatan" selected>Kegiatan</option>
            </select>
            <select name="status2" class="cr-dak-select">
                <option value="">Status ▼</option>
            </select>
        </div>

        <div class="cr-dak-filter-row">
            <div class="cr-dak-search">
                <span class="cr-dak-search__icon">🔍</span>
                <input type="text" name="kode_search" value="{{ request('kode_search') }}"
                       class="cr-dak-input" placeholder="Workshop/HMI">
            </div>
            <div class="cr-dak-date-wrap">
                <span class="cr-dak-date__icon">📅</span>
                <input type="date" name="dari" value="{{ request('dari') }}"
                       class="cr-dak-input cr-dak-input--date" onchange="this.form.submit()">
            </div>
            <div class="cr-dak-date-wrap">
                <span class="cr-dak-date__icon">📅</span>
                <input type="date" name="hingga" value="{{ request('hingga') }}"
                       class="cr-dak-input cr-dak-input--date" onchange="this.form.submit()">
            </div>
            <select name="room_id" class="cr-dak-select" onchange="this.form.submit()">
                <option value="">Ruangan ▼</option>
                @foreach($rooms as $r)
                <option value="{{ $r->room_id }}" {{ request('room_id')==$r->room_id ? 'selected':'' }}>
                    {{ $r->nama_ruangan }}
                </option>
                @endforeach
            </select>
        </div>

        <div class="cr-dak-filter-row cr-dak-filter-row--last">
            <select name="penyelenggara" class="cr-dak-select" onchange="this.form.submit()">
                <option value="">Penyelenggara ▼</option>
                @foreach($penyelenggaraList as $p)
                <option value="{{ $p }}" {{ request('penyelenggara')===$p ? 'selected':'' }}>{{ $p }}</option>
                @endforeach
            </select>
            <div class="cr-dak-date-wrap cr-dak-date-wrap--range">
                <span class="cr-dak-date__icon">📅</span>
                <span class="cr-dak-range-label">Pilih Rentang Tanggal</span>
                <span class="cr-dak-range-caret">▼</span>
            </div>
        </div>

        @if(request()->hasAny(['search','status','room_id','dari','hingga','penyelenggara','kode_search']))
        <div class="cr-dak-filter-reset">
            <a href="/admin/kegiatan" class="cr-dak-reset-btn">✕ Reset Filter</a>
        </div>
        @endif
    </form>

    {{-- ======== TABEL KEGIATAN ======== --}}
    <div class="cr-dak-table-wrap">
        <p class="cr-dak-table-label">TABEL KEGIATAN</p>

        <form method="POST" id="bulkForm" action="/admin/kegiatan/bulk-delete">
            @csrf
            <table class="cr-dak-table">
                <thead>
                    <tr>
                        <th class="cr-dak-th-check">
                            <input type="checkbox" id="checkAll" class="cr-dak-checkbox"
                                   onchange="toggleCheckAll(this)">
                        </th>
                        <th>Nama Kegiatan</th>
                        <th>Penyelenggara</th>
                        <th>Ruangan</th>
                        <th>Tanggal</th>
                        <th>Peserta</th>
                        <th>Status Booking</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($kegiatan as $i => $k)
                    @php
                        $booking  = $k->bookings->first();
                        $ruangan  = $booking?->rooms->pluck('nama_ruangan')->implode(', ') ?: '-';
                        $gedung   = $booking ? ('Gedung ' . chr(64 + (($booking->rooms->first()?->room_id ?? 1) % 3 + 1))) : '';
                        $tanggal  = $booking ? \Carbon\Carbon::parse($booking->tanggal)->format('d-m-Y') : '-';
                        $peserta  = $k->perkiraan_peserta ?? '-';
                        $status   = $booking?->status;
                        $initial  = strtoupper(substr($k->penyelenggara ?? 'K', 0, 1));
                    @endphp
                    <tr class="cr-dak-row {{ $i === 0 ? 'cr-dak-row--highlight' : '' }}"
                        data-id="{{ $k->kegiatan_id }}">
                        <td class="cr-dak-td-check">
                            <input type="checkbox" name="kegiatan_ids[]" value="{{ $k->kegiatan_id }}"
                                   class="cr-dak-checkbox row-check" onchange="updateBulkBar()">
                        </td>
                        <td class="cr-dak-td-nama">{{ $k->nama_kegiatan }}</td>
                        <td class="cr-dak-td-penyelenggara">
                            <div class="cr-dak-org">
                                <div class="cr-dak-org__avatar">{{ $initial }}</div>
                                <span>{{ $k->penyelenggara ?? '-' }}</span>
                            </div>
                        </td>
                        <td class="cr-dak-td-ruangan">
                            <p class="cr-dak-ruangan-name">{{ $ruangan }}</p>
                            @if($gedung)<p class="cr-dak-ruangan-gd">{{ $gedung }}</p>@endif
                        </td>
                        <td class="cr-dak-td-tanggal">{{ $tanggal }}</td>
                        <td class="cr-dak-td-peserta">{{ $peserta }}</td>
                        <td class="cr-dak-td-status">
                            @if($status === 'approved')
                                <span class="cr-dak-status cr-dak-status--approved">Approved ✓</span>
                            @elseif($status === 'pending')
                                <span class="cr-dak-status cr-dak-status--pending">Pending ⌛</span>
                            @elseif($status === 'rejected')
                                <span class="cr-dak-status cr-dak-status--rejected">Rejected ✕</span>
                            @else
                                <span class="cr-dak-status cr-dak-status--none">—</span>
                            @endif

                            <div class="cr-dak-td-actions">
                                <a href="/admin/kegiatan/edit/{{ $k->kegiatan_id }}"
                                   class="cr-dak-icon-btn cr-dak-icon-btn--edit" title="Edit">✏</a>
                                 <a href="#"
                                    class="cr-dak-icon-btn cr-dak-icon-btn--delete" title="Hapus"
                                    onclick="event.preventDefault(); if(confirm('Hapus kegiatan ini?')) { var form = document.getElementById('form-delete-kegiatan'); form.action = '/admin/kegiatan/delete/{{ $k->kegiatan_id }}'; form.submit(); }">🗑</a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="cr-dak-empty">
                            <span>📋</span>
                            <p>Belum ada data kegiatan.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </form>
    </div>

    {{-- ======== FOOTER: bulk bar + pagination ======== --}}
    <div class="cr-dak-footer">
        <div class="cr-dak-bulk" id="bulkBar" style="display:none">
            <span class="cr-dak-bulk__count" id="bulkCount">*0 dipilih —</span>
            <button type="button" class="cr-dak-bulk-btn cr-dak-bulk-btn--hapus" onclick="bulkDelete()">[Hapus]</button>
        </div>

        @if($kegiatan->hasPages())
        <div class="cr-dak-pagination">
            @if($kegiatan->onFirstPage())
                <span class="cr-dak-page-btn cr-dak-page-btn--disabled">‹</span>
            @else
                <a href="{{ $kegiatan->previousPageUrl() }}" class="cr-dak-page-btn">‹</a>
            @endif

            @foreach($kegiatan->getUrlRange(1, $kegiatan->lastPage()) as $page => $url)
                <a href="{{ $url }}" class="cr-dak-page-btn {{ $page == $kegiatan->currentPage() ? 'cr-dak-page-btn--active' : '' }}">
                    {{ $page }}
                </a>
            @endforeach

            @if($kegiatan->hasMorePages())
                <a href="{{ $kegiatan->nextPageUrl() }}" class="cr-dak-page-btn">›</a>
            @else
                <span class="cr-dak-page-btn cr-dak-page-btn--disabled">›</span>
            @endif
        </div>
        @endif
    </div>

</div>

<style>
/* ============================================================
   DATA KEGIATAN ADMIN
   ============================================================ */
.cr-dak-content { 
    max-width: 1100px; 
    margin: 0 auto;}

.cr-dak-flash { padding:12px 16px; border-radius:10px; margin:56px 0 16px; font-family:'Plus Jakarta Sans',sans-serif; font-size:.875rem; font-weight:600; }
.cr-dak-flash--success { background:#D1FAF0; color:#00C896; border:1px solid #A0E8D8; }
.cr-dak-flash--error   { background:#FFF0F3; color:#FF4D6D; border:1px solid #FFD0D8; }

/* Header */
.cr-dak-header { margin-top:56px; margin-bottom:18px; }
.cr-dak-eyebrow { font-family:'DM Sans',sans-serif; font-size:.688rem; font-weight:700; letter-spacing:1.5px; text-transform:uppercase; color:#9AAFC8; margin:0 0 6px; }
.cr-dak-title { display:flex; align-items:center; gap:10px; font-family:'Space Grotesk',sans-serif; font-size:1.5rem; font-weight:700; color:#1A2340; margin:0; letter-spacing:-.3px; }

/* Filter box */
.cr-dak-filterbox { background:#fff; border:1.5px solid #EEF2FB; border-radius:16px; padding:16px 20px; margin-bottom:16px; box-shadow:0 2px 10px rgba(26,35,64,.06); display:flex; flex-direction:column; gap:10px; }
.cr-dak-filter-label { font-family:'DM Sans',sans-serif; font-size:.688rem; font-weight:700; letter-spacing:1.5px; color:#9AAFC8; text-transform:uppercase; margin:0 0 4px; }
.cr-dak-filter-row { display:flex; gap:10px; align-items:center; flex-wrap:wrap; }
.cr-dak-filter-row--last { justify-content:space-between; }

.cr-dak-search { position:relative; display:flex; align-items:center; flex:1; min-width:140px; }
.cr-dak-search--wide { flex:2; }
.cr-dak-search__icon { position:absolute; left:11px; font-size:.75rem; pointer-events:none; }
.cr-dak-input {
    width:100%; padding:9px 12px 9px 32px; background:#F8FAFF; border:1.5px solid #E8EEF7;
    border-radius:10px; font-family:'DM Sans',sans-serif; font-size:.813rem; color:#1A2340;
    outline:none; transition:border-color .2s, box-shadow .2s;
}
.cr-dak-input:focus { border-color:#F4B400; box-shadow:0 0 0 3px rgba(244,180,0,.10); }
.cr-dak-input::placeholder { color:#9AAFC8; }
.cr-dak-input--date { padding-left:36px; }
.cr-dak-date-wrap { position:relative; display:flex; align-items:center; }
.cr-dak-date__icon { position:absolute; left:10px; font-size:.75rem; pointer-events:none; z-index:1; }
.cr-dak-date-wrap--range {
    flex:1; min-width:180px; padding:9px 14px 9px 32px; background:#F8FAFF;
    border:1.5px solid #E8EEF7; border-radius:10px; cursor:pointer;
}
.cr-dak-range-label { font-family:'DM Sans',sans-serif; font-size:.813rem; color:#9AAFC8; }
.cr-dak-range-caret { margin-left:auto; font-size:.625rem; color:#9AAFC8; }

.cr-dak-select {
    padding:9px 28px 9px 12px; appearance:none; -webkit-appearance:none;
    background:#F8FAFF url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='10' height='6'%3E%3Cpath d='M0 0l5 6 5-6z' fill='%239AAFC8'/%3E%3C/svg%3E") no-repeat right 10px center;
    border:1.5px solid #E8EEF7; border-radius:10px;
    font-family:'Plus Jakarta Sans',sans-serif; font-size:.813rem; font-weight:600; color:#1A2340;
    cursor:pointer; outline:none; white-space:nowrap; transition:border-color .15s;
}
.cr-dak-select:focus { border-color:#F4B400; }

.cr-dak-export-btn {
    padding:9px 18px; background:linear-gradient(135deg,#00C896,#00A87E); color:#fff;
    border:none; border-radius:10px; font-family:'Plus Jakarta Sans',sans-serif;
    font-size:.813rem; font-weight:800; cursor:pointer; white-space:nowrap;
    box-shadow:0 3px 10px rgba(0,200,150,.25); transition:transform .15s;
}
.cr-dak-export-btn:hover { transform:scale(1.02); }

.cr-dak-filter-reset { display:flex; justify-content:flex-end; }
.cr-dak-reset-btn { font-family:'Plus Jakarta Sans',sans-serif; font-size:.75rem; font-weight:600; color:#FF4D6D; text-decoration:none; padding:4px 12px; border-radius:6px; background:#FFF0F3; border:1px solid #FFD0D8; }
.cr-dak-reset-btn:hover { background:#FFE4E9; }

/* Table */
.cr-dak-table-wrap { background:#fff; border:1.5px solid #EEF2FB; border-radius:16px; padding:16px 20px; box-shadow:0 2px 10px rgba(26,35,64,.06); overflow-x:auto; }
.cr-dak-table-label { font-family:'DM Sans',sans-serif; font-size:.688rem; font-weight:700; letter-spacing:1.5px; color:#9AAFC8; text-transform:uppercase; margin:0 0 14px; }
.cr-dak-table { width:100%; border-collapse:collapse; min-width:780px; }
.cr-dak-table thead th { font-family:'Plus Jakarta Sans',sans-serif; font-size:.75rem; font-weight:800; color:#1A2340; padding:10px 10px; text-align:left; border-bottom:2px solid #EEF2FB; white-space:nowrap; }
.cr-dak-th-check { width:36px; }

.cr-dak-row td { padding:11px 10px; vertical-align:middle; border-bottom:1px solid #F0F4FF; }
.cr-dak-row:last-child td { border-bottom:none; }
.cr-dak-row:hover td { background:#FFFBF0; }
.cr-dak-row--highlight { background:#FFFCF0; }

.cr-dak-checkbox { width:15px; height:15px; accent-color:#F4B400; cursor:pointer; }
.cr-dak-td-check { width:36px; text-align:center; }

.cr-dak-td-nama { font-family:'Plus Jakarta Sans',sans-serif; font-size:.813rem; font-weight:700; color:#1A2340; max-width:160px; }

.cr-dak-org { display:flex; align-items:center; gap:8px; }
.cr-dak-org__avatar {
    width:24px; height:24px; border-radius:50%; flex-shrink:0;
    background:linear-gradient(135deg,#4FC3F7,#0277BD);
    display:flex; align-items:center; justify-content:center;
    font-family:'Plus Jakarta Sans',sans-serif; font-size:.625rem; font-weight:800; color:#fff;
}
.cr-dak-td-penyelenggara span { font-family:'DM Sans',sans-serif; font-size:.75rem; color:#5A6A8A; }

.cr-dak-ruangan-name { font-family:'Plus Jakarta Sans',sans-serif; font-size:.75rem; font-weight:600; color:#1A2340; margin:0; line-height:1.3; }
.cr-dak-ruangan-gd { font-family:'DM Sans',sans-serif; font-size:.625rem; color:#9AAFC8; margin:0; }

.cr-dak-td-tanggal { font-family:'DM Sans',sans-serif; font-size:.75rem; color:#5A6A8A; white-space:nowrap; }
.cr-dak-td-peserta { font-family:'DM Sans',sans-serif; font-size:.813rem; color:#1A2340; font-weight:600; }

.cr-dak-td-status { display:flex; flex-direction:column; align-items:flex-end; gap:6px; }
.cr-dak-status { font-family:'Plus Jakarta Sans',sans-serif; font-size:.688rem; font-weight:700; padding:4px 10px; border-radius:999px; white-space:nowrap; }
.cr-dak-status--approved { background:#D1FAF0; color:#00C896; }
.cr-dak-status--pending  { background:#FFF3CD; color:#E6820A; }
.cr-dak-status--rejected { background:#FFF0F3; color:#FF4D6D; }
.cr-dak-status--none     { background:#F0F4FF; color:#9AAFC8; }

.cr-dak-td-actions { display:flex; gap:6px; }
.cr-dak-icon-btn { width:26px; height:26px; border-radius:7px; display:flex; align-items:center; justify-content:center; text-decoration:none; font-size:.75rem; transition:transform .12s; }
.cr-dak-icon-btn:hover { transform:scale(1.1); }
.cr-dak-icon-btn--edit   { background:#4FC3F7; color:#fff; }
.cr-dak-icon-btn--delete { background:#FF4D6D; color:#fff; }

.cr-dak-empty { text-align:center; padding:48px; color:#9AAFC8; }
.cr-dak-empty span { font-size:2.5rem; display:block; margin-bottom:8px; }
.cr-dak-empty p { font-family:'Plus Jakarta Sans',sans-serif; font-size:.938rem; font-weight:600; margin:0; }

/* Footer */
.cr-dak-footer { display:flex; align-items:center; justify-content:space-between; margin-top:16px; flex-wrap:wrap; gap:10px; }
.cr-dak-bulk { display:flex; align-items:center; gap:8px; padding:10px 16px; background:linear-gradient(135deg,#F4B400,#FFB020); border-radius:12px; box-shadow:0 3px 10px rgba(244,180,0,.25); }
.cr-dak-bulk__count { font-family:'Plus Jakarta Sans',sans-serif; font-size:.813rem; font-weight:700; color:#1A2340; white-space:nowrap; }
.cr-dak-bulk-btn { padding:6px 14px; border-radius:8px; border:none; cursor:pointer; font-family:'Plus Jakarta Sans',sans-serif; font-size:.75rem; font-weight:800; transition:transform .12s; }
.cr-dak-bulk-btn:hover { transform:scale(1.04); }
.cr-dak-bulk-btn--export { background:#1A2340; color:#fff; }
.cr-dak-bulk-btn--hapus  { background:#FF4D6D; color:#fff; }

.cr-dak-pagination { display:flex; align-items:center; gap:6px; flex-wrap:wrap; margin-left:auto; }
.cr-dak-page-btn { display:inline-flex; align-items:center; justify-content:center; min-width:34px; height:34px; padding:0 8px; background:#fff; border:1.5px solid #E8EEF7; border-radius:8px; font-family:'Plus Jakarta Sans',sans-serif; font-size:.813rem; font-weight:700; color:#5A6A8A; text-decoration:none; cursor:pointer; transition:all .15s; }
.cr-dak-page-btn:hover:not(.cr-dak-page-btn--disabled):not(.cr-dak-page-btn--active) { border-color:#F4B400; color:#1A2340; }
.cr-dak-page-btn--active   { background:#F4B400; border-color:#F4B400; color:#1A2340; }
.cr-dak-page-btn--disabled { opacity:.40; cursor:not-allowed; pointer-events:none; }

@media (max-width:800px) {
    .cr-dak-filter-row { flex-direction:column; }
    .cr-dak-search, .cr-dak-search--wide { min-width:100%; }
    .cr-dak-footer { flex-direction:column; align-items:flex-start; }
    .cr-dak-pagination { margin-left:0; }
}
</style>

<script>
(function(){
    let debounceTimer;
    window.debounceSubmit = function() {
        clearTimeout(debounceTimer);
        debounceTimer = setTimeout(() => document.getElementById('filterForm').submit(), 500);
    };

    window.toggleCheckAll = function(master) {
        document.querySelectorAll('.row-check').forEach(cb => cb.checked = master.checked);
        updateBulkBar();
    };

    window.updateBulkBar = function() {
        const checked = document.querySelectorAll('.row-check:checked');
        const bar = document.getElementById('bulkBar');
        const count = document.getElementById('bulkCount');
        if (checked.length > 0) {
            bar.style.display = 'flex';
            count.textContent = '*' + checked.length + ' dipilih —';
        } else {
            bar.style.display = 'none';
        }
    };

    window.bulkDelete = function() {
        const ids = [...document.querySelectorAll('.row-check:checked')].map(cb => cb.value);
        if (ids.length === 0) return;
        if (!confirm('Hapus ' + ids.length + ' kegiatan yang dipilih?')) return;
        document.getElementById('bulkForm').submit();
    };
})();
</script>
<form id="form-delete-kegiatan" method="POST" style="display: none;">
    @csrf
    @method('DELETE')
</form>
@endsection