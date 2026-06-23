@extends('layouts.dashboard')

@section('title', 'Form Booking Kegiatan')

@section('content')
<div class="cr-dash-content cr-bk-content">

    {{-- Brand pill --}}
    <div class="cr-dash-brand-pill">
        <span class="cr-dash-brand-pill__icon">🟨</span>
        <span class="cr-dash-brand-pill__text">CampusRoom • v1.0 Mahasiswa</span>
    </div>

    {{-- Info banner (collapsible) --}}
    <div class="cr-kg-info" id="infoBanner">
        <div class="cr-kg-info__head" id="infoHead">
            <span class="cr-kg-info__icon">⚠️</span>
            <div class="cr-kg-info__text">
                <strong>Booking kegiatan memerlukan:</strong>
                <ol>
                    <li>Pengajuan minimal H-2 sebelum tanggal kegiatan</li>
                    <li>Surat peminjaman yang ditandatangani Kaprodi</li>
                    <li>Persetujuan Admin (tidak auto-approve)</li>
                    <li>Bisa memilih lebih dari satu ruangan sekaligus</li>
                </ol>
            </div>
            <span class="cr-kg-info__toggle" id="infoToggle">▾ Collapsible</span>
        </div>
    </div>

    {{-- Stepper --}}
    <div class="cr-bk-stepper">
        <div class="cr-bk-step cr-bk-step--active">
            <span class="cr-bk-step__label">[1. Data]</span>
        </div>
        <div class="cr-bk-step__line"></div>
        <div class="cr-bk-step">
            <span class="cr-bk-step__label">[2. Konfirmasi]</span>
        </div>
        <div class="cr-bk-step__line"></div>
        <div class="cr-bk-step">
            <span class="cr-bk-step__label">[3. Selesai]</span>
        </div>
    </div>

    {{-- Error --}}
    @if(session('error'))
    <div class="cr-bk-alert cr-bk-alert--error">⚠️ {{ session('error') }}</div>
    @endif
    @if($errors->any())
    <div class="cr-bk-alert cr-bk-alert--error">⚠️ {{ $errors->first() }}</div>
    @endif

    <form action="/booking/kegiatan/konfirmasi" method="POST" id="formKegiatan" enctype="multipart/form-data">
        @csrf

        {{-- ======== RUANGAN YANG DIPILIH (MULTI-ROOM) ======== --}}
        <section class="cr-bk-section">
            <h2 class="cr-bk-section__title">
                📌 Pilih Ruangan
                <span class="cr-kg-multi-tag">Bisa pilih lebih dari satu</span>
            </h2>

            <div class="cr-kg-room-grid" id="roomGrid">
                @foreach($rooms as $room)
                <label class="cr-kg-room-option" data-room-id="{{ $room->room_id }}">
                    <input type="checkbox" name="room_ids[]" value="{{ $room->room_id }}"
                        class="cr-kg-room-checkbox"
                        data-nama="{{ $room->nama_ruangan }}"
                        {{ $selectedRoomId == $room->room_id ? 'checked' : '' }}>
                    <div class="cr-kg-room-option__img">
                        <svg viewBox="0 0 80 60" fill="none" xmlns="http://www.w3.org/2000/svg" style="width:100%;height:auto;">
                            <rect width="80" height="60" fill="#E8F4F8" rx="4"/>
                            <rect x="6" y="6" width="68" height="30" rx="2" fill="#F0F8FF" stroke="#B0D4E8" stroke-width="1"/>
                            <rect x="10" y="42" width="10" height="6" rx="1.5" fill="#C8DDE8"/>
                            <rect x="24" y="42" width="10" height="6" rx="1.5" fill="#C8DDE8"/>
                            <rect x="38" y="42" width="10" height="6" rx="1.5" fill="#C8DDE8"/>
                            <rect x="52" y="42" width="10" height="6" rx="1.5" fill="#C8DDE8"/>
                            <rect x="10" y="50" width="10" height="6" rx="1.5" fill="#C8DDE8"/>
                            <rect x="24" y="50" width="10" height="6" rx="1.5" fill="#C8DDE8"/>
                            <rect x="38" y="50" width="10" height="6" rx="1.5" fill="#C8DDE8"/>
                            <rect x="52" y="50" width="10" height="6" rx="1.5" fill="#C8DDE8"/>
                            <rect x="66" y="42" width="8" height="14" rx="1.5" fill="#A8C8D8"/>
                        </svg>
                    </div>
                    <div class="cr-kg-room-option__info">
                        <p class="cr-kg-room-option__name">{{ $room->nama_ruangan }}</p>
                        <p class="cr-kg-room-option__meta">👥 {{ $room->kapasitas }} peserta</p>
                    </div>
                    <div class="cr-kg-room-option__check">
                        <span class="cr-kg-room-option__check-icon">✓</span>
                    </div>
                    <span class="cr-kg-room-option__avail" data-avail-for="{{ $room->room_id }}"></span>
                </label>
                @endforeach
            </div>

            <p class="cr-kg-room-empty-warn" id="roomEmptyWarn" style="display:none">
                ⚠️ Pilih minimal satu ruangan.
            </p>
        </section>

        {{-- ======== DATA KEGIATAN ======== --}}
        <section class="cr-bk-section">
            <h2 class="cr-bk-section__title">📋 Data Kegiatan</h2>

            <div class="cr-bk-form-grid">

                {{-- Nama Kegiatan --}}
                <div class="cr-bk-field">
                    <label class="cr-bk-label">Nama Kegiatan</label>
                    <input type="text" name="nama_kegiatan" class="cr-bk-input"
                        placeholder="Contoh: Seminar UI/UX Nasional"
                        value="{{ old('nama_kegiatan') }}" required>
                </div>

                {{-- Penyelenggara --}}
                <div class="cr-bk-field">
                    <label class="cr-bk-label">Penyelenggara</label>
                    <input type="text" name="penyelenggara" class="cr-bk-input"
                        placeholder="Contoh: Himpunan Mahasiswa Informatika"
                        value="{{ old('penyelenggara') }}" required>
                </div>

                {{-- Tanggal Mulai & Selesai --}}
                <div class="cr-bk-field">
                    <label class="cr-bk-label">Tanggal Mulai &amp; Tanggal Selesai</label>
                    <div class="cr-bk-time-row">
                        <div class="cr-bk-time-wrap">
                            <input type="date" name="tanggal" id="inputTanggal" class="cr-bk-input"
                                min="{{ $minTanggal }}"
                                value="{{ old('tanggal') }}" required>
                        </div>
                        <span class="cr-bk-time-sep">—</span>
                        <div class="cr-bk-time-wrap">
                            <input type="date" name="tanggal_selesai" id="inputTanggalSelesai" class="cr-bk-input"
                                min="{{ $minTanggal }}"
                                value="{{ old('tanggal_selesai') }}" required>
                        </div>
                    </div>
                    {{-- H-2 warning area --}}
                    <div class="cr-kg-h2-warning" id="h2Warning" style="display:none">
                        <p class="cr-kg-h2-warning__text">
                            ❌ Tanggal terlalu dekat! Pengajuan minimal H-2.
                        </p>
                        <p class="cr-kg-h2-warning__sub">
                            Tanggal paling awal yang bisa dipilih: <strong>{{ \Carbon\Carbon::parse($minTanggal)->locale('id')->translatedFormat('l, d F Y') }}</strong>
                        </p>
                    </div>
                </div>

                {{-- Waktu Mulai & Selesai --}}
                <div class="cr-bk-field">
                    <label class="cr-bk-label">Waktu Mulai &amp; Waktu Selesai</label>
                    <div class="cr-bk-time-row">
                        <div class="cr-bk-time-wrap">
                            <input type="time" name="jam_mulai" id="inputJamMulai" class="cr-bk-input cr-bk-input--time"
                                value="{{ old('jam_mulai', '08:00') }}" required>
                        </div>
                        <span class="cr-bk-time-sep">—</span>
                        <div class="cr-bk-time-wrap">
                            <input type="time" name="jam_selesai" id="inputJamSelesai" class="cr-bk-input cr-bk-input--time"
                                value="{{ old('jam_selesai', '10:00') }}" required>
                        </div>
                    </div>
                </div>

                {{-- Deskripsi --}}
                <div class="cr-bk-field">
                    <label class="cr-bk-label">Deskripsi Kegiatan</label>
                    <textarea name="deskripsi" class="cr-bk-input cr-bk-textarea"
                        placeholder="Deskripsi Kegiatan">{{ old('deskripsi') }}</textarea>
                </div>

                {{-- Perkiraan Peserta --}}
                <div class="cr-bk-field">
                    <label class="cr-bk-label">Perkiraan Peserta</label>
                    <input type="number" name="perkiraan_peserta" class="cr-bk-input" min="1"
                        placeholder="Contoh: 10"
                        value="{{ old('perkiraan_peserta', 10) }}" required>
                </div>

            </div>
        </section>

        {{-- ======== UPLOAD SURAT ======== --}}
        <section class="cr-bk-section">
            <h2 class="cr-bk-section__title">📄 Upload Surat Peminjaman</h2>
            <p class="cr-kg-upload-sub">Format: PDF/JPG/PNG, Maks 5MB</p>

            <label for="suratInput" class="cr-kg-upload-box" id="uploadBox">
                <span class="cr-kg-upload-icon">⬆️</span>
                <span class="cr-kg-upload-text" id="uploadText">Seret file ke sini atau klik untuk pilih</span>
                <span class="cr-kg-upload-btn">Browse File</span>
                <input type="file" name="surat" id="suratInput" accept=".pdf,.jpg,.jpeg,.png" class="cr-kg-upload-input" required>
            </label>

            <p class="cr-kg-template-hint">
                💡 Belum punya template?
                <a href="/booking/download-template" class="cr-kg-template-link">Download Template Surat</a>
            </p>
        </section>

        {{-- ======== CEK KETERSEDIAAN ======== --}}
        <section class="cr-bk-section">
            <h2 class="cr-bk-section__title">🔍 Cek Ketersediaan <span class="cr-kg-realtime-tag">(Real-time)</span></h2>

            <div class="cr-bk-avail-row">
                <div class="cr-bk-avail-status" id="availStatus">
                    <span class="cr-bk-avail-placeholder">🔎 Lengkapi data untuk cek ketersediaan...</span>
                </div>
                <div class="cr-kg-syarat-box">
                    <span>📄 Surat Kaprodi diperlukan</span>
                </div>
            </div>

            {{-- Per-room availability list --}}
            <div class="cr-kg-avail-list" id="availList"></div>

            <div class="cr-bk-conflict-box" id="conflictBox" style="display:none">
                <span class="cr-bk-conflict-icon">🔴</span>
                <div>
                    <p class="cr-bk-conflict-title">Konflik Warning! Seluruh booking akan ditolak.</p>
                    <p class="cr-bk-conflict-detail" id="conflictDetail"></p>
                </div>
            </div>
        </section>

        {{-- ======== AKSI ======== --}}
        <section class="cr-bk-section cr-bk-section--aksi">
            <h2 class="cr-bk-section__title">⚡ Aksi</h2>
            <div class="cr-bk-aksi-row">
                <button type="submit" class="cr-bk-btn-submit" id="btnSubmit">
                    Lanjut ke Konfirmasi
                </button>
                <a href="/booking" class="cr-bk-btn-batal">
                    <span>×</span> Batal
                </a>
            </div>
        </section>

    </form>

</div>

<style>
/* ===== Base section / form styles ===== */
.cr-bk-content { max-width: 760px; }

.cr-bk-stepper { display: flex; align-items: center; gap: 0; margin: 20px 0 28px; }
.cr-bk-step { display: flex; align-items: center; }
.cr-bk-step__label {
    font-family: 'Plus Jakarta Sans', sans-serif;
    font-size: 0.813rem;
    font-weight: 600;
    color: #9AAFC8;
}
.cr-bk-step--active .cr-bk-step__label {
    color: #1A2340; font-weight: 800;
    background: rgba(244,180,0,0.15);
    padding: 4px 10px; border-radius: 999px;
}
.cr-bk-step__line { flex: 1; height: 1px; min-width: 60px; background: #D0D9EE; margin: 0 12px; }

.cr-bk-alert {
    padding: 12px 16px; border-radius: 10px;
    font-family: 'Plus Jakarta Sans', sans-serif;
    font-size: 0.875rem; font-weight: 600; margin-bottom: 20px;
}
.cr-bk-alert--error { background: #FFF0F3; border: 1px solid #FFD0D8; color: #FF4D6D; }

.cr-bk-section {
    background: #FFFFFF; border-radius: 16px; padding: 22px 24px; margin-bottom: 16px;
    box-shadow: 0 2px 8px rgba(26,35,64,0.06); border: 1.5px solid #EEF2FB;
}
.cr-bk-section__title {
    font-family: 'Plus Jakarta Sans', sans-serif;
    font-size: 1rem; font-weight: 800; color: #1A2340; margin: 0 0 16px;
    display: flex; align-items: center; gap: 8px; flex-wrap: wrap;
}
.cr-kg-realtime-tag {
    font-size: 0.7rem; font-weight: 700; color: #4FC3F7;
    background: rgba(79,195,247,0.12); padding: 2px 8px; border-radius: 999px;
}
.cr-kg-multi-tag {
    font-size: 0.7rem; font-weight: 700; color: #F4B400;
    background: rgba(244,180,0,0.12); padding: 2px 8px; border-radius: 999px;
}

.cr-bk-form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
.cr-bk-field { display: flex; flex-direction: column; gap: 6px; }
.cr-bk-label { font-family: 'Plus Jakarta Sans', sans-serif; font-size: 0.813rem; font-weight: 600; color: #1A2340; }

.cr-bk-input {
    width: 100%; padding: 11px 14px; border: 1.5px solid #E8EEF7; border-radius: 10px;
    font-family: 'DM Sans', sans-serif; font-size: 0.875rem; color: #1A2340;
    background: #FAFBFF; outline: none; transition: border-color .2s, box-shadow .2s; appearance: none;
}
.cr-bk-input:focus { border-color: #F4B400; box-shadow: 0 0 0 3px rgba(244,180,0,.10); background: #fff; }
.cr-bk-input--time { padding: 9px 10px; text-align: center; }
.cr-bk-textarea { resize: vertical; min-height: 70px; font-family: 'DM Sans', sans-serif; }

.cr-bk-time-row { display: flex; align-items: center; gap: 8px; }
.cr-bk-time-wrap { flex: 1; }
.cr-bk-time-sep { font-family: 'DM Sans', sans-serif; font-size: 1rem; color: #9AAFC8; flex-shrink: 0; }

/* Availability */
.cr-bk-avail-row { display: flex; gap: 16px; flex-wrap: wrap; align-items: flex-start; margin-bottom: 12px; }
.cr-bk-avail-status {
    flex: 1; min-width: 200px; padding: 12px 16px; background: #F8FAFF;
    border: 1.5px solid #EEF2FB; border-radius: 10px;
    font-family: 'DM Sans', sans-serif; font-size: 0.838rem; color: #5A6A8A;
    min-height: 46px; display: flex; align-items: center; gap: 8px;
}
.cr-bk-avail-status.available { background: #D1FAF0; border-color: rgba(0,200,150,.25); color: #00C896; font-weight: 600; }
.cr-bk-avail-status.checking  { color: #9AAFC8; }

.cr-kg-syarat-box {
    flex: 1; min-width: 180px; padding: 12px 16px; background: #FFF3CD;
    border: 1.5px solid #FFE9A8; border-radius: 10px;
    font-family: 'Plus Jakarta Sans', sans-serif; font-size: 0.813rem; font-weight: 600; color: #E6820A;
    display: flex; align-items: center;
}

/* Per-room availability list */
.cr-kg-avail-list { display: flex; flex-direction: column; gap: 6px; margin-bottom: 12px; }
.cr-kg-avail-row {
    display: flex; align-items: center; justify-content: space-between; gap: 10px;
    padding: 8px 14px; border-radius: 8px; font-family: 'DM Sans', sans-serif; font-size: 0.8rem;
    background: #F8FAFF; border: 1px solid #EEF2FB;
}
.cr-kg-avail-row.ok { background: #F0FFFA; border-color: rgba(0,200,150,.2); color: #00C896; }
.cr-kg-avail-row.bad { background: #FFF0F3; border-color: rgba(255,77,109,.2); color: #FF4D6D; }
.cr-kg-avail-row__name { font-weight: 700; color: #1A2340; }
.cr-kg-avail-row.ok .cr-kg-avail-row__name,
.cr-kg-avail-row.bad .cr-kg-avail-row__name { color: inherit; }

.cr-bk-conflict-box {
    padding: 12px 16px; background: #FFF0F3; border: 1.5px solid #FFD0D8; border-radius: 10px;
    display: flex; align-items: flex-start; gap: 10px;
}
.cr-bk-conflict-icon { font-size: 0.875rem; flex-shrink: 0; margin-top: 1px; }
.cr-bk-conflict-title { font-family: 'Plus Jakarta Sans', sans-serif; font-size: 0.813rem; font-weight: 700; color: #FF4D6D; margin: 0 0 2px; }
.cr-bk-conflict-detail { font-family: 'DM Sans', sans-serif; font-size: 0.75rem; color: #FF4D6D; margin: 0; }

/* Aksi */
.cr-bk-section--aksi { background: transparent; box-shadow: none; border: none; padding: 0; }
.cr-bk-aksi-row { display: flex; align-items: center; gap: 12px; }
.cr-bk-btn-submit {
    padding: 13px 28px; background: linear-gradient(135deg, #F4B400 0%, #FFB020 100%); color: #1A2340;
    font-family: 'Plus Jakarta Sans', sans-serif; font-size: 0.938rem; font-weight: 700;
    border: none; border-radius: 10px; cursor: pointer;
    box-shadow: 0 3px 12px rgba(244,180,0,.30); transition: transform .15s, box-shadow .15s;
}
.cr-bk-btn-submit:hover { transform: scale(1.02); box-shadow: 0 6px 20px rgba(244,180,0,.40); }
.cr-bk-btn-submit:disabled { background: #EEF2FB; color: #9AAFC8; box-shadow: none; cursor: not-allowed; transform: none; }

.cr-bk-btn-batal {
    display: inline-flex; align-items: center; gap: 6px; padding: 12px 20px; background: #fff;
    border: 1.5px solid #E8EEF7; border-radius: 10px;
    font-family: 'Plus Jakarta Sans', sans-serif; font-size: 0.875rem; font-weight: 600; color: #5A6A8A;
    text-decoration: none; transition: border-color .15s, color .15s;
}
.cr-bk-btn-batal:hover { border-color: #FF4D6D; color: #FF4D6D; }

/* ============================================================
   KEGIATAN SPECIFIC
   ============================================================ */

/* Info banner */
.cr-kg-info {
    background: #FFF3CD;
    border: 1.5px solid #FFE9A8;
    border-radius: 14px;
    margin-bottom: 16px;
    overflow: hidden;
}
.cr-kg-info__head { display: flex; align-items: flex-start; gap: 12px; padding: 14px 18px; cursor: pointer; }
.cr-kg-info__icon { font-size: 1.1rem; flex-shrink: 0; }
.cr-kg-info__text { flex: 1; font-family: 'DM Sans', sans-serif; font-size: 0.838rem; color: #92660A; }
.cr-kg-info__text strong { font-family: 'Plus Jakarta Sans', sans-serif; font-weight: 800; color: #1A2340; }
.cr-kg-info__text ol { margin: 6px 0 0; padding-left: 20px; }
.cr-kg-info__text li { margin-bottom: 2px; }
.cr-kg-info__toggle {
    font-family: 'Plus Jakarta Sans', sans-serif; font-size: 0.75rem; font-weight: 700;
    color: #92660A; white-space: nowrap; flex-shrink: 0; transition: transform .2s;
}
.cr-kg-info.collapsed .cr-kg-info__text { display: none; }
.cr-kg-info.collapsed .cr-kg-info__toggle { transform: rotate(-90deg); }

/* H-2 warning */
.cr-kg-h2-warning {
    margin-top: 10px; padding: 10px 14px;
    background: #FFE4E9; border: 1.5px solid #FFC0CB; border-radius: 10px;
}
.cr-kg-h2-warning__text {
    font-family: 'Plus Jakarta Sans', sans-serif; font-size: 0.813rem; font-weight: 700; color: #FF4D6D; margin: 0 0 4px;
}
.cr-kg-h2-warning__sub {
    font-family: 'DM Sans', sans-serif; font-size: 0.75rem; color: #C0394F; margin: 0;
}
.cr-kg-h2-warning__sub strong { font-weight: 700; }

/* Upload box */
.cr-kg-upload-sub {
    font-family: 'DM Sans', sans-serif; font-size: 0.75rem; color: #9AAFC8; margin: -8px 0 14px;
}
.cr-kg-upload-box {
    display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 8px;
    border: 2px dashed #D0D9EE; border-radius: 12px; padding: 28px 16px;
    background: #F8FAFF; cursor: pointer; transition: border-color .2s, background .2s; position: relative;
}
.cr-kg-upload-box:hover { border-color: #F4B400; background: #FFFBF0; }
.cr-kg-upload-box.has-file { border-color: #00C896; background: #F0FFFA; }
.cr-kg-upload-icon { font-size: 1.5rem; }
.cr-kg-upload-text {
    font-family: 'DM Sans', sans-serif; font-size: 0.838rem; color: #5A6A8A; text-align: center;
}
.cr-kg-upload-btn {
    margin-top: 4px; padding: 8px 22px; background: #1A2340; color: #fff;
    font-family: 'Plus Jakarta Sans', sans-serif; font-size: 0.813rem; font-weight: 700; border-radius: 8px;
}
.cr-kg-upload-input {
    position: absolute; width: 100%; height: 100%; top: 0; left: 0; opacity: 0; cursor: pointer;
}
.cr-kg-template-hint {
    font-family: 'DM Sans', sans-serif; font-size: 0.75rem; color: #9AAFC8; margin: 12px 0 0;
}
.cr-kg-template-link {
    color: #4FC3F7; font-weight: 700; text-decoration: underline;
}
.cr-kg-template-link:hover { color: #0277BD; }

/* ===== Multi-room checkbox grid ===== */
.cr-kg-room-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 12px;
}
.cr-kg-room-option {
    position: relative;
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 12px;
    background: #F8FAFF;
    border: 1.5px solid #EEF2FB;
    border-radius: 12px;
    cursor: pointer;
    transition: border-color .15s, background .15s, box-shadow .15s;
}
.cr-kg-room-option:hover {
    border-color: rgba(244,180,0,.35);
}
.cr-kg-room-checkbox {
    position: absolute;
    opacity: 0;
    width: 0; height: 0;
}
.cr-kg-room-option__img {
    width: 56px; height: 42px; border-radius: 8px; overflow: hidden; background: #E8F4F8; flex-shrink: 0;
}
.cr-kg-room-option__info { flex: 1; min-width: 0; }
.cr-kg-room-option__name {
    font-family: 'Plus Jakarta Sans', sans-serif; font-size: 0.875rem; font-weight: 700; color: #1A2340; margin: 0 0 2px;
}
.cr-kg-room-option__meta {
    font-family: 'DM Sans', sans-serif; font-size: 0.75rem; color: #5A6A8A; margin: 0;
}
.cr-kg-room-option__check {
    width: 22px; height: 22px; border-radius: 6px; border: 1.5px solid #D0D9EE;
    background: #fff; display: flex; align-items: center; justify-content: center; flex-shrink: 0;
    transition: background .15s, border-color .15s;
}
.cr-kg-room-option__check-icon {
    font-size: 0.75rem; color: #fff; opacity: 0; transition: opacity .15s; font-weight: 800;
}

/* Checked state */
.cr-kg-room-checkbox:checked ~ .cr-kg-room-option__check {
    background: linear-gradient(135deg, #F4B400 0%, #FFB020 100%);
    border-color: transparent;
}
.cr-kg-room-checkbox:checked ~ .cr-kg-room-option__check .cr-kg-room-option__check-icon {
    opacity: 1;
}
.cr-kg-room-option:has(.cr-kg-room-checkbox:checked) {
    border-color: #F4B400;
    background: #FFFBF0;
    box-shadow: 0 0 0 3px rgba(244,180,0,.10);
}

/* Per-room availability badge on card */
.cr-kg-room-option__avail {
    position: absolute;
    bottom: 8px;
    right: 12px;
    font-size: 0.65rem;
    font-weight: 700;
    font-family: 'Plus Jakarta Sans', sans-serif;
    padding: 2px 8px;
    border-radius: 999px;
    display: none;
}
.cr-kg-room-option__avail.show-ok {
    display: inline-block;
    background: #D1FAF0; color: #00C896;
}
.cr-kg-room-option__avail.show-bad {
    display: inline-block;
    background: #FFE4E9; color: #FF4D6D;
}

.cr-kg-room-empty-warn {
    margin: 12px 0 0;
    font-family: 'Plus Jakarta Sans', sans-serif; font-size: 0.813rem; font-weight: 700; color: #FF4D6D;
}

@media (max-width: 600px) {
    .cr-bk-form-grid { grid-template-columns: 1fr; }
    .cr-kg-room-grid { grid-template-columns: 1fr; }
}
</style>

<script>
(function () {
    // ─── Info banner collapse ───────────────────────────────────
    const infoBanner = document.getElementById('infoBanner');
    const infoHead   = document.getElementById('infoHead');
    infoHead.addEventListener('click', () => infoBanner.classList.toggle('collapsed'));

    // ─── Elemen ───────────────────────────────────────────────
    const roomCheckboxes = document.querySelectorAll('.cr-kg-room-checkbox');
    const roomEmptyWarn  = document.getElementById('roomEmptyWarn');

    const inputTanggal        = document.getElementById('inputTanggal');
    const inputTanggalSelesai = document.getElementById('inputTanggalSelesai');
    const inputMulai          = document.getElementById('inputJamMulai');
    const inputSelesai        = document.getElementById('inputJamSelesai');
    const h2Warning           = document.getElementById('h2Warning');

    const availStatus   = document.getElementById('availStatus');
    const availList     = document.getElementById('availList');
    const conflictBox   = document.getElementById('conflictBox');
    const conflictDetail= document.getElementById('conflictDetail');
    const btnSubmit     = document.getElementById('btnSubmit');

    const minTanggal = "{{ $minTanggal }}";

    let debounceTimer = null;

    // ─── Validasi H-2 ───────────────────────────────────────────
    function cekH2() {
        if (!inputTanggal.value) {
            h2Warning.style.display = 'none';
            return true;
        }
        if (inputTanggal.value < minTanggal) {
            h2Warning.style.display = 'block';
            return false;
        }
        h2Warning.style.display = 'none';
        return true;
    }

    inputTanggal.addEventListener('change', () => {
        if (inputTanggalSelesai.value && inputTanggalSelesai.value < inputTanggal.value) {
            inputTanggalSelesai.value = inputTanggal.value;
        }
        inputTanggalSelesai.min = inputTanggal.value || minTanggal;
        cekH2();
        triggerCek();
    });

    // ─── Pilih ruangan (multi) trigger cek ────────────────────
    roomCheckboxes.forEach(cb => cb.addEventListener('change', () => {
        // reset badge ruangan yg di-uncheck
        if (!cb.checked) {
            const badge = document.querySelector('.cr-kg-room-option__avail[data-avail-for="' + cb.value + '"]');
            if (badge) { badge.className = 'cr-kg-room-option__avail'; badge.textContent = ''; }
        }
        triggerCek();
    }));

    // ─── Cek Ketersediaan realtime (multi-room) ────────────────
    function triggerCek() {
        clearTimeout(debounceTimer);
        debounceTimer = setTimeout(cekKetersediaan, 500);
    }
    [inputTanggalSelesai, inputMulai, inputSelesai].forEach(el => {
        if (el) el.addEventListener('change', triggerCek);
    });

    function getSelectedRoomIds() {
        return Array.from(roomCheckboxes).filter(cb => cb.checked).map(cb => cb.value);
    }

    function cekKetersediaan() {
        const roomIds   = getSelectedRoomIds();
        const tanggal   = inputTanggal.value;
        const tanggalSelesai = inputTanggalSelesai.value;
        const jamMulai  = inputMulai.value;
        const jamSelesai= inputSelesai.value;

        availList.innerHTML = '';

        if (roomIds.length === 0 || !tanggal || !jamMulai || !jamSelesai) {
            availStatus.className = 'cr-bk-avail-status';
            availStatus.innerHTML = '<span class="cr-bk-avail-placeholder">🔎 Pilih ruangan & lengkapi data untuk cek ketersediaan...</span>';
            conflictBox.style.display = 'none';
            return;
        }

        availStatus.className = 'cr-bk-avail-status checking';
        availStatus.innerHTML = '⏳ Mengecek ketersediaan ' + roomIds.length + ' ruangan...';
        conflictBox.style.display = 'none';

        const token = document.querySelector('meta[name="csrf-token"]')?.content;

        fetch('/booking/cek-ketersediaan-multi', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': token, 'Accept': 'application/json' },
            body: JSON.stringify({ room_ids: roomIds, tanggal, tanggal_selesai: tanggalSelesai, jam_mulai: jamMulai, jam_selesai: jamSelesai }),
        })
        .then(r => r.json())
        .then(data => {
            if (data.status === 'incomplete') return;

            const conflicts = [];

            (data.rooms || []).forEach(r => {
                const badge = document.querySelector('.cr-kg-room-option__avail[data-avail-for="' + r.room_id + '"]');
                const row = document.createElement('div');

                if (r.status === 'available') {
                    if (badge) { badge.className = 'cr-kg-room-option__avail show-ok'; badge.textContent = '✅ Tersedia'; }
                    row.className = 'cr-kg-avail-row ok';
                    row.innerHTML = '<span class="cr-kg-avail-row__name">' + r.nama + '</span><span>✅ Tersedia</span>';
                } else {
                    if (badge) { badge.className = 'cr-kg-room-option__avail show-bad'; badge.textContent = '❌ Bentrok'; }
                    row.className = 'cr-kg-avail-row bad';
                    row.innerHTML = '<span class="cr-kg-avail-row__name">' + r.nama + '</span><span>❌ Bentrok ' + (r.detail || '') + '</span>';
                    conflicts.push(r.nama + (r.detail ? ' (' + r.detail + ')' : ''));
                }
                availList.appendChild(row);
            });

            if (conflicts.length === 0) {
                availStatus.className = 'cr-bk-avail-status available';
                availStatus.innerHTML = '✅ Semua ruangan tersedia pada slot yang dipilih!';
                conflictBox.style.display = 'none';
            } else {
                availStatus.className = 'cr-bk-avail-status';
                availStatus.innerHTML = '❌ Ada ruangan tidak tersedia';
                conflictDetail.textContent = conflicts.join(', ');
                conflictBox.style.display = 'flex';
            }
        })
        .catch(() => {
            availStatus.className = 'cr-bk-avail-status';
            availStatus.innerHTML = '⚠️ Gagal cek ketersediaan';
        });
    }

    // ─── Upload file UI ─────────────────────────────────────────
    const uploadBox   = document.getElementById('uploadBox');
    const uploadInput = document.getElementById('suratInput');
    const uploadText  = document.getElementById('uploadText');

    uploadInput.addEventListener('change', () => {
        if (uploadInput.files.length > 0) {
            uploadText.textContent = '📎 ' + uploadInput.files[0].name;
            uploadBox.classList.add('has-file');
        } else {
            uploadText.textContent = 'Seret file ke sini atau klik untuk pilih';
            uploadBox.classList.remove('has-file');
        }
    });

    // ─── Form submit guard: H-2 & minimal 1 ruangan ─────────────
    document.getElementById('formKegiatan').addEventListener('submit', function (e) {
        let valid = true;

        if (getSelectedRoomIds().length === 0) {
            roomEmptyWarn.style.display = 'block';
            valid = false;
        } else {
            roomEmptyWarn.style.display = 'none';
        }

        if (!cekH2()) {
            valid = false;
        }

        if (!valid) {
            e.preventDefault();
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }
    });

    triggerCek();
    cekH2();
})();
</script>
@endsection