@extends('layouts.admin')

@section('title', 'Manajemen Ruangan')

@section('content')
<div class="cr-adm-content cr-crud-content">

    {{-- Brand pill --}}
    <div class="cr-dash-brand-pill">
        <span class="cr-dash-brand-pill__icon">🟨</span>
        <span class="cr-dash-brand-pill__text">CampusRoom • v1.0 Admin</span>
    </div>

    {{-- Flash messages --}}
    @if(session('success'))
    <div class="cr-crud-flash cr-crud-flash--success">✅ {{ session('success') }}</div>
    @endif
    @if(session('error'))
    <div class="cr-crud-flash cr-crud-flash--error">⚠️ {{ session('error') }}</div>
    @endif

    {{-- ======== PAGE HEADER ======== --}}
    <div class="cr-crud-header">
        <div class="cr-crud-header__left">
            <p class="cr-crud-header__eyebrow">PAGE HEADER</p>
            <h1 class="cr-crud-header__title">
                <span class="cr-crud-header__icon">📋</span>
                Manajemen Ruangan
            </h1>
        </div>
        <button class="cr-crud-add-btn" id="btnTambahRuangan">
            + Tambah Ruangan
        </button>
    </div>

    {{-- ======== FILTER & SEARCH ======== --}}
    <div class="cr-crud-filterbar">
        <p class="cr-crud-filterbar__label">FILTER &amp; SEARCH</p>
        <div class="cr-crud-filterbar__row">
            <div class="cr-crud-search">
                <span class="cr-crud-search__icon">🔍</span>
                <input type="text" id="searchInput"
                       class="cr-crud-search__input"
                       placeholder="Cari nama ruangan">
            </div>
            <div class="cr-crud-status-filter" id="statusFilterWrap">
                <button class="cr-crud-status-btn" id="statusFilterBtn">
                    <span id="statusFilterLabel">Status ▼</span>
                </button>
                <div class="cr-crud-status-dropdown" id="statusDropdown" style="display:none">
                    <button class="cr-crud-status-opt" data-value="">Semua</button>
                    <button class="cr-crud-status-opt" data-value="tersedia">Aktif</button>
                    <button class="cr-crud-status-opt" data-value="tidak tersedia">Tidak Aktif</button>
                    <button class="cr-crud-status-opt" data-value="perawatan">Perawatan</button>
                </div>
            </div>
        </div>
    </div>

    {{-- ======== TABEL RUANGAN ======== --}}
    <div class="cr-crud-table-wrap">
        <p class="cr-crud-table-label">TABEL RUANGAN</p>
        <table class="cr-crud-table" id="roomTable">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama</th>
                    <th>Kapasitas</th>
                    <th>Gedung</th>
                    <th>Status</th>
                    <th><span class="cr-crud-th-gear">⚙</span></th>
                </tr>
            </thead>
            <tbody id="roomTableBody">
                @forelse($rooms as $index => $room)
                @php
                    $gedung = 'Gd ' . (chr(64 + (($room->room_id % 3) + 1)));
                    $isAktif = $room->status === 'tersedia';
                    $activeBookings = \App\Models\Booking::join('booking_rooms', 'bookings.booking_id', '=', 'booking_rooms.booking_id')
                        ->where('booking_rooms.room_id', $room->room_id)
                        ->whereIn('bookings.status', ['approved', 'pending'])
                        ->count();
                @endphp
                <tr class="cr-crud-row {{ $isAktif ? '' : 'cr-crud-row--inactive' }}"
                    data-name="{{ strtolower($room->nama_ruangan) }}"
                    data-status="{{ $room->status }}">
                    <td class="cr-crud-td-num">{{ $rooms->firstItem() + $index }}</td>
                    <td class="cr-crud-td-name">{{ $room->nama_ruangan }}</td>
                    <td class="cr-crud-td-kap">{{ $room->kapasitas }}</td>
                    <td class="cr-crud-td-gd">{{ $gedung }}</td>
                    <td class="cr-crud-td-status">
                        {{-- Toggle switch --}}
                        <form method="POST" action="/rooms/update/{{ $room->room_id }}"
                              class="cr-toggle-form" id="toggleForm{{ $room->room_id }}">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="nama_ruangan" value="{{ $room->nama_ruangan }}">
                            <input type="hidden" name="kapasitas" value="{{ $room->kapasitas }}">
                            <input type="hidden" name="status" id="statusInput{{ $room->room_id }}"
                                   value="{{ $room->status }}">
                            <label class="cr-toggle {{ $isAktif ? 'cr-toggle--on' : '' }}"
                                   id="toggle{{ $room->room_id }}"
                                   onclick="toggleStatus({{ $room->room_id }}, '{{ $room->status }}')"
                                   title="{{ $isAktif ? 'Klik untuk nonaktifkan' : 'Klik untuk aktifkan' }}">
                                <span class="cr-toggle__knob"></span>
                                @if(!$isAktif)
                                <span class="cr-toggle__off-label">Off</span>
                                @endif
                            </label>
                        </form>
                    </td>
                    <td class="cr-crud-td-actions">
                        <button class="cr-crud-btn cr-crud-btn--edit"
                                onclick="openEditModal({{ $room->room_id }}, '{{ addslashes($room->nama_ruangan) }}', {{ $room->kapasitas }}, '{{ $room->status }}')"
                                title="Edit">✏</button>
                        <button class="cr-crud-btn cr-crud-btn--delete"
                                onclick="openDeleteModal({{ $room->room_id }}, '{{ addslashes($room->nama_ruangan) }}', {{ $activeBookings }})"
                                title="Hapus">🗑</button>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="cr-crud-empty">
                        <span>🏫</span>
                        <p>Belum ada ruangan terdaftar. Tambah ruangan pertama!</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>

        {{-- No result state --}}
        <div class="cr-crud-noresult" id="noResult" style="display:none">
            <span>🔍</span><p>Ruangan tidak ditemukan.</p>
        </div>
    </div>

    {{-- ======== PAGINATION ======== --}}
    @if($rooms->hasPages())
    <div class="cr-crud-pagination">
        <p class="cr-crud-pagination__label">PAGINATION</p>
        <div class="cr-crud-pagination__row">
            {{-- Prev --}}
            @if($rooms->onFirstPage())
                <span class="cr-crud-page-btn cr-crud-page-btn--disabled">‹</span>
            @else
                <a href="{{ $rooms->previousPageUrl() }}" class="cr-crud-page-btn">‹</a>
            @endif

            {{-- Numbers --}}
            @foreach($rooms->getUrlRange(1, $rooms->lastPage()) as $page => $url)
                <a href="{{ $url }}"
                   class="cr-crud-page-btn {{ $page == $rooms->currentPage() ? 'cr-crud-page-btn--active' : '' }}">
                    {{ $page }}
                </a>
            @endforeach

            {{-- Next --}}
            @if($rooms->hasMorePages())
                <a href="{{ $rooms->nextPageUrl() }}" class="cr-crud-page-btn">›</a>
            @else
                <span class="cr-crud-page-btn cr-crud-page-btn--disabled">›</span>
            @endif
        </div>
    </div>
    @endif

</div>

{{-- ======== MODAL: TAMBAH RUANGAN ======== --}}
<div class="cr-modal-overlay" id="overlayTambah" style="display:none" onclick="closeTambahModal()"></div>
<div class="cr-modal" id="modalTambah" style="display:none">
    <div class="cr-modal__box">
        <div class="cr-modal__header">
            <h3 class="cr-modal__title">➕ Tambah Ruangan</h3>
            <button class="cr-modal__close" onclick="closeTambahModal()">✕</button>
        </div>
        <form action="/rooms/store" method="POST" class="cr-modal__form">
            @csrf
            <div class="cr-modal__field">
                <label class="cr-modal__label">Nama Ruangan <span class="cr-modal__req">*</span></label>
                <input type="text" name="nama_ruangan" class="cr-modal__input"
                       placeholder="cth: Lab Komputer 1" required>
            </div>
            <div class="cr-modal__field">
                <label class="cr-modal__label">Kapasitas (orang) <span class="cr-modal__req">*</span></label>
                <input type="number" name="kapasitas" class="cr-modal__input"
                       placeholder="cth: 40" min="1" required>
            </div>
            <div class="cr-modal__field">
                <label class="cr-modal__label">Gedung</label>
                <input type="text" name="gedung" class="cr-modal__input"
                       placeholder="cth: Gedung A">
                <p class="cr-modal__hint">Opsional, untuk keterangan</p>
            </div>
            <div class="cr-modal__field">
                <label class="cr-modal__label">Status <span class="cr-modal__req">*</span></label>
                <select name="status" class="cr-modal__select" required>
                    <option value="tersedia">Aktif (Tersedia)</option>
                    <option value="tidak tersedia">Tidak Aktif</option>
                </select>
            </div>
            <div class="cr-modal__actions">
                <button type="button" class="cr-modal__cancel" onclick="closeTambahModal()">Batal</button>
                <button type="submit" class="cr-modal__submit">Simpan Ruangan</button>
            </div>
        </form>
    </div>
</div>

{{-- ======== MODAL: EDIT RUANGAN ======== --}}
<div class="cr-modal-overlay" id="overlayEdit" style="display:none" onclick="closeEditModal()"></div>
<div class="cr-modal" id="modalEdit" style="display:none">
    <div class="cr-modal__box">
        <div class="cr-modal__header">
            <h3 class="cr-modal__title">✏️ Edit Ruangan</h3>
            <button class="cr-modal__close" onclick="closeEditModal()">✕</button>
        </div>
        <form id="editForm" action="" method="POST" class="cr-modal__form">
            @csrf
            @method('PUT')
            <div class="cr-modal__field">
                <label class="cr-modal__label">Nama Ruangan <span class="cr-modal__req">*</span></label>
                <input type="text" name="nama_ruangan" id="editNama" class="cr-modal__input" required>
            </div>
            <div class="cr-modal__field">
                <label class="cr-modal__label">Kapasitas (orang) <span class="cr-modal__req">*</span></label>
                <input type="number" name="kapasitas" id="editKapasitas" class="cr-modal__input" min="1" required>
            </div>
            <div class="cr-modal__field">
                <label class="cr-modal__label">Status <span class="cr-modal__req">*</span></label>
                <select name="status" id="editStatus" class="cr-modal__select" required>
                    <option value="tersedia">Aktif (Tersedia)</option>
                    <option value="tidak tersedia">Tidak Aktif</option>
                </select>
            </div>
            <div class="cr-modal__actions">
                <button type="button" class="cr-modal__cancel" onclick="closeEditModal()">Batal</button>
                <button type="submit" class="cr-modal__submit">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>

{{-- ======== MODAL: HAPUS (sesuai desain: floating card bukan fullscreen) ======== --}}
<div class="cr-del-modal" id="modalHapus" style="display:none">
    <div class="cr-del-modal__box">
        <p class="cr-del-modal__title">🗑️ Hapus Ruangan?</p>
        <p class="cr-del-modal__desc" id="delDesc">
            Anda akan menghapus '<strong id="delNama"></strong>' — Tindakan ini tidak dapat dibatalkan.
            Booking aktif ruangan ini: <strong id="delBookingCount"></strong>.
        </p>
        <div class="cr-del-modal__actions">
            <button class="cr-del-modal__batal" onclick="closeDeleteModal()">[Batal]</button>
            <a href="#" id="delConfirmBtn" class="cr-del-modal__hapus" onclick="confirmDelete(event)">
                🗑 Hapus Permanen
            </a>
        </div>
    </div>
</div>

<form id="form-delete-room" method="POST" style="display: none;">
    @csrf
    @method('DELETE')
</form>

<style>
/* ============================================================
   CRUD ROOMS PAGE
   ============================================================ */
.cr-crud-content { 
    max-width: 1000px; 
    margin: 0 auto; 
    padding: 0 1px;}

/* Flash */
.cr-crud-flash {
    padding: 12px 16px; border-radius: 10px; margin: 56px 0 16px;
    font-family: 'Plus Jakarta Sans', sans-serif; font-size: .875rem; font-weight: 600;
}
.cr-crud-flash--success { background: #D1FAF0; color: #00C896; border: 1px solid #A0E8D8; }
.cr-crud-flash--error   { background: #FFF0F3; color: #FF4D6D; border: 1px solid #FFD0D8; }

/* Page header */
.cr-crud-header {
    display: flex; align-items: flex-end; justify-content: space-between;
    margin-top: 56px; margin-bottom: 20px; flex-wrap: wrap; gap: 12px;
}
.cr-crud-header__eyebrow {
    font-family: 'DM Sans', sans-serif; font-size: .688rem; font-weight: 700;
    letter-spacing: 1.5px; text-transform: uppercase; color: #9AAFC8; margin: 0 0 6px;
}
.cr-crud-header__title {
    display: flex; align-items: center; gap: 10px;
    font-family: 'Space Grotesk', sans-serif; font-size: 1.625rem; font-weight: 700;
    color: #1A2340; margin: 0; letter-spacing: -.3px;
}
.cr-crud-header__icon { font-size: 1.25rem; }

.cr-crud-add-btn {
    padding: 11px 20px;
    background: linear-gradient(135deg, #F4B400 0%, #FFB020 100%);
    color: #1A2340;
    font-family: 'Plus Jakarta Sans', sans-serif;
    font-size: .875rem; font-weight: 800;
    border: none; border-radius: 12px; cursor: pointer;
    box-shadow: 0 3px 12px rgba(244,180,0,.28);
    transition: transform .15s, box-shadow .15s;
    white-space: nowrap;
}
.cr-crud-add-btn:hover { transform: scale(1.02); box-shadow: 0 6px 20px rgba(244,180,0,.38); }

/* Filter bar */
.cr-crud-filterbar {
    background: #fff; border: 1.5px solid #EEF2FB; border-radius: 16px;
    padding: 16px 20px; margin-bottom: 16px;
    box-shadow: 0 2px 10px rgba(26,35,64,.06);
}
.cr-crud-filterbar__label {
    font-family: 'DM Sans', sans-serif; font-size: .688rem; font-weight: 700;
    letter-spacing: 1.5px; color: #9AAFC8; text-transform: uppercase; margin: 0 0 10px;
}
.cr-crud-filterbar__row { display: flex; gap: 10px; align-items: center; flex-wrap: wrap; }

.cr-crud-search {
    flex: 1; min-width: 200px; position: relative; display: flex; align-items: center;
}
.cr-crud-search__icon { position: absolute; left: 12px; font-size: .875rem; pointer-events: none; }
.cr-crud-search__input {
    width: 100%; padding: 10px 14px 10px 36px;
    background: #F8FAFF; border: 1.5px solid #E8EEF7; border-radius: 10px;
    font-family: 'DM Sans', sans-serif; font-size: .875rem; color: #1A2340; outline: none;
    transition: border-color .2s, box-shadow .2s;
}
.cr-crud-search__input:focus { border-color: #F4B400; box-shadow: 0 0 0 3px rgba(244,180,0,.10); }
.cr-crud-search__input::placeholder { color: #9AAFC8; }

.cr-crud-status-filter { position: relative; }
.cr-crud-status-btn {
    padding: 10px 16px; background: #fff; border: 1.5px solid #E8EEF7; border-radius: 10px;
    font-family: 'Plus Jakarta Sans', sans-serif; font-size: .813rem; font-weight: 600;
    color: #5A6A8A; cursor: pointer; white-space: nowrap;
    transition: border-color .15s;
}
.cr-crud-status-btn:hover { border-color: #F4B400; }
.cr-crud-status-dropdown {
    position: absolute; top: calc(100% + 6px); right: 0;
    background: #fff; border: 1.5px solid #E8EEF7; border-radius: 12px;
    box-shadow: 0 8px 24px rgba(26,35,64,.12); min-width: 160px; z-index: 50; overflow: hidden;
}
.cr-crud-status-opt {
    display: block; width: 100%; padding: 10px 16px; background: none; border: none;
    text-align: left; font-family: 'Plus Jakarta Sans', sans-serif; font-size: .813rem;
    font-weight: 600; color: #5A6A8A; cursor: pointer;
    transition: background .12s, color .12s;
}
.cr-crud-status-opt:hover { background: #FFF9E6; color: #1A2340; }

/* Table */
.cr-crud-table-wrap {
    background: #fff; border: 1.5px solid #EEF2FB; border-radius: 16px;
    padding: 16px 20px; box-shadow: 0 2px 10px rgba(26,35,64,.06);
    overflow-x: auto;
}
.cr-crud-table-label {
    font-family: 'DM Sans', sans-serif; font-size: .688rem; font-weight: 700;
    letter-spacing: 1.5px; color: #9AAFC8; text-transform: uppercase; margin: 0 0 14px;
}
.cr-crud-table { width: 100%; border-collapse: collapse; }
.cr-crud-table thead th {
    font-family: 'Plus Jakarta Sans', sans-serif; font-size: .813rem; font-weight: 800;
    color: #1A2340; padding: 10px 12px; text-align: left;
    border-bottom: 2px solid #EEF2FB;
}
.cr-crud-th-gear { font-size: 1rem; }

.cr-crud-row td { padding: 12px 12px; vertical-align: middle; border-bottom: 1px solid #F0F4FF; }
.cr-crud-row--inactive { background: #FAFBFF; }
.cr-crud-row:last-child td { border-bottom: none; }
.cr-crud-row:hover td { background: #FFFBF0; }

.cr-crud-td-num  { font-family: 'DM Sans', sans-serif; font-size: .813rem; color: #9AAFC8; width: 48px; }
.cr-crud-td-name { font-family: 'Plus Jakarta Sans', sans-serif; font-size: .875rem; font-weight: 700; color: #1A2340; }
.cr-crud-td-kap  { font-family: 'DM Sans', sans-serif; font-size: .875rem; color: #5A6A8A; }
.cr-crud-td-gd   { font-family: 'DM Sans', sans-serif; font-size: .875rem; color: #5A6A8A; }
.cr-crud-td-status { width: 80px; }
.cr-crud-td-actions { width: 80px; }

/* Toggle switch */
.cr-toggle-form { display: inline-flex; }
.cr-toggle {
    display: inline-flex; align-items: center;
    width: 52px; height: 28px; border-radius: 999px;
    background: #D0D9E8; cursor: pointer; position: relative;
    transition: background .2s; user-select: none;
}
.cr-toggle--on { background: #00C896; }
.cr-toggle__knob {
    position: absolute; left: 3px;
    width: 22px; height: 22px; border-radius: 50%;
    background: #fff; box-shadow: 0 1px 4px rgba(0,0,0,.15);
    transition: left .2s;
}
.cr-toggle--on .cr-toggle__knob { left: 27px; }
.cr-toggle__off-label {
    position: absolute; right: 5px;
    font-family: 'Plus Jakarta Sans', sans-serif; font-size: .5rem; font-weight: 700;
    color: #fff;
}

/* Action buttons */
.cr-crud-td-actions { display: flex; gap: 6px; align-items: center; }
.cr-crud-btn {
    width: 30px; height: 30px; border-radius: 8px; border: none; cursor: pointer;
    display: flex; align-items: center; justify-content: center;
    font-size: .875rem; transition: transform .12s, box-shadow .12s;
}
.cr-crud-btn:hover { transform: scale(1.10); }
.cr-crud-btn--edit   { background: #4FC3F7; color: #fff; box-shadow: 0 2px 8px rgba(79,195,247,.30); }
.cr-crud-btn--delete { background: #FF4D6D; color: #fff; box-shadow: 0 2px 8px rgba(255,77,109,.30); }

/* Empty / no result */
.cr-crud-empty { text-align: center; padding: 48px 20px; color: #9AAFC8; }
.cr-crud-empty span { font-size: 2.5rem; display: block; margin-bottom: 8px; }
.cr-crud-empty p { font-family: 'Plus Jakarta Sans', sans-serif; font-size: .938rem; font-weight: 600; margin: 0; }
.cr-crud-noresult { display: flex; flex-direction: column; align-items: center; padding: 40px; gap: 8px; color: #9AAFC8; }
.cr-crud-noresult span { font-size: 2rem; }
.cr-crud-noresult p { font-family: 'Plus Jakarta Sans', sans-serif; font-size: .938rem; font-weight: 600; margin: 0; }

/* Pagination */
.cr-crud-pagination { margin-top: 16px; }
.cr-crud-pagination__label {
    font-family: 'DM Sans', sans-serif; font-size: .688rem; font-weight: 700;
    letter-spacing: 1.5px; color: #9AAFC8; text-transform: uppercase; margin: 0 0 10px;
}
.cr-crud-pagination__row { display: flex; align-items: center; gap: 6px; flex-wrap: wrap; }
.cr-crud-page-btn {
    display: inline-flex; align-items: center; justify-content: center;
    min-width: 34px; height: 34px; padding: 0 8px;
    background: #fff; border: 1.5px solid #E8EEF7; border-radius: 8px;
    font-family: 'Plus Jakarta Sans', sans-serif; font-size: .813rem; font-weight: 700;
    color: #5A6A8A; text-decoration: none; cursor: pointer;
    transition: all .15s;
}
.cr-crud-page-btn:hover:not(.cr-crud-page-btn--disabled):not(.cr-crud-page-btn--active) {
    border-color: #F4B400; color: #1A2340;
}
.cr-crud-page-btn--active  { background: #F4B400; border-color: #F4B400; color: #1A2340; }
.cr-crud-page-btn--disabled { opacity: .40; cursor: not-allowed; pointer-events: none; }

/* ============================================================
   MODALS (Tambah & Edit)
   ============================================================ */
.cr-modal-overlay {
    position: fixed; inset: 0; background: rgba(26,35,64,.40);
    backdrop-filter: blur(2px); z-index: 200;
}
.cr-modal {
    position: fixed; top: 50%; left: 50%; transform: translate(-50%, -50%);
    z-index: 201; width: 100%; max-width: 440px; padding: 0 16px;
}
.cr-modal__box {
    background: #fff; border-radius: 20px; padding: 28px 26px;
    box-shadow: 0 20px 60px rgba(26,35,64,.18);
}
.cr-modal__header {
    display: flex; align-items: center; justify-content: space-between; margin-bottom: 20px;
}
.cr-modal__title { font-family: 'Space Grotesk', sans-serif; font-size: 1.125rem; font-weight: 700; color: #1A2340; margin: 0; }
.cr-modal__close {
    width: 28px; height: 28px; border-radius: 8px; border: none; background: #F0F4FF;
    color: #5A6A8A; cursor: pointer; font-size: .875rem;
    display: flex; align-items: center; justify-content: center;
    transition: background .15s;
}
.cr-modal__close:hover { background: #FFE4E9; color: #FF4D6D; }

.cr-modal__form { display: flex; flex-direction: column; gap: 16px; }
.cr-modal__field { display: flex; flex-direction: column; gap: 6px; }
.cr-modal__label {
    font-family: 'Plus Jakarta Sans', sans-serif; font-size: .813rem; font-weight: 700; color: #1A2340;
}
.cr-modal__req { color: #FF4D6D; }
.cr-modal__input, .cr-modal__select {
    padding: 11px 14px; background: #F8FAFF; border: 1.5px solid #E8EEF7; border-radius: 10px;
    font-family: 'DM Sans', sans-serif; font-size: .875rem; color: #1A2340; outline: none;
    transition: border-color .2s, box-shadow .2s;
    appearance: none;
}
.cr-modal__input:focus, .cr-modal__select:focus {
    border-color: #F4B400; box-shadow: 0 0 0 3px rgba(244,180,0,.10); background: #fff;
}
.cr-modal__hint { font-family: 'DM Sans', sans-serif; font-size: .688rem; color: #9AAFC8; margin: 0; }

.cr-modal__actions { display: flex; gap: 10px; margin-top: 4px; }
.cr-modal__cancel {
    flex: 1; padding: 12px; background: #F8FAFF; border: 1.5px solid #E8EEF7;
    border-radius: 10px; font-family: 'Plus Jakarta Sans', sans-serif; font-size: .875rem;
    font-weight: 600; color: #5A6A8A; cursor: pointer; transition: background .15s;
}
.cr-modal__cancel:hover { background: #EEF2FB; }
.cr-modal__submit {
    flex: 2; padding: 12px;
    background: linear-gradient(135deg, #F4B400 0%, #FFB020 100%);
    border: none; border-radius: 10px; font-family: 'Plus Jakarta Sans', sans-serif;
    font-size: .875rem; font-weight: 800; color: #1A2340; cursor: pointer;
    box-shadow: 0 3px 10px rgba(244,180,0,.25); transition: transform .15s;
}
.cr-modal__submit:hover { transform: scale(1.01); }

/* ============================================================
   DELETE MODAL (floating card style sesuai desain)
   ============================================================ */
.cr-del-modal {
    position: fixed; bottom: 80px; right: 32px;
    z-index: 300; width: 320px;
    animation: cr-del-slide-in .2s ease;
}
@keyframes cr-del-slide-in {
    from { opacity: 0; transform: translateY(16px); }
    to   { opacity: 1; transform: translateY(0); }
}
.cr-del-modal__box {
    background: #fff; border-radius: 16px; padding: 20px;
    box-shadow: 0 8px 32px rgba(26,35,64,.18);
    border-left: 4px solid #FF4D6D;
}
.cr-del-modal__title {
    font-family: 'Plus Jakarta Sans', sans-serif; font-size: .938rem;
    font-weight: 800; color: #1A2340; margin: 0 0 8px;
}
.cr-del-modal__desc {
    font-family: 'DM Sans', sans-serif; font-size: .813rem;
    color: #5A6A8A; margin: 0 0 16px; line-height: 1.6;
}
.cr-del-modal__actions { display: flex; gap: 8px; align-items: center; }
.cr-del-modal__batal {
    padding: 8px 16px; background: #F8FAFF; border: 1.5px solid #E8EEF7;
    border-radius: 8px; font-family: 'Plus Jakarta Sans', sans-serif;
    font-size: .75rem; font-weight: 600; color: #5A6A8A; cursor: pointer;
}
.cr-del-modal__batal:hover { background: #EEF2FB; }
.cr-del-modal__hapus {
    flex: 1; padding: 8px 16px; background: #FF4D6D;
    border-radius: 8px; font-family: 'Plus Jakarta Sans', sans-serif;
    font-size: .75rem; font-weight: 800; color: #fff; text-decoration: none;
    display: flex; align-items: center; justify-content: center; gap: 6px;
    box-shadow: 0 2px 8px rgba(255,77,109,.30);
    transition: background .15s;
}
.cr-del-modal__hapus:hover { background: #e6284a; }

/* Responsive */
@media (max-width: 700px) {
    .cr-crud-header { flex-direction: column; align-items: flex-start; }
    .cr-crud-add-btn { width: 100%; text-align: center; }
    .cr-del-modal { right: 16px; left: 16px; width: auto; }
}
</style>

<script>
(function(){
    // ─── Live search ────────────────────────────────────────────
    const searchEl = document.getElementById('searchInput');
    const rows     = document.querySelectorAll('.cr-crud-row');
    const noResult = document.getElementById('noResult');
    let filterStatus = '';

    function applyFilters() {
        const q = searchEl.value.toLowerCase().trim();
        let visible = 0;
        rows.forEach(row => {
            const name   = row.dataset.name  || '';
            const status = row.dataset.status || '';
            let show = true;
            if (q && !name.includes(q))             show = false;
            if (filterStatus && status !== filterStatus) show = false;
            row.style.display = show ? '' : 'none';
            if (show) visible++;
        });
        noResult.style.display = (visible === 0 && rows.length > 0) ? 'flex' : 'none';
    }

    searchEl.addEventListener('input', applyFilters);

    // ─── Status filter dropdown ──────────────────────────────────
    const statusBtn      = document.getElementById('statusFilterBtn');
    const statusDropdown = document.getElementById('statusDropdown');
    const statusLabel    = document.getElementById('statusFilterLabel');
    const statusOpts     = document.querySelectorAll('.cr-crud-status-opt');

    statusBtn.addEventListener('click', e => {
        e.stopPropagation();
        statusDropdown.style.display = statusDropdown.style.display === 'none' ? '' : 'none';
    });
    document.addEventListener('click', () => { statusDropdown.style.display = 'none'; });

    statusOpts.forEach(opt => {
        opt.addEventListener('click', () => {
            filterStatus  = opt.dataset.value;
            statusLabel.textContent = opt.textContent + ' ▼';
            statusDropdown.style.display = 'none';
            applyFilters();
        });
    });

    // ─── Toggle status submit ────────────────────────────────────
    window.toggleStatus = function(id, currentStatus) {
        const newStatus = currentStatus === 'tersedia' ? 'tidak tersedia' : 'tersedia';
        document.getElementById('statusInput' + id).value = newStatus;

        const toggle = document.getElementById('toggle' + id);
        if (newStatus === 'tersedia') toggle.classList.add('cr-toggle--on');
        else toggle.classList.remove('cr-toggle--on');

        document.getElementById('toggleForm' + id).submit();
    };

    // ─── Modal Tambah ────────────────────────────────────────────
    const modalTambah  = document.getElementById('modalTambah');
    const overlayTambah= document.getElementById('overlayTambah');

    document.getElementById('btnTambahRuangan').addEventListener('click', () => {
        modalTambah.style.display   = '';
        overlayTambah.style.display = '';
    });
    window.closeTambahModal = function() {
        modalTambah.style.display   = 'none';
        overlayTambah.style.display = 'none';
    };

    // ─── Modal Edit ──────────────────────────────────────────────
    const modalEdit   = document.getElementById('modalEdit');
    const overlayEdit = document.getElementById('overlayEdit');
    const editForm    = document.getElementById('editForm');

    window.openEditModal = function(id, nama, kapasitas, status) {
        editForm.action = '/rooms/update/' + id;
        document.getElementById('editNama').value      = nama;
        document.getElementById('editKapasitas').value = kapasitas;
        document.getElementById('editStatus').value    = status;
        modalEdit.style.display   = '';
        overlayEdit.style.display = '';
    };
    window.closeEditModal = function() {
        modalEdit.style.display   = 'none';
        overlayEdit.style.display = 'none';
    };

    // ─── Modal Hapus ─────────────────────────────────────────────
    const modalHapus = document.getElementById('modalHapus');
    let deleteId     = null;

    window.openDeleteModal = function(id, nama, bookingCount) {
        deleteId = id;
        document.getElementById('delNama').textContent         = nama;
        document.getElementById('delBookingCount').textContent = bookingCount;
        modalHapus.style.display = '';
    };
    window.closeDeleteModal = function() {
        modalHapus.style.display = 'none';
        deleteId = null;
    };
    window.confirmDelete = function(e) {
        e.preventDefault();
        if (!deleteId) return;
        var form = document.getElementById('form-delete-room');
        form.action = '/rooms/delete/' + deleteId;
        form.submit();
    };

    // ─── Close modal on ESC ──────────────────────────────────────
    document.addEventListener('keydown', e => {
        if (e.key === 'Escape') {
            closeTambahModal(); closeEditModal(); closeDeleteModal();
        }
    });

})();
</script>
@endsection