@extends('layouts.app')

@section('content')
<div class="lg-page">

    {{-- ===================== KIRI: Ilustrasi ===================== --}}
    <div class="lg-left">
        {{-- Ambient blobs --}}
        <div class="lg-left__blob lg-left__blob--1"></div>
        <div class="lg-left__blob lg-left__blob--2"></div>

        {{-- Logo --}}
        <div class="lg-left__logo">
            <span class="lg-logo-campus">Campus</span><span class="lg-logo-room">Room</span>
        </div>

        {{-- Headline --}}
        <div class="lg-left__headline">
            <h1>
                <span class="lg-headline-accent">Booking</span> Ruangan<br>
                Kampus,<br>
                Lebih Mudah dari<br>
                Sebelumnya.
            </h1>
        </div>

        {{-- Floating UI cards --}}
        <div class="lg-left__cards">

            {{-- Calendar card --}}
            <div class="lg-float-card lg-float-card--cal">
                <div class="lg-cal-badge">AVAILABLE</div>
                <div class="lg-cal-header">
                    <span>Juni 2026</span>
                    <div class="lg-cal-nav"><button>‹</button><button>›</button></div>
                </div>
                <div class="lg-cal-days-hdr">
                    <span>Mo</span><span>Di</span><span>Mi</span><span>Ja</span><span>Fr</span><span>Sa</span><span>So</span>
                </div>
                <div class="lg-cal-grid">
                    <span class="lg-cal-e"></span><span class="lg-cal-e"></span><span class="lg-cal-e"></span><span class="lg-cal-e"></span><span class="lg-cal-e"></span>
                    <span>1</span><span>2</span><span>3</span><span>4</span><span>5</span>
                    <span>6</span><span>7</span><span>8</span><span>9</span><span>10</span><span>11</span><span>12</span>
                    <span class="lg-cal-active">13</span><span>14</span><span>15</span><span>16</span><span>17</span><span>18</span><span>19</span>
                    <span>20</span><span>21</span><span>22</span><span>23</span><span>24</span><span>25</span><span>26</span>
                    <span>27</span><span>28</span><span>29</span><span class="lg-cal-today">30</span><span>31</span>
                </div>
            </div>

            {{-- Approved card --}}
            <div class="lg-float-card lg-float-card--approved">
                <div class="lg-approved-icon">✓</div>
                <p class="lg-approved-text">APPROVED</p>
            </div>

            {{-- Available room card --}}
            <div class="lg-float-card lg-float-card--avail">
                <p class="lg-avail-label">Current Room</p>
                <div class="lg-avail-row">
                    <span class="lg-avail-name">Available</span>
                    <span class="lg-avail-badge">Available</span>
                </div>
            </div>

        </div>
    </div>

    {{-- ===================== KANAN: Form ===================== --}}
    <div class="lg-right">
        {{-- Dekorasi circles --}}
        <div class="lg-right__deco lg-right__deco--1"></div>
        <div class="lg-right__deco lg-right__deco--2"></div>

        <div class="lg-form-wrap">

            {{-- Pill brand --}}
            <div class="lg-brand-pill">
                <span class="lg-brand-pill__icon">🟨</span>
                <span class="lg-brand-pill__text">CampusRoom • v1.0 Admin</span>
            </div>

            {{-- Heading --}}
            <div class="lg-form-heading">
                <h2>Selamat Datang<br>Kembali 👋</h2>
                <p>Sistem Peminjaman & Monitoring Ruangan Kampus<br><span class="lg-sub-note"></span></p>
            </div>

            {{-- Error alert --}}
            @if(session('error'))
            <div class="lg-alert-error">
                ⚠️ {{ session('error') }}
            </div>
            @endif

            {{-- Form --}}
            <form action="/login" method="POST" class="lg-form">
                @csrf

                {{-- NIM/NIP --}}
                <div class="lg-field">
                    <label class="lg-field__label">🎓 NIM / NIP</label>
                    <div class="lg-field__input-wrap">
                        <input
                            type="text"
                            name="nim_nip"
                            id="nim_nip"
                            required
                            autocomplete="username"
                            class="lg-input"
                            placeholder="">
                    </div>
                </div>

                {{-- Password --}}
                <div class="lg-field">
                    <label class="lg-field__label">🔒 Password</label>
                    <div class="lg-field__input-wrap lg-field__input-wrap--icon">
                        <input
                            type="password"
                            name="password"
                            id="password"
                            required
                            autocomplete="current-password"
                            class="lg-input lg-input--has-icon"
                            placeholder="">
                        <button type="button" class="lg-eye-btn" id="togglePassword" aria-label="Toggle password">
                            <svg id="eyeIcon" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94"/>
                                <path d="M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19"/>
                                <line x1="1" y1="1" x2="23" y2="23"/>
                            </svg>
                        </button>
                    </div>
                </div>

                {{-- Remember + forgot --}}
                <div class="lg-row-between">
                    <label class="lg-remember">
                        <input type="checkbox" name="remember" class="lg-checkbox">
                        <span>Ingat saya</span>
                    </label>
                    <a href="#" class="lg-forgot">Lupa password?</a>
                </div>

                {{-- Submit --}}
                <button type="submit" class="lg-submit-btn">
                    MASUK SEKARANG
                </button>
            </form>

        </div>
    </div>

</div>

<script>
    // Toggle password visibility
    const toggleBtn = document.getElementById('togglePassword');
    const passwordInput = document.getElementById('password');
    const eyeIcon = document.getElementById('eyeIcon');

    toggleBtn.addEventListener('click', () => {
        const isPassword = passwordInput.type === 'password';
        passwordInput.type = isPassword ? 'text' : 'password';
        eyeIcon.style.opacity = isPassword ? '0.4' : '1';
    });
</script>
@endsection