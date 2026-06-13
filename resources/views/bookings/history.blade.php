@extends('layouts.dashboard')

@section('title', 'Booking Saya')

@section('content')
<div class="cr-dash-content">

    <div class="cr-dash-brand-pill">
        <span class="cr-dash-brand-pill__icon">🟨</span>
        <span class="cr-dash-brand-pill__text">CampusRoom • v1.0 Admin</span>
    </div>

    <h1 class="cr-hist-title">Booking Saya</h1>

    @if(session('success'))
    <div class="cr-hist-alert cr-hist-alert--success">✅ {{ session('success') }}</div>
    @endif

    <div class="cr-hist-list">
        @forelse($bookings as $booking)
        <div class="cr-hist-card">
            <div class="cr-hist-card__icon">
                {{ $booking->jenis === 'perkuliahan' ? '🎓' : '🎯' }}
            </div>
            <div class="cr-hist-card__info">
                <p class="cr-hist-card__title">
                    @if($booking->jenis === 'perkuliahan')
                        Perkuliahan — {{ $booking->rooms->first()?->nama_ruangan ?? '-' }}
                    @else
                        {{ $booking->kegiatan?->nama_kegiatan ?? 'Kegiatan' }} — {{ $booking->rooms->first()?->nama_ruangan ?? '-' }}
                    @endif
                </p>
                <p class="cr-hist-card__meta">
                    📆 {{ \Carbon\Carbon::parse($booking->tanggal)->locale('id')->translatedFormat('l, d F Y') }}
                    &nbsp;•&nbsp;
                    🕐 {{ substr($booking->jam_mulai,0,5) }} – {{ substr($booking->jam_selesai,0,5) }}
                </p>
            </div>
            <div class="cr-hist-card__status">
                @if($booking->status === 'approved')
                    <span class="cr-badge cr-badge--approved">Approved</span>
                @elseif($booking->status === 'pending')
                    <span class="cr-badge cr-badge--pending">Pending</span>
                @else
                    <span class="cr-badge cr-badge--rejected">Rejected</span>
                @endif
            </div>
        </div>
        @empty
        <div class="cr-hist-empty">
            <span>📋</span>
            <p>Belum ada riwayat booking</p>
            <a href="/booking" class="cr-hist-btn">Booking Sekarang</a>
        </div>
        @endforelse
    </div>

</div>

<style>
.cr-hist-title {
    font-family: 'Space Grotesk', sans-serif; font-size: 1.75rem; font-weight: 700; color: #1A2340;
    margin: 56px 0 20px; letter-spacing: -0.3px;
}
.cr-hist-alert {
    padding: 12px 16px; border-radius: 10px; font-family: 'Plus Jakarta Sans', sans-serif;
    font-size: 0.875rem; font-weight: 600; margin-bottom: 16px;
}
.cr-hist-alert--success { background: #D1FAF0; border: 1px solid #A0E8D8; color: #00C896; }

.cr-hist-list { display: flex; flex-direction: column; gap: 12px; }
.cr-hist-card {
    display: flex; align-items: center; gap: 14px; background: #fff; border-radius: 14px;
    padding: 16px 20px; box-shadow: 0 2px 8px rgba(26,35,64,0.06); border: 1.5px solid #EEF2FB;
}
.cr-hist-card__icon {
    width: 42px; height: 42px; border-radius: 10px; background: rgba(79,195,247,0.1);
    display: flex; align-items: center; justify-content: center; font-size: 1.25rem; flex-shrink: 0;
}
.cr-hist-card__info { flex: 1; min-width: 0; }
.cr-hist-card__title { font-family: 'Plus Jakarta Sans', sans-serif; font-size: 0.938rem; font-weight: 700; color: #1A2340; margin: 0 0 4px; }
.cr-hist-card__meta { font-family: 'DM Sans', sans-serif; font-size: 0.8rem; color: #5A6A8A; margin: 0; }
.cr-hist-card__status { flex-shrink: 0; }

.cr-badge { display: inline-flex; align-items: center; padding: 4px 14px; border-radius: 999px; font-family: 'Plus Jakarta Sans', sans-serif; font-size: 0.75rem; font-weight: 700; }
.cr-badge--approved { background: #D1FAF0; color: #00C896; }
.cr-badge--pending  { background: #FFF3CD; color: #E6820A; }
.cr-badge--rejected { background: #FFE4E9; color: #FF4D6D; }

.cr-hist-empty {
    display: flex; flex-direction: column; align-items: center; gap: 10px; padding: 60px 20px;
    background: #fff; border-radius: 14px; box-shadow: 0 2px 8px rgba(26,35,64,0.06);
}
.cr-hist-empty span { font-size: 2.5rem; }
.cr-hist-empty p { font-family: 'Plus Jakarta Sans', sans-serif; font-size: 0.938rem; font-weight: 600; color: #9AAFC8; margin: 0; }
.cr-hist-btn {
    margin-top: 6px; padding: 10px 24px; background: linear-gradient(135deg, #F4B400 0%, #FFB020 100%); color: #1A2340;
    font-family: 'Plus Jakarta Sans', sans-serif; font-size: 0.875rem; font-weight: 700; border-radius: 10px; text-decoration: none;
}
</style>
@endsection