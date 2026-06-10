@extends('layouts.dashboard')

@section('title', 'Form Booking Perkuliahan')

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

    {{-- Error / success --}}
    @if(session('error'))
    <div class="cr-bk-alert cr-bk-alert--error">⚠️ {{ session('error') }}</div>
    @endif
    @if(session('success'))
    <div class="cr-bk-alert cr-bk-alert--success">✅ {{ session('success') }}</div>
    @endif

    <form action="/booking/perkuliahan/store" method="POST" id="formBooking">
        @csrf

        {{-- ======== RUANGAN YANG DIPILIH ======== --}}
        <section class="cr-bk-section">
            <h2 class="cr-bk-section__title">📌 Ruangan yang Dipilih</h2>

            <div class="cr-bk-room-card" id="roomCard">
                {{-- Gambar ruangan --}}
                <div class="cr-bk-room-card__img" id="roomImg">
                    <svg viewBox="0 0 80 60" fill="none" xmlns="http://www.w3.org/2000/svg" style="width:100%;height:auto;">
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

                {{-- Info ruangan --}}
                <div class="cr-bk-room-card__info">
                    <p class="cr-bk-room-card__name" id="roomName">
                        @if($selectedRoom) {{ $selectedRoom->nama_ruangan }} @else Pilih Ruangan @endif
                    </p>
                    <div class="cr-bk-room-card__meta">
                        <span id="roomKapasitas">
                            @if($selectedRoom) 👥 {{ $selectedRoom->kapasitas }} peserta @else – @endif
                        </span>
                        <span>🏢 Lantai 2</span>
                        <span>🏛️ Gedung A</span>
                        <span id="roomStatus">
                            @if($selectedRoom)
                                @if($selectedRoom->status === 'tersedia')
                                    <span class="cr-bk-badge cr-bk-badge--green">✅ Available</span>
                                @else
                                    <span class="cr-bk-badge cr-bk-badge--red">❌ Tidak Tersedia</span>
                                @endif
                            @endif
                        </span>
                    </div>
                </div>

                <button type="button" class="cr-bk-ganti-btn" id="btnGantiRuangan">Ganti Ruangan</button>
            </div>

            {{-- Hidden select ruangan (muncul saat klik Ganti) --}}
            <div class="cr-bk-room-select-wrap" id="roomSelectWrap" style="{{ $selectedRoom ? 'display:none' : '' }}">
                <label class="cr-bk-label">Pilih Ruangan</label>
                <select name="room_id" id="roomSelect" class="cr-bk-select" required>
                    <option value="">-- Pilih Ruangan --</option>
                    @foreach($rooms as $room)
                        <option value="{{ $room->room_id }}"
                            data-nama="{{ $room->nama_ruangan }}"
                            data-kapasitas="{{ $room->kapasitas }}"
                            data-status="{{ $room->status }}"
                            {{ $selectedRoom && $selectedRoom->room_id == $room->room_id ? 'selected' : '' }}>
                            {{ $room->nama_ruangan }} ({{ $room->kapasitas }} orang)
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Hidden input room_id untuk form submit (di-sync dari select) --}}
            <input type="hidden" name="room_id" id="roomIdHidden"
                value="{{ $selectedRoom ? $selectedRoom->room_id : '' }}">
        </section>

        {{-- ======== DATA PERKULIAHAN ======== --}}
        <section class="cr-bk-section">
            <h2 class="cr-bk-section__title">📚 Data Perkuliahan</h2>

            <div class="cr-bk-form-grid">

                {{-- Mata Kuliah --}}
                <div class="cr-bk-field">
                    <label class="cr-bk-label">Mata Kuliah</label>
                    <div class="cr-bk-select-wrap">
                        <select name="mata_kuliah" id="mataKuliahSelect" class="cr-bk-select cr-bk-select--dropdown" required>
                            <option value="">Pilih Mata Kuliah</option>
                            @foreach($schedules as $sc)
                                <option value="{{ $sc->mata_kuliah }}">{{ $sc->mata_kuliah }}</option>
                            @endforeach
                        </select>
                        <span class="cr-bk-select-caret">∨</span>
                    </div>
                    {{-- Dropdown list custom --}}
                    <div class="cr-bk-dropdown-list" id="mkDropdown">
                        @foreach($schedules as $sc)
                            <div class="cr-bk-dropdown-item" data-value="{{ $sc->mata_kuliah }}">{{ $sc->mata_kuliah }}</div>
                        @endforeach
                    </div>
                </div>

                {{-- Dosen --}}
                <div class="cr-bk-field">
                    <label class="cr-bk-label">Dosen</label>
                    <div class="cr-bk-select-wrap">
                        <select name="dosen" id="dosenSelect" class="cr-bk-select" required>
                            <option value="">Pilih Dosen</option>
                            @foreach($schedules->pluck('dosen')->unique() as $dosen)
                                <option value="{{ $dosen }}">{{ $dosen }}</option>
                            @endforeach
                        </select>
                        <span class="cr-bk-select-caret">∨</span>
                    </div>
                </div>

                {{-- Tanggal --}}
                <div class="cr-bk-field">
                    <label class="cr-bk-label">Tanggal</label>
                    <div class="cr-bk-input-wrap cr-bk-input-wrap--icon">
                        <input type="date" name="tanggal" id="inputTanggal"
                            class="cr-bk-input"
                            min="{{ date('Y-m-d') }}"
                            required>
                        <span class="cr-bk-input-icon">📅</span>
                    </div>
                </div>

                {{-- Waktu --}}
                <div class="cr-bk-field">
                    <label class="cr-bk-label">Waktu</label>
                    <div class="cr-bk-time-row">
                        <div class="cr-bk-time-wrap">
                            <label class="cr-bk-label-sm">Mulai</label>
                            <div class="cr-bk-input-wrap">
                                <input type="time" name="jam_mulai" id="inputJamMulai"
                                    class="cr-bk-input cr-bk-input--time"
                                    value="08:00" required>
                            </div>
                        </div>
                        <span class="cr-bk-time-sep">—</span>
                        <div class="cr-bk-time-wrap">
                            <label class="cr-bk-label-sm">Selesai</label>
                            <div class="cr-bk-input-wrap">
                                <input type="time" name="jam_selesai" id="inputJamSelesai"
                                    class="cr-bk-input cr-bk-input--time"
                                    value="10:00" required>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </section>

        {{-- ======== CEK KETERSEDIAAN ======== --}}
        <section class="cr-bk-section">
            <h2 class="cr-bk-section__title">🔍 Cek Ketersediaan</h2>

            <div class="cr-bk-avail-row">
                {{-- Status availability --}}
                <div class="cr-bk-avail-status" id="availStatus">
                    <span class="cr-bk-avail-placeholder">🔎 Mengecek ketersediaan...</span>
                </div>

                {{-- Conflict warning --}}
                <div class="cr-bk-conflict-box" id="conflictBox" style="display:none">
                    <span class="cr-bk-conflict-icon">🔴</span>
                    <div>
                        <p class="cr-bk-conflict-title">Konflik Warning!</p>
                        <p class="cr-bk-conflict-detail" id="conflictDetail"></p>
                    </div>
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
</style>

<script>
(function () {
    // ─── Elemen ───────────────────────────────────────────────
    const roomSelect    = document.getElementById('roomSelect');
    const roomIdHidden  = document.getElementById('roomIdHidden');
    const roomName      = document.getElementById('roomName');
    const roomKapasitas = document.getElementById('roomKapasitas');
    const roomStatus    = document.getElementById('roomStatus');
    const btnGanti      = document.getElementById('btnGantiRuangan');
    const roomSelectWrap= document.getElementById('roomSelectWrap');

    const inputTanggal  = document.getElementById('inputTanggal');
    const inputMulai    = document.getElementById('inputJamMulai');
    const inputSelesai  = document.getElementById('inputJamSelesai');

    const availStatus   = document.getElementById('availStatus');
    const conflictBox   = document.getElementById('conflictBox');
    const conflictDetail= document.getElementById('conflictDetail');
    const btnSubmit     = document.getElementById('btnSubmit');

    let debounceTimer   = null;

    // ─── Ganti Ruangan toggle ──────────────────────────────────
    btnGanti.addEventListener('click', () => {
        roomSelectWrap.style.display = roomSelectWrap.style.display === 'none' ? '' : 'none';
    });

    // Sync select → hidden + update card
    if (roomSelect) {
        roomSelect.addEventListener('change', () => {
            const opt = roomSelect.options[roomSelect.selectedIndex];
            roomIdHidden.value = opt.value;
            roomName.textContent = opt.dataset.nama || 'Pilih Ruangan';
            roomKapasitas.textContent = opt.dataset.kapasitas ? '👥 ' + opt.dataset.kapasitas + ' peserta' : '–';
            const avail = opt.dataset.status === 'tersedia';
            roomStatus.innerHTML = avail
                ? '<span class="cr-bk-badge cr-bk-badge--green">✅ Available</span>'
                : '<span class="cr-bk-badge cr-bk-badge--red">❌ Tidak Tersedia</span>';
            roomSelectWrap.style.display = 'none';
            triggerCek();
        });
    }

    // ─── Cek Ketersediaan realtime ─────────────────────────────
    function triggerCek() {
        clearTimeout(debounceTimer);
        debounceTimer = setTimeout(cekKetersediaan, 500);
    }

    [inputTanggal, inputMulai, inputSelesai].forEach(el => {
        if (el) el.addEventListener('change', triggerCek);
    });

    function cekKetersediaan() {
        const roomId    = roomIdHidden.value;
        const tanggal   = inputTanggal.value;
        const jamMulai  = inputMulai.value;
        const jamSelesai= inputSelesai.value;

        if (!roomId || !tanggal || !jamMulai || !jamSelesai) {
            availStatus.className = 'cr-bk-avail-status';
            availStatus.innerHTML = '<span class="cr-bk-avail-placeholder">🔎 Lengkapi data untuk cek ketersediaan...</span>';
            conflictBox.style.display = 'none';
            return;
        }

        // loading state
        availStatus.className = 'cr-bk-avail-status checking';
        availStatus.innerHTML = '⏳ Mengecek ketersediaan...';
        conflictBox.style.display = 'none';

        const token = document.querySelector('meta[name="csrf-token"]')?.content
                      || document.querySelector('input[name="_token"]')?.value;

        fetch('/booking/cek-ketersediaan', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': token,
                'Accept': 'application/json',
            },
            body: JSON.stringify({ room_id: roomId, tanggal, jam_mulai: jamMulai, jam_selesai: jamSelesai }),
        })
        .then(r => r.json())
        .then(data => {
            if (data.status === 'available') {
                availStatus.className = 'cr-bk-avail-status available';
                availStatus.innerHTML = '✅ ' + data.message;
                conflictBox.style.display = 'none';
                btnSubmit.disabled = false;
            } else if (data.status === 'conflict') {
                availStatus.className = 'cr-bk-avail-status';
                availStatus.innerHTML = '❌ Tidak tersedia';
                conflictDetail.textContent = data.message + (data.detail ? ' — ' + data.detail : '');
                conflictBox.style.display = 'flex';
                btnSubmit.disabled = true;
            } else {
                availStatus.className = 'cr-bk-avail-status';
                availStatus.innerHTML = '🔎 Lengkapi semua data...';
                conflictBox.style.display = 'none';
            }
        })
        .catch(() => {
            availStatus.className = 'cr-bk-avail-status';
            availStatus.innerHTML = '⚠️ Gagal cek ketersediaan';
        });
    }

    // Jika sudah ada room terpilih dari query string, langsung trigger cek
    if (roomIdHidden.value) {
        triggerCek();
    }
})();
</script>
@endsection