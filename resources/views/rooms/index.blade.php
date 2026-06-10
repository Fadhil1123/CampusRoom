@extends('layouts.dashboard')

@section('title', 'Daftar Ruangan')

@section('content')
<div class="cr-dash-content">

    {{-- Brand pill --}}
    <div class="cr-dash-brand-pill">
        <span class="cr-dash-brand-pill__icon">🟨</span>
        <span class="cr-dash-brand-pill__text">CampusRoom • v1.0 Admin</span>
    </div>

    {{-- Page title --}}
    <h1 class="cr-rl-page-title">Daftar Ruangan Kampus</h1>

    {{-- Filter bar --}}
    <div class="cr-rl-filterbar">
        <div class="cr-rl-search">
            <span class="cr-rl-search__icon">🔍</span>
            <input type="text" id="searchInput" class="cr-rl-search__input" placeholder="Cari Ruangan...">
        </div>
        <div class="cr-rl-filter">
            <span class="cr-rl-filter__icon">📅</span>
            <select class="cr-rl-filter__select" id="filterTanggal">
                <option value="">Tanggal</option>
                <option value="today">Hari ini</option>
                <option value="week">Minggu ini</option>
                <option value="month">Bulan ini</option>
            </select>
            <span class="cr-rl-filter__caret">›</span>
        </div>
        <div class="cr-rl-filter">
            <span class="cr-rl-filter__icon">🕐</span>
            <select class="cr-rl-filter__select" id="filterJam">
                <option value="">Jam</option>
                <option value="pagi">07:00 – 12:00</option>
                <option value="siang">12:00 – 17:00</option>
                <option value="malam">17:00 – 21:00</option>
            </select>
            <span class="cr-rl-filter__caret">›</span>
        </div>
        <div class="cr-rl-filter">
            <span class="cr-rl-filter__icon">👥</span>
            <select class="cr-rl-filter__select" id="filterKapasitas">
                <option value="">Kapasitas</option>
                <option value="small">1 – 20</option>
                <option value="medium">21 – 50</option>
                <option value="large">50+</option>
            </select>
            <span class="cr-rl-filter__caret">›</span>
        </div>
    </div>

    {{-- Room grid --}}
    <div class="cr-rl-grid" id="roomGrid">
        @forelse($rooms as $room)
        @php
            $variant = ($room->room_id % 4) + 1;
            $lantai  = 'Lantai ' . (($room->room_id % 3) + 1);
            $isAvail = $room->status === 'tersedia';
        @endphp
        <a href="/rooms/{{ $room->room_id }}"
           class="cr-rl-card"
           data-name="{{ strtolower($room->nama_ruangan) }}"
           data-kapasitas="{{ $room->kapasitas }}">

            {{-- Thumbnail --}}
            <div class="cr-rl-card__thumb cr-rl-card__thumb--{{ $variant }}">
                <svg viewBox="0 0 120 85" xmlns="http://www.w3.org/2000/svg" class="cr-rl-svg">
                    <rect x="12" y="6" width="96" height="26" rx="3" fill="rgba(255,255,255,0.55)" stroke="rgba(255,255,255,0.35)" stroke-width="1"/>
                    <line x1="22" y1="14" x2="64" y2="14" stroke="rgba(26,35,64,0.12)" stroke-width="1"/>
                    <line x1="22" y1="20" x2="54" y2="20" stroke="rgba(26,35,64,0.12)" stroke-width="1"/>
                    <line x1="22" y1="26" x2="58" y2="26" stroke="rgba(26,35,64,0.12)" stroke-width="1"/>
                    <rect x="6"  y="42" width="24" height="11" rx="2" fill="rgba(255,255,255,0.50)"/>
                    <rect x="34" y="42" width="24" height="11" rx="2" fill="rgba(255,255,255,0.50)"/>
                    <rect x="62" y="42" width="24" height="11" rx="2" fill="rgba(255,255,255,0.50)"/>
                    <rect x="90" y="42" width="24" height="11" rx="2" fill="rgba(255,255,255,0.50)"/>
                    <rect x="6"  y="59" width="24" height="11" rx="2" fill="rgba(255,255,255,0.38)"/>
                    <rect x="34" y="59" width="24" height="11" rx="2" fill="rgba(255,255,255,0.38)"/>
                    <rect x="62" y="59" width="24" height="11" rx="2" fill="rgba(255,255,255,0.38)"/>
                    <rect x="90" y="59" width="24" height="11" rx="2" fill="rgba(255,255,255,0.38)"/>
                </svg>
                @if(!$isAvail)
                    <span class="cr-rl-card__unavail-label">Tidak Tersedia</span>
                @endif
            </div>

            {{-- Info --}}
            <div class="cr-rl-card__body">
                <p class="cr-rl-card__name">{{ $room->nama_ruangan }}</p>
                <p class="cr-rl-card__lantai">{{ $lantai }}</p>
                <p class="cr-rl-card__meta">Capacity: {{ $room->kapasitas }}</p>
                <p class="cr-rl-card__fasilitas">Fasilitas: 🖥️ 📶 ❄️ AC</p>
                @if($isAvail)
                    {{-- stop propagation supaya klik Booking tidak trigger link card --}}
                    <span class="cr-rl-card__btn"
                          onclick="event.preventDefault();window.location='/booking/perkuliahan?room_id={{ $room->room_id }}'">
                        Booking
                    </span>
                @else
                    <span class="cr-rl-card__btn cr-rl-card__btn--disabled">Tidak Tersedia</span>
                @endif
            </div>

        </a>
        @empty
        <div class="cr-rl-empty"><span>🏫</span><p>Belum ada ruangan terdaftar.</p></div>
        @endforelse
    </div>

    <div class="cr-rl-noresult" id="noResult" style="display:none">
        <span>🔍</span><p>Ruangan tidak ditemukan.</p>
    </div>

</div>
<script>
(function(){
    const s=document.getElementById('searchInput'),
          k=document.getElementById('filterKapasitas'),
          cards=document.querySelectorAll('.cr-rl-card'),
          nr=document.getElementById('noResult');
    function f(){
        const q=s.value.toLowerCase().trim(),kv=k.value;let v=0;
        cards.forEach(c=>{
            const n=c.dataset.name||'',cap=parseInt(c.dataset.kapasitas)||0;
            let show=true;
            if(q&&!n.includes(q))show=false;
            if(kv==='small'&&!(cap>=1&&cap<=20))show=false;
            if(kv==='medium'&&!(cap>=21&&cap<=50))show=false;
            if(kv==='large'&&cap<=50)show=false;
            c.style.display=show?'':'none';if(show)v++;
        });
        nr.style.display=(v===0&&cards.length>0)?'flex':'none';
    }
    s.addEventListener('input',f);k.addEventListener('change',f);
})();
</script>
@endsection