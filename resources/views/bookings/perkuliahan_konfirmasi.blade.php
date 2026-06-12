@extends('layouts.dashboard')

@section('title', 'Konfirmasi Booking Perkuliahan')

@section('content')
<div class="cr-dash-content cr-bk-content">

    {{-- Brand pill --}}
    <div class="cr-dash-brand-pill">
        <span class="cr-dash-brand-pill__icon">🟨</span>
        <span class="cr-dash-brand-pill__text">CampusRoom • v1.0 Admin</span>
    </div>

    {{-- Page title --}}
    <h1 class="cr-bk-title">Form Booking Perkuliahan</h1>

    {{-- Stepper --}}
    <div class="cr-bk-stepper">
        <div class="cr-bk-step cr-bk-step--done">
            <span class="cr-bk-step__check">✓</span>
            <span class="cr-bk-step__label">[1. Data]</span>
        </div>
        <div class="cr-bk-step__line cr-bk-step__line--done"></div>
        <div class="cr-bk-step cr-bk-step--active">
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

    {{-- ======== RINGKASAN BOOKING ======== --}}
    <section class="cr-bk-section">
        <h2 class="cr-bk-section__title">🗂️ Ringkasan Booking</h2>

        <div class="cr-bk-room-card">
            <div class="cr-bk-room-card__img">
                <svg viewBox="0 0 80 60" fill="none" xmlns="http://www.w3.org/2000/svg" style="width:100%;height:auto;display:block;">
                    <rect width="80" height="60" fill="#E8F4F8" rx="4"/>
                    <rect x="8" y="8" width="50" height="22" rx="2" fill="#F0F8FF" stroke="#B0D4E8" stroke-width="1"/>
                    <rect x="10" y="36" width="14" height="6" rx="1.5" fill="#C8DDE8"/>
                    <rect x="28" y="36" width="14" height="6" rx="1.5" fill="#C8DDE8"/>
                    <rect x="46" y="36" width="14" height="6" rx="1.5" fill="#C8DDE8"/>
                    <rect x="10" y="46" width="14" height="6" rx="1.5" fill="#C8DDE8"/>
                    <rect x="28" y="46" width="14" height="6" rx="1.5" fill="#C8DDE8"/>
                    <rect x="46" y="46" width="14" height="6" rx="1.5" fill="#C8DDE8"/>
                    <rect x="62" y="36" width="12" height="8" rx="1.5" fill="#A8C8D8"/>
                </svg>
            </div>

            <div class="cr-bk-room-card__info">
                <p class="cr-bk-room-card__name">{{ $room->nama_ruangan }}</p>
                <div class="cr-bk-room-card__meta">
                    <span>👥 {{ $room->kapasitas }} peserta</span>
                    <span>🏢 Lantai {{ ($room->room_id % 3) + 1 }}</span>
                    <span>🏛️ Gedung A</span>
                    @if($room->status === 'tersedia')
                        <span class="cr-bk-badge cr-bk-badge--green">✅ Available</span>
                    @else
                        <span class="cr-bk-badge cr-bk-badge--red">❌ Tidak Tersedia</span>
                    @endif
                </div>
            </div>

            <a href="/booking/perkuliahan?room_id={{ $room->room_id }}" class="cr-bk-ganti-btn">Ganti Ruangan</a>
        </div>
    </section>

    {{-- ======== DETAIL DATA ======== --}}
    <section class="cr-bk-section">
        <div class="cr-bk-detail-grid">

            <div class="cr-bk-detail-item">
                <span class="cr-bk-detail-icon cr-bk-detail-icon--yellow">🎓</span>
                <div>
                    <p class="cr-bk-detail-label">Mata Kuliah</p>
                    <p class="cr-bk-detail-val">{{ $mata_kuliah }}</p>
                </div>
            </div>

            <div class="cr-bk-detail-item">
                <span class="cr-bk-detail-icon cr-bk-detail-icon--blue">👤</span>
                <div>
                    <p class="cr-bk-detail-label">Dosen</p>
                    <p class="cr-bk-detail-val">{{ $dosen }}</p>
                </div>
            </div>

            <div class="cr-bk-detail-item">
                <span class="cr-bk-detail-icon cr-bk-detail-icon--gray">🗒️</span>
                <div>
                    <p class="cr-bk-detail-label">Tanggal</p>
                    <p class="cr-bk-detail-val">{{ $tanggal_formatted }}</p>
                </div>
            </div>

            <div class="cr-bk-detail-item">
                <span class="cr-bk-detail-icon cr-bk-detail-icon--gray">🕐</span>
                <div>
                    <p class="cr-bk-detail-label">Waktu</p>
                    <p class="cr-bk-detail-val">
                        {{ substr($jam_mulai,0,5) }} – {{ substr($jam_selesai,0,5) }}
                        ({{ rtrim(rtrim(number_format($durasi,1,',','.'), '0'), ',') }} jam)
                    </p>
                </div>
            </div>

        </div>

        <div class="cr-bk-keterangan">
            <p class="cr-bk-keterangan__title">Keterangan</p>
            <p class="cr-bk-keterangan__text">
                Perkuliahan rutin. Mahasiswa diimbau hadir 15 menit sebelum waktu.
            </p>
        </div>
    </section>

    {{-- ======== STATUS ======== --}}
    <div class="cr-bk-status-box">
        <span>Status: <strong>AKAN DISETUJUI OTOMATIS</strong></span>
        <span class="cr-bk-status-icon">✅</span>
    </div>

    {{-- ======== AKSI ======== --}}
    <form action="/booking/perkuliahan/store" method="POST">
        @csrf
        <input type="hidden" name="room_id"     value="{{ $room->room_id }}">
        <input type="hidden" name="mata_kuliah" value="{{ $mata_kuliah }}">
        <input type="hidden" name="dosen"       value="{{ $dosen }}">
        <input type="hidden" name="tanggal"     value="{{ $tanggal }}">
        <input type="hidden" name="jam_mulai"   value="{{ $jam_mulai }}">
        <input type="hidden" name="jam_selesai" value="{{ $jam_selesai }}">

        <button type="submit" class="cr-bk-btn-konfirmasi">
            ✓ KONFIRMASI &amp; BOOKING
        </button>
    </form>

    <a href="/dashboard" class="cr-bk-btn-kembali">Kembali ke Beranda</a>

</div>

<style>
/* ============================================================
   BOOKING PERKULIAHAN PAGE
   ============================================================ */
.cr-bk-content { max-width: 760px; }

.cr-bk-title {
    font-family: 'Space Grotesk', sans-serif;
    font-size: 1.75rem;
    font-weight: 700;
    color: #1A2340;
    margin: 56px 0 16px;
    letter-spacing: -0.3px;
}

/* Stepper */
.cr-bk-stepper {
    display: flex;
    align-items: center;
    gap: 0;
    margin-bottom: 28px;
}
.cr-bk-step { display: flex; align-items: center; }
.cr-bk-step__label {
    font-family: 'Plus Jakarta Sans', sans-serif;
    font-size: 0.813rem;
    font-weight: 600;
    color: #9AAFC8;
}
.cr-bk-step--active .cr-bk-step__label { color: #1A2340; font-weight: 800; }
.cr-bk-step__line {
    flex: 1;
    height: 1px;
    min-width: 60px;
    background: #D0D9EE;
    margin: 0 12px;
}

/* Alert */
.cr-bk-alert {
    padding: 12px 16px;
    border-radius: 10px;
    font-family: 'Plus Jakarta Sans', sans-serif;
    font-size: 0.875rem;
    font-weight: 600;
    margin-bottom: 20px;
}
.cr-bk-alert--error   { background: #FFF0F3; border: 1px solid #FFD0D8; color: #FF4D6D; }
.cr-bk-alert--success { background: #D1FAF0; border: 1px solid #A0E8D8; color: #00C896; }

/* Section */
.cr-bk-section {
    background: #FFFFFF;
    border-radius: 16px;
    padding: 22px 24px;
    margin-bottom: 16px;
    box-shadow: 0 2px 8px rgba(26,35,64,0.06);
    border: 1.5px solid #EEF2FB;
}
.cr-bk-section__title {
    font-family: 'Plus Jakarta Sans', sans-serif;
    font-size: 1rem;
    font-weight: 800;
    color: #1A2340;
    margin: 0 0 16px;
}

/* Room card */
.cr-bk-room-card {
    display: flex;
    align-items: center;
    gap: 16px;
    padding: 14px;
    background: #F8FAFF;
    border: 1.5px solid #EEF2FB;
    border-radius: 12px;
}
.cr-bk-room-card__img {
    width: 90px;
    height: 66px;
    border-radius: 10px;
    overflow: hidden;
    background: #E8F4F8;
    flex-shrink: 0;
}
.cr-bk-room-card__info { flex: 1; }
.cr-bk-room-card__name {
    font-family: 'Plus Jakarta Sans', sans-serif;
    font-size: 1rem;
    font-weight: 700;
    color: #1A2340;
    margin: 0 0 6px;
}
.cr-bk-room-card__meta {
    display: flex;
    align-items: center;
    gap: 10px;
    flex-wrap: wrap;
    font-family: 'DM Sans', sans-serif;
    font-size: 0.75rem;
    color: #5A6A8A;
}
.cr-bk-badge {
    font-family: 'Plus Jakarta Sans', sans-serif;
    font-size: 0.7rem;
    font-weight: 700;
    padding: 2px 10px;
    border-radius: 999px;
}
.cr-bk-badge--green { background: #D1FAF0; color: #00C896; }
.cr-bk-badge--red   { background: #FFE4E9; color: #FF4D6D; }

.cr-bk-ganti-btn {
    font-family: 'Plus Jakarta Sans', sans-serif;
    font-size: 0.75rem;
    font-weight: 700;
    color: #4FC3F7;
    background: none;
    border: none;
    cursor: pointer;
    padding: 4px 0;
    flex-shrink: 0;
    text-decoration: underline;
    transition: color .15s;
}
.cr-bk-ganti-btn:hover { color: #0277BD; }

/* Room select */
.cr-bk-room-select-wrap {
    margin-top: 14px;
}

/* Form grid */
.cr-bk-form-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 16px;
}

/* Field */
.cr-bk-field { display: flex; flex-direction: column; gap: 6px; }
.cr-bk-label {
    font-family: 'Plus Jakarta Sans', sans-serif;
    font-size: 0.813rem;
    font-weight: 600;
    color: #1A2340;
}
.cr-bk-label-sm {
    font-family: 'Plus Jakarta Sans', sans-serif;
    font-size: 0.7rem;
    font-weight: 600;
    color: #5A6A8A;
    margin-bottom: 3px;
    display: block;
}

/* Input */
.cr-bk-input {
    width: 100%;
    padding: 11px 14px;
    border: 1.5px solid #E8EEF7;
    border-radius: 10px;
    font-family: 'DM Sans', sans-serif;
    font-size: 0.875rem;
    color: #1A2340;
    background: #FAFBFF;
    outline: none;
    transition: border-color .2s, box-shadow .2s;
    appearance: none;
}
.cr-bk-input:focus {
    border-color: #F4B400;
    box-shadow: 0 0 0 3px rgba(244,180,0,.10);
    background: #fff;
}
.cr-bk-input--time { padding: 9px 10px; text-align: center; }
.cr-bk-input-wrap { position: relative; }
.cr-bk-input-wrap--icon .cr-bk-input { padding-right: 38px; }
.cr-bk-input-icon {
    position: absolute;
    right: 10px;
    top: 50%;
    transform: translateY(-50%);
    font-size: 0.875rem;
    pointer-events: none;
}

/* Select */
.cr-bk-select-wrap { position: relative; }
.cr-bk-select {
    width: 100%;
    padding: 11px 32px 11px 14px;
    border: 1.5px solid #E8EEF7;
    border-radius: 10px;
    font-family: 'DM Sans', sans-serif;
    font-size: 0.875rem;
    color: #1A2340;
    background: #FAFBFF;
    outline: none;
    appearance: none;
    -webkit-appearance: none;
    cursor: pointer;
    transition: border-color .2s;
}
.cr-bk-select:focus { border-color: #F4B400; box-shadow: 0 0 0 3px rgba(244,180,0,.10); }
.cr-bk-select-caret {
    position: absolute;
    right: 10px;
    top: 50%;
    transform: translateY(-50%);
    font-size: 0.75rem;
    color: #9AAFC8;
    pointer-events: none;
}
.cr-bk-dropdown-list { display: none; } /* native select saja, dropdown custom opsional */

/* Time row */
.cr-bk-time-row {
    display: flex;
    align-items: flex-end;
    gap: 8px;
}
.cr-bk-time-wrap { flex: 1; }
.cr-bk-time-sep {
    font-family: 'DM Sans', sans-serif;
    font-size: 1rem;
    color: #9AAFC8;
    padding-bottom: 10px;
    flex-shrink: 0;
}

/* Availability */
.cr-bk-avail-row {
    display: flex;
    gap: 16px;
    flex-wrap: wrap;
    align-items: flex-start;
}
.cr-bk-avail-status {
    flex: 1;
    min-width: 200px;
    padding: 12px 16px;
    background: #F8FAFF;
    border: 1.5px solid #EEF2FB;
    border-radius: 10px;
    font-family: 'DM Sans', sans-serif;
    font-size: 0.838rem;
    color: #5A6A8A;
    min-height: 46px;
    display: flex;
    align-items: center;
    gap: 8px;
}
.cr-bk-avail-status.available {
    background: #D1FAF0;
    border-color: rgba(0,200,150,.25);
    color: #00C896;
    font-weight: 600;
}
.cr-bk-avail-status.checking {
    color: #9AAFC8;
}

/* Conflict box */
.cr-bk-conflict-box {
    flex: 1;
    min-width: 200px;
    padding: 12px 16px;
    background: #FFF0F3;
    border: 1.5px solid #FFD0D8;
    border-radius: 10px;
    display: flex;
    align-items: flex-start;
    gap: 10px;
}
.cr-bk-conflict-icon { font-size: 0.875rem; flex-shrink: 0; margin-top: 1px; }
.cr-bk-conflict-title {
    font-family: 'Plus Jakarta Sans', sans-serif;
    font-size: 0.813rem;
    font-weight: 700;
    color: #FF4D6D;
    margin: 0 0 2px;
}
.cr-bk-conflict-detail {
    font-family: 'DM Sans', sans-serif;
    font-size: 0.75rem;
    color: #FF4D6D;
    margin: 0;
}

/* Aksi */
.cr-bk-section--aksi { background: transparent; box-shadow: none; border: none; padding: 0; }
.cr-bk-aksi-row { display: flex; align-items: center; gap: 12px; }

.cr-bk-btn-submit {
    padding: 13px 28px;
    background: linear-gradient(135deg, #F4B400 0%, #FFB020 100%);
    color: #1A2340;
    font-family: 'Plus Jakarta Sans', sans-serif;
    font-size: 0.938rem;
    font-weight: 700;
    border: none;
    border-radius: 10px;
    cursor: pointer;
    box-shadow: 0 3px 12px rgba(244,180,0,.30);
    transition: transform .15s, box-shadow .15s;
}
.cr-bk-btn-submit:hover { transform: scale(1.02); box-shadow: 0 6px 20px rgba(244,180,0,.40); }
.cr-bk-btn-submit:disabled {
    background: #EEF2FB;
    color: #9AAFC8;
    box-shadow: none;
    cursor: not-allowed;
    transform: none;
}

.cr-bk-btn-batal {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 12px 20px;
    background: #fff;
    border: 1.5px solid #E8EEF7;
    border-radius: 10px;
    font-family: 'Plus Jakarta Sans', sans-serif;
    font-size: 0.875rem;
    font-weight: 600;
    color: #5A6A8A;
    text-decoration: none;
    transition: border-color .15s, color .15s;
}
.cr-bk-btn-batal:hover { border-color: #FF4D6D; color: #FF4D6D; }

@media (max-width: 600px) {
    .cr-bk-form-grid { grid-template-columns: 1fr; }
}

/* ============================================================
   STEPPER — done state (tambahan untuk step 2)
   ============================================================ */
.cr-bk-step--done .cr-bk-step__label { color: #1A2340; font-weight: 800; }
.cr-bk-step__check {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 18px;
    height: 18px;
    border-radius: 50%;
    background: #00C896;
    color: #fff;
    font-size: 0.7rem;
    font-weight: 700;
    margin-right: 6px;
}
.cr-bk-step__line--done { background: #00C896; }
.cr-bk-step--active .cr-bk-step__label {
    color: #1A2340;
    font-weight: 800;
    background: rgba(244,180,0,0.14);
    padding: 4px 10px;
    border-radius: 8px;
}

/* ============================================================
   DETAIL GRID
   ============================================================ */
.cr-bk-detail-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 18px 24px;
}
.cr-bk-detail-item {
    display: flex;
    align-items: flex-start;
    gap: 12px;
}
.cr-bk-detail-icon {
    width: 36px;
    height: 36px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1rem;
    flex-shrink: 0;
}
.cr-bk-detail-icon--yellow { background: rgba(244,180,0,0.12); }
.cr-bk-detail-icon--blue   { background: rgba(79,195,247,0.12); }
.cr-bk-detail-icon--gray   { background: #EEF2FB; }

.cr-bk-detail-label {
    font-family: 'DM Sans', sans-serif;
    font-size: 0.75rem;
    color: #9AAFC8;
    margin: 0 0 2px;
}
.cr-bk-detail-val {
    font-family: 'Plus Jakarta Sans', sans-serif;
    font-size: 0.938rem;
    font-weight: 700;
    color: #1A2340;
    margin: 0;
}

/* Keterangan */
.cr-bk-keterangan {
    margin-top: 18px;
    padding-top: 16px;
    border-top: 1px solid #EEF2FB;
}
.cr-bk-keterangan__title {
    font-family: 'Plus Jakarta Sans', sans-serif;
    font-size: 0.875rem;
    font-weight: 800;
    color: #1A2340;
    margin: 0 0 6px;
}
.cr-bk-keterangan__text {
    font-family: 'DM Sans', sans-serif;
    font-size: 0.813rem;
    color: #5A6A8A;
    margin: 0;
    line-height: 1.6;
}

/* ============================================================
   STATUS BOX
   ============================================================ */
.cr-bk-status-box {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 16px 20px;
    background: #D1FAF0;
    border: 1.5px solid rgba(0,200,150,0.25);
    border-radius: 14px;
    font-family: 'Plus Jakarta Sans', sans-serif;
    font-size: 0.938rem;
    font-weight: 600;
    color: #00785F;
    margin-bottom: 16px;
}
.cr-bk-status-box strong { font-weight: 800; }
.cr-bk-status-icon { font-size: 1.1rem; }

/* ============================================================
   ACTION BUTTONS
   ============================================================ */
.cr-bk-btn-konfirmasi {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 100%;
    padding: 15px;
    background: linear-gradient(135deg, #F4B400 0%, #4FC3F7 100%);
    color: #1A2340;
    font-family: 'Plus Jakarta Sans', sans-serif;
    font-size: 0.938rem;
    font-weight: 800;
    letter-spacing: 0.5px;
    border: none;
    border-radius: 12px;
    cursor: pointer;
    box-shadow: 0 4px 18px rgba(244,180,0,0.30);
    transition: transform 0.15s ease, box-shadow 0.15s ease;
    margin-bottom: 12px;
}
.cr-bk-btn-konfirmasi:hover {
    transform: scale(1.01);
    box-shadow: 0 6px 24px rgba(244,180,0,0.40);
}

.cr-bk-btn-kembali {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 100%;
    padding: 14px;
    background: linear-gradient(135deg, #F4B400 0%, #FFB020 100%);
    color: #1A2340;
    font-family: 'Plus Jakarta Sans', sans-serif;
    font-size: 0.875rem;
    font-weight: 700;
    border: 1.5px solid #1A2340;
    border-radius: 12px;
    text-decoration: none;
    transition: transform 0.15s ease;
}
.cr-bk-btn-kembali:hover { transform: scale(1.01); }

@media (max-width: 600px) {
    .cr-bk-detail-grid { grid-template-columns: 1fr; }
    .cr-bk-status-box { flex-direction: column; align-items: flex-start; gap: 6px; }
}
</style>
@endsection