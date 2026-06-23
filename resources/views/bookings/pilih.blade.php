@extends('layouts.dashboard')

@section('title', 'Pilih Jenis Booking')

@section('content')
<div class="cr-dash-content">

    <div class="cr-dash-brand-pill">
        <span class="cr-dash-brand-pill__icon">🟨</span>
        <span class="cr-dash-brand-pill__text">CampusRoom • v1.0 Mahasiswa</span>
    </div>

    <div class="cr-pilih-wrap">

        <div class="cr-pilih-header">
            <a href="/dashboard" class="cr-pilih-back">← Kembali</a>
            <h1 class="cr-pilih-title">Pilih Jenis Booking</h1>
            <p class="cr-pilih-sub">Pilih tipe peminjaman ruangan yang sesuai kebutuhanmu</p>
        </div>

        <div class="cr-pilih-grid">

            {{-- Perkuliahan --}}
            <a href="/booking/perkuliahan?room_id={{ request('room_id') }}" 
            class="cr-pilih-card cr-pilih-card--kuliah">
                <div class="cr-pilih-card__icon">🎓</div>
                <div class="cr-pilih-card__body">
                    <h2 class="cr-pilih-card__title">Perkuliahan</h2>
                    <p class="cr-pilih-card__desc">
                        Booking ruangan untuk kegiatan perkuliahan. Langsung disetujui otomatis tanpa perlu menunggu.
                    </p>
                    <div class="cr-pilih-card__tags">
                        <span class="cr-pilih-tag cr-pilih-tag--green">✅ Auto Approved</span>
                        <span class="cr-pilih-tag cr-pilih-tag--blue">📅 Pilih Jadwal</span>
                    </div>
                </div>
                <div class="cr-pilih-card__arrow">→</div>
            </a>

            {{-- Kegiatan --}}
            <a href="/booking/kegiatan?room_id={{ request('room_id') }}" 
            class="cr-pilih-card cr-pilih-card--kegiatan">
                <div class="cr-pilih-card__icon">🎯</div>
                <div class="cr-pilih-card__body">
                    <h2 class="cr-pilih-card__title">Kegiatan</h2>
                    <p class="cr-pilih-card__desc">
                        Booking ruangan untuk kegiatan organisasi, seminar, atau acara kampus. Memerlukan persetujuan admin dan upload surat.
                    </p>
                    <div class="cr-pilih-card__tags">
                        <span class="cr-pilih-tag cr-pilih-tag--yellow">⏳ Perlu Persetujuan</span>
                        <span class="cr-pilih-tag cr-pilih-tag--blue">📄 Upload Surat</span>
                    </div>
                </div>
                <div class="cr-pilih-card__arrow">→</div>
            </a>

        </div>

    </div>
</div>

<style>
.cr-pilih-wrap {
    margin-top: 64px;
    max-width: 700px;
    margin-left: auto;
    margin-right: auto;
}
.cr-pilih-header {
    display: block;
    width: fit-content;
    margin-right: auto;
    margin-bottom: 16px;
}
.cr-pilih-back {
    display: inline-block;
    font-family: 'Plus Jakarta Sans', sans-serif;
    font-size: 0.813rem;
    font-weight: 600;
    color: #5A6A8A;
    text-decoration: none;
    margin-bottom: 16px;
    transition: color .15s;
}
.cr-pilih-back:hover { color: #1A2340; }
.cr-pilih-title {
    font-family: 'Space Grotesk', sans-serif;
    font-size: 1.75rem;
    font-weight: 700;
    color: #1A2340;
    margin: 0 0 8px;
}
.cr-pilih-sub {
    font-family: 'DM Sans', sans-serif;
    font-size: 0.938rem;
    color: #5A6A8A;
    margin: 0;
}
.cr-pilih-grid {
    display: flex;
    flex-direction: column;
    gap: 16px;
}
.cr-pilih-card {
    background: #fff;
    border: 1.5px solid #EEF2FB;
    border-radius: 18px;
    padding: 24px 20px;
    display: flex;
    align-items: center;
    gap: 18px;
    text-decoration: none;
    transition: transform .2s ease, box-shadow .2s ease, border-color .2s ease;
    box-shadow: 0 2px 10px rgba(26,35,64,0.06);
}
.cr-pilih-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 28px rgba(26,35,64,0.11);
}
.cr-pilih-card--kuliah:hover { border-color: rgba(79,195,247,.4); }
.cr-pilih-card--kegiatan:hover { border-color: rgba(244,180,0,.4); }

.cr-pilih-card__icon {
    font-size: 2.25rem;
    width: 64px;
    height: 64px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 16px;
    flex-shrink: 0;
}
.cr-pilih-card--kuliah .cr-pilih-card__icon { background: rgba(79,195,247,.12); }
.cr-pilih-card--kegiatan .cr-pilih-card__icon { background: rgba(244,180,0,.12); }

.cr-pilih-card__body { flex: 1; }
.cr-pilih-card__title {
    font-family: 'Plus Jakarta Sans', sans-serif;
    font-size: 1.063rem;
    font-weight: 800;
    color: #1A2340;
    margin: 0 0 6px;
}
.cr-pilih-card__desc {
    font-family: 'DM Sans', sans-serif;
    font-size: 0.838rem;
    color: #5A6A8A;
    margin: 0 0 10px;
    line-height: 1.55;
}
.cr-pilih-card__tags { display: flex; gap: 6px; flex-wrap: wrap; }
.cr-pilih-tag {
    font-family: 'Plus Jakarta Sans', sans-serif;
    font-size: 0.688rem;
    font-weight: 700;
    padding: 3px 10px;
    border-radius: 999px;
}
.cr-pilih-tag--green  { background: #D1FAF0; color: #00C896; }
.cr-pilih-tag--yellow { background: #FFF3CD; color: #E6820A; }
.cr-pilih-tag--blue   { background: rgba(79,195,247,.15); color: #0277BD; }

.cr-pilih-card__arrow {
    font-size: 1.25rem;
    color: #9AAFC8;
    flex-shrink: 0;
    transition: transform .2s ease, color .2s ease;
}
.cr-pilih-card:hover .cr-pilih-card__arrow {
    transform: translateX(4px);
    color: #1A2340;
}
</style>
@endsection