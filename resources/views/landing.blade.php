@extends('layouts.app')

@section('content')

{{-- ========================================
     NAVBAR
======================================== --}}
<nav id="navbar" class="cr-navbar">
    <div class="cr-navbar__inner">
        <a href="/" class="cr-navbar__logo">
            <span class="cr-logo-campus">Campus</span><span class="cr-logo-room">Room</span>
        </a>
        <ul class="cr-navbar__links">
            <li><a href="#tentang">Tentang</a></li>
            <li><a href="#fitur">Fitur</a></li>
        </ul>
        <div class="cr-navbar__actions">
            <a href="/login" class="cr-btn cr-btn--outline">Login</a>
        </div>
        {{-- Mobile hamburger --}}
        <button class="cr-hamburger" id="hamburgerBtn" aria-label="Menu">
            <span></span><span></span><span></span>
        </button>
    </div>
    {{-- Mobile menu --}}
    <div class="cr-mobile-menu" id="mobileMenu">
        <a href="#tentang">Tentang</a>
        <a href="#fitur">Fitur</a>
        <a href="/login" class="cr-btn cr-btn--outline">Login</a>
    </div>
</nav>

{{-- ========================================
     HERO SECTION
======================================== --}}
<section class="cr-hero" id="tentang">
    {{-- Ambient blobs --}}
    <div class="cr-hero__blob cr-hero__blob--yellow"></div>
    <div class="cr-hero__blob cr-hero__blob--blue"></div>

    <div class="cr-hero__inner">
        {{-- LEFT: Text --}}
        <div class="cr-hero__text">
            <span class="cr-badge">✨ Sistem Baru Kampus</span>

            <h1 class="cr-hero__headline">
                <span class="cr-headline-accent">Booking</span> Ruangan<br>
                Lebih Mudah dari<br>
                Sebelumnya.
            </h1>

            <p class="cr-hero__sub">
                Kelola peminjaman ruang kelas, laboratorium, dan aula kampus
                dalam satu platform terintegrasi.
            </p>

            <div class="cr-hero__ctas">
                <a href="/login" class="cr-btn cr-btn--dark cr-btn--lg">Mulai Booking</a>
                <a href="#fitur" class="cr-btn cr-btn--ghost cr-btn--lg">Pelajari Lebih</a>
            </div>

            <div class="cr-hero__stats">
                <div class="cr-stat">
                    <span class="cr-stat__num">120+</span>
                    <span class="cr-stat__lbl">Ruangan</span>
                </div>
                <div class="cr-stat__divider"></div>
                <div class="cr-stat">
                    <span class="cr-stat__num">98%</span>
                    <span class="cr-stat__lbl">Kepuasan</span>
                </div>
                <div class="cr-stat__divider"></div>
                <div class="cr-stat">
                    <span class="cr-stat__num">23%</span>
                    <span class="cr-stat__lbl">Kepuasaingan</span>
                </div>
            </div>
        </div>

        {{-- RIGHT: Floating UI mockup --}}
        <div class="cr-hero__mockup">
            {{-- Background circle blobs --}}
            <div class="cr-mockup__bg-circle cr-mockup__bg-circle--gold"></div>
            <div class="cr-mockup__bg-circle cr-mockup__bg-circle--blue"></div>
            <div class="cr-mockup__bg-circle cr-mockup__bg-circle--sm"></div>

            {{-- Card 1: Calendar --}}
            <div class="cr-float-card cr-float-card--calendar" style="animation-delay:0s">
                <div class="cr-calendar">
                    <div class="cr-calendar__header">
                        <span>Juni 2026</span>
                        <div class="cr-calendar__nav">
                            <button>‹</button>
                            <button>›</button>
                        </div>
                    </div>
                    <div class="cr-calendar__days-header">
                        <span>Mo</span><span>Di</span><span>Me</span><span>Js</span><span>Fr</span><span>Sa</span><span>So</span>
                    </div>
                    <div class="cr-calendar__grid">
                        <span class="cr-cal-empty"></span>
                        <span class="cr-cal-empty"></span>
                        <span class="cr-cal-empty"></span>
                        <span class="cr-cal-empty"></span>
                        <span class="cr-cal-empty"></span>
                        <span>1</span><span>2</span><span>3</span><span>4</span><span>5</span>
                        <span>6</span><span>7</span><span>8</span><span>9</span><span>10</span><span>11</span><span>12</span>
                        <span class="cr-cal-active">13</span><span>14</span><span>15</span><span>16</span><span>17</span><span>18</span><span>19</span>
                        <span>20</span><span>21</span><span>22</span><span>23</span><span>24</span><span>25</span><span>26</span>
                        <span>27</span><span>28</span><span>29</span><span class="cr-cal-today">30</span><span>31</span>
                    </div>
                </div>
            </div>

            {{-- Card 2: Approved checkmark --}}
            <div class="cr-float-card cr-float-card--approved" style="animation-delay:0.5s">
                <div class="cr-approved-icon">✓</div>
                <p class="cr-approved-label">GREEN CHECKMARK</p>
                <p class="cr-approved-sub">APPROVED</p>
            </div>

            {{-- Card 3: Available badge --}}
            <div class="cr-float-card cr-float-card--available" style="animation-delay:1s">
                <p class="cr-available__label">Current Room</p>
                <div class="cr-available__row">
                    <span class="cr-available__name">Available</span>
                    <span class="cr-badge-status cr-badge-status--green">Available</span>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- ========================================
     MARQUEE STRIP
======================================== --}}
<div class="cr-marquee-wrap">
    <div class="cr-marquee-track">
        @php
            $items = [
                'Ruang HMTI','Ruang B-1','Ruang Baca','Pasca Sarjana',
                'Laboratorium Big Data','Laboratorium MTI','Lab Komputer Dasar',
                'Ruang A-14','Ruang A-15','Perpustakaan','Ruang A-16','Lobby',
                'Ruang HMTI','Ruang B-1','Ruang Baca','Pasca Sarjana',
                'Laboratorium Big Data','Laboratorium MTI','Lab Komputer Dasar',
                'Ruang A-14','Ruang A-15','Perpustakaan','Ruang A-16','Lobby',
            ];
        @endphp
        @foreach($items as $item)
            <span>{{ $item }}</span>
            <span class="cr-marquee-dot">•</span>
        @endforeach
    </div>
</div>

{{-- ========================================
     SECTION FITUR
======================================== --}}
<section class="cr-section cr-section--fitur" id="fitur">
    <p class="cr-section__eyebrow">SECTION FITUR</p>

    <div class="cr-fitur-grid">
        @php
            $fiturs = [
                ['icon' => '⚡', 'icon_bg' => 'yellow', 'title' => 'Booking Cepat',
                 'desc' => 'Booking ruangan kampus secara praktis, kapan saja dan di mana saja'],
                ['icon' => '📅', 'icon_bg' => 'blue', 'title' => 'Jadwal Real-time',
                 'desc' => 'Cek ketersediaan ruangan secara langsung dan akurat. Hindari bentrok jadwal dengan sistem pembaruan data yang terintegrasi secara otomatis'],
                ['icon' => '✅', 'icon_bg' => 'yellow', 'title' => 'Approved Instant',
                 'desc' => 'Dapatkan konfirmasi pemesanan secara langsung tanpa menunggu verifikasi manual yang lama. Ruangan langsung siap digunakan setelah dibooking'],
            ];
        @endphp
        @foreach($fiturs as $f)
        <div class="cr-fitur-card">
            <div class="cr-fitur-card__icon cr-fitur-card__icon--{{ $f['icon_bg'] }}">{{ $f['icon'] }}</div>
            <h3 class="cr-fitur-card__title">{{ $f['icon'] }} {{ $f['title'] }}</h3>
            <p class="cr-fitur-card__desc">{{ $f['desc'] }}</p>
            <a href="#" class="cr-fitur-card__link">Border details</a>
        </div>
        @endforeach
    </div>
</section>

{{-- ========================================
     SECTION HOW IT WORKS
======================================== --}}
<section class="cr-section cr-section--how">
    <p class="cr-section__eyebrow">SECTION HOW IT WORKS</p>

    <div class="cr-how-grid">
        @php
            $steps = [
                ['num' => '1.', 'label' => 'Pilih Ruangan'],
                ['num' => '2.', 'label' => 'Isi Form'],
                ['num' => '3.', 'label' => 'Langsung Disetujui'],
            ];
        @endphp
        @foreach($steps as $i => $step)
            <div class="cr-how-step">
                <div class="cr-how-step__circle">{{ $step['num'] }}</div>
                <p class="cr-how-step__label">{{ $step['label'] }}</p>
            </div>
            @if(!$loop->last)
                <div class="cr-how-connector"></div>
            @endif
        @endforeach
    </div>
</section>

{{-- ========================================
     SECTION CTA
======================================== --}}
<section class="cr-section cr-cta-section">
    <div class="cr-cta-inner">
        <h2 class="cr-cta-headline">Mulai kelola<br>ruanganmu hari ini</h2>
        <a href="/login" class="cr-btn cr-btn--white">Masuk Sekarang →</a>
    </div>
</section>

{{-- ========================================
     FOOTER
======================================== --}}
<footer class="cr-footer">
    <div class="cr-footer__inner">
        <div class="cr-footer__brand">
            <div class="cr-footer__logo">
                <span class="cr-footer__logo-emoji">🏫</span>
                <span class="cr-footer__logo-text">
                    <span class="cr-logo-campus">Campus</span><span class="cr-logo-room">Room</span>
                </span>
            </div>
        </div>

        <div class="cr-footer__links">
            <p class="cr-footer__col-title">Tentang</p>
            <a href="#">Fitur</a>
        </div>

        <div class="cr-footer__links">
            <p class="cr-footer__col-title">About us</p>
            <a href="#">Contact</a>
            <a href="#" class="cr-footer__link-underline">Hover Links</a>
        </div>

        <div class="cr-footer__address">
            <p class="cr-footer__col-title">Universitas Lambung Mangkurat</p>
            <p>Banjarmasin<br>Kalimantan Selatan</p>
        </div>
    </div>
</footer>

{{-- ========================================
     SCRIPTS
======================================== --}}
<script>
    // Navbar scroll shadow
    const navbar = document.getElementById('navbar');
    window.addEventListener('scroll', () => {
        navbar.classList.toggle('cr-navbar--scrolled', window.scrollY > 60);
    });

    // Hamburger toggle
    const hamburger = document.getElementById('hamburgerBtn');
    const mobileMenu = document.getElementById('mobileMenu');
    hamburger.addEventListener('click', () => {
        hamburger.classList.toggle('is-open');
        mobileMenu.classList.toggle('is-open');
    });
</script>

@endsection