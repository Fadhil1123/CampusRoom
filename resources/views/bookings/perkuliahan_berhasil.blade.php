@extends('layouts.dashboard')

@section('title', 'Booking Berhasil')

@section('content')
<div class="cr-dash-content cr-bk-content cr-bs-content">

    {{-- Brand pill --}}
    <div class="cr-dash-brand-pill">
        <span class="cr-dash-brand-pill__icon">🟨</span>
        <span class="cr-dash-brand-pill__text">CampusRoom • v1.0 Mahasiswa</span>
    </div>

    {{-- Stepper — semua selesai --}}
    <div class="cr-bk-stepper cr-bs-stepper">
        <div class="cr-bk-step cr-bk-step--done">
            <span class="cr-bk-step__check">✓</span>
            <span class="cr-bk-step__label">[1. Data]</span>
        </div>
        <div class="cr-bk-step__line cr-bk-step__line--done"></div>
        <div class="cr-bk-step cr-bk-step--done">
            <span class="cr-bk-step__check">✓</span>
            <span class="cr-bk-step__label">[2. Konfirmasi]</span>
        </div>
        <div class="cr-bk-step__line cr-bk-step__line--done"></div>
        <div class="cr-bk-step cr-bk-step--done">
            <span class="cr-bk-step__check">✓</span>
            <span class="cr-bk-step__label">[3. Selesai]</span>
        </div>
    </div>

    {{-- ======== ANIMASI SUKSES ======== --}}
    <div class="cr-bs-anim-wrap">
        <svg class="cr-bs-confetti" viewBox="0 0 240 240" xmlns="http://www.w3.org/2000/svg">
            {{-- confetti particles --}}
            <circle class="cr-bs-particle cr-bs-particle--1" cx="40"  cy="40"  r="4" fill="#F4B400"/>
            <circle class="cr-bs-particle cr-bs-particle--2" cx="200" cy="50"  r="5" fill="#4FC3F7"/>
            <circle class="cr-bs-particle cr-bs-particle--3" cx="30"  cy="190" r="4" fill="#00C896"/>
            <circle class="cr-bs-particle cr-bs-particle--4" cx="210" cy="195" r="6" fill="#F4B400"/>
            <rect   class="cr-bs-particle cr-bs-particle--5" x="55"  y="15"  width="8" height="8" fill="#4FC3F7"/>
            <rect   class="cr-bs-particle cr-bs-particle--6" x="180" y="20"  width="7" height="7" fill="#F4B400"/>
            <rect   class="cr-bs-particle cr-bs-particle--7" x="20"  y="120" width="7" height="7" fill="#F4B400"/>
            <rect   class="cr-bs-particle cr-bs-particle--8" x="215" y="130" width="8" height="8" fill="#4FC3F7"/>
            <circle class="cr-bs-particle cr-bs-particle--9"  cx="120" cy="15"  r="4" fill="#00C896"/>
            <circle class="cr-bs-particle cr-bs-particle--10" cx="120" cy="225" r="5" fill="#4FC3F7"/>

            {{-- main circle + checkmark --}}
            <circle class="cr-bs-circle-bg" cx="120" cy="120" r="62" fill="none" stroke="#EEF2FB" stroke-width="10"/>
            <circle class="cr-bs-circle" cx="120" cy="120" r="62" fill="none"
                    stroke="url(#bsGradient)" stroke-width="10" stroke-linecap="round"
                    transform="rotate(-90 120 120)"/>
            <path class="cr-bs-check" d="M90 122 L112 145 L154 98"
                  fill="none" stroke="#1A2340" stroke-width="10"
                  stroke-linecap="round" stroke-linejoin="round"/>

            <defs>
                <linearGradient id="bsGradient" x1="0%" y1="0%" x2="100%" y2="100%">
                    <stop offset="0%"  stop-color="#F4B400"/>
                    <stop offset="100%" stop-color="#4FC3F7"/>
                </linearGradient>
            </defs>
        </svg>
    </div>

    {{-- ======== HEADLINE ======== --}}
    <h1 class="cr-bs-title">Booking Berhasil Diajukan!</h1>

    {{-- ======== STATUS ======== --}}
    <div class="cr-bs-status-pill">
        <span class="cr-bs-status-icon">🕐</span>
        <span>Status: <strong>DISETUJUI OTOMATIS</strong></span>
    </div>

    {{-- ======== AKSI ======== --}}
    <a href="/booking/history" class="cr-bs-btn-primary">
        <span class="cr-bs-btn-icon">✓</span> Lihat Detail Booking
    </a>

    <a href="/dashboard" class="cr-bs-btn-secondary">
        Kembali ke Dashboard
    </a>

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
   STEPPER — done state (semua step selesai)
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

/* ============================================================
   SUCCESS PAGE
   ============================================================ */
.cr-bs-content {
    display: flex;
    flex-direction: column;
    align-items: center;
    text-align: center;
}

.cr-bs-stepper {
    justify-content: center;
    width: 100%;
    max-width: 420px;
    margin-top: 64px;
}
.cr-bs-stepper .cr-bk-step__line {
    min-width: 30px;
    margin: 0 8px;
}

/* Animation wrapper */
.cr-bs-anim-wrap {
    width: 220px;
    height: 220px;
    margin: 32px auto 8px;
}
.cr-bs-confetti {
    width: 100%;
    height: 100%;
}

/* Circle draw animation */
.cr-bs-circle {
    stroke-dasharray: 390;
    stroke-dashoffset: 390;
    animation: cr-bs-draw-circle 0.8s cubic-bezier(0.65, 0, 0.35, 1) forwards;
}
@keyframes cr-bs-draw-circle {
    to { stroke-dashoffset: 0; }
}

/* Checkmark draw animation */
.cr-bs-check {
    stroke-dasharray: 100;
    stroke-dashoffset: 100;
    animation: cr-bs-draw-check 0.5s cubic-bezier(0.65, 0, 0.35, 1) 0.6s forwards;
}
@keyframes cr-bs-draw-check {
    to { stroke-dashoffset: 0; }
}

/* Confetti particles */
.cr-bs-particle {
    opacity: 0;
    transform-origin: center;
    animation: cr-bs-confetti-pop 1.5s ease-out 0.9s forwards;
}
.cr-bs-particle--1  { animation-delay: 0.85s; }
.cr-bs-particle--2  { animation-delay: 0.90s; }
.cr-bs-particle--3  { animation-delay: 0.95s; }
.cr-bs-particle--4  { animation-delay: 1.00s; }
.cr-bs-particle--5  { animation-delay: 0.88s; }
.cr-bs-particle--6  { animation-delay: 0.93s; }
.cr-bs-particle--7  { animation-delay: 0.98s; }
.cr-bs-particle--8  { animation-delay: 1.03s; }
.cr-bs-particle--9  { animation-delay: 0.91s; }
.cr-bs-particle--10 { animation-delay: 0.96s; }

@keyframes cr-bs-confetti-pop {
    0%   { opacity: 0; transform: scale(0) translateY(0); }
    30%  { opacity: 1; transform: scale(1.2) translateY(-6px); }
    100% { opacity: 0; transform: scale(0.6) translateY(-26px); }
}

/* Headline */
.cr-bs-title {
    font-family: 'Space Grotesk', sans-serif;
    font-size: 1.875rem;
    font-weight: 800;
    color: #1A2340;
    line-height: 1.3;
    letter-spacing: -0.3px;
    margin: 8px 0 20px;
    max-width: 480px;
}

/* Status pill */
.cr-bs-status-pill {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 10px 24px;
    background: rgba(244, 180, 0, 0.14);
    border: 1.5px solid rgba(244, 180, 0, 0.30);
    border-radius: 999px;
    font-family: 'Plus Jakarta Sans', sans-serif;
    font-size: 0.875rem;
    font-weight: 600;
    color: #B8860B;
    margin-bottom: 28px;
}
.cr-bs-status-pill strong { font-weight: 800; color: #8a6400; }
.cr-bs-status-icon { font-size: 1rem; }

/* Buttons */
.cr-bs-btn-primary {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    width: 100%;
    max-width: 480px;
    padding: 16px;
    background: linear-gradient(135deg, #F4B400 0%, #4FC3F7 100%);
    color: #1A2340;
    font-family: 'Plus Jakarta Sans', sans-serif;
    font-size: 0.938rem;
    font-weight: 800;
    border-radius: 12px;
    text-decoration: none;
    box-shadow: 0 4px 18px rgba(244,180,0,0.30);
    transition: transform 0.15s ease, box-shadow 0.15s ease;
    margin-bottom: 12px;
}
.cr-bs-btn-primary:hover {
    transform: scale(1.01);
    box-shadow: 0 6px 24px rgba(244,180,0,0.40);
}
.cr-bs-btn-icon {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 22px;
    height: 22px;
    border-radius: 50%;
    background: #1A2340;
    color: #fff;
    font-size: 0.75rem;
}

.cr-bs-btn-secondary {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 100%;
    max-width: 480px;
    padding: 14px;
    background: #FFFFFF;
    color: #9AAFC8;
    font-family: 'Plus Jakarta Sans', sans-serif;
    font-size: 0.875rem;
    font-weight: 700;
    border: 1.5px solid #E8EEF7;
    border-radius: 12px;
    text-decoration: none;
    transition: border-color 0.15s ease, color 0.15s ease;
}
.cr-bs-btn-secondary:hover {
    border-color: #5A6A8A;
    color: #5A6A8A;
}

@media (prefers-reduced-motion: reduce) {
    .cr-bs-circle, .cr-bs-check, .cr-bs-particle { animation: none; opacity: 1; }
    .cr-bs-circle { stroke-dashoffset: 0; }
    .cr-bs-check  { stroke-dashoffset: 0; }
}
</style>
@endsection