@extends('layouts.admin')

@section('title', 'Manajemen Jadwal Kuliah')

@section('content')
<div class="cr-dash-content cr-sch-content">

    {{-- Brand pill --}}
    <div class="cr-dash-brand-pill">
        <span class="cr-dash-brand-pill__icon">🟨</span>
        <span class="cr-dash-brand-pill__text">CampusRoom • v1.0 Admin</span>
    </div>

    {{-- ===== PAGE HEADER ===== --}}
    <div class="cr-sch-header">
        <div class="cr-sch-header__left">
            <p class="cr-sch-header__kicker">PAGE HEADER</p>
            <h1 class="cr-sch-header__title">
                <span class="cr-sch-header__icon">📅</span>
                Manajemen Jadwal Kuliah
            </h1>
        </div>
        <button class="cr-sch-btn-tambah" id="btnTambah">
            + Tambah Jadwal
        </button>
    </div>

    {{-- Flash messages --}}
    @if(session('success'))
    <div class="cr-sch-flash cr-sch-flash--success">✅ {{ session('success') }}</div>
    @endif
    @if(session('error'))
    <div class="cr-sch-flash cr-sch-flash--error">⚠️ {{ session('error') }}</div>
    @endif

    {{-- ===== FILTER & SEARCH ===== --}}
    <div class="cr-sch-filter-bar">
        <p class="cr-sch-filter-bar__label">FILTER &amp; SEARCH</p>

        <div class="cr-sch-filter-bar__controls">
            <div class="cr-sch-filter-bar__view-toggle">
                <button class="cr-sch-view-btn cr-sch-view-btn--active" id="btnViewTabel">
                    [View Tabel]
                </button>
                <button class="cr-sch-view-btn" id="btnViewMingguan">
                    [View Mingguan]
                </button>
            </div>
        </div>
    </div>

    <form method="GET" action="/schedules" class="cr-sch-search-row" id="filterForm">
        <div class="cr-sch-search-wrap">
            <span class="cr-sch-search-icon">🔍</span>
            <input type="text" name="search" class="cr-sch-search-input"
                   placeholder="Cari nama mata kuliah..."
                   value="{{ request('search') }}">
        </div>

        <div class="cr-sch-select-wrap">
            <select name="room_id" class="cr-sch-select" onchange="this.form.submit()">
                <option value="">Semua Ruangan</option>
                @foreach($rooms as $room)
                    <option value="{{ $room->room_id }}"
                        {{ request('room_id') == $room->room_id ? 'selected' : '' }}>
                        {{ $room->nama_ruangan }}
                    </option>
                @endforeach
            </select>
            <span class="cr-sch-select-caret">▼</span>
        </div>

        <div class="cr-sch-select-wrap">
            <select name="hari" class="cr-sch-select" onchange="this.form.submit()">
                <option value="">Semua Hari</option>
                @foreach(['Senin','Selasa','Rabu','Kamis','Jumat'] as $h)
                    <option value="{{ $h }}" {{ request('hari') == $h ? 'selected' : '' }}>{{ $h }}</option>
                @endforeach
            </select>
            <span class="cr-sch-select-caret">▼</span>
        </div>

        <button type="submit" class="cr-sch-search-btn">Cari</button>
    </form>

    {{-- ===== TABLE VIEW ===== --}}
    <div id="viewTabel">
        <div class="cr-sch-table-wrap">
            <table class="cr-sch-table">
                <thead>
                    <tr>
                        <th class="cr-sch-th cr-sch-th--no">No</th>
                        <th class="cr-sch-th">Mata Kuliah</th>
                        <th class="cr-sch-th">Dosen</th>
                        <th class="cr-sch-th">Ruangan</th>
                        <th class="cr-sch-th">Hari</th>
                        <th class="cr-sch-th">Jam</th>
                        <th class="cr-sch-th cr-sch-th--action">⚙️</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($schedules as $i => $sc)
                    <tr class="cr-sch-tr {{ $i % 2 === 0 ? 'cr-sch-tr--even' : '' }}">
                        <td class="cr-sch-td cr-sch-td--no">{{ $schedules->firstItem() + $i }}</td>
                        <td class="cr-sch-td cr-sch-td--bold">{{ $sc->mata_kuliah }}</td>
                        <td class="cr-sch-td">{{ $sc->dosen }}</td>
                        <td class="cr-sch-td">{{ $sc->room?->nama_ruangan ?? '-' }}</td>
                        <td class="cr-sch-td">
                            <span class="cr-sch-hari-badge cr-sch-hari-badge--{{ strtolower($sc->hari) }}">
                                {{ $sc->hari }}
                            </span>
                        </td>
                        <td class="cr-sch-td cr-sch-td--jam">
                            {{ substr($sc->jam_mulai,0,5) }} – {{ substr($sc->jam_selesai,0,5) }}
                        </td>
                        <td class="cr-sch-td cr-sch-td--action">
                            {{-- Edit: buka modal dengan data terisi --}}
                            <button class="cr-sch-icon-btn cr-sch-icon-btn--edit"
                                title="Edit"
                                onclick="openEditModal(
                                    {{ $sc->schedule_id }},
                                    '{{ addslashes($sc->mata_kuliah) }}',
                                    '{{ addslashes($sc->dosen) }}',
                                    '{{ $sc->room_id }}',
                                    '{{ $sc->hari }}',
                                    '{{ substr($sc->jam_mulai,0,5) }}',
                                    '{{ substr($sc->jam_selesai,0,5) }}'
                                )">
                                ✏️
                            </button>
                            {{-- Hapus --}}
                            <form action="/schedules/delete/{{ $sc->schedule_id }}"
                                  method="POST" style="display:inline"
                                  onsubmit="return confirm('Hapus jadwal ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="cr-sch-icon-btn cr-sch-icon-btn--del" title="Hapus">🗑️</button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="cr-sch-empty-row">
                            <span>📋</span>
                            <p>Belum ada jadwal kuliah terdaftar.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- PAGINATION --}}
        <div class="cr-sch-pagination">
            <p class="cr-sch-pagination__label">PAGINATION</p>
            <div class="cr-sch-pagination__nav">
                @if($schedules->onFirstPage())
                    <button class="cr-sch-page-btn" disabled>‹</button>
                @else
                    <a href="{{ $schedules->previousPageUrl() }}" class="cr-sch-page-btn">‹</a>
                @endif

                @foreach($schedules->getUrlRange(1, $schedules->lastPage()) as $page => $url)
                    <a href="{{ $url }}"
                       class="cr-sch-page-num {{ $schedules->currentPage() == $page ? 'cr-sch-page-num--active' : '' }}">
                        {{ $page }}
                    </a>
                @endforeach

                @if($schedules->hasMorePages())
                    <a href="{{ $schedules->nextPageUrl() }}" class="cr-sch-page-btn">›</a>
                @else
                    <button class="cr-sch-page-btn" disabled>›</button>
                @endif
            </div>
        </div>
    </div>

    {{-- ===== WEEKLY VIEW ===== --}}
    <div id="viewMingguan" style="display:none">
        @php
            $hari_list = ['Senin','Selasa','Rabu','Kamis','Jumat'];
            $allSchedules = \App\Models\Schedule::with('room')->orderBy('jam_mulai')->get();
        @endphp
        <div class="cr-sch-week-grid">
            @foreach($hari_list as $hari)
            <div class="cr-sch-week-col">
                <div class="cr-sch-week-col__head">{{ $hari }}</div>
                @forelse($allSchedules->where('hari', $hari) as $sc)
                <div class="cr-sch-week-slot">
                    <p class="cr-sch-week-slot__time">{{ substr($sc->jam_mulai,0,5) }}–{{ substr($sc->jam_selesai,0,5) }}</p>
                    <p class="cr-sch-week-slot__mk">{{ $sc->mata_kuliah }}</p>
                    <p class="cr-sch-week-slot__room">{{ $sc->room?->nama_ruangan }}</p>
                </div>
                @empty
                <div class="cr-sch-week-empty">–</div>
                @endforelse
            </div>
            @endforeach
        </div>
    </div>

</div>

{{-- ===== MODAL TAMBAH / EDIT ===== --}}
<div class="cr-sch-modal-overlay" id="modalOverlay" style="display:none">
    <div class="cr-sch-modal" id="modal">
        <div class="cr-sch-modal__head">
            <span class="cr-sch-modal__close" id="btnCloseModal">✕</span>
            <h2 class="cr-sch-modal__title" id="modalTitle">Tambah Jadwal Kuliah</h2>
        </div>

        <form id="modalForm" method="POST" action="/schedules/store">
            @csrf
            <input type="hidden" name="_method" id="formMethod" value="POST">
            <input type="hidden" name="schedule_id" id="fieldScheduleId">

            <div class="cr-sch-modal__body">

                {{-- Mata Kuliah --}}
                <div class="cr-sch-modal__field">
                    <label class="cr-sch-modal__label">Mata Kuliah *</label>
                    <input type="text" name="mata_kuliah" id="fieldMataKuliah"
                           class="cr-sch-modal__input" placeholder="Nama mata kuliah" required>
                </div>

                {{-- Dosen --}}
                <div class="cr-sch-modal__field">
                    <label class="cr-sch-modal__label">Dosen *</label>
                    <input type="text" name="dosen" id="fieldDosen"
                           class="cr-sch-modal__input" placeholder="Nama dosen" required>
                </div>

                {{-- Ruangan --}}
                <div class="cr-sch-modal__field">
                    <label class="cr-sch-modal__label">Ruangan *</label>
                    <div class="cr-sch-modal__select-wrap">
                        <select name="room_id" id="fieldRuangan" class="cr-sch-modal__select" required>
                            <option value="">Pilih Ruangan</option>
                            @foreach($rooms as $room)
                            <option value="{{ $room->room_id }}">{{ $room->nama_ruangan }}</option>
                            @endforeach
                        </select>
                        <span class="cr-sch-modal__caret">▼</span>
                    </div>
                </div>

                {{-- Hari & Jam --}}
                <div class="cr-sch-modal__row">
                    <div class="cr-sch-modal__field">
                        <label class="cr-sch-modal__label">Hari *</label>
                        <div class="cr-sch-modal__select-wrap">
                            <select name="hari" id="fieldHari" class="cr-sch-modal__select" required>
                                <option value="">Pilih Hari</option>
                                @foreach(['Senin','Selasa','Rabu','Kamis','Jumat'] as $h)
                                <option value="{{ $h }}">{{ $h }}</option>
                                @endforeach
                            </select>
                            <span class="cr-sch-modal__caret">▼</span>
                        </div>
                    </div>
                    <span class="cr-sch-modal__sep">.</span>
                    <div class="cr-sch-modal__field">
                        <label class="cr-sch-modal__label">Jam Mulai *</label>
                        <div class="cr-sch-modal__select-wrap">
                            <input type="time" name="jam_mulai" id="fieldJamMulai"
                                   class="cr-sch-modal__input cr-sch-modal__input--time"
                                   value="08:00" required>
                        </div>
                    </div>
                </div>

                <div class="cr-sch-modal__field">
                    <label class="cr-sch-modal__label">Jam Selesai *</label>
                    <div class="cr-sch-modal__select-wrap">
                        <input type="time" name="jam_selesai" id="fieldJamSelesai"
                               class="cr-sch-modal__input cr-sch-modal__input--time"
                               value="10:00" required>
                    </div>
                </div>

                {{-- Cek Bentrok Status --}}
                <div class="cr-sch-modal__cek" id="cekStatus">
                    <span class="cr-sch-modal__cek-icon">⚠️</span>
                    <span class="cr-sch-modal__cek-text" id="cekText">Cek otomatis: Tidak ada bentrok.</span>
                </div>

            </div>

            <div class="cr-sch-modal__footer">
                <button type="button" class="cr-sch-modal__btn-batal" id="btnBatal">[Batal]</button>
                <button type="submit" class="cr-sch-modal__btn-simpan" id="btnSimpan">
                    💾 Simpan
                </button>
            </div>
        </form>
    </div>
</div>

<style>
/* ============================================================
   JADWAL CRUD PAGE
   ============================================================ */
.cr-sch-content { max-width: 900px; }

/* ── Header ── */
.cr-sch-header {
    display: flex; align-items: flex-end; justify-content: space-between;
    margin-top: 56px; margin-bottom: 20px; gap: 16px;
}
.cr-sch-header__kicker {
    font-family: 'Plus Jakarta Sans', sans-serif; font-size: 0.7rem; font-weight: 800;
    letter-spacing: 0.1em; color: #9AAFC8; text-transform: uppercase; margin: 0 0 4px;
}
.cr-sch-header__title {
    font-family: 'Space Grotesk', sans-serif; font-size: 1.5rem; font-weight: 700;
    color: #1A2340; margin: 0; display: flex; align-items: center; gap: 10px;
    letter-spacing: -0.3px;
}
.cr-sch-header__icon { font-size: 1.25rem; }
.cr-sch-btn-tambah {
    padding: 10px 20px; background: linear-gradient(135deg, #F4B400 0%, #FFB020 100%);
    color: #1A2340; font-family: 'Plus Jakarta Sans', sans-serif; font-size: 0.875rem;
    font-weight: 800; border: none; border-radius: 10px; cursor: pointer; white-space: nowrap;
    box-shadow: 0 3px 12px rgba(244,180,0,.30); transition: transform .15s, box-shadow .15s;
}
.cr-sch-btn-tambah:hover { transform: scale(1.02); box-shadow: 0 5px 18px rgba(244,180,0,.40); }

/* Flash */
.cr-sch-flash {
    padding: 12px 16px; border-radius: 10px; font-family: 'Plus Jakarta Sans', sans-serif;
    font-size: 0.875rem; font-weight: 600; margin-bottom: 16px;
}
.cr-sch-flash--success { background: #D1FAF0; border: 1px solid #A0E8D8; color: #00C896; }
.cr-sch-flash--error   { background: #FFF0F3; border: 1px solid #FFD0D8; color: #FF4D6D; }

/* ── Filter bar ── */
.cr-sch-filter-bar {
    display: flex; align-items: center; justify-content: space-between; margin-bottom: 10px;
}
.cr-sch-filter-bar__label {
    font-family: 'Plus Jakarta Sans', sans-serif; font-size: 0.7rem; font-weight: 800;
    letter-spacing: 0.1em; color: #9AAFC8; text-transform: uppercase; margin: 0;
}
.cr-sch-filter-bar__view-toggle { display: flex; gap: 8px; }
.cr-sch-view-btn {
    padding: 7px 14px; border-radius: 8px; border: 1.5px solid #E8EEF7;
    background: #fff; font-family: 'Plus Jakarta Sans', sans-serif;
    font-size: 0.75rem; font-weight: 600; color: #5A6A8A; cursor: pointer; transition: all .15s;
}
.cr-sch-view-btn--active {
    background: linear-gradient(135deg, #F4B400 0%, #FFB020 100%);
    border-color: transparent; color: #1A2340; font-weight: 800;
}

.cr-sch-search-row {
    display: flex; gap: 10px; margin-bottom: 20px; align-items: center; flex-wrap: wrap;
}
.cr-sch-search-wrap {
    flex: 1; min-width: 200px; position: relative;
    background: #fff; border: 1.5px solid #E8EEF7; border-radius: 10px; overflow: hidden;
    display: flex; align-items: center;
}
.cr-sch-search-icon {
    padding: 0 10px; font-size: 0.875rem; color: #9AAFC8; flex-shrink: 0;
}
.cr-sch-search-input {
    flex: 1; padding: 10px 12px 10px 0; border: none; outline: none;
    font-family: 'DM Sans', sans-serif; font-size: 0.875rem; color: #1A2340; background: transparent;
}
.cr-sch-select-wrap { position: relative; }
.cr-sch-select {
    padding: 10px 36px 10px 14px; border: 1.5px solid #E8EEF7; border-radius: 10px;
    font-family: 'DM Sans', sans-serif; font-size: 0.838rem; color: #1A2340; background: #fff;
    outline: none; -webkit-appearance: none; -moz-appearance: none; appearance: none; cursor: pointer; transition: border-color .2s;
}
.cr-sch-select:focus { border-color: #F4B400; }
.cr-sch-select-caret {
    position: absolute; right: 10px; top: 50%; transform: translateY(-50%);
    font-size: 0.7rem; color: #9AAFC8; pointer-events: none;
}
.cr-sch-search-btn {
    padding: 10px 20px; background: #1A2340; color: #fff; border: none; border-radius: 10px;
    font-family: 'Plus Jakarta Sans', sans-serif; font-size: 0.813rem; font-weight: 700;
    cursor: pointer; transition: opacity .15s;
}
.cr-sch-search-btn:hover { opacity: 0.85; }

/* ── Table ── */
.cr-sch-table-wrap {
    background: #fff; border-radius: 16px; overflow: hidden;
    box-shadow: 0 2px 8px rgba(26,35,64,0.06); border: 1.5px solid #EEF2FB; margin-bottom: 16px;
}
.cr-sch-table { width: 100%; border-collapse: collapse; }
.cr-sch-th {
    padding: 14px 16px; font-family: 'Plus Jakarta Sans', sans-serif;
    font-size: 0.813rem; font-weight: 800; color: #1A2340; text-align: left;
    background: #F8FAFF; border-bottom: 1.5px solid #EEF2FB;
    letter-spacing: 0.02em;
}
.cr-sch-th--no     { width: 48px; text-align: center; }
.cr-sch-th--action { width: 80px; text-align: center; }

.cr-sch-tr { transition: background .15s; }
.cr-sch-tr--even { background: #FAFBFF; }
.cr-sch-tr:hover { background: rgba(244,180,0,0.04); }

.cr-sch-td {
    padding: 13px 16px; font-family: 'DM Sans', sans-serif;
    font-size: 0.838rem; color: #5A6A8A;
    border-bottom: 1px solid #EEF2FB;
}
.cr-sch-td--no     { text-align: center; font-weight: 700; color: #9AAFC8; }
.cr-sch-td--bold   { font-family: 'Plus Jakarta Sans', sans-serif; font-weight: 700; color: #1A2340; }
.cr-sch-td--jam    { font-family: 'DM Mono', monospace; font-size: 0.813rem; color: #1A2340; font-weight: 600; }
.cr-sch-td--action { text-align: center; }

/* hari badge */
.cr-sch-hari-badge {
    display: inline-block; padding: 2px 10px; border-radius: 999px;
    font-family: 'Plus Jakarta Sans', sans-serif; font-size: 0.7rem; font-weight: 700;
    background: rgba(79,195,247,0.12); color: #0277BD;
}
.cr-sch-hari-badge--selasa   { background: rgba(244,180,0,0.12); color: #E6820A; }
.cr-sch-hari-badge--rabu     { background: rgba(0,200,150,0.12); color: #00C896; }
.cr-sch-hari-badge--kamis    { background: rgba(255,77,109,0.12); color: #FF4D6D; }
.cr-sch-hari-badge--jumat    { background: rgba(100,80,200,0.12); color: #6450C8; }

/* action buttons */
.cr-sch-icon-btn {
    width: 32px; height: 32px; border-radius: 8px; border: none; cursor: pointer;
    font-size: 0.875rem; display: inline-flex; align-items: center; justify-content: center;
    transition: transform .12s, background .12s;
}
.cr-sch-icon-btn--edit { background: rgba(79,195,247,0.12); }
.cr-sch-icon-btn--edit:hover { background: rgba(79,195,247,0.25); transform: scale(1.08); }
.cr-sch-icon-btn--del  { background: rgba(255,77,109,0.10); }
.cr-sch-icon-btn--del:hover  { background: rgba(255,77,109,0.22); transform: scale(1.08); }

.cr-sch-empty-row {
    padding: 40px; text-align: center;
    font-family: 'Plus Jakarta Sans', sans-serif; font-size: 0.875rem; color: #9AAFC8;
}

/* ── Pagination ── */
.cr-sch-pagination { display: flex; flex-direction: column; gap: 8px; align-items: flex-end; margin-bottom: 32px; }
.cr-sch-pagination__label {
    font-family: 'Plus Jakarta Sans', sans-serif; font-size: 0.7rem; font-weight: 800;
    letter-spacing: 0.1em; color: #9AAFC8; text-transform: uppercase; margin: 0;
}
.cr-sch-pagination__nav { display: flex; align-items: center; gap: 6px; }
.cr-sch-page-btn {
    width: 32px; height: 32px; border-radius: 8px; border: 1.5px solid #E8EEF7;
    background: #fff; font-size: 0.938rem; color: #5A6A8A; cursor: pointer;
    display: flex; align-items: center; justify-content: center; text-decoration: none;
    transition: all .15s;
}
.cr-sch-page-btn:hover:not(:disabled) { border-color: #F4B400; color: #1A2340; }
.cr-sch-page-btn:disabled { opacity: 0.35; cursor: default; }
.cr-sch-page-num {
    min-width: 32px; height: 32px; border-radius: 8px; background: #F0F4FF;
    font-family: 'Plus Jakarta Sans', sans-serif; font-size: 0.813rem; font-weight: 700;
    color: #5A6A8A; display: flex; align-items: center; justify-content: center;
    text-decoration: none; transition: all .15s;
}
.cr-sch-page-num--active { background: #1A2340; color: #fff; }

/* ── Weekly View ── */
.cr-sch-week-grid {
    display: grid; grid-template-columns: repeat(5, 1fr); gap: 10px; margin-bottom: 32px;
}
.cr-sch-week-col { display: flex; flex-direction: column; gap: 8px; }
.cr-sch-week-col__head {
    padding: 10px; background: #1A2340; color: #fff; border-radius: 10px; text-align: center;
    font-family: 'Plus Jakarta Sans', sans-serif; font-size: 0.813rem; font-weight: 700;
}
.cr-sch-week-slot {
    padding: 10px 12px; background: #fff; border: 1.5px solid #EEF2FB; border-radius: 10px;
    border-left: 3px solid #F4B400;
}
.cr-sch-week-slot__time {
    font-family: 'DM Sans', sans-serif; font-size: 0.688rem; color: #9AAFC8; margin: 0 0 3px;
}
.cr-sch-week-slot__mk {
    font-family: 'Plus Jakarta Sans', sans-serif; font-size: 0.775rem; font-weight: 700;
    color: #1A2340; margin: 0 0 2px;
}
.cr-sch-week-slot__room {
    font-family: 'DM Sans', sans-serif; font-size: 0.688rem; color: #5A6A8A; margin: 0;
}
.cr-sch-week-empty {
    padding: 10px; text-align: center; font-family: 'DM Sans', sans-serif;
    font-size: 0.75rem; color: #D0D9EE;
}

/* ============================================================
   MODAL
   ============================================================ */
.cr-sch-modal-overlay {
    position: fixed; inset: 0; background: rgba(26,35,64,0.45);
    backdrop-filter: blur(4px); z-index: 1000;
    display: flex; align-items: center; justify-content: center;
    padding: 20px;
}
.cr-sch-modal {
    background: #fff; border-radius: 18px; width: 100%; max-width: 420px;
    box-shadow: 0 20px 60px rgba(26,35,64,0.18);
    animation: modalIn .2s ease;
}
@keyframes modalIn {
    from { opacity: 0; transform: scale(0.95) translateY(10px); }
    to   { opacity: 1; transform: scale(1) translateY(0); }
}

.cr-sch-modal__head {
    display: flex; align-items: center; gap: 12px;
    padding: 18px 20px 14px; border-bottom: 1px solid #EEF2FB;
}
.cr-sch-modal__close {
    font-size: 0.875rem; color: #9AAFC8; cursor: pointer; flex-shrink: 0;
    width: 24px; height: 24px; display: flex; align-items: center; justify-content: center;
    border-radius: 6px; transition: background .15s;
}
.cr-sch-modal__close:hover { background: #FFE4E9; color: #FF4D6D; }
.cr-sch-modal__title {
    font-family: 'Plus Jakarta Sans', sans-serif; font-size: 1rem; font-weight: 800; color: #1A2340; margin: 0;
}

.cr-sch-modal__body { padding: 18px 20px; display: flex; flex-direction: column; gap: 14px; }
.cr-sch-modal__field { display: flex; flex-direction: column; gap: 5px; }
.cr-sch-modal__label {
    font-family: 'Plus Jakarta Sans', sans-serif; font-size: 0.788rem; font-weight: 700; color: #1A2340;
}
.cr-sch-modal__input {
    padding: 10px 12px; border: 1.5px solid #E8EEF7; border-radius: 9px;
    font-family: 'DM Sans', sans-serif; font-size: 0.875rem; color: #1A2340; background: #FAFBFF;
    outline: none; transition: border-color .2s;
}
.cr-sch-modal__input:focus { border-color: #F4B400; box-shadow: 0 0 0 3px rgba(244,180,0,.08); background: #fff; }
.cr-sch-modal__input--time { text-align: center; }
.cr-sch-modal__select-wrap { position: relative; }
.cr-sch-modal__select {
    width: 100%; padding: 10px 30px 10px 12px; border: 1.5px solid #E8EEF7; border-radius: 9px;
    font-family: 'DM Sans', sans-serif; font-size: 0.875rem; color: #1A2340; background: #FAFBFF;
    outline: none; -webkit-appearance: none; -moz-appearance: none; appearance: none; cursor: pointer; transition: border-color .2s;
}
.cr-sch-modal__select:focus { border-color: #F4B400; }
.cr-sch-modal__caret {
    position: absolute; right: 10px; top: 50%; transform: translateY(-50%);
    font-size: 0.7rem; color: #9AAFC8; pointer-events: none;
}
.cr-sch-modal__row { display: flex; align-items: flex-end; gap: 8px; }
.cr-sch-modal__row .cr-sch-modal__field { flex: 1; }
.cr-sch-modal__sep { font-size: 1.1rem; color: #9AAFC8; padding-bottom: 10px; flex-shrink: 0; }

/* Cek bentrok status */
.cr-sch-modal__cek {
    display: flex; align-items: center; gap: 8px;
    padding: 10px 14px; border-radius: 9px;
    background: #FFF3CD; border: 1px solid #FFE9A8;
    font-family: 'DM Sans', sans-serif; font-size: 0.813rem; color: #E6820A;
    transition: background .2s, border-color .2s;
}
.cr-sch-modal__cek.ok { background: #D1FAF0; border-color: rgba(0,200,150,.25); color: #00C896; }
.cr-sch-modal__cek.conflict { background: #FFF0F3; border-color: #FFD0D8; color: #FF4D6D; }
.cr-sch-modal__cek.checking { background: #F8FAFF; border-color: #EEF2FB; color: #9AAFC8; }

.cr-sch-modal__footer {
    padding: 14px 20px 18px; display: flex; gap: 10px; border-top: 1px solid #EEF2FB;
}
.cr-sch-modal__btn-batal {
    flex: 1; padding: 10px; border: 1.5px solid #E8EEF7; border-radius: 9px;
    background: #fff; font-family: 'Plus Jakarta Sans', sans-serif; font-size: 0.813rem;
    font-weight: 700; color: #5A6A8A; cursor: pointer; transition: border-color .15s;
}
.cr-sch-modal__btn-batal:hover { border-color: #FF4D6D; color: #FF4D6D; }
.cr-sch-modal__btn-simpan {
    flex: 2; padding: 10px; border: none; border-radius: 9px;
    background: linear-gradient(135deg, #F4B400 0%, #FFB020 100%); color: #1A2340;
    font-family: 'Plus Jakarta Sans', sans-serif; font-size: 0.875rem; font-weight: 800;
    cursor: pointer; box-shadow: 0 3px 10px rgba(244,180,0,.28); transition: transform .15s;
}
.cr-sch-modal__btn-simpan:hover { transform: scale(1.02); }

@media (max-width: 768px) {
    .cr-sch-week-grid { grid-template-columns: repeat(3, 1fr); overflow-x: auto; }
    .cr-sch-search-row { flex-direction: column; }
}
</style>

<script>
(function() {
    const overlay  = document.getElementById('modalOverlay');
    const modal    = document.getElementById('modal');
    const form     = document.getElementById('modalForm');
    const title    = document.getElementById('modalTitle');
    const btnClose = document.getElementById('btnCloseModal');
    const btnBatal = document.getElementById('btnBatal');
    const btnTambah= document.getElementById('btnTambah');

    // fields
    const fId    = document.getElementById('fieldScheduleId');
    const fMK    = document.getElementById('fieldMataKuliah');
    const fDosen = document.getElementById('fieldDosen');
    const fRoom  = document.getElementById('fieldRuangan');
    const fHari  = document.getElementById('fieldHari');
    const fMulai = document.getElementById('fieldJamMulai');
    const fSel   = document.getElementById('fieldJamSelesai');
    const fMethod= document.getElementById('formMethod');
    const cekBox = document.getElementById('cekStatus');
    const cekTxt = document.getElementById('cekText');

    const token  = document.querySelector('meta[name="csrf-token"]')?.content;
    let bentrokTimer;

    // ── Open modal (Tambah) ──
    btnTambah.addEventListener('click', () => openAddModal());

    function openAddModal() {
        title.textContent = 'Tambah Jadwal Kuliah';
        form.action = '/schedules/store';
        fMethod.value = 'POST';
        fId.value = '';
        fMK.value = ''; fDosen.value = ''; fRoom.value = '';
        fHari.value = ''; fMulai.value = '08:00'; fSel.value = '10:00';
        setCekStatus('idle', '⚠️ Cek otomatis: Tidak ada bentrok.');
        overlay.style.display = 'flex';
    }

    // ── Open modal (Edit) — dipanggil dari tombol baris tabel ──
    window.openEditModal = function(id, mk, dosen, roomId, hari, mulai, selesai) {
        title.textContent = 'Edit Jadwal Kuliah';
        form.action = '/schedules/update/' + id;
        fMethod.value = 'PUT';
        fId.value = id;
        fMK.value = mk;
        fDosen.value = dosen;
        fRoom.value = roomId;
        fHari.value = hari;
        fMulai.value = mulai;
        fSel.value = selesai;
        setCekStatus('idle', '⚠️ Cek otomatis: Tidak ada bentrok.');
        overlay.style.display = 'flex';
        triggerCek();
    };

    // ── Close modal ──
    [btnClose, btnBatal].forEach(btn => btn.addEventListener('click', closeModal));
    overlay.addEventListener('click', e => { if (e.target === overlay) closeModal(); });
    function closeModal() { overlay.style.display = 'none'; }

    // ── Cek bentrok realtime ──
    [fRoom, fHari, fMulai, fSel].forEach(el => el.addEventListener('change', triggerCek));

    function triggerCek() {
        clearTimeout(bentrokTimer);
        bentrokTimer = setTimeout(cekBentrok, 400);
    }

    function cekBentrok() {
        const roomId = fRoom.value, hari = fHari.value, mulai = fMulai.value, selesai = fSel.value;
        if (!roomId || !hari || !mulai || !selesai) {
            setCekStatus('idle', '⚠️ Lengkapi data untuk cek bentrok...');
            return;
        }
        setCekStatus('checking', '⏳ Mengecek...');

        const body = { room_id: roomId, hari, jam_mulai: mulai, jam_selesai: selesai };
        if (fId.value) body.exclude_id = fId.value;

        fetch('/schedules/cek-bentrok', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': token, 'Accept': 'application/json' },
            body: JSON.stringify(body),
        })
        .then(r => r.json())
        .then(d => {
            if (d.status === 'ok') {
                setCekStatus('ok', '✅ Cek otomatis: ' + d.message);
            } else {
                setCekStatus('conflict', '❌ Bentrok: ' + d.message);
            }
        })
        .catch(() => setCekStatus('idle', '⚠️ Gagal cek bentrok.'));
    }

    function setCekStatus(type, msg) {
        cekBox.className = 'cr-sch-modal__cek ' + type;
        cekTxt.textContent = msg;
        const icon = cekBox.querySelector('.cr-sch-modal__cek-icon');
        if (icon) icon.textContent = type === 'ok' ? '✅' : type === 'conflict' ? '❌' : type === 'checking' ? '⏳' : '⚠️';
    }

    // ── View toggle ──
    const btnTbl = document.getElementById('btnViewTabel');
    const btnMgg = document.getElementById('btnViewMingguan');
    const vTbl   = document.getElementById('viewTabel');
    const vMgg   = document.getElementById('viewMingguan');

    btnTbl.addEventListener('click', () => {
        vTbl.style.display = ''; vMgg.style.display = 'none';
        btnTbl.classList.add('cr-sch-view-btn--active');
        btnMgg.classList.remove('cr-sch-view-btn--active');
    });
    btnMgg.addEventListener('click', () => {
        vTbl.style.display = 'none'; vMgg.style.display = '';
        btnMgg.classList.add('cr-sch-view-btn--active');
        btnTbl.classList.remove('cr-sch-view-btn--active');
    });
})();
</script>

@endsection