@extends('layouts.dashboard')

@section('title', 'Detail Ruangan: ' . $room->nama_ruangan)

@section('content')
<div class="cr-dash-content cr-rd-content">

    {{-- Brand pill --}}
    <div class="cr-dash-brand-pill">
        <span class="cr-dash-brand-pill__icon">🟨</span>
        <span class="cr-dash-brand-pill__text">CampusRoom • v1.0 Admin</span>
    </div>

    {{-- Back button --}}
    <a href="/rooms" class="cr-rd-back">← Kembali ke Daftar Ruangan</a>

    {{-- HERO IMAGE --}}
    <div class="cr-rd-hero cr-rd-hero--{{ ($room->room_id % 4) + 1 }}">
        <svg viewBox="0 0 900 220" xmlns="http://www.w3.org/2000/svg" class="cr-rd-hero-svg">
            {{-- ceiling lights --}}
            <rect x="180" y="0" width="120" height="14" rx="4" fill="rgba(255,255,255,0.18)"/>
            <rect x="600" y="0" width="120" height="14" rx="4" fill="rgba(255,255,255,0.18)"/>
            {{-- back wall window --}}
            <rect x="60" y="30" width="160" height="100" rx="6" fill="rgba(255,255,255,0.12)" stroke="rgba(255,255,255,0.2)" stroke-width="1.5"/>
            <line x1="140" y1="30" x2="140" y2="130" stroke="rgba(255,255,255,0.15)" stroke-width="1"/>
            <line x1="60" y1="80" x2="220" y2="80" stroke="rgba(255,255,255,0.15)" stroke-width="1"/>
            {{-- whiteboard --}}
            <rect x="280" y="20" width="380" height="110" rx="5" fill="rgba(255,255,255,0.22)" stroke="rgba(255,255,255,0.30)" stroke-width="1.5"/>
            <line x1="310" y1="50" x2="560" y2="50" stroke="rgba(26,35,64,0.10)" stroke-width="1.5"/>
            <line x1="310" y1="70" x2="520" y2="70" stroke="rgba(26,35,64,0.10)" stroke-width="1.5"/>
            <line x1="310" y1="90" x2="540" y2="90" stroke="rgba(26,35,64,0.10)" stroke-width="1.5"/>
            {{-- desk rows --}}
            <rect x="60"  y="155" width="120" height="30" rx="4" fill="rgba(255,255,255,0.28)"/>
            <rect x="200" y="155" width="120" height="30" rx="4" fill="rgba(255,255,255,0.28)"/>
            <rect x="340" y="155" width="120" height="30" rx="4" fill="rgba(255,255,255,0.28)"/>
            <rect x="480" y="155" width="120" height="30" rx="4" fill="rgba(255,255,255,0.28)"/>
            <rect x="620" y="155" width="120" height="30" rx="4" fill="rgba(255,255,255,0.28)"/>
            <rect x="760" y="155" width="110" height="30" rx="4" fill="rgba(255,255,255,0.28)"/>
            <rect x="60"  y="193" width="120" height="24" rx="4" fill="rgba(255,255,255,0.18)"/>
            <rect x="200" y="193" width="120" height="24" rx="4" fill="rgba(255,255,255,0.18)"/>
            <rect x="340" y="193" width="120" height="24" rx="4" fill="rgba(255,255,255,0.18)"/>
            <rect x="480" y="193" width="120" height="24" rx="4" fill="rgba(255,255,255,0.18)"/>
            <rect x="620" y="193" width="120" height="24" rx="4" fill="rgba(255,255,255,0.18)"/>
            <rect x="760" y="193" width="110" height="24" rx="4" fill="rgba(255,255,255,0.18)"/>
        </svg>
        <div class="cr-rd-hero-overlay">
            <h1 class="cr-rd-hero-title">Detail Ruangan:<br>{{ $room->nama_ruangan }}</h1>
        </div>
    </div>

    {{-- BODY: 2 columns --}}
    <div class="cr-rd-body">

        {{-- LEFT: Detail Info --}}
        <div class="cr-rd-left">
            <h2 class="cr-rd-section-title">Detail Ruangan</h2>

            <div class="cr-rd-info-list">
                <div class="cr-rd-info-item">
                    <span class="cr-rd-info-icon">👥</span>
                    <div>
                        <p class="cr-rd-info-label">Kapasitas</p>
                        <p class="cr-rd-info-val">{{ $room->kapasitas }} orang • Lantai {{ ($room->room_id % 3) + 1 }}</p>
                    </div>
                </div>
                <div class="cr-rd-info-item">
                    <span class="cr-rd-info-icon">📍</span>
                    <div>
                        <p class="cr-rd-info-label">Lokasi</p>
                        <p class="cr-rd-info-val">Gedung Utama</p>
                    </div>
                </div>
            </div>

            <div class="cr-rd-facilities">
                <p class="cr-rd-facilities-title">Facilities</p>
                <ul class="cr-rd-fac-list">
                    <li><span>🖥️</span> PC &amp; Monitor</li>
                    <li><span>📶</span> WiFi Kencang</li>
                    <li><span>❄️</span> AC</li>
                    <li><span>📽️</span> Proyektor</li>
                    <li><span>🖵</span> Smart Screen</li>
                </ul>
            </div>

            <p class="cr-rd-desc">
                Ruangan nyaman dengan peralatan lengkap, cocok untuk seminar atau workshop
                hingga {{ $room->kapasitas }} peserta. Sangat strategis di
                Lantai {{ ($room->room_id % 3) + 1 }} Gedung Utama.
            </p>

            @if($room->status === 'tersedia')
                <a href="/booking/perkuliahan?room_id={{ $room->room_id }}"
                   class="cr-rd-booking-btn">Booking Sekarang</a>
            @else
                <span class="cr-rd-booking-btn cr-rd-booking-btn--disabled">Tidak Tersedia</span>
            @endif
        </div>

        {{-- RIGHT: Weekly Schedule --}}
        <div class="cr-rd-right">
            <div class="cr-rd-sched-header">
                <h2 class="cr-rd-section-title">Jadwal Mingguan</h2>
                <div class="cr-rd-sched-actions">
                    <button class="cr-rd-sched-btn cr-rd-sched-btn--active" id="btnHariIni">Lihat Jadwal Hari Ini</button>
                    <button class="cr-rd-sched-btn" id="btnBulanIni">Lihat Jadwal Bulan Ini</button>
                </div>
            </div>

            {{-- Weekly timetable --}}
            <div class="cr-rd-timetable-wrap">
                <table class="cr-rd-timetable">
                    <thead>
                        <tr>
                            <th class="cr-rd-tt-time-col"></th>
                            @foreach(['Mon','Tue','Wed','Thu','Fri','Sat','Sun'] as $day)
                                <th class="cr-rd-tt-day {{ $day === $todayShort ? 'cr-rd-tt-day--today' : '' }}">{{ $day }}</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $hours = ['08:00','09:00','10:00','11:00','12:00','13:00','14:00','15:00','16:00','17:00'];
                            $days  = ['Mon','Tue','Wed','Thu','Fri','Sat','Sun'];

                            // Map schedules into grid: [hour][day] = event
                            $schedGrid = [];
                            foreach ($schedules as $sc) {
                                $dayMap = [
                                    'Senin'  => 'Mon', 'Selasa' => 'Tue',
                                    'Rabu'   => 'Wed', 'Kamis'  => 'Thu',
                                    'Jumat'  => 'Fri',
                                ];
                                $d = $dayMap[$sc->hari] ?? null;
                                if (!$d) continue;
                                $h = substr($sc->jam_mulai, 0, 5);
                                $roundH = (intval(substr($h,0,2))) . ':00';
                                if (!isset($schedGrid[$roundH])) $schedGrid[$roundH] = [];
                                $schedGrid[$roundH][$d] = [
                                    'label'  => $sc->mata_kuliah,
                                    'time'   => substr($sc->jam_mulai,0,5) . ' - ' . substr($sc->jam_selesai,0,5),
                                    'type'   => 'approved',
                                ];
                            }

                            // Map bookings into grid
                            foreach ($bookings as $bk) {
                                $bkDate = \Carbon\Carbon::parse($bk->tanggal);
                                $bkDay  = $bkDate->format('D'); // Mon,Tue,...
                                $h      = (intval(substr($bk->jam_mulai,0,2))) . ':00';
                                if (!isset($schedGrid[$h])) $schedGrid[$h] = [];
                                if (!isset($schedGrid[$h][$bkDay])) {
                                    $schedGrid[$h][$bkDay] = [
                                        'label' => $bk->kegiatan->nama_kegiatan ?? 'Booking',
                                        'time'  => substr($bk->jam_mulai,0,5) . ' - ' . substr($bk->jam_selesai,0,5),
                                        'type'  => $bk->status,
                                    ];
                                }
                            }
                        @endphp

                        @foreach($hours as $hour)
                        <tr>
                            <td class="cr-rd-tt-hour">{{ $hour }}</td>
                            @foreach($days as $day)
                            <td class="cr-rd-tt-cell">
                                @if(isset($schedGrid[$hour][$day]))
                                    @php $ev = $schedGrid[$hour][$day]; @endphp
                                    <div class="cr-rd-event cr-rd-event--{{ $ev['type'] }}">
                                        <span class="cr-rd-event-label">{{ $ev['type'] === 'approved' ? '📗' : ($ev['type'] === 'pending' ? '⏳' : '🔴') }} {{ $ev['label'] }}</span>
                                        <span class="cr-rd-event-time">({{ $ev['time'] }})</span>
                                    </div>
                                @else
                                    <span class="cr-rd-cell-empty"></span>
                                @endif
                            </td>
                            @endforeach
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Legend --}}
            <div class="cr-rd-legend">
                <span class="cr-rd-legend-item cr-rd-legend-item--approved">✓ Approved</span>
                <span class="cr-rd-legend-item cr-rd-legend-item--pending">⏳ Pending</span>
                <span class="cr-rd-legend-item cr-rd-legend-item--maintenance">🔴 Maintenance</span>
                <span class="cr-rd-legend-item cr-rd-legend-item--available">Available</span>
            </div>
        </div>

    </div>{{-- end cr-rd-body --}}

</div>

<script>
(function(){
    const btnHari  = document.getElementById('btnHariIni');
    const btnBulan = document.getElementById('btnBulanIni');

    btnHari.addEventListener('click', function(){
        btnHari.classList.add('cr-rd-sched-btn--active');
        btnBulan.classList.remove('cr-rd-sched-btn--active');
    });
    btnBulan.addEventListener('click', function(){
        btnBulan.classList.add('cr-rd-sched-btn--active');
        btnHari.classList.remove('cr-rd-sched-btn--active');
    });
})();
</script>
@endsection