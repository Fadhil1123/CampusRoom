@extends('layouts.dashboard')

@section('title', 'Pengajuan Booking Dikirim')

@section('content')
<div class="cr-dash-content cr-bk-content">

    <div class="cr-dash-brand-pill">
        <span class="cr-dash-brand-pill__icon">🟨</span>
        <span class="cr-dash-brand-pill__text">CampusRoom • v1.0 Admin</span>
    </div>

    {{-- Stepper --}}
    <div class="cr-bk-stepper">
        <div class="cr-bk-step cr-bk-step--done">
            <span class="cr-bk-step__label">✔ [1. Data]</span>
        </div>
        <div class="cr-bk-step__line cr-bk-step__line--done"></div>
        <div class="cr-bk-step cr-bk-step--done">
            <span class="cr-bk-step__label">✔ [2. Konfirmasi]</span>
        </div>
        <div class="cr-bk-step__line cr-bk-step__line--done"></div>
        <div class="cr-bk-step cr-bk-step--active">
            <span class="cr-bk-step__label">✔ [3. Selesai]</span>
        </div>
    </div>

    {{-- ======== ANIMASI HOURGLASS ======== --}}
    <div class="cr-done-wrap">
        <div class="cr-done-illustration">
            <svg viewBox="0 0 160 160" xmlns="http://www.w3.org/2000/svg" class="cr-hourglass-svg">
                <defs>
                    <linearGradient id="hgGrad" x1="0%" y1="0%" x2="100%" y2="100%">
                        <stop offset="0%" stop-color="#F4B400"/>
                        <stop offset="100%" stop-color="#4FC3F7"/>
                    </linearGradient>
                </defs>
                {{-- Frame --}}
                <rect x="50" y="20" width="60" height="8" rx="3" fill="url(#hgGrad)"/>
                <rect x="50" y="132" width="60" height="8" rx="3" fill="url(#hgGrad)"/>
                {{-- Glass body --}}
                <path d="M58 28 L102 28 L102 50 L82 80 L102 110 L102 132 L58 132 L58 110 L78 80 L58 50 Z"
                    fill="none" stroke="url(#hgGrad)" stroke-width="4" stroke-linejoin="round"/>
                {{-- Sand top --}}
                <path d="M62 32 L98 32 L98 48 L80 72 L62 48 Z" fill="#FFE9B0" opacity="0.85"/>
                {{-- Sand bottom --}}
                <path d="M70 128 L90 128 L90 116 L80 100 L70 116 Z" fill="#B8E8F8" opacity="0.85"/>
                {{-- Falling stream --}}
                <line x1="80" y1="74" x2="80" y2="98" stroke="#FFD54F" stroke-width="2" stroke-dasharray="2 3">
                    <animate attributeName="stroke-dashoffset" from="0" to="10" dur="0.6s" repeatCount="indefinite"/>
                </line>
                {{-- Sparkles / confetti particles --}}
                <circle cx="30" cy="40" r="3" fill="#F4B400" opacity="0.8">
                    <animate attributeName="opacity" values="0.8;0;0.8" dur="1.5s" repeatCount="indefinite"/>
                </circle>
                <circle cx="135" cy="55" r="2.5" fill="#4FC3F7" opacity="0.7">
                    <animate attributeName="opacity" values="0.7;0;0.7" dur="1.5s" begin="0.3s" repeatCount="indefinite"/>
                </circle>
                <circle cx="25" cy="110" r="2" fill="#4FC3F7" opacity="0.6">
                    <animate attributeName="opacity" values="0.6;0;0.6" dur="1.5s" begin="0.6s" repeatCount="indefinite"/>
                </circle>
                <circle cx="140" cy="105" r="3" fill="#F4B400" opacity="0.75">
                    <animate attributeName="opacity" values="0.75;0;0.75" dur="1.5s" begin="0.9s" repeatCount="indefinite"/>
                </circle>
                <circle cx="40" cy="135" r="2" fill="#FFD54F" opacity="0.6">
                    <animate attributeName="opacity" values="0.6;0;0.6" dur="1.5s" begin="1.1s" repeatCount="indefinite"/>
                </circle>
                <circle cx="125" cy="20" r="2.5" fill="#F4B400" opacity="0.7">
                    <animate attributeName="opacity" values="0.7;0;0.7" dur="1.5s" begin="0.4s" repeatCount="indefinite"/>
                </circle>
                {{-- Whole hourglass gentle rotation --}}
                <animateTransform attributeName="transform" type="rotate" values="0 80 80; 8 80 80; 0 80 80; -8 80 80; 0 80 80" dur="2.4s" repeatCount="indefinite"/>
            </svg>
        </div>

        <h1 class="cr-done-title">Pengajuan Booking Dikirim!</h1>

        <div class="cr-done-status-badge">
            🕐 Status: <strong>MENUNGGU PERSETUJUAN</strong>
        </div>

        <p class="cr-done-desc">
            Admin akan meninjau pengajuan Anda.<br>
            Anda akan mendapat notifikasi hasilnya.
        </p>

        @if(!empty($done['nama_kegiatan']))
        <div class="cr-done-summary">
            <span class="cr-done-summary__icon">🎯</span>
            <div>
                <p class="cr-done-summary__label">Kegiatan</p>
                <p class="cr-done-summary__val">{{ $done['nama_kegiatan'] }}</p>
            </div>
            @if($room)
            <div class="cr-done-summary__divider"></div>
            <div>
                <p class="cr-done-summary__label">Ruangan</p>
                <p class="cr-done-summary__val">{{ $room->nama_ruangan }}</p>
            </div>
            @endif
        </div>
        @endif

        <div class="cr-done-aksi">
            <a href="/booking/history" class="cr-done-btn-primary">
                ✅ Lihat Status Booking
            </a>
            <a href="/dashboard" class="cr-done-btn-secondary">
                🏠 Kembali ke Dashboard
            </a>
        </div>
    </div>

</div>

<style>
.cr-bk-content { max-width: 760px; }

.cr-bk-stepper { display: flex; align-items: center; gap: 0; margin: 20px 0 32px; }
.cr-bk-step { display: flex; align-items: center; }
.cr-bk-step__label { font-family: 'Plus Jakarta Sans', sans-serif; font-size: 0.813rem; font-weight: 600; color: #9AAFC8; }
.cr-bk-step--done .cr-bk-step__label { color: #00C896; font-weight: 700; }
.cr-bk-step--active .cr-bk-step__label {
    color: #1A2340; font-weight: 800; background: rgba(244,180,0,0.15); padding: 4px 10px; border-radius: 999px;
}
.cr-bk-step__line { flex: 1; height: 1px; min-width: 60px; background: #D0D9EE; margin: 0 12px; }
.cr-bk-step__line--done { background: #00C896; }

/* ======= Done page ======= */
.cr-done-wrap {
    display: flex; flex-direction: column; align-items: center; text-align: center;
    padding: 24px 16px 8px;
}
.cr-done-illustration { width: 180px; height: 180px; margin-bottom: 8px; }
.cr-hourglass-svg { width: 100%; height: 100%; }

.cr-done-title {
    font-family: 'Space Grotesk', sans-serif; font-size: 1.5rem; font-weight: 700; color: #1A2340;
    margin: 8px 0 16px;
}

.cr-done-status-badge {
    display: inline-flex; align-items: center; gap: 6px;
    background: #FFF3CD; border: 1.5px solid #FFE9A8; color: #E6820A;
    font-family: 'Plus Jakarta Sans', sans-serif; font-size: 0.875rem; font-weight: 700;
    padding: 8px 20px; border-radius: 999px; margin-bottom: 18px;
}
.cr-done-status-badge strong { font-weight: 800; }

.cr-done-desc {
    font-family: 'DM Sans', sans-serif; font-size: 0.938rem; color: #5A6A8A; line-height: 1.7; margin: 0 0 24px;
}

/* Summary card */
.cr-done-summary {
    display: flex; align-items: center; gap: 16px; background: #fff; border: 1.5px solid #EEF2FB;
    border-radius: 14px; padding: 16px 22px; margin-bottom: 28px; box-shadow: 0 2px 8px rgba(26,35,64,0.06);
    width: 100%; max-width: 460px;
}
.cr-done-summary__icon { font-size: 1.5rem; flex-shrink: 0; }
.cr-done-summary__label { font-family: 'DM Sans', sans-serif; font-size: 0.7rem; color: #9AAFC8; margin: 0 0 2px; text-align: left; }
.cr-done-summary__val { font-family: 'Plus Jakarta Sans', sans-serif; font-size: 0.875rem; font-weight: 700; color: #1A2340; margin: 0; text-align: left; }
.cr-done-summary__divider { width: 1px; height: 36px; background: #EEF2FB; flex-shrink: 0; margin-left: auto; }

/* Aksi */
.cr-done-aksi { display: flex; flex-direction: column; gap: 12px; width: 100%; max-width: 460px; }
.cr-done-btn-primary {
    display: flex; align-items: center; justify-content: center; gap: 8px; padding: 15px;
    background: linear-gradient(135deg, #4FC3F7 0%, #1A2340 100%); color: #fff;
    font-family: 'Plus Jakarta Sans', sans-serif; font-size: 1rem; font-weight: 800;
    border-radius: 12px; text-decoration: none; box-shadow: 0 4px 16px rgba(79,195,247,.30);
    transition: transform .15s, box-shadow .15s;
}
.cr-done-btn-primary:hover { transform: scale(1.01); box-shadow: 0 6px 24px rgba(79,195,247,.40); }

.cr-done-btn-secondary {
    display: flex; align-items: center; justify-content: center; gap: 8px; padding: 14px;
    background: #F0F4FF; color: #9AAFC8;
    font-family: 'Plus Jakarta Sans', sans-serif; font-size: 0.938rem; font-weight: 700;
    border-radius: 12px; text-decoration: none; transition: background .15s, color .15s;
}
.cr-done-btn-secondary:hover { background: #E8EEF7; color: #5A6A8A; }
</style>
@endsection