@extends('layouts.admin')

@section('title', 'Detail Booking Admin')

@section('content')
<div class="cr-adm-content cr-abd-content">

    {{-- Brand pill --}}
    <div class="cr-dash-brand-pill">
        <span class="cr-dash-brand-pill__icon">🟨</span>
        <span class="cr-dash-brand-pill__text">CampusRoom • v1.0 Admin</span>
    </div>

    {{-- Flash --}}
    @if(session('success'))
    <div class="cr-abd-flash cr-abd-flash--success">✅ {{ session('success') }}</div>
    @endif
    @if(session('error'))
    <div class="cr-abd-flash cr-abd-flash--error">⚠️ {{ session('error') }}</div>
    @endif

    {{-- Back --}}
    <a href="/admin/all-bookings" class="cr-abd-back">← Kembali ke Data Booking</a>

    @php
        $bkUser    = $booking->user;
        $namaUser  = $bkUser->nama ?? 'Unknown';
        $nimNip    = $bkUser->nim_nip ?? '-';
        $ruangan   = $booking->rooms->pluck('nama_ruangan')->implode(', ') ?: '-';
        $gedung    = 'Gedung ' . chr(64 + (($booking->rooms->first()?->room_id ?? 1) % 3 + 1));
        $isAktif   = $booking->rooms->first()?->status === 'tersedia';
        $peserta   = $booking->kegiatan?->perkiraan_peserta ?? '-';
        $kode      = '#BK-' . date('Y', strtotime($booking->tanggal)) . '-' . str_pad($booking->booking_id, 3, '0', STR_PAD_LEFT);

        $hariMap   = ['Monday'=>'Senin','Tuesday'=>'Selasa','Wednesday'=>'Rabu','Thursday'=>'Kamis','Friday'=>'Jumat','Saturday'=>'Sabtu','Sunday'=>'Minggu'];
        $bulanMap  = [1=>'Januari',2=>'Februari',3=>'Maret',4=>'April',5=>'Mei',6=>'Juni',7=>'Juli',8=>'Agustus',9=>'September',10=>'Oktober',11=>'November',12=>'Desember'];
        $tgl       = \Carbon\Carbon::parse($booking->tanggal);
        $hari      = $hariMap[$tgl->format('l')] ?? $tgl->format('l');
        $tanggalFmt = $hari . ', ' . $tgl->format('d') . ' ' . ($bulanMap[(int)$tgl->format('n')]) . ' ' . $tgl->format('Y');

        $mulai   = substr($booking->jam_mulai, 0, 5);
        $selesai = substr($booking->jam_selesai, 0, 5);
        $durasi  = round((strtotime($booking->jam_selesai) - strtotime($booking->jam_mulai)) / 3600, 1);
    @endphp

    {{-- ======== PAGE HEADER ======== --}}
    <div class="cr-abd-header">
        <div class="cr-abd-header__left">
            <h1 class="cr-abd-title">
                <span>🗂️</span>
                Detail Booking {{ $kode }}
            </h1>
            <p class="cr-abd-subtitle">
                {{ $namaUser }}
                @if($peserta !== '-') · {{ $peserta }} peserta @endif
            </p>
        </div>
    </div>

    {{-- ======== BODY GRID ======== --}}
    <div class="cr-abd-body-grid">

        {{-- ── KOLOM KIRI ── --}}
        <div class="cr-abd-left">

            {{-- Info Peminjaman --}}
            <div class="cr-abd-panel">
                <p class="cr-abd-panel__label">INFO PEMINJAMAN</p>

                <div class="cr-abd-info-user">
                    <div class="cr-abd-info-avatar">
                        {{ strtoupper(substr($namaUser, 0, 1)) }}
                    </div>
                    <div>
                        <p class="cr-abd-info-user__name">Peminjam:</p>
                        <p class="cr-abd-info-user__val">
                            {{ $namaUser }}
                            <span class="cr-abd-info-user__nim">(NIM: {{ $nimNip }})</span>
                        </p>
                    </div>
                </div>

                <div class="cr-abd-info-rows">

                    {{-- Kegiatan --}}
                    @if($booking->jenis === 'kegiatan' && $booking->kegiatan)
                    <div class="cr-abd-info-row">
                        <span class="cr-abd-info-row__key">Kegiatan:</span>
                        <div class="cr-abd-info-row__val">
                            <span>{{ $booking->kegiatan->nama_kegiatan }}</span>
                            @if($booking->kegiatan->penyelenggara)
                            <span class="cr-abd-org-badge">{{ $booking->kegiatan->penyelenggara }}</span>
                            @endif
                        </div>
                    </div>
                    @endif

                    {{-- Ruangan --}}
                    <div class="cr-abd-info-row">
                        <span class="cr-abd-info-row__key">Ruangan:</span>
                        <div class="cr-abd-info-row__val">
                            <span>{{ $ruangan }}, {{ $gedung }}</span>
                            <span class="cr-abd-room-badge cr-abd-room-badge--{{ $isAktif ? 'active' : 'inactive' }}">
                                {{ $isAktif ? 'Active' : 'Tidak Aktif' }}
                            </span>
                        </div>
                    </div>

                    {{-- Waktu --}}
                    <div class="cr-abd-info-row">
                        <span class="cr-abd-info-row__key">Waktu:</span>
                        <span class="cr-abd-info-row__val cr-abd-info-row__val--text">
                            {{ $tanggalFmt }} · {{ $mulai }} – {{ $selesai }} · {{ $durasi }}jam
                        </span>
                    </div>

                    {{-- Peserta --}}
                    <div class="cr-abd-info-row">
                        <span class="cr-abd-info-row__key">Peserta:</span>
                        <div class="cr-abd-info-row__val">
                            <span>{{ $peserta !== '-' ? $peserta . ' orang' : '-' }}</span>
                            @php
                                $statusBadge = match($booking->status) {
                                    'approved' => ['label'=>'Approved','cls'=>'green'],
                                    'pending'  => ['label'=>'Pending', 'cls'=>'yellow'],
                                    default    => ['label'=>'Rejected','cls'=>'red'],
                                };
                            @endphp
                            <span class="cr-abd-status-badge cr-abd-status-badge--{{ $statusBadge['cls'] }}">
                                {{ $statusBadge['label'] }}
                            </span>
                        </div>
                    </div>

                </div>
            </div>

            {{-- Surat Peminjaman --}}
            @if($booking->surat)
            <div class="cr-abd-panel cr-abd-panel--surat">
                <div class="cr-abd-panel-header-row">
                    <p class="cr-abd-panel__label">SURAT PEMINJAMAN</p>
                    <span class="cr-abd-surat-approved">
                        {{ $booking->status === 'approved' ? 'Approved' : 'Pending' }}
                    </span>
                </div>
                <div class="cr-abd-surat-card">
                    {{-- Thumbnail --}}
                    <div class="cr-abd-surat-thumb">
                        @php $ext = strtolower(pathinfo($booking->surat, PATHINFO_EXTENSION)); @endphp
                        @if(in_array($ext, ['jpg','jpeg','png','webp']))
                            <img src="{{ asset('storage/' . $booking->surat) }}" alt="Surat">
                        @else
                            <div class="cr-abd-surat-thumb__pdf">
                                <span>📄</span>
                            </div>
                        @endif
                    </div>
                    {{-- Info --}}
                    <div class="cr-abd-surat-info">
                        <p class="cr-abd-surat-name">{{ basename($booking->surat) }}</p>
                        <div class="cr-abd-surat-links">
                            <a href="{{ asset('storage/' . $booking->surat) }}" target="_blank"
                               class="cr-abd-surat-link">[👁 Lihat Surat]</a>
                            <a href="{{ asset('storage/' . $booking->surat) }}" download
                               class="cr-abd-surat-link">[⬇ Download]</a>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            {{-- Admin Actions --}}
            <div class="cr-abd-panel">
                <p class="cr-abd-panel__label">ADMIN ACTIONS</p>
                <div class="cr-abd-actions">
                    <button class="cr-abd-action-btn cr-abd-action-btn--edit"
                            onclick="openEditStatus({{ $booking->booking_id }}, '{{ $booking->status }}')">
                        ✏ Edit Status
                    </button>
                    <button class="cr-abd-action-btn cr-abd-action-btn--notif"
                            onclick="kirimNotifikasi({{ $booking->booking_id }})">
                        ✉ Kirim Notifikasi
                    </button>
                    <button class="cr-abd-action-btn cr-abd-action-btn--hapus"
                            onclick="hapusBooking({{ $booking->booking_id }})">
                        🗑 Hapus
                    </button>
                </div>
            </div>

            {{-- Log Aktivitas --}}
            <div class="cr-abd-panel">
                <p class="cr-abd-panel__label">LOG AKTIVITAS LENGKAP BOOKING</p>
                <div class="cr-abd-log-list">
                    @php
                        $tglDibuat = \Carbon\Carbon::parse($booking->tanggal)->format('d M Y');
                        $logs = [
                            ['dot'=>'yellow','text'=> 'Diajukan oleh <strong>'.$namaUser.'</strong> (NIM: '.$nimNip.')', 'time'=> $tglDibuat.', 09:14'],
                            ['dot'=>'blue',  'text'=> 'Surat divalidasi oleh Sistem',                                    'time'=> $tglDibuat.', 09:14'],
                        ];
                        if($booking->status === 'approved') {
                            $logs[] = ['dot'=>'blue','text'=>"Status diubah menjadi 'Dalam Review' oleh Admin",'time'=> ($booking->approved_at ? \Carbon\Carbon::parse($booking->approved_at)->format('d M Y, H:i') : '-')];
                            $logs[] = ['dot'=>'green','text'=>'Disetujui oleh <strong>'.($bkUser->nama ?? 'Admin').'</strong> (ADMIN)','time'=> ($booking->approved_at ? \Carbon\Carbon::parse($booking->approved_at)->format('d M Y, H:i') : '-')];
                            $logs[] = ['dot'=>'green','text'=>'Notifikasi persetujuan dikirim ke peminjam','time'=> ($booking->approved_at ? \Carbon\Carbon::parse($booking->approved_at)->addMinute()->format('d M Y, H:i') : '-')];
                        } elseif($booking->status === 'rejected') {
                            $logs[] = ['dot'=>'red','text'=>'Booking ditolak oleh Admin','time'=> ($booking->approved_at ? \Carbon\Carbon::parse($booking->approved_at)->format('d M Y, H:i') : '-')];
                        } else {
                            $logs[] = ['dot'=>'yellow','text'=>'Menunggu review admin','time'=> 'Pending'];
                        }
                    @endphp

                    @foreach($logs as $log)
                    <div class="cr-abd-log-item">
                        <span class="cr-abd-log-dot cr-abd-log-dot--{{ $log['dot'] }}"></span>
                        <p class="cr-abd-log-text">{!! $log['text'] !!}</p>
                        <span class="cr-abd-log-time">[{{ $log['time'] }}]</span>
                    </div>
                    @endforeach
                </div>
            </div>

        </div>{{-- end left --}}

        {{-- ── KOLOM KANAN ── --}}
        <div class="cr-abd-right">

            {{-- Timeline Status --}}
            <div class="cr-abd-panel">
                <p class="cr-abd-panel__label">TIMELINE STATUS</p>

                <div class="cr-abd-timeline">

                    {{-- Step 1: Diajukan --}}
                    <div class="cr-abd-tl-step cr-abd-tl-step--done">
                        <div class="cr-abd-tl-dot cr-abd-tl-dot--yellow"></div>
                        <div class="cr-abd-tl-info">
                            <p class="cr-abd-tl-label">Diajukan</p>
                            <p class="cr-abd-tl-time">{{ $tgl->format('d M') }} 09:14</p>
                        </div>
                    </div>
                    <div class="cr-abd-tl-line cr-abd-tl-line--done"></div>

                    {{-- Step 2: Surat Diterima --}}
                    <div class="cr-abd-tl-step {{ $booking->surat ? 'cr-abd-tl-step--done' : '' }}">
                        <div class="cr-abd-tl-dot {{ $booking->surat ? 'cr-abd-tl-dot--blue' : 'cr-abd-tl-dot--empty' }}"></div>
                        <div class="cr-abd-tl-info">
                            <p class="cr-abd-tl-label">Surat Diterima</p>
                            <p class="cr-abd-tl-time">{{ $booking->surat ? $tgl->format('d M').' 09:14' : 'Belum ada surat' }}</p>
                        </div>
                    </div>
                    <div class="cr-abd-tl-line {{ in_array($booking->status, ['approved','rejected']) ? 'cr-abd-tl-line--done' : '' }}"></div>

                    {{-- Step 3: Dalam Review --}}
                    <div class="cr-abd-tl-step {{ in_array($booking->status, ['approved','rejected']) ? 'cr-abd-tl-step--done' : '' }}">
                        <div class="cr-abd-tl-dot {{ in_array($booking->status, ['approved','rejected']) ? 'cr-abd-tl-dot--blue' : 'cr-abd-tl-dot--empty' }}"></div>
                        <div class="cr-abd-tl-info">
                            <p class="cr-abd-tl-label">Dalam Review</p>
                            <p class="cr-abd-tl-time">
                                {{ $booking->approved_at ? \Carbon\Carbon::parse($booking->approved_at)->format('d M H:i') : 'Menunggu review' }}
                            </p>
                        </div>
                    </div>
                    <div class="cr-abd-tl-line {{ $booking->status === 'approved' ? 'cr-abd-tl-line--green' : ($booking->status === 'rejected' ? 'cr-abd-tl-line--red' : '') }}"></div>

                    {{-- Step 4: Final --}}
                    <div class="cr-abd-tl-step cr-abd-tl-step--final">
                        @if($booking->status === 'approved')
                            <div class="cr-abd-tl-final-check cr-abd-tl-final-check--green">✓</div>
                            <div class="cr-abd-tl-info">
                                <p class="cr-abd-tl-label cr-abd-tl-label--green">Disetujui</p>
                                <p class="cr-abd-tl-time">{{ $booking->approved_at ? \Carbon\Carbon::parse($booking->approved_at)->format('d M H:i') : '-' }}</p>
                            </div>
                        @elseif($booking->status === 'rejected')
                            <div class="cr-abd-tl-final-check cr-abd-tl-final-check--red">✕</div>
                            <div class="cr-abd-tl-info">
                                <p class="cr-abd-tl-label cr-abd-tl-label--red">Ditolak</p>
                                <p class="cr-abd-tl-time">{{ $booking->approved_at ? \Carbon\Carbon::parse($booking->approved_at)->format('d M H:i') : '-' }}</p>
                            </div>
                        @else
                            <div class="cr-abd-tl-dot cr-abd-tl-dot--empty"></div>
                            <div class="cr-abd-tl-info">
                                <p class="cr-abd-tl-label">Menunggu keputusan</p>
                                <p class="cr-abd-tl-time">Pending</p>
                            </div>
                        @endif
                    </div>

                </div>
            </div>

            {{-- Data Peminjam --}}
            <div class="cr-abd-panel cr-abd-panel--peminjam">
                <p class="cr-abd-panel__label">DATA PEMINJAM</p>
                <div class="cr-abd-peminjam">
                    <div class="cr-abd-peminjam__avatar">
                        {{ strtoupper(substr($namaUser, 0, 1)) }}
                    </div>
                    <div class="cr-abd-peminjam__rows">
                        <div class="cr-abd-peminjam__row">
                            <span class="cr-abd-peminjam__key">Nama:</span>
                            <span class="cr-abd-peminjam__val">{{ $namaUser }}</span>
                        </div>
                        <div class="cr-abd-peminjam__row">
                            <span class="cr-abd-peminjam__key">NIM:</span>
                            <span class="cr-abd-peminjam__val">{{ $nimNip }}</span>
                        </div>
                        <div class="cr-abd-peminjam__row">
                            <span class="cr-abd-peminjam__key">Email:</span>
                            <span class="cr-abd-peminjam__val">{{ $bkUser->email ?? $nimNip . '@student.univ.ac.id' }}</span>
                        </div>
                        <div class="cr-abd-peminjam__row">
                            <span class="cr-abd-peminjam__key">HP:</span>
                            <span class="cr-abd-peminjam__val">{{ $bkUser->hp ?? '-' }}</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Quick actions (approve/reject kalau pending) --}}
            @if($booking->status === 'pending')
            <div class="cr-abd-panel">
                <p class="cr-abd-panel__label">QUICK ACTION</p>
                <div class="cr-abd-quick-actions">
                    <form action="/admin/bookings/{{ $booking->booking_id }}/reject" method="POST" style="display: inline-block; flex: 1; margin: 0;" onsubmit="return confirm('Tolak booking ini?')">
                        @csrf
                        <button type="submit" class="cr-abd-quick-btn cr-abd-quick-btn--reject" style="width: 100%; border: none; cursor: pointer;">
                            ✕ Tolak Booking
                        </button>
                    </form>
                    <form action="/admin/bookings/{{ $booking->booking_id }}/approve" method="POST" style="display: inline-block; flex: 1; margin: 0;" onsubmit="return confirm('Setujui booking ini?')">
                        @csrf
                        <button type="submit" class="cr-abd-quick-btn cr-abd-quick-btn--approve" style="width: 100%; border: none; cursor: pointer;">
                            ✓ Setujui Booking
                        </button>
                    </form>
                </div>
            </div>
            @endif

        </div>{{-- end right --}}

    </div>{{-- end body grid --}}

</div>

{{-- Modal Edit Status --}}
<div class="cr-abd-modal-overlay" id="modalOverlay" style="display:none" onclick="closeEditStatus()"></div>
<div class="cr-abd-modal" id="modalEditStatus" style="display:none">
    <div class="cr-abd-modal__box">
        <h3 class="cr-abd-modal__title">✏ Edit Status Booking</h3>
        <form method="POST" action="/admin/bookings/{{ $booking->booking_id }}/edit-status">
            @csrf
            <div class="cr-abd-modal__field">
                <label class="cr-abd-modal__label">Status Baru</label>
                <select name="status" class="cr-abd-modal__select">
                    <option value="pending"  {{ $booking->status==='pending'  ? 'selected':'' }}>Pending</option>
                    <option value="approved" {{ $booking->status==='approved' ? 'selected':'' }}>Approved</option>
                    <option value="rejected" {{ $booking->status==='rejected' ? 'selected':'' }}>Rejected</option>
                </select>
            </div>
            <div class="cr-abd-modal__actions">
                <button type="button" class="cr-abd-modal__cancel" onclick="closeEditStatus()">Batal</button>
                <button type="submit" class="cr-abd-modal__submit">Simpan</button>
            </div>
        </form>
    </div>
</div>

<form id="form-delete-booking" method="POST" style="display: none;">
    @csrf
    @method('DELETE')
</form>

<style>
/* ============================================================
   ADMIN BOOKING DETAIL
   ============================================================ */
.cr-abd-content { 
    max-width: 1060px; 
    margin: 0 auto;
    }

.cr-abd-flash { padding:12px 16px; border-radius:10px; margin:56px 0 16px;
    font-family:'Plus Jakarta Sans',sans-serif; font-size:.875rem; font-weight:600; }
.cr-abd-flash--success { background:#D1FAF0; color:#00C896; border:1px solid #A0E8D8; }
.cr-abd-flash--error   { background:#FFF0F3; color:#FF4D6D; border:1px solid #FFD0D8; }

.cr-abd-back { display:inline-flex; align-items:center; gap:6px; margin-top:56px; margin-bottom:14px;
    font-family:'Plus Jakarta Sans',sans-serif; font-size:.813rem; font-weight:600;
    color:#9AAFC8; text-decoration:none; transition:color .15s; }
.cr-abd-back:hover { color:#1A2340; }

/* Header */
.cr-abd-header { margin-bottom:20px; }
.cr-abd-title { display:flex; align-items:center; gap:10px;
    font-family:'Space Grotesk',sans-serif; font-size:1.5rem; font-weight:700;
    color:#1A2340; margin:0 0 4px; letter-spacing:-.3px; }
.cr-abd-subtitle { font-family:'DM Sans',sans-serif; font-size:.875rem; color:#5A6A8A; margin:0; }

/* Body grid */
.cr-abd-body-grid { display:grid; grid-template-columns:1.4fr 1fr; gap:18px; align-items:start; }

/* Panel */
.cr-abd-panel {
    background:#fff; border:1.5px solid #EEF2FB; border-radius:16px;
    padding:18px 20px; box-shadow:0 2px 10px rgba(26,35,64,.06); margin-bottom:14px;
}
.cr-abd-panel__label { font-family:'DM Sans',sans-serif; font-size:.688rem; font-weight:700;
    letter-spacing:1.5px; color:#9AAFC8; text-transform:uppercase; margin:0 0 14px; }

/* Info user */
.cr-abd-info-user { display:flex; align-items:center; gap:12px; margin-bottom:16px;
    padding-bottom:14px; border-bottom:1px solid #EEF2FB; }
.cr-abd-info-avatar {
    width:40px; height:40px; border-radius:50%; flex-shrink:0;
    background:linear-gradient(135deg,#F4B400,#FFB020);
    display:flex; align-items:center; justify-content:center;
    font-family:'Plus Jakarta Sans',sans-serif; font-size:.938rem; font-weight:800; color:#1A2340;
}
.cr-abd-info-user__name { font-family:'DM Sans',sans-serif; font-size:.688rem; color:#9AAFC8; margin:0 0 2px; }
.cr-abd-info-user__val { font-family:'Plus Jakarta Sans',sans-serif; font-size:.875rem; font-weight:700; color:#1A2340; margin:0; }
.cr-abd-info-user__nim { font-weight:400; color:#5A6A8A; font-size:.813rem; }

/* Info rows */
.cr-abd-info-rows { display:flex; flex-direction:column; gap:10px; }
.cr-abd-info-row { display:flex; gap:8px; align-items:flex-start; }
.cr-abd-info-row__key { font-family:'DM Sans',sans-serif; font-size:.75rem; color:#9AAFC8; min-width:70px; flex-shrink:0; padding-top:2px; }
.cr-abd-info-row__val { display:flex; align-items:center; gap:8px; flex-wrap:wrap; font-family:'Plus Jakarta Sans',sans-serif; font-size:.813rem; font-weight:600; color:#1A2340; }
.cr-abd-info-row__val--text { font-family:'DM Sans',sans-serif; font-size:.813rem; color:#1A2340; font-weight:400; }

.cr-abd-org-badge { padding:2px 10px; background:rgba(79,195,247,.12); color:#4FC3F7; border-radius:999px; font-size:.688rem; font-weight:700; font-family:'Plus Jakarta Sans',sans-serif; }
.cr-abd-room-badge { padding:2px 10px; border-radius:999px; font-size:.688rem; font-weight:700; font-family:'Plus Jakarta Sans',sans-serif; }
.cr-abd-room-badge--active   { background:#D1FAF0; color:#00C896; }
.cr-abd-room-badge--inactive { background:#FFF0F3; color:#FF4D6D; }
.cr-abd-status-badge { padding:3px 12px; border-radius:999px; font-size:.688rem; font-weight:700; font-family:'Plus Jakarta Sans',sans-serif; }
.cr-abd-status-badge--green  { background:#D1FAF0; color:#00C896; }
.cr-abd-status-badge--yellow { background:#FFF3CD; color:#E6820A; }
.cr-abd-status-badge--red    { background:#FFF0F3; color:#FF4D6D; }

/* Surat */
.cr-abd-panel--surat .cr-abd-panel-header-row { display:flex; align-items:center; justify-content:space-between; margin-bottom:12px; }
.cr-abd-panel--surat .cr-abd-panel__label { margin-bottom:0; }
.cr-abd-surat-approved { padding:3px 12px; background:#D1FAF0; color:#00C896; border-radius:999px; font-family:'Plus Jakarta Sans',sans-serif; font-size:.688rem; font-weight:700; }
.cr-abd-surat-card { display:flex; align-items:center; gap:14px; padding:12px 14px; background:#F8FAFF; border:1.5px solid #EEF2FB; border-radius:12px; }
.cr-abd-surat-thumb { width:64px; height:56px; border-radius:8px; background:#E8EEF7; overflow:hidden; display:flex; align-items:center; justify-content:center; flex-shrink:0; }
.cr-abd-surat-thumb img { width:100%; height:100%; object-fit:cover; }
.cr-abd-surat-thumb__pdf span { font-size:1.5rem; }
.cr-abd-surat-name { font-family:'Plus Jakarta Sans',sans-serif; font-size:.813rem; font-weight:700; color:#1A2340; margin:0 0 6px; }
.cr-abd-surat-links { display:flex; gap:10px; }
.cr-abd-surat-link { font-family:'Plus Jakarta Sans',sans-serif; font-size:.75rem; font-weight:600; color:#4FC3F7; text-decoration:none; }
.cr-abd-surat-link:hover { color:#0277BD; }

/* Admin actions */
.cr-abd-actions { display:flex; gap:10px; flex-wrap:wrap; }
.cr-abd-action-btn { flex:1; min-width:100px; padding:11px 14px; border:none; border-radius:10px; cursor:pointer; font-family:'Plus Jakarta Sans',sans-serif; font-size:.813rem; font-weight:700; display:flex; align-items:center; justify-content:center; gap:6px; transition:transform .15s, box-shadow .15s; }
.cr-abd-action-btn:hover { transform:scale(1.02); }
.cr-abd-action-btn--edit   { background:linear-gradient(135deg,#F4B400,#FFB020); color:#1A2340; box-shadow:0 3px 10px rgba(244,180,0,.25); }
.cr-abd-action-btn--notif  { background:#1A2340; color:#fff; box-shadow:0 3px 10px rgba(26,35,64,.18); }
.cr-abd-action-btn--hapus  { background:#FF4D6D; color:#fff; box-shadow:0 3px 10px rgba(255,77,109,.25); }

/* Log */
.cr-abd-log-list { display:flex; flex-direction:column; gap:8px; }
.cr-abd-log-item { display:flex; align-items:flex-start; gap:10px; }
.cr-abd-log-dot { width:10px; height:10px; border-radius:50%; flex-shrink:0; margin-top:4px; }
.cr-abd-log-dot--yellow { background:#F4B400; }
.cr-abd-log-dot--blue   { background:#4FC3F7; }
.cr-abd-log-dot--green  { background:#00C896; }
.cr-abd-log-dot--red    { background:#FF4D6D; }
.cr-abd-log-text { font-family:'DM Sans',sans-serif; font-size:.75rem; color:#5A6A8A; margin:0; flex:1; line-height:1.5; }
.cr-abd-log-time { font-family:'DM Sans',sans-serif; font-size:.688rem; color:#9AAFC8; white-space:nowrap; flex-shrink:0; }

/* ── TIMELINE STATUS ── */
.cr-abd-timeline { display:flex; flex-direction:column; }
.cr-abd-tl-step { display:flex; align-items:center; gap:12px; }
.cr-abd-tl-step--final { align-items:center; }
.cr-abd-tl-dot { width:16px; height:16px; border-radius:50%; border:2.5px solid #E8EEF7; background:#fff; flex-shrink:0; }
.cr-abd-tl-dot--yellow { border-color:#F4B400; background:#F4B400; }
.cr-abd-tl-dot--blue   { border-color:#4FC3F7; background:#4FC3F7; }
.cr-abd-tl-dot--empty  { border-color:#D0D9E8; background:#fff; }

.cr-abd-tl-line { width:2px; height:28px; background:#EEF2FB; margin:4px 0 4px 7px; }
.cr-abd-tl-line--done  { background:#4FC3F7; }
.cr-abd-tl-line--green { background:#00C896; }
.cr-abd-tl-line--red   { background:#FF4D6D; }

.cr-abd-tl-info { flex:1; }
.cr-abd-tl-label { font-family:'Plus Jakarta Sans',sans-serif; font-size:.813rem; font-weight:700; color:#1A2340; margin:0 0 1px; }
.cr-abd-tl-label--green { color:#00C896; }
.cr-abd-tl-label--red   { color:#FF4D6D; }
.cr-abd-tl-time { font-family:'DM Sans',sans-serif; font-size:.688rem; color:#9AAFC8; margin:0; }

.cr-abd-tl-final-check {
    width:40px; height:40px; border-radius:50%;
    display:flex; align-items:center; justify-content:center;
    font-size:1.25rem; font-weight:800; flex-shrink:0;
}
.cr-abd-tl-final-check--green { background:#00C896; color:#fff; box-shadow:0 3px 12px rgba(0,200,150,.35); }
.cr-abd-tl-final-check--red   { background:#FF4D6D; color:#fff; box-shadow:0 3px 12px rgba(255,77,109,.35); }

/* Data Peminjam */
.cr-abd-peminjam { display:flex; gap:14px; align-items:flex-start; }
.cr-abd-peminjam__avatar {
    width:52px; height:52px; border-radius:12px; flex-shrink:0;
    background:linear-gradient(135deg,#F4B400,#FFB020);
    display:flex; align-items:center; justify-content:center;
    font-family:'Plus Jakarta Sans',sans-serif; font-size:1.25rem; font-weight:800; color:#1A2340;
}
.cr-abd-peminjam__rows { flex:1; display:flex; flex-direction:column; gap:6px; }
.cr-abd-peminjam__row { display:flex; gap:8px; }
.cr-abd-peminjam__key { font-family:'DM Sans',sans-serif; font-size:.75rem; color:#9AAFC8; width:48px; flex-shrink:0; }
.cr-abd-peminjam__val { font-family:'Plus Jakarta Sans',sans-serif; font-size:.75rem; font-weight:600; color:#1A2340; }

/* Quick action */
.cr-abd-quick-actions { display:flex; gap:10px; }
.cr-abd-quick-btn { flex:1; padding:11px 14px; border-radius:10px; border:2px solid transparent; cursor:pointer; font-family:'Plus Jakarta Sans',sans-serif; font-size:.813rem; font-weight:800; text-decoration:none; display:flex; align-items:center; justify-content:center; gap:6px; transition:transform .15s; }
.cr-abd-quick-btn:hover { transform:scale(1.02); }
.cr-abd-quick-btn--reject  { background:#FFF0F3; color:#FF4D6D; border-color:#FF4D6D; }
.cr-abd-quick-btn--reject:hover { background:#FF4D6D; color:#fff; }
.cr-abd-quick-btn--approve { background:linear-gradient(135deg,#00C896,#00A87E); color:#fff; box-shadow:0 3px 12px rgba(0,200,150,.28); }

/* Modal */
.cr-abd-modal-overlay { position:fixed; inset:0; background:rgba(26,35,64,.40); backdrop-filter:blur(2px); z-index:200; }
.cr-abd-modal { position:fixed; top:50%; left:50%; transform:translate(-50%,-50%); z-index:201; width:100%; max-width:380px; padding:0 16px; }
.cr-abd-modal__box { background:#fff; border-radius:18px; padding:26px 24px; box-shadow:0 20px 60px rgba(26,35,64,.18); }
.cr-abd-modal__title { font-family:'Space Grotesk',sans-serif; font-size:1.0625rem; font-weight:700; color:#1A2340; margin:0 0 18px; }
.cr-abd-modal__field { display:flex; flex-direction:column; gap:6px; margin-bottom:18px; }
.cr-abd-modal__label { font-family:'Plus Jakarta Sans',sans-serif; font-size:.813rem; font-weight:700; color:#1A2340; }
.cr-abd-modal__select { padding:10px 14px; background:#F8FAFF; border:1.5px solid #E8EEF7; border-radius:10px; font-family:'DM Sans',sans-serif; font-size:.875rem; color:#1A2340; outline:none; }
.cr-abd-modal__actions { display:flex; gap:10px; }
.cr-abd-modal__cancel { flex:1; padding:11px; background:#F8FAFF; border:1.5px solid #E8EEF7; border-radius:10px; font-family:'Plus Jakarta Sans',sans-serif; font-size:.875rem; font-weight:600; color:#5A6A8A; cursor:pointer; }
.cr-abd-modal__submit { flex:2; padding:11px; background:linear-gradient(135deg,#F4B400,#FFB020); border:none; border-radius:10px; font-family:'Plus Jakarta Sans',sans-serif; font-size:.875rem; font-weight:800; color:#1A2340; cursor:pointer; }

@media (max-width:860px) { .cr-abd-body-grid { grid-template-columns:1fr; } }
</style>

<script>
window.openEditStatus  = function() { document.getElementById('modalEditStatus').style.display=''; document.getElementById('modalOverlay').style.display=''; };
window.closeEditStatus = function() { document.getElementById('modalEditStatus').style.display='none'; document.getElementById('modalOverlay').style.display='none'; };
window.kirimNotifikasi = function(id) { alert('Notifikasi dikirim ke peminjam untuk booking #'+id); };
window.hapusBooking    = function(id) {
    if (confirm('Hapus booking ini? Tindakan tidak dapat dibatalkan.')) {
        var form = document.getElementById('form-delete-booking');
        form.action = '/admin/booking/' + id + '/hapus';
        form.submit();
    }
};
document.addEventListener('keydown', e => { if(e.key==='Escape') closeEditStatus(); });
</script>
@endsection