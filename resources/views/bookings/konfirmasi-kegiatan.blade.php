@extends('layouts.dashboard')

@section('title', 'Konfirmasi Booking Kegiatan')

@section('content')
<div class="cr-dash-content cr-bk-content">

    <div class="cr-dash-brand-pill">
        <span class="cr-dash-brand-pill__icon">🟨</span>
        <span class="cr-dash-brand-pill__text">CampusRoom • v1.0 Admin</span>
    </div>

    <h1 class="cr-bk-title">Form Booking Kegiatan</h1>

    {{-- Stepper --}}
    <div class="cr-bk-stepper">
        <div class="cr-bk-step cr-bk-step--done">
            <span class="cr-bk-step__label">[1. Data] ✔</span>
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

    @if(session('error'))
    <div class="cr-bk-alert cr-bk-alert--error">⚠️ {{ session('error') }}</div>
    @endif

    {{-- ======== RINGKASAN BOOKING ======== --}}
    <section class="cr-bk-section">
        <h2 class="cr-bk-section__title">🗂️ Ringkasan Booking</h2>

        {{-- Ruangan (multi) & Jenis Booking --}}
        <div class="cr-konf-grid">
            <div class="cr-konf-item-full">
                <p class="cr-konf-item__label">
                    Ruangan ({{ $rooms->count() }})
                </p>
                <div class="cr-konf-room-list">
                    @foreach($rooms as $room)
                    <span class="cr-konf-room-chip">🏢 {{ $room->nama_ruangan }}</span>
                    @endforeach
                </div>
            </div>
            <div class="cr-konf-item">
                <div class="cr-konf-item__icon cr-konf-item__icon--blue">🎯</div>
                <div>
                    <p class="cr-konf-item__label">Jenis Booking</p>
                    <p class="cr-konf-item__val">Kegiatan</p>
                </div>
            </div>
        </div>

        {{-- Nama Kegiatan & Penyelenggara --}}
        <div class="cr-konf-grid">
            <div class="cr-konf-item-full">
                <p class="cr-konf-item__label">Nama Kegiatan</p>
                <p class="cr-konf-item__val cr-konf-item__val--lg">{{ $draft['nama_kegiatan'] }}</p>
            </div>
            <div class="cr-konf-item-full">
                <p class="cr-konf-item__label">Penyelenggara</p>
                <p class="cr-konf-item__val cr-konf-item__val--lg">{{ $draft['penyelenggara'] }}</p>
            </div>
        </div>

        {{-- Tanggal & Waktu --}}
        <div class="cr-konf-grid">
            <div class="cr-konf-item">
                <div class="cr-konf-item__icon cr-konf-item__icon--neutral">📆</div>
                <div>
                    <p class="cr-konf-item__label">Tanggal</p>
                    <p class="cr-konf-item__val">
                        {{ \Carbon\Carbon::parse($draft['tanggal'])->locale('id')->translatedFormat('l, d F Y') }}
                        @if($draft['tanggal'] != $draft['tanggal_selesai'])
                            – {{ \Carbon\Carbon::parse($draft['tanggal_selesai'])->locale('id')->translatedFormat('d F Y') }}
                        @endif
                    </p>
                </div>
            </div>
            <div class="cr-konf-item">
                <div class="cr-konf-item__icon cr-konf-item__icon--neutral">🕐</div>
                <div>
                    <p class="cr-konf-item__label">Waktu</p>
                    @php
                        $mulai = \Carbon\Carbon::createFromFormat('H:i', substr($draft['jam_mulai'],0,5));
                        $selesai = \Carbon\Carbon::createFromFormat('H:i', substr($draft['jam_selesai'],0,5));
                        $durasiMenit = $mulai->diffInMinutes($selesai);
                        $durasiJam = floor($durasiMenit / 60);
                        $durasiSisaMenit = $durasiMenit % 60;
                        $durasiText = $durasiJam > 0 ? $durasiJam . ' jam' : '';
                        if ($durasiSisaMenit > 0) $durasiText .= ' ' . $durasiSisaMenit . ' menit';
                    @endphp
                    <p class="cr-konf-item__val">
                        {{ substr($draft['jam_mulai'],0,5) }} – {{ substr($draft['jam_selesai'],0,5) }} ({{ trim($durasiText) }})
                    </p>
                </div>
            </div>
        </div>

        {{-- Peserta & Status --}}
        <div class="cr-konf-grid">
            <div class="cr-konf-item">
                <div class="cr-konf-item__icon cr-konf-item__icon--neutral">👥</div>
                <div>
                    <p class="cr-konf-item__label">Peserta</p>
                    <p class="cr-konf-item__val">{{ $draft['perkiraan_peserta'] }}</p>
                </div>
            </div>
            <div class="cr-konf-item">
                <div class="cr-konf-item__icon cr-konf-item__icon--neutral">🕐</div>
                <div>
                    <p class="cr-konf-item__label">Status</p>
                    <p class="cr-konf-item__val">
                        <span class="cr-konf-status-badge">MENUNGGU PERSETUJUAN ADMIN</span>
                    </p>
                </div>
            </div>
        </div>

        @if(!empty($draft['deskripsi']))
        <div class="cr-konf-ket">
            <p class="cr-konf-ket__title">Deskripsi</p>
            <p class="cr-konf-ket__text">{{ $draft['deskripsi'] }}</p>
        </div>
        @endif

        <div class="cr-konf-ket">
            <p class="cr-konf-ket__title">📎 File Surat</p>
            <p class="cr-konf-ket__text">{{ $draft['surat_nama_asli'] }}</p>
        </div>

    </section>

    {{-- ======== INFO MULTI-ROOM ======== --}}
    @if($rooms->count() > 1)
    <div class="cr-konf-multi-info">
        ℹ️ Booking ini mencakup <strong>{{ $rooms->count() }} ruangan</strong> sekaligus dalam satu pengajuan. Jika salah satu ruangan tidak tersedia saat diproses, seluruh booking akan ditolak.
    </div>
    @endif

    {{-- ======== STATUS BANNER ======== --}}
    <div class="cr-konf-status cr-konf-status--diajukan">
        ✔ Booking Diajukan! ✅
    </div>

    {{-- ======== AKSI ======== --}}
    <div class="cr-konf-aksi">
        <form action="/booking/kegiatan/store" method="POST">
            @csrf
            <button type="submit" class="cr-konf-btn-confirm">
                ✔ KONFIRMASI &amp; BOOKING
            </button>
        </form>

        <div class="cr-konf-aksi-row">
            <a href="/booking/kegiatan" class="cr-konf-btn-back">Kembali</a>
            <form action="/booking/kegiatan/batal" method="POST" class="cr-konf-form-batal">
                @csrf
                <button type="submit" class="cr-konf-btn-batalkan">Batalkan Pengajuan</button>
            </form>
        </div>
    </div>

</div>

<style>
.cr-bk-content { max-width: 760px; }
.cr-bk-title {
    font-family: 'Space Grotesk', sans-serif; font-size: 1.75rem; font-weight: 700; color: #1A2340;
    margin: 24px 0 16px; letter-spacing: -0.3px;
}
.cr-bk-stepper { display: flex; align-items: center; gap: 0; margin-bottom: 28px; }
.cr-bk-step { display: flex; align-items: center; }
.cr-bk-step__label { font-family: 'Plus Jakarta Sans', sans-serif; font-size: 0.813rem; font-weight: 600; color: #9AAFC8; }
.cr-bk-step--active .cr-bk-step__label { color: #1A2340; font-weight: 800; background: rgba(244,180,0,0.15); padding: 4px 10px; border-radius: 999px; }
.cr-bk-step--done .cr-bk-step__label { color: #00C896; font-weight: 700; }
.cr-bk-step__line { flex: 1; height: 1px; min-width: 60px; background: #D0D9EE; margin: 0 12px; }
.cr-bk-step__line--done { background: #00C896; }

.cr-bk-alert {
    padding: 12px 16px; border-radius: 10px; font-family: 'Plus Jakarta Sans', sans-serif;
    font-size: 0.875rem; font-weight: 600; margin-bottom: 20px;
}
.cr-bk-alert--error { background: #FFF0F3; border: 1px solid #FFD0D8; color: #FF4D6D; }

.cr-bk-section {
    background: #FFFFFF; border-radius: 16px; padding: 22px 24px; margin-bottom: 16px;
    box-shadow: 0 2px 8px rgba(26,35,64,0.06); border: 1.5px solid #EEF2FB;
}
.cr-bk-section__title { font-family: 'Plus Jakarta Sans', sans-serif; font-size: 1rem; font-weight: 800; color: #1A2340; margin: 0 0 16px; }

/* Konf grid */
.cr-konf-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 16px; }
.cr-konf-item { display: flex; align-items: flex-start; gap: 12px; }
.cr-konf-item-full { display: flex; flex-direction: column; gap: 2px; }
.cr-konf-item__icon {
    width: 40px; height: 40px; border-radius: 10px; display: flex; align-items: center; justify-content: center;
    font-size: 1.125rem; flex-shrink: 0;
}
.cr-konf-item__icon--yellow  { background: rgba(244,180,0,0.12); }
.cr-konf-item__icon--blue    { background: rgba(79,195,247,0.12); }
.cr-konf-item__icon--neutral { background: #F0F4FF; }
.cr-konf-item__label { font-family: 'DM Sans', sans-serif; font-size: 0.75rem; color: #9AAFC8; margin: 0 0 2px; }
.cr-konf-item__val { font-family: 'Plus Jakarta Sans', sans-serif; font-size: 0.875rem; font-weight: 700; color: #1A2340; margin: 0; }
.cr-konf-item__val--lg { font-size: 0.938rem; }

/* Multi-room chips */
.cr-konf-room-list { display: flex; gap: 8px; flex-wrap: wrap; margin-top: 4px; }
.cr-konf-room-chip {
    display: inline-flex; align-items: center; gap: 4px;
    background: rgba(244,180,0,0.12); color: #1A2340;
    font-family: 'Plus Jakarta Sans', sans-serif; font-size: 0.8rem; font-weight: 700;
    padding: 5px 12px; border-radius: 999px;
}

.cr-konf-status-badge {
    display: inline-flex; align-items: center; font-family: 'Plus Jakarta Sans', sans-serif;
    font-size: 0.7rem; font-weight: 800; padding: 4px 12px; border-radius: 999px;
    background: #FFF3CD; color: #E6820A;
}

/* Keterangan */
.cr-konf-ket { border-top: 1px solid #EEF2FB; padding-top: 16px; margin-top: 4px; }
.cr-konf-ket__title { font-family: 'Plus Jakarta Sans', sans-serif; font-size: 0.875rem; font-weight: 800; color: #1A2340; margin: 0 0 6px; }
.cr-konf-ket__text { font-family: 'DM Sans', sans-serif; font-size: 0.838rem; color: #5A6A8A; line-height: 1.6; margin: 0; }

/* Multi-room info banner */
.cr-konf-multi-info {
    background: rgba(79,195,247,0.10); border: 1.5px solid rgba(79,195,247,0.25);
    border-radius: 12px; padding: 12px 16px; margin-bottom: 16px;
    font-family: 'DM Sans', sans-serif; font-size: 0.813rem; color: #0277BD; line-height: 1.6;
}
.cr-konf-multi-info strong { font-weight: 700; }

/* Status banner */
.cr-konf-status {
    border-radius: 12px; padding: 14px 20px; font-family: 'Plus Jakarta Sans', sans-serif;
    font-size: 0.938rem; font-weight: 700; margin-bottom: 20px; text-align: center;
}
.cr-konf-status--diajukan {
    background: #D1FAF0; border: 1.5px solid rgba(0,200,150,.25); color: #00C896;
}

/* Aksi */
.cr-konf-aksi { display: flex; flex-direction: column; gap: 12px; }
.cr-konf-btn-confirm {
    width: 100%; padding: 15px; background: linear-gradient(135deg, #F4B400 0%, #FFB020 100%); color: #1A2340;
    font-family: 'Plus Jakarta Sans', sans-serif; font-size: 1rem; font-weight: 800; letter-spacing: 0.5px;
    border: none; border-radius: 12px; cursor: pointer;
    box-shadow: 0 4px 16px rgba(244,180,0,.32); transition: transform .15s, box-shadow .15s;
}
.cr-konf-btn-confirm:hover { transform: scale(1.01); box-shadow: 0 6px 24px rgba(244,180,0,.42); }

.cr-konf-aksi-row { display: flex; gap: 12px; }
.cr-konf-btn-back {
    flex: 1; display: flex; align-items: center; justify-content: center; padding: 14px;
    background: linear-gradient(135deg, #F4B400 0%, #FFB020 100%); color: #1A2340;
    border-radius: 12px; font-family: 'Plus Jakarta Sans', sans-serif; font-size: 0.938rem; font-weight: 700;
    text-decoration: none; box-shadow: 0 3px 12px rgba(244,180,0,.25); transition: transform .15s;
}
.cr-konf-btn-back:hover { transform: scale(1.01); }

.cr-konf-form-batal { flex: 1; }
.cr-konf-btn-batalkan {
    width: 100%; padding: 14px; background: #fff; border: 1.5px solid #E8EEF7; border-radius: 12px;
    font-family: 'Plus Jakarta Sans', sans-serif; font-size: 0.938rem; font-weight: 700; color: #5A6A8A;
    cursor: pointer; transition: border-color .15s, color .15s;
}
.cr-konf-btn-batalkan:hover { border-color: #FF4D6D; color: #FF4D6D; }

@media (max-width: 600px) {
    .cr-konf-grid { grid-template-columns: 1fr; }
    .cr-konf-aksi-row { flex-direction: column; }
}
</style>
@endsection