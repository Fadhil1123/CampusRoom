@extends($layout)

@section('title', 'Profil Saya')

@section('content')
<div class="cr-dash-content cr-prf-content">

    {{-- Brand pill --}}
    <div class="cr-dash-brand-pill">
        <span class="cr-dash-brand-pill__icon">🟨</span>
        <span class="cr-dash-brand-pill__text">CampusRoom • v1.0 {{ session('user') && session('user')->role === 'admin' ? 'Admin' : 'Mahasiswa' }}</span>
    </div>

    {{-- Header --}}
    <div class="cr-prf-header">
        <h1 class="cr-prf-header__title">👤 Profil Saya</h1>
        <p class="cr-prf-header__sub">Kelola informasi akun dan keamanan kamu di sini.</p>
    </div>

    @if(session('success'))
    <div class="cr-prf-flash cr-prf-flash--success">✅ {{ session('success') }}</div>
    @endif
    @if(session('error'))
    <div class="cr-prf-flash cr-prf-flash--error">⚠️ {{ session('error') }}</div>
    @endif

    <div class="cr-prf-grid">

        {{-- ===== LEFT: Avatar + Stats card ===== --}}
        <div class="cr-prf-side">
            <div class="cr-prf-panel cr-prf-panel--avatar">

                <form action="/profile/update" method="POST" enctype="multipart/form-data" id="fotoForm">
                    @csrf
                    <label for="fotoInput" class="cr-prf-avatar-wrap">
                        @if($user->foto)
                            <img src="{{ asset('storage/' . $user->foto) }}" alt="{{ $user->nama }}" class="cr-prf-avatar-img">
                        @else
                            <div class="cr-prf-avatar-initials {{ $user->role === 'admin' ? 'cr-prf-avatar-initials--admin' : '' }}">
                                {{ strtoupper(substr($user->nama, 0, 1)) }}
                            </div>
                        @endif
                        <span class="cr-prf-avatar-edit">📷</span>
                        <input type="file" name="foto" id="fotoInput" accept="image/*" class="cr-prf-avatar-input" onchange="document.getElementById('fotoForm').submit()">
                    </label>
                </form>

                @if($user->foto)
                <form action="/profile/delete-photo" method="POST" onsubmit="return confirm('Hapus foto profil?')">
                    @csrf
                    <button type="submit" class="cr-prf-remove-photo">Hapus Foto</button>
                </form>
                @endif

                <h2 class="cr-prf-name">{{ $user->nama }}</h2>
                <p class="cr-prf-nim">{{ $user->nim_nip }}</p>

                @if($user->role === 'admin')
                    <span class="cr-prf-role-badge cr-prf-role-badge--admin">ADMIN</span>
                @else
                    <span class="cr-prf-role-badge">Mahasiswa</span>
                @endif

                @if($user->role === 'admin' && $user->jurusan)
                <p class="cr-prf-jurusan">🎓 {{ $user->jurusan }}</p>
                @elseif($user->role !== 'admin')
                <p class="cr-prf-jurusan">🎓 Teknologi Informasi</p>
                @endif
            </div>

            {{-- Stats (hanya untuk mahasiswa) --}}
            @if($user->role !== 'admin')
            <div class="cr-prf-panel cr-prf-panel--stats">
                <h3 class="cr-prf-panel__title">Ringkasan Booking</h3>
                <div class="cr-prf-stat-row">
                    <div class="cr-prf-stat-item">
                        <span class="cr-prf-stat-num">{{ $totalBooking }}</span>
                        <span class="cr-prf-stat-label">Total</span>
                    </div>
                    <div class="cr-prf-stat-item cr-prf-stat-item--green">
                        <span class="cr-prf-stat-num">{{ $bookingApproved }}</span>
                        <span class="cr-prf-stat-label">Disetujui</span>
                    </div>
                    <div class="cr-prf-stat-item cr-prf-stat-item--yellow">
                        <span class="cr-prf-stat-num">{{ $bookingPending }}</span>
                        <span class="cr-prf-stat-label">Menunggu</span>
                    </div>
                </div>
                <a href="/booking/history" class="cr-prf-stat-link">Lihat Riwayat Booking →</a>
            </div>
            @endif
        </div>

        {{-- ===== RIGHT: Form Edit + Password ===== --}}
        <div class="cr-prf-main">

            {{-- Form Edit Profil --}}
            <div class="cr-prf-panel">
                <h3 class="cr-prf-panel__title">📝 Informasi Profil</h3>

                <form action="/profile/update" method="POST" class="cr-prf-form">
                    @csrf

                    <div class="cr-prf-form-grid">
                        <div class="cr-prf-field">
                            <label class="cr-prf-label">Nama Lengkap</label>
                            <input type="text" class="cr-prf-input cr-prf-input--disabled" value="{{ $user->nama }}" disabled>
                            <span class="cr-prf-hint">Nama lengkap tidak dapat diubah</span>
                        </div>

                        <div class="cr-prf-field">
                            <label class="cr-prf-label">NIM / NIP</label>
                            <input type="text" class="cr-prf-input cr-prf-input--disabled" value="{{ $user->nim_nip }}" disabled>
                            <span class="cr-prf-hint">NIM/NIP tidak dapat diubah</span>
                        </div>

                        <div class="cr-prf-field">
                            <label class="cr-prf-label">Email</label>
                            <input type="email" name="email" class="cr-prf-input"
                                   placeholder="nama@student.univ.ac.id"
                                   value="{{ old('email', $user->email) }}">
                        </div>

                        <div class="cr-prf-field">
                            <label class="cr-prf-label">No. HP</label>
                            <input type="text" name="no_hp" class="cr-prf-input"
                                   placeholder="08xxxxxxxxxx"
                                   value="{{ old('no_hp', $user->no_hp) }}">
                        </div>

                        <div class="cr-prf-field cr-prf-field--full">
                            <label class="cr-prf-label">
                                {{ $user->role === 'admin' ? 'Jabatan / Unit' : 'Jurusan / Program Studi' }}
                            </label>
                            @if($user->role === 'admin')
                                <input type="text" name="jurusan" class="cr-prf-input"
                                       placeholder="Contoh: Tata Usaha Fakultas"
                                       value="{{ old('jurusan', $user->jurusan) }}">
                            @else
                                <input type="text" class="cr-prf-input cr-prf-input--disabled"
                                       value="Teknologi Informasi" disabled>
                                <span class="cr-prf-hint">Jurusan / Program Studi tidak dapat diubah</span>
                            @endif
                        </div>
                    </div>

                    <button type="submit" class="cr-prf-btn-save">💾 Simpan Perubahan</button>
                </form>
            </div>

            {{-- Form Ganti Password --}}
            <div class="cr-prf-panel">
                <h3 class="cr-prf-panel__title">🔒 Keamanan — Ubah Password</h3>

                @if(session('success_password'))
                <div class="cr-prf-flash cr-prf-flash--success">✅ {{ session('success_password') }}</div>
                @endif
                @if(session('error_password'))
                <div class="cr-prf-flash cr-prf-flash--error">⚠️ {{ session('error_password') }}</div>
                @endif

                <form action="/profile/update-password" method="POST" class="cr-prf-form">
                    @csrf

                    <div class="cr-prf-form-grid">
                        <div class="cr-prf-field cr-prf-field--full">
                            <label class="cr-prf-label">Password Lama</label>
                            <input type="password" name="password_lama" class="cr-prf-input" placeholder="••••••••" required>
                        </div>

                        <div class="cr-prf-field">
                            <label class="cr-prf-label">Password Baru</label>
                            <input type="password" name="password_baru" class="cr-prf-input" placeholder="Minimal 6 karakter" required minlength="6">
                        </div>

                        <div class="cr-prf-field">
                            <label class="cr-prf-label">Konfirmasi Password Baru</label>
                            <input type="password" name="password_baru_confirmation" class="cr-prf-input" placeholder="Ulangi password baru" required minlength="6">
                        </div>
                    </div>

                    <button type="submit" class="cr-prf-btn-save cr-prf-btn-save--danger">🔑 Ubah Password</button>
                </form>
            </div>

        </div>

    </div>

</div>

<style>
/* ============================================================
   PROFIL PAGE
   ============================================================ */
.cr-prf-content { max-width: 880px; }

.cr-prf-header { margin-top: 56px; margin-bottom: 20px; }
.cr-prf-header__title {
    font-family: 'Space Grotesk', sans-serif; font-size: 1.5rem; font-weight: 700;
    color: #1A2340; margin: 0 0 4px; letter-spacing: -0.3px;
}
.cr-prf-header__sub {
    font-family: 'DM Sans', sans-serif; font-size: 0.875rem; color: #5A6A8A; margin: 0;
}

.cr-prf-flash {
    padding: 12px 16px; border-radius: 10px; font-family: 'Plus Jakarta Sans', sans-serif;
    font-size: 0.875rem; font-weight: 600; margin-bottom: 16px;
}
.cr-prf-flash--success { background: #D1FAF0; border: 1px solid #A0E8D8; color: #00C896; }
.cr-prf-flash--error   { background: #FFF0F3; border: 1px solid #FFD0D8; color: #FF4D6D; }

/* Grid layout */
.cr-prf-grid { display: grid; grid-template-columns: 260px 1fr; gap: 18px; margin-bottom: 32px; }
.cr-prf-side { display: flex; flex-direction: column; gap: 16px; }
.cr-prf-main { display: flex; flex-direction: column; gap: 16px; }

/* Panel */
.cr-prf-panel {
    background: #fff; border-radius: 16px; padding: 22px;
    box-shadow: 0 2px 8px rgba(26,35,64,0.06); border: 1.5px solid #EEF2FB;
}
.cr-prf-panel__title {
    font-family: 'Plus Jakarta Sans', sans-serif; font-size: 0.938rem; font-weight: 800;
    color: #1A2340; margin: 0 0 16px;
}

/* Avatar panel */
.cr-prf-panel--avatar { display: flex; flex-direction: column; align-items: center; text-align: center; }
.cr-prf-avatar-wrap {
    position: relative; display: block; width: 96px; height: 96px; cursor: pointer; margin-bottom: 14px;
}
.cr-prf-avatar-img {
    width: 96px; height: 96px; border-radius: 50%; object-fit: cover; border: 3px solid rgba(244,180,0,0.4);
}
.cr-prf-avatar-initials {
    width: 96px; height: 96px; border-radius: 50%;
    background: linear-gradient(135deg, #F4B400, #FFD54F);
    display: flex; align-items: center; justify-content: center;
    font-family: 'Plus Jakarta Sans', sans-serif; font-weight: 800; font-size: 2.25rem; color: #1A2340;
    border: 3px solid rgba(255,255,255,0.6);
}
.cr-prf-avatar-initials--admin {
    background: linear-gradient(135deg, #F4B400, #FFD54F);
}
.cr-prf-avatar-edit {
    position: absolute; bottom: 0; right: 0; width: 30px; height: 30px; border-radius: 50%;
    background: #1A2340; color: #fff; display: flex; align-items: center; justify-content: center;
    font-size: 0.875rem; border: 2.5px solid #fff;
}
.cr-prf-avatar-input { display: none; }

.cr-prf-remove-photo {
    background: none; border: none; color: #FF4D6D; font-family: 'Plus Jakarta Sans', sans-serif;
    font-size: 0.72rem; font-weight: 600; cursor: pointer; text-decoration: underline; margin-bottom: 12px;
}

.cr-prf-name {
    font-family: 'Plus Jakarta Sans', sans-serif; font-size: 1.063rem; font-weight: 800;
    color: #1A2340; margin: 0 0 2px;
}
.cr-prf-nim {
    font-family: 'DM Sans', sans-serif; font-size: 0.813rem; color: #9AAFC8; margin: 0 0 10px;
}
.cr-prf-role-badge {
    display: inline-block; background: rgba(79,195,247,0.12); color: #0277BD;
    font-family: 'Plus Jakarta Sans', sans-serif; font-size: 0.7rem; font-weight: 800;
    padding: 3px 12px; border-radius: 999px; margin-bottom: 10px;
}
.cr-prf-role-badge--admin { background: #FF4D6D; color: #fff; }
.cr-prf-jurusan {
    font-family: 'DM Sans', sans-serif; font-size: 0.813rem; color: #5A6A8A; margin: 4px 0 0;
}

/* Stats panel */
.cr-prf-stat-row { display: flex; gap: 8px; margin-bottom: 14px; }
.cr-prf-stat-item {
    flex: 1; display: flex; flex-direction: column; align-items: center; gap: 2px;
    background: #F8FAFF; border-radius: 10px; padding: 10px 4px;
}
.cr-prf-stat-item--green  { background: #F0FFFA; }
.cr-prf-stat-item--yellow { background: #FFFBF0; }
.cr-prf-stat-num {
    font-family: 'Space Grotesk', sans-serif; font-size: 1.25rem; font-weight: 700; color: #1A2340;
}
.cr-prf-stat-label {
    font-family: 'DM Sans', sans-serif; font-size: 0.65rem; color: #9AAFC8;
}
.cr-prf-stat-link {
    display: block; text-align: center; font-family: 'Plus Jakarta Sans', sans-serif;
    font-size: 0.775rem; font-weight: 700; color: #4FC3F7; text-decoration: none;
}
.cr-prf-stat-link:hover { color: #0277BD; }

/* Form */
.cr-prf-form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 14px; margin-bottom: 16px; }
.cr-prf-field { display: flex; flex-direction: column; gap: 5px; }
.cr-prf-field--full { grid-column: 1 / -1; }
.cr-prf-label {
    font-family: 'Plus Jakarta Sans', sans-serif; font-size: 0.788rem; font-weight: 700; color: #1A2340;
}
.cr-prf-input {
    padding: 10px 13px; border: 1.5px solid #E8EEF7; border-radius: 9px;
    font-family: 'DM Sans', sans-serif; font-size: 0.875rem; color: #1A2340; background: #FAFBFF;
    outline: none; transition: border-color .2s;
}
.cr-prf-input:focus { border-color: #F4B400; box-shadow: 0 0 0 3px rgba(244,180,0,.08); background: #fff; }
.cr-prf-input--disabled { background: #F0F4FF; color: #9AAFC8; cursor: not-allowed; }
.cr-prf-hint { font-family: 'DM Sans', sans-serif; font-size: 0.7rem; color: #9AAFC8; }

.cr-prf-btn-save {
    padding: 11px 24px; background: linear-gradient(135deg, #F4B400, #FFB020); color: #1A2340;
    font-family: 'Plus Jakarta Sans', sans-serif; font-size: 0.875rem; font-weight: 800;
    border: none; border-radius: 10px; cursor: pointer;
    box-shadow: 0 3px 10px rgba(244,180,0,.28); transition: transform .15s;
}
.cr-prf-btn-save:hover { transform: scale(1.02); }
.cr-prf-btn-save--danger {
    background: linear-gradient(135deg, #FF4D6D, #FF7B93);
    box-shadow: 0 3px 10px rgba(255,77,109,.28);
}

@media (max-width: 768px) {
    .cr-prf-grid { grid-template-columns: 1fr; }
    .cr-prf-form-grid { grid-template-columns: 1fr; }
}
</style>

@endsection